<template>
    <div>
        <b-row>
            <b-col>
                <h3  > Устройство  id:  {{ fields.id}} </h3>
            </b-col>
            <b-col sm="12" md="4" class="text-right">
                <button title="переход на редактирование POS провайдера" class="btn btn-primary w-100 my-3"
                        v-if = "this.fields.provider_id && this.fields.provider_pos_id"
                        @click="goToPOSedit">
                  Перейти на точку продаж id {{fields.provider_pos_id}}</button>
            </b-col>
        </b-row>
        <form>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <input-field label="Наименование приборв" placeholder="Z-80"
                            v-model="fields.name"
                            :errors="validateErrors.name"
                    />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <input-field  label="Alias"  placeholder="LL20"
                                  v-model="fields.alias"
                                  :errors="validateErrors.alias"
                    />
                </div>
                <div class="form-group col-md-6">
                    <input-field  label="Опции/Примечания"  placeholder="введите примечания"
                                  v-model="fields.options"
                                  :errors="validateErrors.options"
                    />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <input-field  label="Код IMEI"  placeholder="35-419002-389644-3"
                                  v-model="fields.imei"
                                  :errors="validateErrors.imei"
                    />
                </div>
                <div class="form-group col-md-6">
                    <h5> Тариф </h5>
                    <treeselect
                            :options="options2"
                            placeholder="Выберите тариф"
                            v-model="fields.device_tariff_id"
                            :normalizer="normalizer"
                    />
                </div>
            </div>

        </form>
    </div>
</template>

<script>

    import { library } from '@fortawesome/fontawesome-svg-core';
    import {
        faSave
    } from '@fortawesome/free-solid-svg-icons';
    import InputField from "./../components/InputField";

    library.add([
        faSave
    ]);
    // для  treeselect
    const simulateAsyncOperation = fn => {
        setTimeout(fn, 2000)
    };

    export default {
        name: "DeviceEdit",
        components: {InputField},
        props: ['dtRowId'],
        data() {
            return{
                options2: [],
                breadcrumbList: null,
                crud: null,
                provId: null,
                fields: { id: 0 },
            }
        },
        computed: {
            validateErrors(){
                return this.crud.validateErrors;
            },
            // requestError(){
            //     return this.crud.requestError;
            // },
            // requestSuccess(){
            //     return this.crud.success;
            // }
        },
        created: function () {
            this.crud = this.$initCrud(); // загрузить плагин crud
            this.fields.id = this.$route.params.id;
            this.crud.success = false;
            this.crud.setRequestState();

            this.$on('saveForm', this.saveDataCustom); //слушатель  исполнения от общей кнопки сохранить

            this.initTariffList(this.fields.id);
            return;
        },
        methods:  {
            // переименовываем имена полей для  treeselect, и еще для вычесления ссылки селектора вызваем  -  temp1.$children[4].openMenu()  temp1 - это this сохраненная в глобальной переменной temp1
            normalizer(node) {
                return {
                    id: node.key,
                    label: node.name,
                }
            },

            // инициализация выбора тарифа
            initTariffList () {
                this.crud.loadData('/device-tariff/')
                    .then((response_data) => {
                        this.options2 = response_data[0];
                        this.initData();
                        console.log("данные treeselect - загружены")
                    })
                    .catch((response_err) => {
                        this.errors = response_err
                    });
            },

            initData(){
                if (this.fields.id) {
                    let loader = this.$loading.show();  // включить лоадер
                    this.crud.loadData('/device/', this.fields.id)
                        .then((response_data) => {       // обработка promise
                            this.fields = this.crud.fields;
                            loader.hide();  // выключить лоадер
                            return (response_data);
                        })
                        .catch((response_err) =>{
                            console.log ( 'error created: id:'+ this.fields.id);
                            loader.hide();  // выключить лоадер
                            return (response_err);
                        });
                }
                return;
            },
            goToPOSedit: function() {
                this.$router.push({name: 'managerProvidersPosEdit',
                        params: {provider_id: this.fields.provider_id ,
                            id: this.fields.provider_pos_id}});
            },

            saveDataCustom: function () {
                if (this.fields.id > 0)
                {
                    let loader;
                    loader = this.crud.updateData('/device/', this.fields);
                    loader.then((response_data) =>{       // обработка promise
//                            this.$refs.reloadTable.$refs.table.reset();// перезагрузить данные в таблице при помощи функции из  CoreTable
                        return (response_data) ;
                    })
                        .catch((response_err) =>{
//                        this.errors = response_err; // загрузить ошибки полученные от запроса
                        return (response_err) ;
                        });
                } else
                    {
                        let loader;
                        loader = this.crud.storeData('/device/', this.fields);
                        loader.then((response_data) =>{       // обработка promise
                            this.fields.id = this.crud.fields.id;
                            console.log ( "Запись - сохраненна "  + this.fields.id)
                        })
                        .catch((response_err) =>{
//                                this.errors.push(response_err); // загрузить ошибки полученные от запроса
                            return (response_err) ;
                        });
                    }
                console.log ( 'запрос на сохранение оправленa saveDataCustom /device/ ');
                return;
            },

        }
    }
</script>

<style lang="scss" scoped>

    .table-wrapper  {

        &.is-rounded {
            border-radius: 0.5em;

            .top-controls {
                border-radius: 0.5em 0.5em 0 0;
            }

            .bottom-controls {
                border-radius: 0 0 0.5em 0.5em;
            }
        }

        .table-responsive {
            position: relative;
            display: block;
            width: 100%;
            min-height: .01%;
            overflow-x: auto;

            .table {
                font-size: 0.95em;

                td, th {
                    vertical-align: middle;
                }
            }
        }

        .no-records-found {
            padding: 1em;
        }
    }

</style>



