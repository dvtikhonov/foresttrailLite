<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-between mb-4">
        <a class="navbar-brand">{{userName}}</a>

        <div class="btn-group dropleft">

            <b-dropdown variant="outline-warning">
                <template v-slot:button-content class="px-3">
                    {{ $t("headerPanel.menuOptions") }} <svg class="icon icon-cog"><use xlink:href="#icon-cog"></use></svg>
                </template>
                <!--<router-link class="dropdown-item" :to="{name: 'managerTracker'}">{{ $t("headerPanel.menuTracker") }}</router-link>-->
                <!--<router-link class="dropdown-item" :to="{name:'managerUsers'}">{{ $t("headerPanel.menuUsers") }}</router-link>-->
                <!--<router-link class="dropdown-item" :to="{name:'managerCalibration'}">Пересчитать путь</router-link>-->
                <router-link class="dropdown-item" :to="{name:'managerProvidersList'}">Провайдеры</router-link>
                <router-link class="dropdown-item" :to="{name:'managerDeviceList'}">Устройства</router-link>
                <router-link class="dropdown-item" :to="{name:'managerDeviceTariffList'}">Тарифы</router-link>
            </b-dropdown>

            <button type="button" class="btn btn-warning" v-on:click="logout" style="line-height: 12px;">
                <svg class="icon icon-switch"><use xlink:href="#icon-switch"></use></svg>
            </button>
        </div>
    </nav>
</template>

<script>

    export default {
        components: {},
        name: "header-panel",
        computed: {
            managerPanel() {
                return this.$store.state.managerPanel;
            },
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
            },
        },
        methods:{
            logout(){
                if( ! confirm(this.$t('headerPanel.confirmLogout')) ) {return false;}
                this.auth = false;
            },
        },
        mounted() {

        },
    }
</script>

<style scoped>

</style>