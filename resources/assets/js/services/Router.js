import VueRouter from "vue-router";

const routerPush = VueRouter.prototype.push;
VueRouter.prototype.push = function push(location) {
    return routerPush.call(this, location).catch(error=> error);
};

const login = () => import('../modules/login/Index');
const webLocator = () => import('../modules/web_locator/Index');


const crud = () => import('../modules/manager/components/CrudLayout');

const manager = () => import('../modules/manager/components/Layout');
const managerTracker = () => import('../modules/manager/Tracker');
const managerCalibration = () => import('../modules/manager/Calibration');
const managerUsers = () => import('../modules/manager/Users');
const managerProvidersList = () => import('../modules/manager/providers/List');
const managerProvidersForm = () => import('../modules/manager/providers/Form');
const managerPointOfSalesEdit = () => import('../modules/manager/providers/tabs/PointOfSalesEdit');
const managerDeviceList = () => import('../modules/manager/devices/List');
const managerDeviceEdit = () => import('../modules/manager/devices/Form');
const deviceTariffList = () => import('../modules/manager/deviceTariff/List');
const deviceTariffEdit = () => import('../modules/manager/deviceTariff/Form');
const managerPaymentEdit = () => import('../modules/manager/providers/tabs/PaymentEdit');


const commonProviderPayment = () => import('../modules/common/ProviderPosPayment');
const provider = () => import('../modules/provider/components/Layout');
const providerDashboard = () => import('../modules/provider/Dashboard');
const providerReportDetails = () => import('../modules/provider/ReportDetails');

function checkRoutes(vue) {
    let auth = vue.$store.state.auth;
    switch (auth.role) {
        case 'admin':
        case 'manager':
            if(vue.$route.matched && vue.$route.matched[0].path !== "/manager"){
                router.push({path: '/manager/devices'});
            }
            break;
        case 'provider':
            if(vue.$route.matched && vue.$route.matched[0].path !== "/provider"){
                router.push({path: '/provider/'});
            }
            break;
        case 'client':
            if(vue.$route.matched && vue.$route.matched[0].path !== "/web-locator") {
                router.push({path: '/web-locator'});
//                router.push({path: '/provider/'});
            }
            break;
    }
}

