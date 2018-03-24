<template>
    <div class="elements">
        <element-item v-for="item in items" :key="item.id" :item="item" :count="items.length" v-on:update="getElementsItems"></element-item>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#elementsModal">Создать новый элемент</button>
            <button type="button" class="btn btn-warning" @click="exit">Список анкет</button>
        </div>

        <!-- Модальное окно: создание нового элемента -->
        <div class="modal fade bs-example-modal-lg" id="elementsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Создание элемента анкеты</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Наименование: *</label></div>
                            <div class="col-sm-10 clearfix"><input type="text" placeholder="наименование" v-model="newItem.title"/>
                                <span class="input-validator title" v-if="validation.title != undefined">{{ validation.title[0] }}</span></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Описание:</label></div>
                            <div class="col-sm-10 clearfix"><textarea type="text" placeholder="описание"
                                                                v-model="newItem.description"></textarea></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Обязательный?</label></div>
                            <div class="col-sm-10 clearfix"><label class="checkbox"><input type="checkbox" v-model="newItem.is_required"/>
                                <span class="alert alert-info">отметьте этот пункт, если ответ на вопрос обязателен</span></label></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Тип элемента</label></div>
                            <div class="col-sm-10 clearfix"><select v-model="newItem.type">
                                    <option value="table">Таблица</option>
                                    <option value="radio">Один из многих</option>
                                    <option value="checkbox">Много из многих</option>
                                    <option value="title">Заголовок и текст</option>
                                    <option value="directory">Выбор из справочника</option>
                                    <option value="input-text">Произвольный текст</option>
                                    <option value="input-number">Целое число</option>
                                    <option value="input-double">Дробное число</option>
                                    <option value="input-date">Дата</option>
                                    <option value="input-web">Адрес сайта</option>
                                    <option value="input-email">Email</option>
                                    <option value="input-phone">Телефон</option>
                                </select></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" @click="create">Создать</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import ElementItem from './ElementItem.vue';

    export default {

        props: ['testId'],

        components: {
            elementItem: ElementItem,
        },

        data() {
            return {
                items: [ ],
                newItem: {
                    title: '', description: '', is_required: false, type: 'table'
                },
                validation: { }
            }
        },

        methods: { 

            getElementsItems() {

                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'elementsList', {
                    id: self.testId
                }).then(function (response) {
                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');
                    if(response.data.status == 'success') self.items = response.data.result;
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

            },

            create() {
                
                let self = this;
                self.validation = {};
                self.$store.state.loader = true;
                
                let data = {
                    
                    title:          this.newItem.title,
                    description:    this.newItem.description,
                    is_required:    this.newItem.is_required,
                    type:           this.newItem.type,
                    sort:           this.items.length,
                    test_id:        this.testId

                };

                axios.post(window.baseurl + 'elementInsert', { data: data }).then(function(response) {

                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');
                    
                    if(response.data.status == 'fail') {
                        
                        self.validation = response.data.error;

                    } else if(response.data.status == 'success') {

                        $('#elementsModal').modal('hide');
                        self.getElementsItems();
                        self.newItem = { title: '', description: '', is_required: false, type: 'table' };           

                    }

                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

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
            this.getElementsItems();
        },
    }
</script>