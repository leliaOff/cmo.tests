<template>
    <div class="element-item">
        <!-- шапка -->
        <div class="element-header">
            <span class="type-icon" v-bind:class="type_icon"></span><h2>{{ type_title }}</h2>
            <button type="button" class="btn btn-default" v-if="item.sort != 0" @click="sort('up')"><span class="glyphicon glyphicon-arrow-up"></span></button>
            <button type="button" class="btn btn-default" v-if="item.sort != (count - 1)" @click="sort('down')"><span class="glyphicon glyphicon-arrow-down"></span></button>
        </div>
        <!-- данные элемента -->
        <div class="element-data">
            <div class="row">
                <div class="col-sm-3 clearfix"><label class="input-title">Наименование: *</label></div>
                <div class="col-sm-9 clearfix"><input type="text" placeholder="наименование" v-model="current.title"/>
                    <span class="input-validator title" v-if="validation.title != undefined">{{ validation.title[0] }}</span></div>
            </div>
            <div class="row">
                <div class="col-sm-3 clearfix"><label class="input-title">Описание:</label></div>
                <div class="col-sm-9 clearfix"><textarea type="text" placeholder="описание"
                                                    v-model="current.description"></textarea></div>
            </div>
            <div class="row">
                <div class="col-sm-3 clearfix"><label class="input-title">Обязательный?</label></div>
                <div class="col-sm-9 clearfix"><label class="checkbox"><input type="checkbox" v-model="current.is_required"/>
                    <span class="alert alert-info">отметьте этот пункт, если ответ на вопрос обязателен</span></label></div>
            </div>
        </div>
        <div class="element-setting-button btn-group" v-if="item.type != 'title'">
            <button class="btn btn-text" @click="elementSettingVisibility" v-if="visibility === 1">Скрыть настройки</button>
            <button class="btn btn-text" @click="elementSettingVisibility" v-else>Показать настройки</button>
        </div>
        <!-- настройки элемента -->
        <div class="element-setting" v-show="visibility === 1">
            <element-setting-table v-if="item.type == 'table'" :setting="current.setting" :files="current.files" v-on:onChangeFiles="onChangeFiles"></element-setting-table>
            <element-setting-checkbox v-if="item.type == 'checkbox'" :setting="current.setting" :files="current.files" v-on:onChangeFiles="onChangeFiles"></element-setting-checkbox>
            <element-setting-radio v-if="item.type == 'radio'" :setting="current.setting" :files="current.files" v-on:onChangeFiles="onChangeFiles"></element-setting-radio>
            <element-setting-directory v-if="item.type == 'directory'" :setting="current.setting" :files="current.files" v-on:onChangeFiles="onChangeFiles"></element-setting-directory>
        </div>
        <!-- кнопки элемента -->
        <div class="element-footer btn-group">
            <button type="button" class="btn btn-success" @click="update">Сохранить</button>
            <button type="button" class="btn btn-default" @click="cansel">Отменить</button>
            <button type="button" class="btn btn-danger" @click="remove">Удалить</button>
        </div>
    </div>
</template>

