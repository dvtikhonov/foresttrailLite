export default {
    install(Vue, options) {

        Vue.prototype.$findTrackerByAlias = function (alias) {
            let result = false;
            let trackers = this.$store.state.trackers;
            trackers.forEach((item, index)=>{
                if(item.alias === alias){
                    result = index;
                    return true;
                }
            });
            return result;
        };

        Vue.prototype.$trackersAlias = function (alias) {
            let result = [];
            let trackers = this.$store.state.trackers;
            trackers.forEach((item, index)=>{
                result.push(item.alias);
            });
            return result;
        };

        Vue.prototype.$getCookie = function (name) {
            let matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        };

        Vue.prototype.$setCookie = function (name, value, options) {

            options = options || {};

            var expires = options.expires;

            if (typeof expires == "number" && expires) {
                var d = new Date();
                d.setTime(d.getTime() + expires * 1000);
                expires = options.expires = d;
            }
            if (expires && expires.toUTCString) {
                options.expires = expires.toUTCString();
            }

            value = encodeURIComponent(value);

            var updatedCookie = name + "=" + value;

            for (var propName in options) {
                updatedCookie += "; " + propName;
                var propValue = options[propName];
                if (propValue !== true) {
                    updatedCookie += "=" + propValue;
                }
            }

            document.cookie = updatedCookie;
        };

        Vue.prototype.$deleteCookie = function (name) {
            this.setCookie(name, "", {
                expires: -1
            })
        };
    }
}