<template>
    <div class="modal fade" id="answerSelectorWindow" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Выберите необходимый ответ</h4>
                </div>
                <div class="modal-body">
                    
                    <!-- Деловая часть элемента -->
                    <h2>{{ element.title }}</h2>
                    <div class="alert alert-info" v-if="element.description" v-html="element.description"></div>

                    <!-- Структура элемента в зависимости от типа -->
                    <div class="element-item-setting">
                        <element-setting-table v-if="element.type == 'table'" :setting="element.data" :oldResult="oldResult" v-on:update="setResult"></element-setting-table>
                        <element-setting-checkbox v-if="element.type == 'checkbox'" :setting="element.data" :oldResult="oldResult" v-on:update="setResult"></element-setting-checkbox>
                        <element-setting-radio v-if="element.type == 'radio'" :setting="element.data" :oldResult="oldResult" :id="element.id" v-on:update="setResult"></element-setting-radio>
                        <element-setting-directory v-if="element.type == 'directory'" :setting="element.data" :oldResult="oldResult" v-on:update="setResult"></element-setting-directory>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" @click="save" type="button" >Принять</button>
                    <button class="btn btn-info" @click="cansel" type="button" >Отменить</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import ElementSettingTable from '../../../tests/ElementsSettings/Table.vue';
    import ElementSettingCheckbox from '../../../tests/ElementsSettings/Checkbox.vue';
    import ElementSettingRadio from '../../../tests/ElementsSettings/Radio.vue';
    import ElementSettingDirectory from '../../../tests/ElementsSettings/Directory.vue';

    /* Компонент - всплывающее окно с ответами */
    export default {

        props: ['value', 'element', 'index'],

        components: {
            elementSettingTable     : ElementSettingTable,
            elementSettingCheckbox  : ElementSettingCheckbox,
            elementSettingRadio     : ElementSettingRadio,
            elementSettingDirectory : ElementSettingDirectory,
        },

        data() {
            return {
                answer      : false
            }
        },

        computed: {
            oldResult() {
                return this.value;
            }
        },

        methods: {

            save: function() {
                this.$emit('onChange', this.answer, this.index);
                $('#answerSelectorWindow').modal('hide');
            },

            cansel: function() {
                this.$emit('onChange', false);
                $('#answerSelectorWindow').modal('hide');
            },

            setResult(result) {
                this.answer = result;
            },
            
            
        },
    }
</script>