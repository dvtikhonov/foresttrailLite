<template>
    <b-container v-if="auth.token" class="pb-3">
        <header-panel></header-panel>
        <breadcrumb-dynamic/>
        <router-view class="page"></router-view>
    </b-container>
</template>

<script>
    import HeaderPanel from './HeaderPanel';
    import BreadcrumbDynamic from "./BreadcrumbDynamic";

    export default {
        components: {
            BreadcrumbDynamic,
            HeaderPanel
        },
        name: "layout",
        computed: {
            auth() {
                return this.$store.state.auth;
            },
        },
    }
</script>

<style scoped>

</style>