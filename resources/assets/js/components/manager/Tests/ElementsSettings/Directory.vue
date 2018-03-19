<template>
    <div>
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Выберите справочник:</label></div>
            <div class="col-sm-9 clearfix">
                <select v-model="setting.alias">
                    <option v-for="directory in directories" :value="directory.alias">{{ directory.title }}</option>
                </select>
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

        data() {
            return {
                directories: [],
            }
        },

        methods: {

            list() {

                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'directoriesList', {
                    // ...
                }).then(function (response) {
                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');
                    if(response.data.status == 'success') self.directories = response.data.result;
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

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

        mounted() {
            this.list();
        },
    }
</script>