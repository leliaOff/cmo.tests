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
            <div class="col-sm-7 clearfix"><label>Общее число респондентов, прошедших тест</label></div>
            <div class="col-sm-3 clearfix"><label>{{ general.countPeople }}</label></div>
            <div class="col-sm-2 clearfix"><button class="btn btn-text" @click="removeIncisions" title="Посмотреть общие результаты без разрезов">Подробно</button></div>
        </div>
        <div class="row">
            <div class="col-sm-7 clearfix"><label>Последнее обновление: </label></div>
            <div class="col-sm-5 clearfix"><label>{{ general.tm }}</label></div>
        </div>        
        <div class="row">
            <div class="col-sm-7 clearfix"><label>Число вопросов</label></div>
            <div class="col-sm-5 clearfix"><label>{{ general.countElements }}</label></div>
        </div>
        
        <h2 v-if="linksResults.length > 0">Статистика по ссылкам</h2>
        <div class="row" v-for="result in linksResults" :key="result.id" v-if="result.count > 0">
            <div class="col-sm-7 clearfix"><label>{{ result.title }}</label></div>
            <div class="col-sm-3 clearfix"><label>{{ result.count }}</label></div>
            <div class="col-sm-2 clearfix"><button class="btn btn-text" @click="addIncisions(result.alias, result.id)" title="Посмотреть результаты в разрезе">Подробно</button></div>
        </div>
        
        <h2>Подробная статистика</h2>
        <div class="element-item" v-for="(item, i) in items" :key="item.id" v-if="item.type != 'title'">
            <h4>{{ (i + 1) }}. {{ item.title }}</h4>
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
                    :elementId="item.id" :item="item" :param="setting">
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
                linksResults: [],
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
                    id: self.testId, incisions: this.$store.state.incisions
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

            addIncisions(alias, item_id) {
                let incisions = this.$store.state.incisions;
                incisions[alias] = item_id;
                this.$store.state.incisions = Object.assign({}, incisions);
                this.list();
            },

            removeIncisions(elementId) {
                this.$store.state.incisions[alias] = {};
                this.list();
            },

            /* Настройки */
            settingChange() {
                $.each(this.setting, function(key, value) {
                    localStorage['resultSetting_' + key] = value;
                });
            },

            getLinksStat() {

                this.$store.state.loader = true;

                axios.get(window.baseurl + 'getLinksResult/' + this.testId).then(response => {
                    
                    this.$store.state.loader = false; 
                    this.linksResults = response.data;
                    
                }).catch(error => {
                    this.$store.state.loader = false;
                    console.log(error);
                });

            },
            
        },

        mounted() {
            this.list();
            this.getLinksStat();
        },
    }
</script>