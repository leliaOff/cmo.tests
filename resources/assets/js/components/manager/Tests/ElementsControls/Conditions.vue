<template>
    <div class="row">
        <div class="col-sm-12 clearfix" v-for="(condition, i) in conditionsFilter">
            <div class="condition-form row" v-bind:class="{'condition-form': true, 'row': true, 'delete': condition.is_delete}">

                <!-- И / ИЛИ -->
                <div class="col-sm-2 clearfix">
                    <select v-model="condition.combination" v-if="i > 0">
                        <option value="and">И</option>
                        <option value="or">ИЛИ</option>
                    </select>
                </div>

                <!-- Вопрос -->
                <div class="col-sm-4 clearfix"><elements-selector :index="i" :elementId="elementId" v-model="condition.element" v-on:onChange="onChangeElement"></elements-selector></div>

                <!-- Операнд -->
                <div class="col-sm-2 clearfix">
                    <select v-model="condition.operand" v-if="condition.conditions_element_id != 0">
                        <option value="=">Равно</option>
                        <option value="!">Не равно</option>
                        <option value="<" v-if="['number'].indexOf(condition.element.type) != -1">Меньше</option>
                        <option value="<=" v-if="['number'].indexOf(condition.element.type) != -1">Меньше или равно</option>
                        <option value=">" v-if="['number'].indexOf(condition.element.type) != -1">Больше</option>
                        <option value=">=" v-if="['number'].indexOf(condition.element.type) != -1">Больше или равно</option>
                    </select>
                </div>

                <!-- Ввети ответ -->
                <div class="col-sm-3 clearfix">
                    <button class="btn btn-text" v-if="condition.conditions_element_id != 0" 
                        data-toggle="modal" data-target="#answerSelectorWindow" 
                        @click="setCurrentElementData(condition.element, i)">
                            <span v-if="condition.conditions_answer === ''">Ввести ответ</span>
                            <span v-else>Изменить ответ</span>
                    </button>
                </div>

                <!-- Кнопка удаления -->
                <div class="col-sm-1 clearfix align-right"><button class="btn btn-text" title="Удалить условие" @click="deleteConditionForm(i)"><span class="glyphicon glyphicon-trash"></span></button></div>

            </div>
        </div>
        <div class="col-sm-12 clearfix align-right"><button class="btn btn-warning" @click="addConditionForm">Добавить условие</button></div>

        <!-- Диалоговое окно с выбором ответа -->
        <answers-selector v-if="currentElementData" v-model="currentAnswer" :element="currentElementData" :index="currentIndex" v-on:onChange="onChangeAnswer"></answers-selector>

    </div>
</template>

<script>

    import ElementsSelector from './ElementsSelector.vue';
    import AnswersSelector from './AnswersSelector.vue';

    export default {

        props: ['value', 'elementId'],

        components: {
            ElementsSelector        : ElementsSelector,
            AnswersSelector         : AnswersSelector,
        },

        data() {
            return {
                conditions          : this.value,
                currentElementData  : false,
                currentAnswer       : false,
                currentIndex        : 0
            }
        },

        computed: {
            conditionsFilter() {
                let conditions = [];
                $.each(this.conditions, (i, value) => {
                    conditions.push(value);
                });
                return conditions;
            }
        },

        watch: {
            conditions: {
                handler: function () {
                    this.$emit('onChange', this.conditions);
                },
                deep: true
            },
        },

        methods: {

            /* Добавляем новую строку с условием */
            addConditionForm() {
                this.conditions.push({
                    conditions_element_id   : 0,
                    element                 : {},
                    conditions_answer       : false,
                    operand                 : '=',
                    combination             : 'and'
                });
            },

            /* Удаляем строку с условием */
            deleteConditionForm(index) {
                let condition                           = this.conditions[index];
                condition.is_delete                     = true;
                this.conditions.splice(index, 1, condition);
            },

            /* Отслеживаем изменения элемента анкеты */
            onChangeElement(element, index) {

                if(element == undefined) return;

                let conditions                          = this.conditions;
                conditions[index].conditions_element_id = element.id;
                conditions[index].element               = element;
                this.conditions                         = conditions;
            },

            /* Задать данные текущего элемента анкеты для выбора необходимого ответа в диалоговом окне */
            setCurrentElementData(element, index) {
                this.currentElementData = element;
                this.currentAnswer = this.conditions[index].conditions_answer;
                this.currentIndex = index;
            },
            
            /* Отслеживаем изменение ответа */
            onChangeAnswer(answer, index) {

                if(answer !== false) {
                    let conditions                          = this.conditions;
                    conditions[index].conditions_answer     = answer;
                    this.conditions                         = conditions;
                }

                this.currentElementData = false;
                this.currentAnswer = false;
                this.currentIndex = 0;


            }
            
        },
    }
</script>