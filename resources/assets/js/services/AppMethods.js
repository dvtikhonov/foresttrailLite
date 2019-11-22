export default {
    install(Vue, options) {

        Vue.prototype.$vueTableI18n = function (e) {
            return e ? this.$t('VueTablei18n.'+e) : '';
        };

    }
}