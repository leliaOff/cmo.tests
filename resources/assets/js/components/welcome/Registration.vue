<template>
    <div class="container welcome form">
        <welcome-menu></welcome-menu>
        <h1>Регистрация</h1>
        <div class="form" v-if="!success">
            <div class="row">
                <div class="col-sm-5 clearfix"><label class="title">Адрес электронной почты:</label></div>
                <div class="col-sm-7 clearfix"><input type="text" name="email" placeholder="user@mail.ru" v-model="email"/>
                    <span class="input-validator" v-if="validation.email != undefined">{{ validation.email[0] }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5 clearfix"><label class="title">Пароль:</label></div>
                <div class="col-sm-7 clearfix"><input type="password" name="password" v-model="password"/>
                    <span class="input-validator password" v-if="validation.password != undefined">{{ validation.password[0] }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5 clearfix"><label class="title">Повторите пароль:</label></div>
                <div class="col-sm-7 clearfix"><input type="password" name="repassword" v-model="repassword"/></div>
            </div>
            <div class="row">
                <div class="col-sm-12 clearfix"><button class="btn btn-primary" @click="registration">Регистрация</button></div>
            </div>
        </div>
        <div class="alert alert-success" v-if="success">Регистрация успешно пройдена. Для продолжения перейтиде на страницу входа</div>
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
                repassword: '',
                validation: { },
                success: false
            }
        },

        methods: {
            
            registration() {                
                
                let self = this;
                self.validation = { };
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'registration', {
                    email: this.email,
                    password: this.password,
                    repassword: this.repassword,
                }).then(function (response) {
                    self.$store.state.loader = false;
                    if (response.data.status === 'fail') {
                        self.validation = response.data.error;
                    } else {
                        self.success = true;
                    }
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });
            },

        },

        mounted() {
            if(window.userID != 0) this.welcome();
        },

    }
</script>