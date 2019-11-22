<template>
    <div>
        <view-tracks v-if="tracks" :tracks="tracks" class="mb-4"/>
        <b-row v-if="showFilters">
            <b-col xs="6">
                <select-date-time-range v-model="params.coordinates_in_date_range"/>
            </b-col>
        </b-row>
        <vue-table class="box"
                   :path="tablePath"
                   id="list"
                   :params="params"
                   @row-edit="openForm"
                   @row-create="openForm"
                   @row-show="loadTracks"
                   @destroy="destroyRow"
                   @toggle-filters="showFilters = !showFilters"
                   ref="table"
                   :i18n = "$vueTableI18n"
        >
        </vue-table>
    </div>
</template>

<script>
    import ViewTracks from "../../common/ViewTracks";
    import VueTable from "./../../../components/enso/vuedatatable/VueTable.vue";

    import { library } from '@fortawesome/fontawesome-svg-core';
    import {
        faFilter
    } from '@fortawesome/free-solid-svg-icons';
    import SelectDateTimeRange from "../components/SelectDateTimeRange";
    library.add(faFilter);

    export default {
        name: "DeviceList",
        components: {
            SelectDateTimeRange,
            ViewTracks,
            VueTable,
        },
        data: function () {
            return {
                dtRowId: null,
                params: {
                    coordinates_in_date_range: null
                },
                tracks: null,
                showFilters: false
            }
        },
        computed: {
            tablePath(){
                return configs.apiUrl + '/device/init';
            }
        },
        created: function () {
            this.crud = this.$initCrud(); // загрузить плагин crud
            this.crud.success = false;
        },
        methods: {
            openForm: function (row) {  // редактировать запись из таблицы
                if(row) {
                        this.dtRowId = row.dtRowId;
                    this.$router.push({name: 'managerDeviceEdit', params: {id: row.dtRowId}});
                }else {
                    this.$router.push({name: 'managerDeviceEdit'});  // создать запись
                }
            },
            destroyRow: function(e) {  // удалить запись из таблицы
                this.crud.deleteData('/device/', e.dtRowId);
                this.$refs.table.$children[0].$listeners.reload();
                return   console.log ("событие Удаление - выполненно "+ e.dtRowId)
            },
            loadTracks(row){
                let table = this.$refs.table;
                table.clearHighlighted();
                table.highlight(row.dtRowId);
                this.axios.post(configs.apiUrl + "/device/" + row.dtRowId + "/tracks", {date: this.params.coordinates_in_date_range}).then((response) => {
                    if(response.data.length > 0) {this.tracks = [response.data]; return;}
                    this.tracks = null;
                    table.clearHighlighted();
                }).catch((response)=>{
                    console.log(response);
                })
            }
        },
    }
</script>

<style scoped>
</style>