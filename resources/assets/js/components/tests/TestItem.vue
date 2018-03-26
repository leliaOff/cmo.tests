<template>
    <div class="container welcome form test-item" :class="current == -1 ? 'finish' : 'start'">
        <!-- Шапка -->
        <welcome-menu v-if="current == -1"></welcome-menu>

        <!-- Ошибка ссылки -->
        <div v-if="isValid == -1" class="alert alert-danger">Внимание! Вы перешли по несуществующей ссылке. Прохождение теста по этой ссылке невозможно</div>
        
        <div v-if="isValid == 1">
            
            <h1>{{ item.name }}</h1>
            <h3 v-if="directoryTitle!= ''">{{ directoryTitle }}</h3>

            <div class="alert alert-success" v-if="!isFinish">{{ item.description }}</div>
            <div class="alert alert-success" v-if="isFinish && finishResult == ''">{{ item.after }}</div>
            <div class="alert alert-danger" v-if="finishResult != ''">{{ finishResult }}</div>
            
            <!-- Контент -->
            <div class="elements-content" v-if="current != -1">
                
                <!-- Список элементов -->
                <div class="element-item" v-for="(element, i) in elements" :key="element.id" v-if="current == i">
                    
                    <!-- Деловая часть элемента -->
                    <h2>{{ current + 1 }}. {{ element.title }}</h2>
                    <div class="alert alert-info" v-if="element.description" v-html="element.description"></div>
                    <image-gallery v-model="element.files"></image-gallery>
                    
                    <!-- Структура элемента в зависимости от типа -->
                    <div class="element-item-setting">

                        <!-- Таблица -->
                        <element-setting-table          v-if="element.type == 'table'"
                            :setting="element.data" :oldResult="element.result" v-on:update="setResult">
                        </element-setting-table>
                        
                        <!-- Много из многих -->
                        <element-setting-checkbox       v-if="element.type == 'checkbox'"
                            :setting="element.data" :index="i" v-on:update="setResult">
                        </element-setting-checkbox>

                        <!-- один из многих -->
                        <element-setting-radio          v-if="element.type == 'radio'"
                            :setting="element.data" :index="i" :id="element.id" v-on:update="setResult">
                        </element-setting-radio>

                        <!-- Элемент справочника -->
                        <element-setting-directory      v-if="element.type == 'directory'"
                            :setting="element.data" :oldResult="element.result" v-on:update="setResult">
                        </element-setting-directory>

                        <!-- Произвольный текст -->
                        <element-setting-input-text     v-if="element.type == 'input-text'"
                            :setting="element.data" :oldResult="element.result" v-on:update="setResult">
                        </element-setting-input-text>

                        <!-- Произвольное целочисленное  -->
                        <element-setting-input-number   v-if="element.type == 'input-number'"
                            :setting="element.data" :oldResult="element.result" v-on:update="setResult">
                        </element-setting-input-number>

                        <!-- Произвольное дробное -->
                        <element-setting-input-double   v-if="element.type == 'input-double'"
                            :setting="element.data" :oldResult="element.result" v-on:update="setResult">
                        </element-setting-input-double>

                        <!-- Произвольная дата  -->
                        <element-setting-input-date     v-if="element.type == 'input-date'"
                            :setting="element.data" :oldResult="element.result" v-on:update="setResult">
                        </element-setting-input-date>

                        <!-- Произвольный сайт -->
                        <element-setting-input-web      v-if="element.type == 'input-web'"
                            :setting="element.data" :oldResult="element.result" v-on:update="setResult">
                        </element-setting-input-web>

                        <!-- Произвольный email -->
                        <element-setting-input-email    v-if="element.type == 'input-email'"
                            :setting="element.data" :oldResult="element.result" v-on:update="setResult">
                        </element-setting-input-email>

                        <!-- Произвольный телефон -->
                        <element-setting-input-phone    v-if="element.type == 'input-phone'"
                            :setting="element.data" :oldResult="element.result" v-on:update="setResult">
                        </element-setting-input-phone>

                    </div>

                </div>

                <button class="btn btn-default btn-turn" :class="(current == 0 ? 'opacity-hide' : '')"
                    @click="prev"><span class="glyphicon glyphicon-chevron-left">
                </span></button><!--
                --><button class="btn btn-success btn-turn" v-show="isNext === true"
                    v-if="(current != (elements.length - 1))" @click="next"><span class="glyphicon glyphicon-chevron-right">
                </span></button><!--
                --><button class="btn btn-success btn-turn" v-else :class="(isNext == false ? 'disabled' : '')"
                    @click="finish">Закончить анкетирование</button>
            
            </div>
            <!-- Подвал -->
            <div class="btn-group" v-if="current == -1">
                <button type="button" class="btn btn-primary" @click="start">Начать анкентирование</button>
            </div>
        </div>


    </div>
</template>

