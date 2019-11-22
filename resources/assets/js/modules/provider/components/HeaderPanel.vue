<template>
<div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-between mb-4">
        <a class="navbar-brand">{{userName}}</a>
        <div class="btn-group dropleft">

            <b-button variant="outline-warning" @click="goToDetailPayment">
                    Баланс {{balance}} руб.
            </b-button>
            <button type="button" class="btn btn-warning" v-on:click="logout" style="line-height: 12px;">
                <svg class="icon icon-switch"><use xlink:href="#icon-switch"></use></svg>
            </button>
        </div>
    </nav>
</div>
</template>

<script>



    export default {
        components: {},
        data: function () {
            return {
                balance: null,
            }
        },
        name: "header-panel",
        computed: {
            auth: {
                get() {
                    return this.$store.state.auth;
                },
                set(val) {
                    this.$store.state.auth = val;
                }
            },
            userName() {
                return this.$store.state.auth.name;
            }
        },
        watch:{
            '$route': {
                handler: function () {
                    this.initBalance();
                },
                deep: true
            }
        },
        methods:{
            logout(){
                if( ! confirm(this.$t('headerPanel.confirmLogout')) ) {return false;}
                this.auth = false;
            },
            goToDetailPayment: function() {
                this.$router.push({
                    name: 'commonProviderPayment',
                });
            },

            initBalance() {
                let url = configs.apiUrl + '/provider-pos-payment/balance';
                this.errors = {};
                this.axios.get(url)
                    .then((response_data) => {
                        this.balance = response_data.data;
                        console.log("данные баланса - загружены")
                    })
                    .catch((response_err) => {
                        this.errors = response_err
                    });
            }

        },
        created() {
            this.initBalance();
        },
    }

</script>

<style scoped>

</style>