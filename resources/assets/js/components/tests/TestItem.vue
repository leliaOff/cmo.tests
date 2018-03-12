<template>
    <div class="container welcome form test-item" :class="current == -1 ? 'finish' : 'start'">
        <!-- Шапка -->
        <welcome-menu v-if="current == -1"></welcome-menu>
        <h1>{{ item.name }}</h1>
        <div class="alert alert-success" v-if="!isFinish">{{ item.description }}</div>
        <div class="alert alert-success" v-else>{{ item.after }}</div>
        <div class="alert alert-warning" v-if="finishResult != ''">{{ finishResult }}</div>
        <!-- Контент -->
        <div class="elements-content" v-if="current != -1">
            
            <!-- Список элементов -->
            <div class="element-item" v-for="(element, i) in elements" :key="element.id" v-if="current == i">
                
                <!-- Деловая часть элемента -->
                <h2>{{ element.title }}</h2>
                <div class="alert alert-info" v-if="element.description" v-html="element.description"></div>
                
                <!-- Структура элемента в зависимости от типа -->
                <div class="element-item-setting">
                    <element-setting-table v-if="element.type == 'table'" :setting="element.data" :oldResult="element.result" v-on:update="setResult"></element-setting-table>
                    <element-setting-checkbox v-if="element.type == 'checkbox'" :setting="element.data" :oldResult="element.result" v-on:update="setResult"></element-setting-checkbox>
                    <element-setting-radio v-if="element.type == 'radio'" :setting="element.data" :oldResult="element.result" :id="element.id" v-on:update="setResult"></element-setting-radio>
                    <element-setting-directory v-if="element.type == 'directory'" :setting="element.data" :oldResult="element.result" v-on:update="setResult"></element-setting-directory>
                </div>

            </div>

            <button class="btn btn-default btn-turn" :class="(current == 0 ? 'disabled' : '')"
                @click="prev"><span class="glyphicon glyphicon-chevron-left">
            </span></button><!--
            --><button class="btn btn-success btn-turn" :class="(isNext == false ? 'disabled' : '')"
                v-if="current != (elements.length - 1)" @click="next"><span class="glyphicon glyphicon-chevron-right">
            </span></button><!--
            --><button class="btn btn-success btn-turn" v-else :class="(isNext == false ? 'disabled' : '')"
                @click="finish">Закончить анкетирование</button>
        
        </div>
        <!-- Подвал -->
        <div class="btn-group">
            <button type="button" class="btn btn-primary" @click="start" v-if="current == -1">Начать анкентирование</button>
            <button type="button" class="btn btn-warning" @click="exit" v-if="current == -1">Все тесты и анкеты</button>
        </div>
    </div>
</template>