<script>

    import ElementSettingTable from './ElementsSettings/Table.vue';
    import ElementSettingCheckbox from './ElementsSettings/Checkbox.vue';
    import ElementSettingRadio from './ElementsSettings/Radio.vue';
    import ElementSettingDirectory from './ElementsSettings/Directory.vue';

    export default {

        props: ['item', 'count'],

        components: {
            elementSettingTable: ElementSettingTable,
            elementSettingCheckbox: ElementSettingCheckbox,
            elementSettingRadio: ElementSettingRadio,
            elementSettingDirectory: ElementSettingDirectory,
        },

        computed: {
            type_title() {
                if(this.item.type == 'table') return 'Таблица';
                if(this.item.type == 'radio') return 'Один из многих';
                if(this.item.type == 'checkbox') return 'Много из многих';
                if(this.item.type == 'title') return 'Заголовок и текст';
                if(this.item.type == 'directory') return 'Выбор из справочника';
            },
            type_icon() {
                if(this.item.type == 'table') return 'glyphicon glyphicon-th';
                if(this.item.type == 'radio') return 'glyphicon glyphicon-record';
                if(this.item.type == 'checkbox') return 'glyphicon glyphicon-check';
                if(this.item.type == 'title') return 'glyphicon glyphicon-font';
                if(this.item.type == 'directory') return 'glyphicon glyphicon-cloud-download';
            }
        },

        data() {

            /* Настройки в зависимости от типа элемента */
            let setting = { };
            if(this.item.type == 'table') {
                setting = {
                    'cols': [{value: ''}],
                    'rows': [{value: ''}],
                    'count': 1
                };
            } else if(this.item.type == 'radio') {
                setting = {
                    'rows': [{value: ''}],
                };
            } else if(this.item.type == 'checkbox') {
                setting = {
                    'rows': [{value: ''}],
                    'count': 2
                };
            } else if(this.item.type == 'title') {
                setting = {
                    'data': ''
                };
            } else if(this.item.type == 'directory') {
                setting = {
                    'alias': 'schools'
                };
            }
            /* конец Настройки в зависимости от типа элемента */

            let show = localStorage[this.item.id] == undefined ? true : localStorage[this.item.id];

            return {
                validation: { },
                current: { 
                    title: this.item.title,                    
                    description: this.item.description,                    
                    is_required: this.item.is_required,              
                    sort: this.item.sort,
                    setting: setting,
                    files: this.item.files
                },
                /* Отобразить/скрыть настройки элемента */
                visibility: localStorage[this.item.id] == undefined ? 1 : parseInt(localStorage[this.item.id], 10)
            }
        },

        methods: {

            get() {

                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'elementGet', {
                    id: self.item.id
                }).then(function (response) {
                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');
                    if(response.data.status == 'success') {
                        
                        /* Настройки в зависимости от типа элемента */
                        let setting = {};
                        if(self.item.type == 'table') {
                            setting = {
                                'cols': response.data.setting.cols != undefined ? response.data.setting.cols : [],
                                'rows': response.data.setting.rows != undefined ? response.data.setting.rows : [],
                                'count': response.data.setting.count != undefined ? response.data.setting.count : 1
                            };
                            setting.cols.push({value: ''});
                            setting.rows.push({value: ''});
                        } else if(self.item.type == 'radio') {
                            setting = {
                                'rows': response.data.setting.rows != undefined ? response.data.setting.rows : []
                            };
                            setting.rows.push({value: ''});
                        } else if(self.item.type == 'checkbox') {
                            setting = {
                                'rows': response.data.setting.rows != undefined ? response.data.setting.rows : [],
                                'count': response.data.setting.count != undefined ? response.data.setting.count : 2
                            };
                            setting.rows.push({value: ''});
                        } else if(self.item.type == 'title') {
                            setting = {
                                'data': response.data.setting.data != undefined ? response.data.setting.data : ''
                            };
                        } else if(self.item.type == 'directory') {
                            setting = {
                                'alias': response.data.setting.alias != undefined ? response.data.setting.alias : 'schools'
                            };
                        }
                        /* конец Настройки в зависимости от типа элемента */

                        self.item.setting = Object.assign({}, setting);
                        self.current.setting = Object.assign({}, setting);

                    }
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

            },

            onChangeFiles(files) {
                this.current.files = files;
            },

            update() {

                this.validation = {};
                this.$store.state.loader = true;

                //data
                
                let data = {
                    
                    title:          this.current.title,
                    description:    this.current.description,
                    is_required:    this.current.is_required,

                };

                //settings
                if(this.current.setting.cols != undefined) this.current.setting.cols.splice((this.current.setting.cols.length - 1), 1);
                if(this.current.setting.rows != undefined) this.current.setting.rows.splice((this.current.setting.rows.length - 1), 1);

                axios.post(window.baseurl + 'elementUpdate', {
                    
                    data: data, id: this.item.id, setting: this.current.setting, type: this.item.type, files: this.current.files

                }).then(response => {

                    this.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') this.$router.push('/');
                    
                    if(response.data.status == 'fail') {
                        
                        this.validation = response.data.error;

                    } else if(response.data.status == 'success') {

                        if(this.current.setting.cols != undefined) this.current.setting.cols.push({value: ''});
                        if(this.current.setting.rows != undefined) this.current.setting.rows.push({value: ''});
                      
                        this.item.setting       = Object.assign({}, this.current.setting);
                        this.item.title         = this.current.title;
                        this.item.description   = this.current.description;
                        this.item.is_required   = this.current.is_required;
                        this.item.files         = response.data.result.files;
                        this.current.files      = response.data.result.files;

                    }

                }).catch(error => {
                    this.$store.state.loader = false;
                    console.log(error);
                });

            },

            cansel() {

                let self = this;

                self.$store.state.sureModal.text = 'Вы уверены, что хотите отменить изменения?';
                self.$store.state.sureModal.action = function() {
                    
                    self.current.title = self.item.title;
                    self.current.description = self.item.description;
                    self.current.is_required = self.item.is_required;
                    self.get();
                }
                $('#sureWindow').modal('show');

            },

            sort(type) {
                
                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'elementSort', {                    
                    id: self.item.id, sort: type
                }).then(function(response) {
                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');                    
                    self.$emit('update');
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

            },

            remove() {

                let self = this;

                self.$store.state.sureModal.text = 'Вы уверены, что хотите удалить элемент?';
                self.$store.state.sureModal.action = function() {
                    axios.post(window.baseurl + 'elementDelete', {
                        id: self.item.id, test_id: self.item.test_id
                    }).then(function (response) {
                        self.$store.state.loader = false;                    
                        if(response.data.status == 'relogin') self.$router.push('/');
                        self.$emit('update');
                    }).catch(function (error) {
                        self.$store.state.loader = false;
                        console.log(error);
                    });
                }

                $('#sureWindow').modal('show');

            },

            elementSettingVisibility() {
                this.visibility = this.visibility == 1 ? 0 : 1;
                localStorage[this.item.id] = this.visibility;
            }
            
        },

        mounted() {
            this.get();
        },
    }
</script>