<template>
    <div>
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Максимум можно выбрать:</label></div>
            <div class="col-sm-9 clearfix"><input type="text" placeholder="2" v-model="setting.count" /></div>
        </div>
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Варианты ответов:</label></div>
            <div class="col-sm-9 clearfix">
                <div class="table-container">
                    <table>
                        <tr>
                            <td class="row-item"></td>
                            <td v-for="(col, i) in setting.cols" class="element-table">
                                <input type="text" v-model="col.value" v-on:keyup="columnUpdate" placeholder="введите значение"/>
                                <button type="button" class="btn btn-default" v-if="i < (setting.cols.length - 1)" @click="columnRemove(i)"><span class="glyphicon glyphicon glyphicon-trash"></span></button>
                            </td>
                        </tr>
                        <tr v-for="(row, i) in setting.rows">
                            <td class="row-item element-table">
                                <textarea v-model="row.value" v-on:keyup="rowUpdate" placeholder="введите значение"></textarea>
                                <button type="button" class="btn btn-default" v-if="i < (setting.rows.length - 1)" @click="rowRemove(i)"><span class="glyphicon glyphicon glyphicon-trash"></span></button>
                            </td>
                            <td v-for="col in setting.cols"></td>
                        </tr>
                    </table>
                </div> 
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Изображения:</label></div>
            <div class="col-sm-9 clearfix">
                <div v-bind:class="{ 'file-item': true, 'delete': file.is_delete }" v-for="(file, i) in files" :title="file.title">
                    <img :src="file.path" />
                    <button type="button" class="btn btn-default" @click="deleteFile(i)">
                        <span class="glyphicon glyphicon-repeat" v-if="files[i].is_delete == true"></span>
                        <span class="glyphicon glyphicon-trash" v-else></span>
                    </button>
                </div><!--
                --><button class="add-file" @click="selectFiles" title="Добавить изображения"><i class="glyphicon glyphicon-plus"></i></button>
                <input type="file" multiple="true" class="files" @change="uploadFiles" accept="image/jpeg,image/png,image/gif"/>
            </div>
        </div> 
    </div>
</template>

<script>
    export default {

        props: ['setting', 'files'],

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

            selectFiles() {
                this.$el.querySelector('input.files').click();
            },

            uploadFiles() {

                let files = this.$el.querySelector('input.files').files;
                
                var url = window.baseurl + 'elementsFileUpload';
				const config = { headers: { 'content-type': 'multipart/form-data' } };
				
				let data = new FormData();
                for(let i = 0; i < files.length; i++) {
                    data.append('files[]', files[i]);
                }

				axios.post(url, data, config).then(response => {
					
                    $.each(response.data, (i, value) => {
                        this.files.push({
                            path:   value,
                        });
                    });
                    this.$emit('onChangeFiles', this.files);

				}).catch(error => {
                    // ...
                });

            },

            deleteFile(i) {
                let list = this.files;
                list[i].is_delete = list[i].is_delete ? false: true;
                this.files = Object.assign({}, list);
            }
            
        },
    }
</script>