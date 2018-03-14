<template>
    <div class="container manager">
        <manager-menu></manager-menu>
        <div class="content">
            <h1>Образовательные организации</h1>
            <div class="datatable">
                <datatable :columns="columns" :data="rows" filterable paginate></datatable>
            </div>
            <button class="btn btn-primary btn-add" @click="create" data-toggle="modal" data-target="#elementsModal">Добавить</button>
            <school-element v-on:update="list"></school-element>
        </div>
    </div>
</template>

<script>

    import SchoolElement from './SchoolElement.vue';

    /* Компонент - кнопки таблицы */
    Vue.component('actionsSchool', {
        template: `<div class="actions">
                    <button class="btn btn-primary" @click="show" data-toggle="modal" data-target="#elementsModal">Редактировать</button>
                    <button class="btn btn-warning" @click="remove">Удалить</button>
                   </div>`,
        props: ['row'],
        methods: {

            show() {
                
                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'schoolGet', {
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

                self.$store.state.sureModal.text = 'Вы уверены, что хотите удалить образовательную организацию "' + self.row.name + '"?';
                self.$store.state.sureModal.action = function() {
                    axios.post(window.baseurl + 'schoolDelete', {
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
            schoolElement: SchoolElement
        },

        data() {
            return {
                columns: [
                    {label: '#', field: 'id'},
                    {label: 'Код', field: 'code'},
                    {label: 'Наименование', field: 'name'},
                    {label: 'Email', field: 'email'},
                    {label: 'Телефон', field: 'phone'},
                    {label: '', component: 'actionsSchool'}
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

                axios.post(window.baseurl + 'schoolsList', {
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
                    name: '', description: '', INN: '', email: '', phone: '', id: 0
                };

            }

        },

        mounted() {
            this.list();
        },

    }
</script>