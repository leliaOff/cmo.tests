<template>
    <div class="container manager">
        <manager-menu></manager-menu>
        <h1>Муниципалитеты</h1>
        <div class="datatable">
            <datatable :columns="columns" :data="rows" filterable paginate></datatable>
        </div>
        <button class="btn btn-primary btn-add" @click="create" data-toggle="modal" data-target="#elementsModal">Добавить</button>
        <municipality-element v-on:update="list"></municipality-element>
    </div>
</template>

<script>

    import MunicipalityElement from './MunicipalityElement.vue';

    /* Компонент - кнопки таблицы */
    Vue.component('actionsMunicipality', {
        template: `<div class="actions">
                    <button class="btn btn-primary" @click="show" data-toggle="modal" data-target="#elementsModal">Редактировать</button>
                    <button class="btn btn-warning" @click="remove">Удалить</button>
                   </div>`,
        props: ['row'],
        methods: {

            show() {
                
                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'municipalityGet', {
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

                self.$store.state.sureModal.text = 'Вы уверены, что хотите удалить муниципалитет "' + self.row.name + '"?';
                self.$store.state.sureModal.action = function() {
                    axios.post(window.baseurl + 'municipalityDelete', {
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
            municipalityElement: MunicipalityElement
        },

        data() {
            return {
                columns: [
                    {label: '#', field: 'id'},
                    {label: 'Код', field: 'code'},
                    {label: 'Наименование', field: 'name'},
                    {label: '', component: 'actionsMunicipality'}
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

                axios.post(window.baseurl + 'municipalitiesList', {
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