<template>
    <div>
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Максимум можно выбрать:</label></div>
            <div class="col-sm-9 clearfix"><input type="text" placeholder="2" v-model="setting.count" /></div>
        </div>
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Тип шкалы таблицы:</label></div>
            <div class="col-sm-9 clearfix">
                <select v-model="setting.type">
                    <option value="d">Цифровая</option>
                    <option value="n">Номинальная</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Варианты ответов:</label></div>
            <div class="col-sm-9 clearfix">
                <div class="table-container">
                    <table>
                        <tr>
                            <td class="row-item"></td>
                            <td v-for="(col, i) in setting.cols" :key="'column_' + i" class="element-table">
                                <input type="text" v-model="col.value" v-on:keyup="columnUpdate" placeholder="введите значение"/>
                                <button type="button" class="btn btn-default" v-if="i < (setting.cols.length - 1)" @click="columnRemove(i)"><span class="glyphicon glyphicon glyphicon-trash"></span></button>
                            </td>
                        </tr>
                        <tr v-for="(row, i) in setting.rows" :key="'row_' + i">
                            <td class="row-item element-table">
                                <textarea v-model="row.value" v-on:keyup="rowUpdate" placeholder="введите значение"></textarea>
                                <button type="button" class="btn btn-default" v-if="i < (setting.rows.length - 1)" @click="rowRemove(i)"><span class="glyphicon glyphicon glyphicon-trash"></span></button>
                            </td>
                            <td v-for="(col, j) in setting.cols" :key="'cell_' + i + '-' + j"></td>
                        </tr>
                    </table>
                </div> 
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Изображения:</label></div>
            <div class="col-sm-9 clearfix"><images-gallery v-model="files"></images-gallery></div>
        </div> 
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Условия появления:</label></div>
            <div class="col-sm-9 clearfix"><conditions v-model="conditions" :elementId="elementId" v-on:onChange="onChangeConditions"></conditions></div>
        </div>
    </div>
</template>

<script>

    import ImagesGallery    from '../ElementsControls/ImagesGallery.vue';
    import Conditions       from '../ElementsControls/Conditions.vue';

    export default {

        props: ['setting', 'files', 'conditions', 'elementId'],

        components: {
            ImagesGallery   : ImagesGallery,
            Conditions      : Conditions,
        },

        watch: {
            files: function() {
                this.$emit('onChangeFiles', this.files);
            }
        },

        methods: {

            columnUpdate() {

                if(this.setting.cols[this.setting.cols.length - 1].value != '') {
                    this.setting.cols.push({value: ''});
                }

            },

            columnRemove(index) {

                this.setting.cols.splice(index, 1);

            },

            rowUpdate() {

                if(this.setting.rows[this.setting.rows.length - 1].value != '') {
                    this.setting.rows.push({value: ''});
                }

            },

            rowRemove(index) {

                this.setting.rows.splice(index, 1);

            },

            /* Изменили список условий */
            onChangeConditions(conditions) {
                this.$emit('onChangeConditions', conditions);
            }
            
        },
    }
</script>