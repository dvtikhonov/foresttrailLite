export default {
    install(Vue, options) {
        Vue.filter('toCurrency', function (value) {
            value = parseFloat(value);
            if(typeof value == 'number') {
                return new Intl.NumberFormat('ru-RU', { maximumFractionDigits: 2}).format(value) + ' руб.';
            }else{
                return value;
            }
        });
        Vue.filter('toDate', function (value) {
            if(typeof value === 'string') {
                let date = new Date(value.replace(/-/g,"/"));
                return date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear();
            }else{
                return value;
            }
        });
        Vue.filter('maximumTwoFractionDigits', function (value) {
            if(typeof value == 'number') {
                return new Intl.NumberFormat('ru-RU', { maximumFractionDigits: 2}).format(value);
            }else{
                return value;
            }
        });
    }
}