const router = new VueRouter({
    routes: [
        { name: 'login', path: '/', component: login },
        {
            name: 'manager',
            path: '/manager',
            component: manager,
            children: [
                // {
                //     name: 'managerTracker',
                //     path: '/', // /manager/tracker
                //     component: managerTracker,
                // },
                // {
                //     name: 'managerCalibration',
                //     path: 'calibration',
                //     component: managerCalibration,
                // },
                // {
                //     name: 'managerUsers',
                //     path: 'users',
                //     component: managerUsers,
                // },
                {
                    name: 'managerProviders',
                    path: 'providers',
                    component: crud,
                    children: [
                        {
                            name: 'managerProvidersList',
                            path: '/',
                            component: managerProvidersList,
                            meta: {
                                breadcrumb: [
                                    { text: 'Провайдеры', to: {name:'managerTracker'} }
                                ]
                            }
                        },
                        {
                            name: 'managerProvidersCreate',
                            path: 'create',
                            component: managerProvidersForm,
                            meta: {
                                isForm: true,
                                breadcrumb: [
                                    { text: 'Провайдеры', to: {name:'managerTracker'} },
                                    { text: 'Создание', to: {name:'managerProvidersCreate'} }
                                ]
                            }
                        },
                        {
                            name: 'managerProvidersEdit',
                            path: 'edit/:id?',
                            component: managerProvidersForm,
                            meta: {
                                isForm: true,
                                breadcrumb: [
                                    { text: 'Провайдеры', to: {name:'managerProvidersList'} },
                                    { text: 'Редактирование', to: {name:'managerProvidersEdit'} }
                                ]
                            },
                        },
                        {
                            name: 'managerProvidersPosEdit',
                            path: 'edit/:provider_id/point-of-sales-edit/:id?',
                            component: managerPointOfSalesEdit,
                            meta: {
                                isForm: true,
                                breadcrumb: [
                                    { text: 'Провайдеры', to: {name:'managerProvidersList'} },
                                    { text: 'Редактирование', to: {name:'managerProvidersEdit'}, params: {id: 'provider_id'}},
                                    { text: 'Филиал', to: {name:'managerProvidersPosCreate'} }
                                ]
                            },
                        },
                        {
                            name: 'managerProviderPayment',
                            path: 'payment/',
                            component: commonProviderPayment,
                            meta: {
                                isForm: true,
                                breadcrumb: [
                                    { text: 'Провайдеры', to: {name:'managerProvidersList'} },
                                    { text: 'Редактирование', to: {name:'managerProvidersEdit'} }
                                ]
                            },
                        },
                        {
                            name: 'managerPaymentEdit',
                            path: 'payment/:provider_id/payment-edit/:id?',
                            component: managerPaymentEdit,
                            meta: {
                                isForm: true,
                                breadcrumb: [
                                    { text: 'Провайдеры', to: {name:'managerProvidersList'} },
                                    { text: 'Редактирование', to: {name:'managerProvidersEdit'}, params: {id: 'provider_id'}},
                                    { text: 'Оформление операции', to: {name:'managerPaymentEdit'} }
                                ]
                            },
                        },



                    ]
                },
                {
                    name: 'managerDevices',
                    path: 'devices',
                    component: crud,
                    children: [
                        {
                            name: 'managerDeviceList',
                            path: '/',
                            component: managerDeviceList,
                            meta: {
                                breadcrumb: [
                                    {text: 'Устройства', to: {name: 'managerDeviceList'}},
                                ]
                            },
                        },
                        {
                            name: 'managerDeviceEdit',
                            path: 'edit/:id?',
                            component: managerDeviceEdit,
                            meta: {
                                isForm: true,
                                breadcrumb: [
                                    { text: 'Устройства', to: {name: 'managerDeviceList' }},
                                    { text: 'Редактирование', to: {name:'managerDeviceEdit'}},
                                ]
                            },
                        },

                    ]
                },
                {
                    name: 'managerDeviceTariff',
                    path: 'device-tariff',
                    component: crud,
                    children: [
                        {
                            name: 'managerDeviceTariffList',
                            path: '/',
                            component: deviceTariffList,
                            meta: {
                                breadcrumb: [
                                    {text: 'Тариф', to: {name: 'managerDeviceTariffList'}},
                                ]
                            },
                        },
                        {
                            name: 'managerDeviceTariffEdit',
                            path: 'edit/:id?',
                            component: deviceTariffEdit,
                            meta: {
                                isForm: true,
                                breadcrumb: [
                                    { text: 'Тариф', to: {name: 'managerDeviceTariffList' }},
                                    { text: 'Редактирование', to: {name:'managerDeviceTariffEdit'}},
                                ]
                            },
                        }
                    ]
                },
            ]
        },
        {
            name: 'provider',
            path: '/provider',
            component: provider,
            children: [
                {
                    name: 'providerDashboard',
                    path: '/', // /manager/tracker
                    component: providerDashboard,
                    meta: {
                        breadcrumb: [
                            { text: 'Отчет по дням', to: {name: 'providerDashboard' }}
                        ]
                    },
                },
                {
                    name: 'providerReportDetails',
                    path: 'report-details/:date',
                    component: providerReportDetails,
                    meta: {
                        breadcrumb: [
                            { text: 'Отчет по дням', to: {name: 'providerDashboard' }},
                            {
                                text: 'day',
                                to: {name:'providerReportDetails'},
                                textReplace: 'day',
                                params: {
                                    'day': 'date'
                                }
                            },
                        ]
                    },
                },
                {
                    name: 'commonProviderPayment',
                    path: 'payment/',
                    component: commonProviderPayment,

                    meta: {
                        isForm: true,
                        breadcrumb: [
                            { text: 'Отчет по дням', to: {name:'providerDashboard'} },
                            { text: 'Баланс', to: {name:'commonProviderPayment'}}
                        ]
                    },
                },
            ]
        },
        {
            name: 'webLocator',
            path: '/web-locator',
            component: webLocator
        },
    ]
});

export {
    router,
    checkRoutes
};