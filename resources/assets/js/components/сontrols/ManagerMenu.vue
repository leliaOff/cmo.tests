<template>
    <div>
        <div class="manager-menu">
            <a class="logo" @click="home">
                <span class="h1">Центр Мониторинга в Образовании</span>
                <span class="h2">по Астраханской области</span>
                <span class="h3">единая платформа анкетирования</span>
            </a><div class="menu-content">
                <ul class="menu">
                    <li><button class="btn btn-text" @click="router('users')">Пользователи</button></li>
                    <li><button class="btn btn-text">Справочники</button>
                        <ul class="sub-menu">
                            <li><button class="btn btn-text" @click="router('directories')">Менеджер справочников</button></li>
                            <li><button class="btn btn-text" @click="router('schools')">Образовательные организации</button></li>
                            <li><button class="btn btn-text" @click="router('municipalities')">Муниципальные образования</button></li>
                        </ul>
                    </li>
                    <li><button class="btn btn-text" @click="router('tests')">Анкеты и тесты</button></li>
                    <li><button class="btn btn-text" @click="logout">Выход</button></li>
                </ul>
            </div>
        </div>
        <!-- Модальное окно -->
        <modals-component></modals-component>
        <!-- Загрузчик -->
        <loader></loader>
    </div>
</template>

<script>
    export default {
        
        name:   'manager-menu',

        data() {
            return {
                loader: false,
            }
        },

        methods: {
            
            home() {
                this.$router.push('/');
            },

            logout() {
                
                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'logout', { }).then(function (response) {
                    self.$store.state.loader = false;
                    window.userID = 0;
                    window.userName = '';
                    window.userRole = '';               
                    self.$router.push('/');
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });
                
            },

            router(url) {
                
                this.$router.push('/manager/' + url);

            },
        },

        created() {
            
            if(window.userID == 0) this.$router.push('/');

        },

    }
</script>