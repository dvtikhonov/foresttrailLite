<template>
    <b-list-group flush>
        <b-list-group-item href="#"
                           v-for="user in users"
                           :key="user.id"
                           :active="value === user.id"
                           v-on:click="selectUser(user.id)"
        >
            {{user.name}} {{user.phone}}
        </b-list-group-item>
    </b-list-group>
</template>

<script>
    export default {
        name: "select-user",
        props: ['value'],
        data: function () {
            return {
                users: [],
            }
        },
        mounted: function () {
            this.$nextTick(() => {
                this.getUsers();
            });
        },
        methods: {
            getUsers(){
                this.$http.post('/api/v1/passport/user-list').then((response) => {
                    this.users = response.data;
                }).catch((data) => {
                    console.error('Get user list error: ', data);
                });
            },
            selectUser(id){
               this.$emit('input', id);
            }
        },
    }
</script>

<style scoped>

</style>