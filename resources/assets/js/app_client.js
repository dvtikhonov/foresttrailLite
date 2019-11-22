
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');

import configs from './configs/default';
window.configs = configs;

import Index from "./modules/client/Index.vue";
import Vuex from "vuex";
import ClientStore from "./stores/Client";
import axios from 'axios';
import VueAxios from 'vue-axios';
import ClientMethods from './services/ClientMethods'

import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.min.css';

window.Vue.use(Loading);
window.Vue.use(Vuex);
window.Vue.use(VueAxios, axios);
window.Vue.use(ClientMethods);

const store = new Vuex.Store(ClientStore);

const app = new Vue({
    store,
    components: {Index},
    template: '<index/>',
    created() {
        let trackers = null;
        if(localStorage){
            trackers = localStorage.getItem('trackers');
        }else {
            trackers = this.$getCookie('trackers');
        }

        if(trackers) {this.common.trackers =  JSON.parse( trackers );}

        this.windowResize();
        window.addEventListener("resize", this.windowResize);
        this.$on('windowResize', this.windowResize);
    },
    computed: {
        common() {
            return this.$store.state;
        },
    },
    methods:{
        windowResize(){
            this.common.dimension.width = window.innerWidth;
            this.common.dimension.height = window.innerHeight;
        }
    },
    watch: {
        'common.trackers': {
            handler: function (val) {
                if(localStorage) {
                    localStorage.setItem('trackers', JSON.stringify(val));
                }else{
                    this.$setCookie('trackers', JSON.stringify(val), { expires: 2 });
                }
            },
            deep: true
        },
    },
    el: '#app',
});