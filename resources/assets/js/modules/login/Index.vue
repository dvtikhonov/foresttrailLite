<template>
    <b-container>
        <b-row>
            <b-form id="Login" class="form-signin" @submit="submitForm">
                <b-alert variant="danger"
                         dismissible
                         :show="errorCountDown"
                         @dismissed="errorCountDown=0"
                         @dismiss-count-down="countDownChanged">
                    <p class="text-left">{{error}}</p>
                    <b-progress variant="warning"
                                :max="countDownSec"
                                :value="errorCountDown"
                                height="4px">
                    </b-progress>
                </b-alert>
                <h1 class="h3 mb-3 font-weight-normal">{{$t('auth.title')}}</h1>
                <b-form-group>
                    <b-form-input id="inputEmail"
                                  type="text"
                                  v-model="form.email"
                                  required
                                  placeholder="Email или Номер телефона">
                    </b-form-input>
                    <b-form-input id="inputPassword"
                                  type="password"
                                  v-model="form.password"
                                  required
                                  placeholder="Пароль">
                    </b-form-input>
                </b-form-group>

                <div class="forgot">
                    <a href="reset.html">Забыли пароль?</a>
                </div>
                <div class="checkbox mb-3">
                    <label>
                        <input type="checkbox" value="remember-me"> Remember me
                    </label>
                </div>

                <b-button type="submit" variant="primary" size="lg" :block="true">Вход</b-button>
            </b-form>
        </b-row>
    </b-container>
</template>

<script>
    export default {
        name: "login",

        computed: {
            auth: {
                get() {
                    return this.$store.state.auth;
                },
                set(val) {
                    this.$store.state.auth = val;
                }
            },
        },

        data:function () {
            return {
                form: {
                    password: '12345678',
                    email: null,
                },
                countDownSec: 10,
                errorCountDown: 0,
                error: null,
            }
        },

        methods: {
            countDownChanged (countDownSec) {
                this.errorCountDown = countDownSec;
            },
            submitForm(e){
                e.preventDefault();

                let loader = this.$loading.show();

                let data = {
                    password: this.form.password,
                };
                if(this.form.email.indexOf('@') !== -1){
                    data.email = this.form.email;
                }else{
                    data.phone = this.form.email;
                }
                console.log(data);
                this.$http.post('/api/v1/passport/login', data).then((response) => {
                    this.auth = {};
                    this.auth.name = response.data.name;
                    this.auth.role = response.data.role;
                    this.auth.token = response.data.token;
                    this.auth.id = response.data.id;
                    this.$root.checkToken(response.data.token).then(()=>{
                        loader.hide();
                    });
                }).catch((data) => {
                    this.error = data.response.data.error;
                    this.errorCountDown = this.countDownSec;
                    loader.hide();
                });
            }
        }
    }
</script>

<style scoped>
    .form-signin {
        width: 100%;
        max-width: 330px;
        padding: 15px;
        margin: 0 auto;
        text-align: center;
        background: #383838;
    }
</style>