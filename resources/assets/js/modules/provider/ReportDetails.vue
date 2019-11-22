<template>
    <div class="report-details">
        <div>
            <view-tracks v-if="tracks" :tracks="tracks"/>
            <vue-table class="box" ref="table"
                       :path="tablePath"
                       id="trackersReport"
                       @row-show="loadTracks"
                       :params="{ day }"
                       :i18n = "$vueTableI18n"
            />
        </div>
    </div>
</template>

<script>
    import VueTable from "../../components/enso/vuedatatable/VueTable";
    import ViewTracks from "../common/ViewTracks";

    export default {
        components: {
            ViewTracks,
            VueTable
        },
        name: "report-details",

        computed: {
            day(){
                let date = this.$route.params.date.split('.');
                return [date[2], date[1], date[0]].join('-');
            },
            tablePath(){
                return configs.apiUrl + '/report-trackers-details/init';
            }
        },

        data: function () {
            return {
                tracks: null
            }
        },
        methods: {
            loadTracks(row){
                let table = this.$refs.table;
                table.clearHighlighted();
                table.highlight(row.dtRowId);
                this.axios.get(configs.apiUrl + "/report-trackers-details/tracks/" + row.dtRowId).then((response) => {
                    this.tracks = response.data;
                }).catch((response)=>{
                    console.log(response);
                })
            },
        }
    }
</script>

<style lang="scss" scoped>

    .report-details ::v-deep div {
        @import "~@styles/partials/report_details.scss";
    }

</style>