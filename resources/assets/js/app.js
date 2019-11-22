
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import 'core-js/stable'
import 'regenerator-runtime/runtime'

import Vuex from 'vuex';
import VueI18n from 'vue-i18n';
import axios from 'axios';
import VueAxios from 'vue-axios';
import VueRouter from 'vue-router';
import translates from './services/i18n';
import Formatters from './services/Formatters';
import {router, checkRoutes} from './services/Router';
import BootstrapVue from 'bootstrap-vue';
import VueCookies from 'vue-cookies';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import AppMethods from './services/AppMethods';

import CommonStore from './stores/Common';


import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.min.css';

import configs from './configs/default';

// для  treeselect
import Treeselect from '@riophae/vue-treeselect' // select-ор
import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import Crud from './services/Crud'

window.configs = configs;

window.Vue = require('vue');

window.Vue.use(VueRouter);
window.Vue.use(Vuex);
window.Vue.use(VueAxios, axios);
window.Vue.use(Loading);
window.Vue.use(BootstrapVue);
window.Vue.use(VueCookies);

window.Vue.component('fa', FontAwesomeIcon);
// установка форматтеров
window.Vue.use(Formatters);

// для  treeselect
window.Vue.use(Treeselect);
// общий  Crud
window.Vue.use(Crud);
window.Vue.use(AppMethods);

// общая шина хранилиища
const store = new Vuex.Store(CommonStore);

const i18n = new VueI18n({
    locale: 'ru',
    messages: translates,
});

VueCookies.config('7d');

// для  treeselect
Vue.component('Treeselect',  Treeselect );

const app = new Vue({
    router,
    i18n,
    store,
    created() {
        let auth = null;
        if(typeof sessionStorage === 'undefined'){
            auth = JSON.parse(VueCookies.get('auth'));
        }else {
            auth = JSON.parse(sessionStorage.getItem('auth'));
        }
        console.log(auth );
        if(auth){
            this.checkToken(auth.token).then((auth) => {
                this.auth = auth;
            }).catch(() => {
                this.auth = false;
            });
        }else{
            this.auth = false;
        }

        this.windowResize();
        window.addEventListener("resize", this.windowResize);
    },
    computed: {
        common() {
            return this.$store.state;
        },
        auth:{
            get() {
                return this.$store.state.auth;
            },
            set(val) {
                this.$store.state.auth = val;
            }
        },
    },
    watch: {
        auth: {
            handler: function (auth) {
                if(auth){
                    this.setToken();

                    if(typeof sessionStorage === 'undefined'){
                        VueCookies.set('auth', JSON.stringify(auth));
                    } else {
                        sessionStorage.setItem('auth', JSON.stringify(auth));
                    }
                    checkRoutes(this);
                }else{
                    this.$router.push('/');

                    if(typeof sessionStorage === 'undefined'){
                        VueCookies.set('auth', false);
                    }else {
                        sessionStorage.removeItem('auth');
                    }
                }
            },
            deep: true
        },
        '$route': {
            handler: function () {
                checkRoutes(this);
            },
            deep: true
        },
    },
    methods:{
        setToken(){
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.auth.token;
        },
        checkToken(token){
            return new Promise((resolve, reject) => {
                let loader = this.$loading.show();

                this.$http.post('/api/v1/passport/get-details',null, {
                    headers:{'Authorization':'Bearer ' + token}
                }).then((response) => {
                    let auth = {};
                    auth.name = response.data.name;
                    auth.role = response.data.role;
                    auth.id = response.data.id;
                    auth.token = token;
                    loader.hide();
                    resolve(auth);
                }).catch(() => {
                    loader.hide();
                    reject();
                });
            });
        },
        windowResize(){
            this.common.dimension.width = window.outerWidth;
            this.common.dimension.height = window.outerHeight;
        }
    },
    el: '#app',
});