<template>
    <div class="results">
        <div class="row without-back">
            <div class="col-sm-12 clearfix">
                <a class="btn btn-text" :href="'/getExcel/' + testId" v-if="testId == 1">Выгрузить в матрицу</a>
            </div>
        </div>
        <h2>Настройки отображения статистики</h2>
        <div class="row">
            <div class="col-sm-8 clearfix"><label for="setting-nr">Скрыть нулевые результаты?</label></div>
            <div class="col-sm-4 clearfix"><input type="checkbox" id="setting-nr" v-model="setting.nr" @change="settingChange"/></div>
        </div>
        <h2>Общая статистика</h2>
        <div class="row">
            <div class="col-sm-8 clearfix"><label>Последнее обновление: </label></div>
            <div class="col-sm-4 clearfix"><label>{{ general.tm }}</label></div>
        </div>
        <div class="row">
            <div class="col-sm-8 clearfix"><label>Общее число респондентов, прошедших тест</label></div>
            <div class="col-sm-4 clearfix"><label>{{ general.countPeople }}</label></div>
        </div>
        <div class="row">
            <div class="col-sm-8 clearfix"><label>Число вопросов</label></div>
            <div class="col-sm-4 clearfix"><label>{{ general.countElements }}</label></div>
        </div>
        <h2>Подробная статистика</h2>
        <div class="element-item" v-for="(item, i) in items" :key="item.id" v-if="item.type != 'title'">
            <h3>{{ (i + 1) }}. {{ item.title }} <button class="btn btn-text" @click="removeIncisions(item.id)" v-if="incisions[item.id] != undefined">Общие сведения</button></h3>
            <div class="row">
                <div class="col-sm-8 clearfix"><label>Число респондентов, давших ответ</label></div>
                <div class="col-sm-4 clearfix"><label>{{ item.stat.count }}</label></div>
            </div>
            <div>
            
                <!-- Много из многих -->
                <element-setting-checkbox v-if="item.type == 'checkbox'" 
                    :setting="item.data" :item="item" :param="setting">
                </element-setting-checkbox>

                <!-- Один из многих -->
                <element-setting-radio v-if="item.type == 'radio'" 
                    :setting="item.data" :item="item" :param="setting">
                </element-setting-radio>

                <!-- Таблица -->
                <element-setting-table v-if="item.type == 'table'" :setting="item.data" 
                    :item="item"></element-setting-table>
                
                <!-- Элемент справочника -->
                <element-setting-directory v-if="item.type == 'directory'" 
                    :elementId="item.id" :item="item" :param="setting" 
                    v-on:setIncisions="addIncisions">
                </element-setting-directory>
                
            </div>
        </div>
    </div>
</template>

<script>

    import ElementSettingTable          from './ResultsElementsSettings/Table.vue';
    import ElementSettingCheckbox       from './ResultsElementsSettings/Checkbox.vue';
    import ElementSettingRadio          from './ResultsElementsSettings/Radio.vue';
    import ElementSettingDirectory      from './ResultsElementsSettings/Directory.vue';

    export default {

        props: ['testId'],

        components: {
            elementSettingTable:        ElementSettingTable,
            elementSettingCheckbox:     ElementSettingCheckbox,
            elementSettingRadio:        ElementSettingRadio,
            elementSettingDirectory:    ElementSettingDirectory,
        },

        data() {
            return {
                items: [ ],
                general: { 
                    countPeople: 0, 
                    countFinish: 0,
                    countResult: 0, 
                    countElements: 0,
                    tm: ''
                },
                //Разрезы
                incisions: [],
                //Настройки
                setting: { 
                    nr: (localStorage['resultSetting_nr'] == 'true' ? true : false),
                }
            }
        },

        methods: {

            list() {

                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'resultsList', {
                    id: self.testId, incisions: this.incisions
                }).then(function (response) {
                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');
                    if(response.data.status == 'success') {
                        //Список элементов
                        self.items = response.data.result;
                        //Общая статистика
                        self.general = Object.assign({}, response.data.general);
                    }
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

            },

            addIncisions(elementId, result) {                
                this.incisions[elementId] = result;
                this.$store.state.incisions = Object.assign({}, this.incisions);
                this.list();
            },

            removeIncisions(elementId) {                
                delete this.incisions[elementId];
                this.$store.state.incisions = Object.assign({}, this.incisions);
                this.list();
            },

            /* Настройки */
            settingChange() {
                $.each(this.setting, function(key, value) {
                    localStorage['resultSetting_' + key] = value;
                });
            },

            /* Выгрузить */
            exportExcel() {
                window.location
            }
            
        },

        mounted() {
            this.list();
        },
    }
</script>