<script>

    import ElementSettingTable          from './ElementsSettings/Table.vue';
    import ElementSettingCheckbox       from './ElementsSettings/Checkbox.vue';
    import ElementSettingRadio          from './ElementsSettings/Radio.vue';
    import ElementSettingDirectory      from './ElementsSettings/Directory.vue';
    import ElementSettingInputText      from './ElementsSettings/InputText.vue';
    import ElementSettingInputNumber    from './ElementsSettings/InputNumber.vue';
    import ElementSettingInputDouble    from './ElementsSettings/InputDouble.vue';
    import ElementSettingInputDate      from './ElementsSettings/InputDate.vue';
    import ElementSettingInputWeb       from './ElementsSettings/InputWeb.vue';
    import ElementSettingInputEmail     from './ElementsSettings/InputEmail.vue';
    import ElementSettingInputPhone     from './ElementsSettings/InputPhone.vue';

    export default {

        components: {
            elementSettingTable:        ElementSettingTable,
            elementSettingCheckbox:     ElementSettingCheckbox,
            elementSettingRadio:        ElementSettingRadio,
            elementSettingDirectory:    ElementSettingDirectory,
            elementSettingInputText:    ElementSettingInputText,
            elementSettingInputNumber:  ElementSettingInputNumber,
            elementSettingInputDouble:  ElementSettingInputDouble,
            elementSettingInputDate:    ElementSettingInputDate,
            elementSettingInputWeb:     ElementSettingInputWeb,
            elementSettingInputEmail:   ElementSettingInputEmail,
            elementSettingInputPhone:   ElementSettingInputPhone,
        },

        data() {
            
            return {
                linkData        : {
                    alias   : (this.$route.params.alias  == undefined ? '' : this.$route.params.alias),
                    item    : (this.$route.params.item   == undefined ? '' : this.$route.params.item),
                    token   : (this.$route.params.token  == undefined ? '' : this.$route.params.token),
                },
                isValid         : 0,
                directoryTitle  : '',
                item            : { 'name': '' },
                elements        : [],
                current         : -1,
                testId          : this.$route.params.id,
                isNext          : false,
                user            : 0,
                isFinish        : false,
                finishResult    : ''
            }
        },

        methods: {

            tokenValidation() {

                axios.post(window.baseurl + 'linkValidation', {
                    id: this.testId, alias: this.linkData.alias, item: this.linkData.item, token: this.linkData.token
                }).then(response => {
                    this.get();
                    this.directoryTitle = response.data.title;
                    this.isValid = 1;
                }).catch(error => {
                    this.isValid = -1;
                });

            },

            get() {
                
                this.$store.state.loader = true;
                let id = this.$route.params.id;

                axios.post(window.baseurl + 'frontGetTest', {
                    id: id, user: this.user
                }).then(response => {
                    this.$store.state.loader = false;
                    if(response.data.status == 'success') {
                        
                        //Код пользователя
                        this.user = response.data.user;

                        //Элементы теста
                        this.item = response.data.result;
                        this.elements = response.data.elements;

                        this.isNext = this.is_MoveOn();

                    }
                }).catch(error => {

                    this.$store.state.loader = false;
                    console.log(error);

                });

            },

            setResult(result) {

                let index                   = this.current;
                this.elements[index].result = result;
                this.isNext                 = this.is_MoveOn();

            },

            next() {
                
                let index = parseInt(this.current, 10);
                if(index == this.elements.length - 1) return;

                if(this.is_MoveOn() == false) return;

                this.updateRusult();

                index = this.getConditionNextElement(index + 1);
                if(index === false) {
                    this.finish();
                } else {
                    this.current = index;
                    this.isNext = this.is_MoveOn();
                }

            },

            prev() {
                let index = parseInt(this.current, 10);
                if(index == 0) return;

                this.updateRusult();

                index = this.getConditionPrevElement(index - 1);
                if(index !== false) {
                    this.current = index;
                    this.isNext = this.is_MoveOn();
                }
                
            },

            start() {
                
                this.current = 0;
                
                this.user = 0;

                this.isFinish = false;
                this.get();

                this.finishResult = '';

            },

            finish() {
                if(this.is_MoveOn() == false) return;
                this.updateRusult();
                this.saveResult();
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
                    id:     this.elements[index].id,
                    type:   this.elements[index].type, 
                    result: result
                };              

            },

            saveResult() {

                axios.post(window.baseurl + 'frontResult', {
                    results : this.$store.state.results,
                    user    : this.user,
                    id      : this.testId, 
                    alias   : this.linkData.alias,
                    item    : this.linkData.item,
                    token   : this.linkData.token
                }).then((response) => { 
                    
                    let isTrue = true;
                    $.each(response.data.result, (key, value) => {
                        if(value.result == 'fail') {
                            isTrue = false;
                        }
                    });

                    if(isTrue === true) {
                        this.current = -1;
                        location.reload();
                    } else {
                        this.finishResult = 'Не удалось сохранить один или несколько ответов';
                    }

                }).catch((error) => {

                    if(error.response.status == 500) {
                        this.finishResult = 'Критическая ошибка приложения. Повторите запрос позже';
                    } else if(error.response.status == 403) {
                        this.finishResult = 'Ошибка целостности ссылки. Невозможно сохранить результаты с данными параметрами';
                    } else if(error.response.status == 409) {
                        this.finishResult = 'При сохранении результатов возникла ошибка. Повторите запрос позже [' + error.response.data + ']';
                    } else {
                        this.finishResult = 'При сохранении результатов возникла ошибка. Повторите запрос позже';
                    }
                    
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
                            if(cell !== false) {
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
                        if(value !== false && value != '') {
                            count++;
                            return false;
                        }
                    });
                    //Если хотя бы один true
                    if(count > 0) return true;

                } else if(element.type == 'radio') {
                    if(element.result != undefined) return true;
                } else if(element.type == 'title') {
                    return true;
                } else if(element.type == 'directory') {
                    if(element.result != undefined && element.result != '') return true;
                } else if(element.type == 'input-text') {
                    if(element.result != undefined && element.result != '') return true;
                } else if(element.type == 'input-number') {
                    if(element.result != undefined && element.result != '') return true;
                } else if(element.type == 'input-double') {
                    if(element.result != undefined && element.result != '') return true;
                } else if(element.type == 'input-date') {
                    if(element.result != undefined && element.result != '') return true;
                } else if(element.type == 'input-web') {
                    if(element.result != undefined && element.result != '') return true;
                } else if(element.type == 'input-email') {
                    if(element.result != undefined && element.result != '') return true;
                } else if(element.type == 'input-phone') {
                    if(element.result != undefined && element.result != '') return true;
                }

                return false;
            },

            /* Ищем следующий элемент, подходящий под условие */
            getConditionNextElement(index) {
                
                for(let i = index; i < this.elements.length; i++) {
                    //Для каждого Элемента определяем условность
                    let is_condition = this.is_Condition(i);
                    //Если есть хотя бы один доступный элемент
                    if(is_condition == true) return i;
                }

                return false;
            },

            /* Ищем предыдущий элемент, подходящий под условие */
            getConditionPrevElement(index) {
                
                for(let i = index; i >= 0; i--) {
                    //Для каждого Элемента определяем условность
                    let is_condition = this.is_Condition(i);
                    //Если есть хотя бы один доступный элемент
                    if(is_condition == true) return i;
                }

                return false;

            },

            is_Condition(index) {

                let conditions = this.elements[index].conditions;
                if(conditions.length == 0) return true;
                let result = [];
                
                for(let i = 0; i < conditions.length; i++) {
                    let elementsResults = this.getResultById(conditions[i].conditions_element_id);
                    
                    let value = this.is_TrueResult(elementsResults, conditions[i].conditions_answer);
                    if(conditions[i].operand == '!') value = !value;
                    
                    result.push({
                        value:          value,
                        combination:    conditions[i].combination
                    });
                }

                //Собираем полный результат
                for(let i = 1; i < result.length; i++) {
                    
                    if(result[i].combination == 'or') continue;

                    result[i - 1].value = (result[i].value === true && result[i - 1].value === true);
                    result.splice(i, 1);
                    i--;

                }

                for(let i = 0; i < result.length; i++) {                    
                    if(result[i].value === true) return true;
                }

                return false;

            },

            getResultById(id) {
                for(let i = 0; i < this.$store.state.results.length; i++) {

                    if(this.$store.state.results[i] == undefined) {
                        continue;
                    }

                    if(this.$store.state.results[i].id === id) {
                        return this.$store.state.results[i];
                    }
                }
                return false;
            },

            is_TrueResult(result, answer) {

                if(result.type == 'radio' || result.type == 'directory') {
                    return (parseInt(result.result, 10) == parseInt(answer, 10));
                } else if(result.type == 'checkbox' || result.type == 'table') {
                    let convertResult = Object.keys(result.result).map(i => result.result[i]);
                    console.log(JSON.stringify(convertResult));
                    console.log(answer);
                    return (JSON.stringify(convertResult) == answer);
                }

                return false;
            },

            fancyboxInit() {
                $(".image-item a").fancybox({
                    'transitionIn'		: 'none',
                    'transitionOut'		: 'none',
                    'titlePosition' 	: 'over',
                });
            },

            closeWindowDialog() {
                let self = this;
                window.onbeforeunload = function() {
                    if(self.current > 0) {
                        return "Вы еще не закончили тест. Уверены, что хотите закрыть вкладку? Данные тестирования не сохранятся!";
                    }
                };
            }

        },

        mounted() {
            this.tokenValidation();
            this.closeWindowDialog();
        },

    }
</script>