<template>
    <div class="container manager">
        <manager-menu></manager-menu>
        <div class="content">
            <h1>Пользователи</h1>
            <div class="datatable">
                <datatable :columns="columns" :data="rows" filterable paginate></datatable>
            </div>
            <button class="btn btn-primary btn-add" @click="create" data-toggle="modal" data-target="#elementsModal">Добавить</button>
            <user-element v-on:update="list"></user-element>
        </div>
    </div>
</template>

<script>

    import UserElement from './UserElement.vue';

    /* Компонент - кнопки таблицы */
    Vue.component('actionsUser', {
        template: `<div class="actions">
                    <button class="btn btn-primary" @click="show" data-toggle="modal" data-target="#elementsModal">Редактировать</button>
                    <button class="btn btn-warning" @click="remove">Удалить</button>
                   </div>`,
        props: ['row'],
        methods: {

            show() {
                
                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'userGet', {
                    id: self.row.id
                }).then(function (response) {
                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');
                    if(response.data.status == 'success') self.$store.state.current = response.data.result;
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

            },

            remove() {

                let self = this;

                self.$store.state.sureModal.text = 'Вы уверены, что хотите удалить пользователя "' + self.row.name + '"?';
                self.$store.state.sureModal.action = function() {
                    axios.post(window.baseurl + 'userDelete', {
                        id: self.row.id
                    }).then(function (response) {
                        self.$store.state.loader = false;                    
                        if(response.data.status == 'relogin') self.$router.push('/');
                        if(response.data.status == 'success') self.$store.state.rows = response.data.result;
                    }).catch(function (error) {
                        self.$store.state.loader = false;
                        console.log(error);
                    });
                }

                $('#sureWindow').modal('show');

            }
        }
    });

    export default {

        components: {
            userElement: UserElement
        },

        data() {
            return {
                columns: [
                    {label: 'Код', field: 'id'},
                    {label: 'Логин', field: 'name'},
                    {label: 'Роль', field: 'role'},
                    {label: '', component: 'actionsUser'}
                ]
            }
        },

        computed: {
            rows() {
                return this.$store.state.rows
            }
        },

        methods: {

            list() {
                
                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'usersList', {
                    // ...
                }).then(function (response) {
                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');
                    if(response.data.status == 'success') self.$store.state.rows = response.data.result;
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

            },

            create() {

                this.$store.state.current = {
                    email: '', role: 'user', password: '', confirmation: '', id: 0
                };

            }

        },

        mounted() {
            this.list();
        },

    }
</script>