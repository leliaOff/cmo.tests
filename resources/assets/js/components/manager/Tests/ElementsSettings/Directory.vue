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

            /* Изменили список условий */
            onChangeConditions(conditions) {
                this.$emit('onChangeConditions', conditions);
            }
            
        },

        mounted() {
            this.list();
        },
    }
</script>