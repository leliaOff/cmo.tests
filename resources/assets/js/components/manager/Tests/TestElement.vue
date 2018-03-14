<template>
    <div class="container manager">
        <manager-menu></manager-menu>
        <div class="content">
            <h1 v-if="$route.params.id == 'create'">Создание новой анкеты</h1>
            <h1 v-else>Редактор анкеты "{{ data.name }}"</h1>
            <nav class="tab"><ul>
                <li><button class="btn btn-tab" v-bind:class="{ active: tab == 'data' }" @click="setTab('data')">Параметры анкеты</button></li><!--
                --><li><button class="btn btn-tab" v-bind:class="{ active: tab == 'elements' }" @click="setTab('elements')" v-if="data.id != 0">Элементы анкеты</button></li><!--
                --><li><button class="btn btn-tab" v-bind:class="{ active: tab == 'results' }" @click="setTab('results')" v-if="data.id != 0">Результаты анкетирования</button></li>
            </ul></nav>
            
            <!-- Параметры анкеты -->
            <div class="tab-item" v-if="tab == 'data'">
                <div class="row">
                    <div class="col-sm-2 clearfix"><label class="input-title">Наименование: *</label></div>
                    <div class="col-sm-10 clearfix"><input type="text" placeholder="наименование" v-model="current.name"/>
                        <span class="input-validator name" v-if="validation.name != undefined">{{ validation.name[0] }}</span></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 clearfix"><label class="input-title">Описание:</label></div>
                    <div class="col-sm-10 clearfix"><textarea type="text" placeholder="описание"
                                                        v-model="current.description"></textarea></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 clearfix"><label class="input-title">Благодарность после прохождения теста:</label></div>
                    <div class="col-sm-10 clearfix"><textarea type="text" placeholder="описание"
                                                        v-model="current.after"></textarea></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 clearfix"><label class="input-title">Состояние: *</label></div>
                    <div class="col-sm-10 clearfix"><select v-model="current.state">
                            <option value="draft" selected>Черновик</option>
                            <option value="published">Опубликован</option>
                        </select></div>
                </div>
                <div class="btn-group btn-group-right">
                    <button type="button" class="btn btn-success" @click="updateDialog">Сохранить</button>
                    <button type="button" class="btn btn-primary" @click="cansel">Отменить</button>
                    <button type="button" class="btn btn-warning" @click="exit">Список анкет</button>
                </div>
            </div>

            <!-- Элементы анкеты -->
            <div class="tab-item" v-if="tab == 'elements'">
                <elements :testId="data.id"></elements>
            </div>

            <!-- Результаты анкеты -->
            <div class="tab-item" v-if="tab == 'results'">
                <results :testId="data.id"></results>
            </div>
        </div>
    </div>
</template>

<script>

    import TestsElements from './Elements.vue';
    import TestsResults from './Results.vue';

    export default {

        data() {
            return {
                tab: 'data',
                data: { 'name': '', 'description': '', 'after': '', 'state': 'draft', id: 0 },
                current: { 'name': '', 'description': '', 'after': '', 'state': 'draft', id: 0 },
                validation: {}
            }
        },

        components: {
            elements: TestsElements,
            results: TestsResults,
        },

        methods: {

            get(id) {
                
                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'testGet', {
                    id: id
                }).then(function (response) {
                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');
                    if(response.data.status == 'success') {
                        self.data = Object.assign({}, response.data.result);
                        self.current = Object.assign({}, response.data.result);
                        if(localStorage['manager_test_tab'] != undefined) {
                            self.tab = localStorage['manager_test_tab'];
                        }
                    }
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

            },

            setTab(tab) {

                this.tab = tab;
                localStorage['manager_test_tab'] = tab;

            },

            updateDialog() {

                let self = this;

                if(self.data.id != 0) {

                    self.$store.state.sureModal.text = 'Вы уверены, что хотите внести изменения в анкету "' + self.data.name + '"?';
                    self.$store.state.sureModal.action = function() {
                        self.update();
                    }
                    $('#sureWindow').modal('show');

                } else {

                    self.update();

                }
                
            },

            update() {

                let self = this;
                self.validation = {};
                self.$store.state.loader = true;
                
                let data = {
                    
                    name: this.current.name,
                    description: this.current.description,
                    after: this.current.after,
                    state: this.current.state,

                };
                var url = this.data.id == 0 ? window.baseurl + 'testInsert' : window.baseurl + 'testUpdate';

                axios.post(url, {
                    
                    data: data, id: this.data.id
                    
                }).then(function (response) {

                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');
                    
                    if(response.data.status == 'fail') {
                        
                        self.validation = response.data.error;

                    } else if(response.data.status == 'success') {

                        if(self.data.id == 0) {
                            self.current.id = response.data.result;
                            self.data = Object.assign({}, self.current);
                            self.$router.push('/manager/test/' + response.data.result);
                        } else {
                            self.data = Object.assign({}, self.current);
                        }

                    }
                }).catch(function (error) {
                    
                    self.$store.state.loader = false;
                    console.log(error);

                });

            },

            cansel() {

                this.current = Object.assign({}, this.data);

            },

            exit() {

                let self = this;
                
                let isChange = false;
                $.each(this.data, function(key, value) {

                    if(self.current[key] != value) {
                        isChange = true;
                        return -1;
                    }

                });
                if(!isChange) {
                    this.$router.push('/manager/tests');
                } else {

                    self.$store.state.sureModal.text = 'Вы уверены, что хотите выйти, не сохранив анкету "' + self.data.name + '"?';
                    self.$store.state.sureModal.action = function() {
                        self.$router.push('/manager/tests');
                    }
                    $('#sureWindow').modal('show');

                }
            },
            
        },

        mounted() {
            if(this.$route.params.id != 'create') this.get(this.$route.params.id);
        },
    }
</script>