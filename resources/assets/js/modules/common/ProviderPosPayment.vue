<template>
    <div>
        <b-row>
            <b-col>
                <treeselect  @input="selectPos"
                             :options="options2"
                             placeholder="Выберите точки продаж"
                             v-model="queryHas.provider_pos_id"
                             :normalizer="normalizer"
                             :multiple="true"
                             v-if="options2.length > 1"
                />
            </b-col>
            <b-col sm="12" :md="options2.length > 1 ? 4 : 12">
                <select-date-time-range v-model="dateFilter">
                </select-date-time-range>
            </b-col>
        </b-row>
        <vue-table class="box"
                   :path="tablePath"
                   id="pos-payment"
                   :params = "queryHas"
                   @row-create="openForm"
                   @destroy="destroyRow"
                   ref = "reloadTable"
                   :i18n = "$vueTableI18n"
        />
    </div>
</template>

<script>
    const VueTable = () => import('./../../components/enso/vuedatatable/VueTable.vue');
    import SelectDateTimeRange from "./../manager/components/SelectDateTimeRange";

    export default {
        name: "provider-pos-payment",
        components: {VueTable, SelectDateTimeRange},
        data: function () {
            return {
                crud: null,
                fields: {},
                dateFilter: null,
                options2: [],
                queryHas: {
                        provider_pos_id: null,      // номер POS  передоваемое в запросе к серверу
                        date: null //
                },
            }
        },
        computed: {
            tablePath(){
                return configs.apiUrl + '/provider-pos-payment/init';
            }
        },
        watch: {
            dateFilter(val){
                return this.queryHas.date =  val ;
            }
        },
        created: function () {
            this.crud = this.$initCrud(); // загрузить плагин crud
            this.crud.success = false;
            this.InitPos(this.$route.params.id);
        },
        methods: {
            // переименовываем имена полей для  treeselect
            normalizer(node) {
                return {
                    id: node.key,
                    label: node.name,
                }
            },
            //  событиия treeselect
            selectPos(e) {
                return console.log('событие selected - выполненно');
            },
// инициализация выбора точек продаж
            InitPos(id) {
                let url = configs.apiUrl + '/provider-pos/?provider_id=' + id;
                this.errors = {};
                this.axios.get(url)
                    .then((response_data) => {
                        this.fields = response_data.data[0];
                        this.queryHas.provider_pos_id =  this.fields.pos_id ;//
                        this.options2 = this.fields.pos_tmp;
                        console.log("данные сесии - загружены")
                    })
                    .catch((response_err) => {
                        this.errors = response_err
                    });
                return
            },
            openForm: function (row) {  // редактировать запись из таблицы
                if(row) {
//                        this.$store.state.tabIndex = 2 ;
                    this.dtRowId = row.dtRowId;
                    this.$router.push({name: 'managerPaymentEdit', params: {provider_id: this.$route.params.id ,id: row.dtRowId}});
                }else {
//                    this.$store.state.tabIndex = 2 ;
                    this.$router.push({name: 'managerPaymentEdit', params: {provider_id: this.$route.params.id}});
                }
            },
            destroyRow: function(e) {  // удалить запись из таблицы
                this.crud.deleteData('/provider-pos-payment/', e.dtRowId);
                this.$refs.reloadTable.$children[0].$listeners.reload();
                return   console.log ( "событие Удаление - выполненно "  + e.dtRowId)
            },
        }
    }
</script>

<style scoped>

</style>