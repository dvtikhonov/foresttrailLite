<template>
    <div>
        <b-row class="justify-content-center">
            <b-col sm="12">
                <b-table striped hover :items="items" :fields="fields">
                    <template slot="actions" slot-scope="data">
                        <b-button variant="link"
                                  @click="rowEdit(data.item)" >
                            <fa icon="edit"/>
                        </b-button>
                    </template>
                </b-table>
            </b-col>
        </b-row>
        <b-row>
            <b-col md="6" class="my-1">
                <b-pagination
                        :total-rows="totalRows"
                        :per-page="perPage"
                        v-model="currentPage"
                        :prev-text="$t('manager.crud.pagination.last')"
                        :next-text="$t('manager.crud.pagination.next')"
                        hide-goto-end-buttons
                        @input="getAll(currentPage)"/>
            </b-col>
        </b-row>
        <b-modal id="modalEditItem"
                 size="lg"
                 ref="modalItem"
                 :title="$t('manager.crud.edit.title')"
                 :cancel-title="$t('manager.crud.edit.cancel')"
                 :ok-title="$t('manager.crud.edit.ok')"
                 v-model="modalEditItem"
                 :hide-footer="true"
        >
            <user-edit v-model="editItem" />
        </b-modal>
    </div>
</template>

<script>

    import UserEdit from "./components/users/UserEdit";
    import { library } from '@fortawesome/fontawesome-svg-core';
    import { faEdit } from '@fortawesome/free-solid-svg-icons';
    library.add(faEdit);

    export default {
        name: "users",

        components: {
            UserEdit,
            library
        },

        data() {
            return {
                totalRows: null,
                currentPage: 1,
                perPage: 10,
                fields: {
                    id: {
                        label: this.$t("manager.users.id"),
                        sortable: true
                    },
                    name: {
                        label: this.$t("manager.users.name"),
                        sortable: false
                    },
                    phone: {
                        label: this.$t("manager.users.phone"),
                        sortable: true
                    },
                    group_number: {
                        key: 'client.group_number',
                        label: this.$t("manager.users.groupId"),
                        sortable: true
                    },
                    actions: {
                        label: '',
                        sortable: false,
                        class:'action'
                    }
                },
                items: [],
                editItem: null,
                modalEditItem: false,
            }
        },

        mounted: function() {
            this.getAll();
        },

        methods: {
            getAll(current) {
                if(current === undefined){
                    current = 1;
                }
                let loader = this.$loading.show();
                this.axios.get(configs.apiUrl + "/users?page=" + (current - 1) +"&limit=" + this.perPage).then((response)=>{
                    if(response.data.success === true) {
                        this.items = response.data.items;
                        this.totalRows = response.data.totalRows
                    }
                    loader.hide();
                }).catch((e)=>{
                    loader.hide();
                });
            },
            rowEdit(item){
                this.modalEditItem = true;
                if( ! item.client){
                    item.client = {}
                }
                this.editItem = item;
            },
            rowDelete(item){

            },
        }
    }
</script>

<style scoped>

</style>