<template>
    <b-breadcrumb :items="breadcrumbList" v-show="breadcrumbList" dusk="breadcrumb-component"></b-breadcrumb>
</template>

<script>
    export default {
        name: "breadcrumb-dynamic",

        data: function () {
            return {
                breadcrumbList: null
            }
        },

        mounted () {
            this.updateList()
        },

        watch: {
            $route() { this.updateList() }
        },

        methods: {
            routeTo (pRouteTo) {
                if (this.breadcrumbList[pRouteTo].link) this.$router.push(this.breadcrumbList[pRouteTo].link)
            },
            updateList () {

                let metaBreadcrumb = this.$route.meta.breadcrumb;
                for(let indexMeta in metaBreadcrumb){
                    if(metaBreadcrumb[indexMeta].hasOwnProperty('params')){

                        // добавленеие параметров в хлебные крошки  парметров из текущего  маршрута
                        // params: {id: 'provider_id'} в мета описании. В маршруте ищем значение 'provider_id'.
                        // Значение добавляем в to как параметр (https://bootstrap-vue.js.org/docs/reference/router-links)
                        let setParams = {};
                        for(let param in metaBreadcrumb[indexMeta].params){
                            let paramAlias = metaBreadcrumb[indexMeta].params[param];
                            setParams[param] = this.$route.params[paramAlias];
                            if(metaBreadcrumb[indexMeta].textReplace){
                                metaBreadcrumb[indexMeta].text = metaBreadcrumb[indexMeta].textReplace.replace(param, setParams[param]);
                            }
                        }
                        metaBreadcrumb[indexMeta].to['params'] = setParams;
                    }
                }
                this.breadcrumbList = metaBreadcrumb;
            }
        }
    }
</script>

<style scoped>

</style>