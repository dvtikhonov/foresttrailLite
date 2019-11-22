<template>
    <div
        :class="{'general-form': isForm}">
        <div  v-if="isForm">
            <div  v-if = "requestError.length >0 "  class = "text-danger" >Ошибка - {{requestError}}</div>
            <b-alert v-if = "requestSuccess "  variant="success"  show dismissible>Запись сохраненна</b-alert>
        </div>
        <router-view ref="view"></router-view>
        <div class="group-buttons" v-if="isForm">
            <button class="btn btn-primary" @click="save">Сохранить</button>
            <router-link data-toggle="buttons"  class="btn btn-secondary"
                         :to="toUp">Отмена
            </router-link>
        </div>
    </div>
</template>

<script>
    export default {
        name: "crudLayout",
        data: function () {
            return {
                requestError:  {},
                requestSuccess: null,
            }
        },
        computed: {
            isForm(){
                let meta = this.$route.meta;
                return meta && meta.isForm ? meta.isForm : false;
            },
            toUp(){
                let meta = this.$route.meta;

                if(meta && meta.breadcrumb){
                    return meta.breadcrumb[meta.breadcrumb.length-2].to;
                } else {
                    let match = this.$route.matched;
                    if (match.length > 2) {
                        return {path: match[match.length - 2].path};
                    }
                    return false;
                }
            }
        },
        created() {
            // обслуживание событий 'crudRequestState' от всех ниже стоящих компанентов
          this.$root.$on('crudRequestState',(state)=> {
              this.requestError = state.requestError;
              this.requestSuccess = state.requestSuccess;
          })
        },
        methods:{
            save(){
                this.$refs.view.$emit('saveForm');
            }
        }
    }
</script>