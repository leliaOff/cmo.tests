<template>
    <div class="container manager">
        <manager-menu></manager-menu>
        <div class="content">
            <h1>Анкеты и тесты</h1>
            <div class="datatable">
                <datatable :columns="columns" :data="rows" filterable paginate></datatable>
            </div>
            <button class="btn btn-primary btn-add" @click="create">Добавить</button>
        </div>
    </div>
</template>

<script>

    /* Компонент - кнопки таблицы */
    Vue.component('actionsTest', {
        template: `<div class="actions">
                    <button class="btn btn-primary" @click="show">Подробнее</button>
                    <button class="btn btn-warning" @click="remove">Удалить</button>
                   </div>`,
        props: ['row'],
        methods: {

            show() {
                
                this.$router.push('test/' + this.row.id);

            },

            remove() {

                let self = this;

                self.$store.state.sureModal.text = 'Вы уверены, что хотите удалить анкету "' + self.row.name + '"?';
                self.$store.state.sureModal.action = function() {
                    axios.post(window.baseurl + 'testDelete', {
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

        data() {
            return {
                columns: [
                    {label: 'Код', field: 'id'},
                    {label: 'Наименование', field: 'name'},
                    {label: 'Дата создания', field: 'datetime'},
                    {label: 'Состояние', field: 'state'},
                    {label: '', component: 'actionsTest'}
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

                axios.post(window.baseurl + 'testsList', {
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

                //localStorage['manager_mode'] = 'test/create';
                this.$router.push('test/create');

            }

        },

        mounted() {
            this.list();
        },

    }
</script>