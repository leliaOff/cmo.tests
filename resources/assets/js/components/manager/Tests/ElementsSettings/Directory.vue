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
    </div>
</template>

<script>
    export default {

        props: ['setting'],

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
            
        },

        mounted() {
            this.list();
        },
    }
</script>