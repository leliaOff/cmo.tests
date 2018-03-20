<template>
    <div class="row">
        <div class="col-sm-12 clearfix" v-for="(condition, i) in conditions">
            <div class="condition-form row">

                <!-- И / ИЛИ -->
                <div class="col-sm-2 clearfix">
                    <select v-model="condition.combination" v-if="i > 0">
                        <option value="and">И</option>
                        <option value="or">ИЛИ</option>
                    </select>
                </div>

                <!-- Вопрос -->
                <div class="col-sm-5 clearfix"><elements-selector v-model="i" v-on:onChange="onChangeElement"></elements-selector></div>

                <!-- Операнд -->
                <div class="col-sm-3 clearfix">
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
                <div class="col-sm-2 clearfix align-right">
                    <button class="btn btn-text" v-if="condition.conditions_element_id != 0" 
                        data-toggle="modal" data-target="#answerSelectorWindow" 
                        @click="setCurrentElementData(condition.element, i)">
                            <span v-if="condition.conditions_answer == ''">Ввести ответ</span>
                            <span v-else>Изменить ответ</span>
                    </button>
                </div>

            </div>
        </div>
        <div class="col-sm-12 clearfix align-right"><button class="btn btn-warning" @click="addConditionForm">Добавить условие</button></div>

        <!-- Диалоговое окно с выбором ответа -->
        <answers-selector v-if="currentElementData" v-model="currentElementData" :index="currentIndex" v-on:onChange="onChangeAnswer"></answers-selector>

    </div>
</template>

<script>

    import ElementsSelector from './ElementsSelector.vue';
    import AnswersSelector from './AnswersSelector.vue';

    export default {

        props: ['value'],

        components: {
            ElementsSelector: ElementsSelector,
            AnswersSelector: AnswersSelector,
        },

        data() {
            return {
                conditions          : this.value,
                currentElementData  : false,
                currentIndex        : 0
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
                this.currentIndex = index;
            },
            
            /* Отслеживаем изменение ответа */
            onChangeAnswer(answer, index) {

                if(!answer) return;

                let conditions                          = this.conditions;
                conditions[index].conditions_answer     = answer;
                this.conditions                         = conditions;

            }
            
        },
    }
</script>