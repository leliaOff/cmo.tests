<template>
    <div class="container manager">
        <manager-menu></manager-menu>
        <div class="content">
            <h1>Менеджер справочников</h1>
            <div class="datatable">
                <datatable :columns="columns" :data="rows" filterable paginate></datatable>
            </div>
            <button class="btn btn-primary btn-add" @click="create" data-toggle="modal" data-target="#elementsModal">Добавить</button>
            <directory-element v-on:update="list"></directory-element>
        </div>
    </div>
</template>

<script>

    import DirectoryElement from './DirectoryElement.vue';

    /* Компонент - кнопки таблицы */
    Vue.component('actionsDirectory', {
        template: `<div class="actions">
                    <button class="btn btn-primary" @click="show" data-toggle="modal" data-target="#elementsModal">Редактировать</button>
                    <button class="btn btn-warning" @click="remove">Удалить</button>
                   </div>`,
        props: ['row'],
        methods: {

            show() {
                
                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'directoryGet', {
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

                self.$store.state.sureModal.text = 'Вы уверены, что хотите удалить "' + self.row.title + '"?';
                self.$store.state.sureModal.action = function() {
                    axios.post(window.baseurl + 'directoryDelete', {
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
            directoryElement: DirectoryElement
        },

        data() {
            return {
                columns: [
                    {label: 'Код', field: 'id'},
                    {label: 'Алиас', field: 'alias'},
                    {label: 'Наименование', field: 'title'},
                    {label: 'Описание', field: 'description'},
                    {label: '', component: 'actionsDirectory'}
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

                axios.post(window.baseurl + 'directoriesList', {
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
                    alias: '', name: '', description: '', id: 0
                };

            }

        },

        mounted() {
            this.list();
        },

    }
</script>