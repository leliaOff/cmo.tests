<template>
    <div>
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Максимум можно выбрать:</label></div>
            <div class="col-sm-9 clearfix"><input type="text" placeholder="2" v-model="setting.count" /></div>
        </div>
        <div class="table-container">
            <table>
                <tr>
                    <td class="row-item"></td>
                    <td v-for="(col, i) in setting.cols">
                        <input type="text" v-model="col.value" v-on:keyup="columnUpdate" placeholder="введите значение"/>
                        <button type="button" class="btn btn-default" v-if="i < (setting.cols.length - 1)" @click="columnRemove(i)"><span class="glyphicon glyphicon glyphicon-trash"></span></button>
                    </td>
                </tr>
                <tr v-for="(row, i) in setting.rows">
                    <td class="row-item">
                        <textarea v-model="row.value" v-on:keyup="rowUpdate" placeholder="введите значение"></textarea>
                        <button type="button" class="btn btn-default" v-if="i < (setting.rows.length - 1)" @click="rowRemove(i)"><span class="glyphicon glyphicon glyphicon-trash"></span></button>
                    </td>
                    <td v-for="col in setting.cols"></td>
                </tr>
            </table>
        </div>        
    </div>
</template>

<script>
    export default {

        props: ['setting'],

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
            
        },
    }
</script>