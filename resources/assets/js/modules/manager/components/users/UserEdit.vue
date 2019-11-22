<template>
    <div>
        <form @submit.stop.prevent="handleSave" ref="form" v-if="item">
            <b-row>
                <b-col sm="12">
                    <b-form-group>
                        <label for="nameInputId">{{$t('manager.users.name')}}</label>
                        <b-form-input type="text" id="nameInputId"
                                      :placeholder="$t('manager.users.name')"
                                      v-model="item.name"
                                      :state="validateState('name')"
                                      aria-describedby="nameLiveFeedback"
                        ></b-form-input>

                        <b-form-invalid-feedback id="nameLiveFeedback" >
                            <template v-if="validateState('name') === false " v-for="nErr in errors.name" >
                                <span  v-text="nErr"></span><br>
                            </template>
                        </b-form-invalid-feedback>
                    </b-form-group>

                    <b-form-group>
                        <label for="phoneInputId">{{$t('manager.users.phone')}}</label>
                        <b-form-input type="text" id="phoneInputId"
                                      :placeholder="$t('manager.users.phone')"
                                      v-model="item.phone"
                                      :state="validateState('phone')"
                                      aria-describedby="phoneLiveFeedback"
                        ></b-form-input>

                        <b-form-invalid-feedback id="phoneLiveFeedback" >
                            <template v-if="validateState('phone') === false " v-for="nErr in errors.phone" >
                                <span  v-text="nErr"></span><br>
                            </template>
                        </b-form-invalid-feedback>
                    </b-form-group>

                    <b-form-group>
                        <label for="groupIdInputId">{{$t('manager.users.groupId')}}</label>
                        <b-form-input type="text" id="groupIdInputId"
                                      :placeholder="$t('manager.users.groupId')"
                                      v-model="item.client.group_number"
                                      :state="validateState('group_number')"
                                      aria-describedby="groupIdLiveFeedback"
                        ></b-form-input>

                        <b-form-invalid-feedback id="groupIdLiveFeedback" >
                            <template v-if="validateState('group_number') === false " v-for="nErr in errors.group_number" >
                                <span  v-text="nErr"></span><br>
                            </template>
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-col>
                <b-col sm="12">
                    <b-button-toolbar class="justify-content-end" aria-label="Toolbar with button groups and dropdown menu">
                        <b-button-group class="mx-1">
                            <b-btn class="btn btn-danger  ml-auto" @click="handleCancel">Отмена</b-btn>
                        </b-button-group>
                        <b-button-group class="mx-1">
                            <b-btn class="btn btn-success success-btn" @click="handleSave">Сохранить</b-btn>
                        </b-button-group>
                    </b-button-toolbar>
                </b-col>
            </b-row>

        </form>
    </div>
</template>

<script>
    export default {
        name: "user-edit",
        props: [
            'value',
        ],

        data: function () {
            return {
                item: null,
                errors:{},
            }
        },

        watch: {
            value: {
                handler: function (val) {
                    this.linkItem = val;
                    this.item = Object.assign({}, val);
                },
                deep: true,
            }
        },

        mounted: function () {
        },

        methods: {
            validateState (field) {
                return (this.errors[field] !== undefined && this.errors[field].length) ? false : null;
            },
            handleSave(){
                let loader = this.$loading.show({
                    container: this.$refs.form
                });
                this.axios.put(configs.apiUrl + "/users/" + this.item.id, this.item).then(response=>{
                    if(response.data.success === true){
                        this.errors = [];
                        this.linkItem = Object.assign(this.linkItem, this.item);
                        this.$root.$emit('bv::hide::modal','modalEditItem');
                        loader.hide();
                    }
                }).catch(e => {
                    let { data } = e.response;
                    if(data.errorType !== undefined && data.errorType === "VALIDATION_ERROR")  {
                        this.errors = data.errors;
                    }
                    else{
                        //console.error(data.errors);
                    }
                    this.$root.$emit('bv::hide::modal','modalEditItem');
                    loader.hide();
                });
            },
            handleCancel(){
                this.$root.$emit('bv::hide::modal','modalEditItem');
            },
        },
    }
</script>

<style scoped>

</style>