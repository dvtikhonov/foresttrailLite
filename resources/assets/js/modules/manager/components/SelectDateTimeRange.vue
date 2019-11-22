<template>
    <date-picker
            range
            lang="ru"
            type="datetime"
            v-model="date"
            :first-day-of-week="1"
            :shortcuts="shortcuts"
            format="YYYY-MM-DD HH:mm:ss"
            value-type="date"
            style="width:100%"
    ></date-picker>

</template>

<script>
    import DatePicker from "vue2-datepicker";

    export default {
        name: "select-date-time-range",
        props: ['value'],

        components: {
            DatePicker
        },

        data:function () {
            return {
                date: this.value,
                shortcuts: [
                    {
                        text: this.$t("manager.dateRange.today"),
                        onClick: (self) => {
                            self.currentValue =  [ new Date(Date.now() - 3600 * 1000 * 24 * 1), new Date() ];
                            self.updateDate(true);
                        }
                    },
                    {
                        text: this.$t("manager.dateRange.Last7Days"),
                        onClick (self) {
                            self.currentValue = [new Date(Date.now() - 3600 * 1000 * 24 * 7), new Date()];
                            self.updateDate(true);
                        }
                    },
                    {
                        text: this.$t("manager.dateRange.Last30Days"),
                        onClick (self) {
                            self.currentValue = [new Date(Date.now() - 3600 * 1000 * 24 * 30), new Date()];
                            self.updateDate(true);
                        }
                    }
                ],
            }
        },

        watch: {
            date: function (val) {
                this.selectUser(val);
            }
        },

        methods: {
            selectUser(id){

                this.$emit('input', id);
            }
        },
    }
</script>

<style scoped>

</style>