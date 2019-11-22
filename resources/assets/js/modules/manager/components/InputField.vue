<template>
    <div role="group">
        <label :for="randName">{{label}}</label>
        <b-form-input
                :id="randName"
                :value="value"
                @input="input"
                :type="type"
                :state="isValid"
                :aria-describedby="describedBy"
                :placeholder="placeholder"
                :disabled="disabled"
                trim
        ></b-form-input>

        <!-- This will only be shown if the preceding input has an invalid state -->
        <b-form-invalid-feedback :id="randName+'-feedback'">
            {{firstError}}
        </b-form-invalid-feedback>
    </div>
</template>

<script>
    export default {
        props: ['value', 'errors', 'label', 'placeholder', 'type', 'disabled'],
        computed: {
            randName(){
                return Math.random().toString();
            },
            isValid(){
                return this.errors ? this.errors.length === 0 : true;
            },
            describedBy(){
                return this.randName + '-help ' + this.randName + '-feedback';
            },
            firstError(){
                return this.errors ? this.errors[0] : null;
            }
        },
        data() {
            return {
                name: ''
            }
        },
        methods:{
            input(newValue){
                this.$emit('input', newValue);
            }
        }
    }
</script>

<style scoped>

</style>