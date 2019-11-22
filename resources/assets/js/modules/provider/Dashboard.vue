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
        </b-row>
        <div>
            <vue-table class="box"
                       :path="tablePath"
                       id="trackersReport"
                       @row-show="showDetails"
                       :params = "queryHas"
                       :i18n = "$vueTableI18n"

            />
        </div>
    </div>
</template>

<script>
    import VueTable from "../../components/enso/vuedatatable/VueTable";

    export default {
        components: {VueTable},
        name:       "dashboard",
        data: function () {
            return {
//                crud: null,
//                fields: {},
//                dateFilter: null,
                options2: [],
                queryHas: {
                    provider_pos_id: null,      // номер POS  передоваемое в запросе к серверу
                    day: null //
                },
            }
        },

        computed: {
            tablePath(){
                return configs.apiUrl + '/report-trackers/init';
            }
        },
        created: function () {
            // this.crud = this.$initCrud(); // загрузить плагин crud
            // this.crud.success = false;
            this.InitPos(null);
        },
        methods: {
            showDetails(row){
                this.$router.push({name: 'providerReportDetails', params: {date: row.dtRowId}});
            },
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
        }
    }
</script>

<style scoped>

</style>