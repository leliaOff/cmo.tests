<template>
    <div>
        <span class="h1">Центр Мониторинга в Образовании</span>
        <span class="h2">по Астраханской области</span>
        <span class="h3">единая система анкетирования</span>
        <div class="actions">
            <button v-for="(item, key) in items"
                v-if="( (item.auth == '') || 
                        (item.auth == 'auth' && userID && (item.role == undefined || item.role == userRole)) || 
                        (item.auth == 'anon' && !userID))"
                @click="clickButton(item)" class="btn" type="button" 
                v-bind:class="[item.class != undefined ? item.class : 'btn-text', item.url != path ? '' : 'active']">
                    {{ item.title }}
            </button>
        </div>
        <!-- Загрузчик -->
        <loader></loader>
    </div>
</template>

<script>
    export default {
        name:   'welcome-menu',
        data() {
            return {
                userID: window.userID,
                userRole: window.userRole,
                path: this.$route.path,
                buttons: {
                    welcome: {
                        url: '', title: 'На главную', auth: ''
                    },
                    manager: {
                        url: 'manager', title: 'Панель управления', auth: 'auth', role: 'admin'
                    },
                    about: {
                        url: 'about', title: 'О проекте', auth: ''
                    },
                    /* login: {
                        url: 'login', title: 'Вход', auth: 'anon'
                    },
                    registration: {
                        url: 'registration', title: 'Регистрация', auth: 'anon'
                    }, */
                    exit: {
                        title: 'Выход', auth: 'auth', click: this.logout
                    }
                }
            }
        },
        computed: {
            items() {
                return this.buttons;
            }
        },
        methods: {
            clickButton(item) {
                if(item.click == undefined) this.$router.push('/' + item.url);
                else item.click();
            },
            logout() {
                
                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'logout', { }).then(function (response) {
                    self.$store.state.loader = false;
                    window.userID = 0;
                    window.userName = '';
                    window.userRole = '';               
                    self.$router.push('/login');
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });
                
            }
        },
    }
</script>