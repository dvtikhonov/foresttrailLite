// Плагин crud
export default {
    install(Vue, options) {  // 4. добавление метода экземпляра. http://unetway.com/tutorial/plaginy-javascrpt-frejmvorka-vuejs/
        Vue.prototype.$initCrud = function () {
            return new CommonCRUD(this); // вернуть экхемпляр crud
        }
    }
}

class CommonCRUD  {
    constructor(Vue) {
        this.loaded = true;
        this.success = false;
        this.validateErrors = [];
        this.requestError = [];
        this.responseError = [];
        this.vue = Vue;
    }
    initProcess(){
        this.loaded = false;
        this.success = false;
        // clean errors
        this.validateErrors = [];
        this.requestError = [];
        this.responseError = [];
        this.setRequestState();
    }
    setError(error){
        this.loaded = true;
        this.validateErrors = error.response.data.errors;
        if (error.message && !(error.response.status === 422) && error.response.status )
        {
            this.requestError = error.message;
        }

        this.setRequestState();
    }
    setData(response){
        this.fields = response.data; //сохранить данные.
        this.loaded = true;
        this.setRequestState();
    }
    // передать объект через событие crudRequestState в корневой компанент
    setRequestState(){
        this.vue.$root.$emit( 'crudRequestState' ,{requestSuccess: this.success, requestError: this.requestError });
//            this.vue.$store.state.requestSuccess = this.success;
    }

// Вариант  перерабртанного mixin через  классы с применнием Promise.
// доступно через this.$store.state.cashier.loadData ('/api/posts/')
// запросы к api по сохранению/удалению/модификации данных таблицы
// подключение к базе и имя таблицы заложены в маршруте и контрольна уровне laravel

    loadData (action, key='') {  // если key пустой то выведет все записи таблицы, иначе одну запись по значанию key.
        return new Promise((resolve, reject) => { // обернуть в Promise функцию loadData
            this.initProcess();
            // запрос API к таблице пост, затем сохранить в post данные
//            axios.get(configs.apiUrl + action+key) //  путь api  '/api/posts/'
            this.vue.axios.get(configs.apiUrl+action+key) //  путь api  '/api/posts/'
            // если  асинхронный запрос Promise закончился с положительным результатом  , затем сохранить в fields данные
                .then((response) => {
                    this.setData(response);
                    resolve (response.data);
                }).catch(error => {
                this.setError(error);
                reject(error);  // при ошибке завершение ожидания вернуть ошибочные данные
            });
        });
    }
    // модификация записи  в таблице
    updateData(action, field) {
        return new Promise((resolve, reject) => { // обернуть в Promise функцию patchData
            this.initProcess();
            // запрос API к таблице пост,
            this.vue.axios.patch(configs.apiUrl + action + field.id, field) //отправить  по маршруту /api/posts/key,  данные field, методом patch
            // если  асинхронный запрос Promise закончился с положительным результатом  , затем сохранить в users данные
                .then((response) => {
                    this.success = true;
                    this.setData(response);
                    resolve(response.data); // при правильном завершение ожидания вернуть данные
                })
                // если  асинхронный запрос Promise закончился с отрицательным результатом, сохранить сообщение в error и взвести флаг loading
                .catch((error) => {
                    this.setError(error);
                    reject(error);  // при ошибке завершение ожидания вернуть ошибочные данные
                });
        });
    }

    // сохранение в таблице запись,
    storeData(action, field) {
        return new Promise((resolve, reject) => { // обернуть в Promise функцию patchData
            this.initProcess();
            this.vue.axios.post(configs.apiUrl + action, field)//отправить  по маршруту /api/action/,  данные field, методом post
                .then(response => {
                this.success = true;
                this.setData(response);
                resolve(response.data); // при правильном завершение ожидания вернуть данные
            }).catch(error => {
                this.setError(error);
                reject(error);  // при ошибке завершение ожидания вернуть ошибочные данные
            });
        })
    }

// удалить запись  в таблице ,
    deleteData(action,key1,key2) {
        return new Promise((resolve, reject) => { // обернуть в Promise функцию delete
            if (this.loaded) {
                this.loaded = false;
                this.success = false;
                this.errors = {};

                action = configs.apiUrl + action;

                // проверить наличие второго аргувмента
                (this.vue.axios.delete((key2 == undefined) ? action + key1 : action + key1 + '/' + key2)) //отправить  по маршруту /api/posts/key1,   методом delete
                //отправить  по маршруту /api/posts/key1/key2,   методом delete
                // если  асинхронный запрос Promise закончился с положительным результатом  , затем сохранить в users данные
                    .then((response) => {
                        this.fields = response.data; //сохранить данные.
                        this.loaded = true;
                        this.success = true;
                        resolve(response.data); // при правильном завершение ожидания вернуть данные
                        // если  асинхронный запрос Promise закончился с отрицательным результатом, сохранить сообщение в error и взвести флаг loading
                    }).catch(error => {
                    this.loaded = true;
                    this.errors = error.response.data || error.message;
                    reject(this.errors);  // при ошибке завершение ожидания вернуть ошибочные данные
                    });
            }
        });
    }

}