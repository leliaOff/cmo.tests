<template>
    <div>
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
</template>

<script>

    export default {

        props: ['value'],

        data() {
            return {
                files: this.value
            }
        },

        watch: {
            files: function() {
                this.value = Object.assign({}, this.files);
            }
        },

        methods: {

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
                    
                    //this.$emit('onChangeFiles', this.files);

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