<script>

    import ElementSettingTable from './ElementsSettings/Table.vue';
    import ElementSettingCheckbox from './ElementsSettings/Checkbox.vue';
    import ElementSettingRadio from './ElementsSettings/Radio.vue';
    import ElementSettingDirectory from './ElementsSettings/Directory.vue';

    export default {

        components: {
            elementSettingTable: ElementSettingTable,
            elementSettingCheckbox: ElementSettingCheckbox,
            elementSettingRadio: ElementSettingRadio,
            elementSettingDirectory: ElementSettingDirectory,
        },

        data() {
            
            let id = this.$route.params.id;
            let user = localStorage['user_test_' + id] == undefined ? 0 : localStorage['user_test_' + id];
            
            return {
                item: { 'name': '' },
                elements: [],
                current: -1,
                testId: id,
                isNext: true,
                user: user,
                isFinish: false,
                finishResult: ''
            }
        },

        methods: {

            get() {
                
                let self = this;
                self.$store.state.loader = true;
                let id = self.$route.params.id;

                axios.post(window.baseurl + 'frontGetTest', {
                    id: id, user: self.user
                }).then(function (response) {
                    self.$store.state.loader = false;
                    if(response.data.status == 'success') {
                        //Код пользователя
                        if(self.user == 0) {
                            self.user = response.data.user;
                            localStorage['user_test_' + self.testId] = response.data.user;
                        }
                        //Элементы теста
                        self.item = response.data.result;
                        self.elements = response.data.elements;
                        //Шаг
                        let step = isNaN(localStorage['start_test_' + self.testId]) || localStorage['start_test_' + self.testId] == undefined ? -1 : localStorage['start_test_' + self.testId];
                        step = parseInt(step, 10);
                        if(step == -1) return;

                        self.current = step;
                        localStorage['start_test_' + self.testId] = step;
                        self.isNext = self.is_MoveOn();
                    }
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

            },

            setResult(result) {

                 let index = parseInt(localStorage['start_test_' + this.testId], 10);
                 this.elements[index].result = result;
                 this.isNext = this.is_MoveOn();

            },

            next() {
                let index = parseInt(this.current, 10);
                if(index == this.elements.length - 1) return;

                if(this.is_MoveOn() == false) return;

                this.updateRusult();

                index += 1;
                this.current = index;
                localStorage['start_test_' + this.testId] = index;
                
                this.isNext = this.is_MoveOn();
            },

            prev() {
                let index = parseInt(this.current, 10);
                if(index == 0) return;

                this.updateRusult();

                index -= 1;
                this.current = index;
                localStorage['start_test_' + this.testId] = index;
            },

            start() {
                this.current = 0;
                localStorage['start_test_' + this.testId] = 0;
                
                this.user = 0;
                localStorage['user_test_' + this.testId] = 0;

                this.isFinish = false;
                this.get();

                this.finishResult = '';
            },

            finish() {

                if(this.is_MoveOn() == false) return;

                this.updateRusult();
                this.saveResult();

                this.user = 0;
                localStorage['user_test_' + this.testId] = 0;

                this.current = -1;
                localStorage['start_test_' + this.testId] = -1;
                this.isFinish = true;
            },

            exit() {
                this.$router.push('/tests');
            },
            
            updateRusult() {

                let index = this.current;
                if(index < 0 || index >= this.elements.length) return;

                let result = this.elements[index].result;
                if(result == undefined) return;

                this.$store.state.results[index] = {
                    id: this.elements[index].id,
                    type: this.elements[index].type, 
                    result: result
                };              

            },

            saveResult() {

                axios.post(window.baseurl + 'frontResult', {
                    results: this.$store.state.results, user: this.user
                }).then(function (response) { 
                    
                    let self = this;
                    $.each(response.data, function(key, value) {
                        if(value.result == 'fail') {
                            self.finishResult = 'Не удалось сохранить один или несколько ответов';
                        }
                    });

                }).catch(function (error) {
                    console.log(error);
                });

            },

            is_MoveOn() {

                let element = this.elements[this.current];
                if(element == undefined || element.is_required == 0) return true;
                if(element.result == undefined) return false;

                if(element.type == 'table') {
                    
                    //Проверка хотя бы одной отметки в каждой строке
                    let count = 0; // будет получать + за каждою строку
                    let minCount = 0;
                    $.each(element.result, function(i, cols) {
                        minCount++;
                        $.each(cols, function(j, cell) {
                            if(cell == true) {
                                count++;
                                return false;
                            }
                        });
                    });
                    //Если в каждой строке есть хотя бы один true
                    if(count == minCount) return true;

                } else if(element.type == 'checkbox') {
                    
                    //Проверка хотя бы одной отметки в каждой строке
                    let count = 0; 
                    $.each(element.result, function(i, value) {
                        if(value == true) {
                            count++;
                            return false;
                        }
                    });
                    //Если хотя бы один true
                    if(count > 0) return true;

                } else if(element.type == 'radio') {
                    
                    if(element.result != undefined) {
                        return true;
                    }

                } else if(element.type == 'title') {

                    return true;
                    
                } else if(element.type == 'directory') {
                    
                    if(element.result != undefined && element.result != '') {
                        return true;
                    }

                }

                return false;
            }

        },

        mounted() {
            this.get();
        },

    }
</script>