<template>
    <div class="container welcome form">
        <welcome-menu></welcome-menu>
        <h1>Вход</h1>
        <div class="alert alert-danger" v-if="validation != ''">{{ validation }}</div>
        <div class="form">
            <div class="row">
                <div class="col-sm-5 clearfix"><label class="title">Адрес электронной почты:</label></div>
                <div class="col-sm-7 clearfix"><input type="text" name="email" placeholder="user@mail.ru" v-model="email"/></div>
            </div>
            <div class="row">
                <div class="col-sm-5 clearfix"><label class="title">Пароль:</label></div>
                <div class="col-sm-7 clearfix"><input type="password" name="password" v-model="password"/></div>
            </div>
            <div class="row">
                <div class="col-sm-12 clearfix"><button class="btn btn-primary" @click="login">Войти</button></div>
            </div>
        </div>
        <!-- Загрузчик -->
        <loader></loader>
    </div>
</template>

<script>
    export default {

        data() {
            return {
                email: '',
                password: '',
                validation: ''
            }
        },

        methods: {
            
            login() {
                
                let self = this;
                self.validation = '';
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'login', {
                    email: this.email,
                    password: this.password
                }).then(function (response) {
                    self.$store.state.loader = false;                    
                    if (response.data.status === 'fail') {
                        self.validation = response.data.error;
                    } else {
                        window.userID = response.data.result.id;
                        window.userName = response.data.result.name;
                        window.userRole = response.data.result.role;
                        self.$router.push('/');
                    }
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });
                
            },

        },

        mounted() {
            if(window.userID != 0) this.$router.push('/');
        },

    }
</script>