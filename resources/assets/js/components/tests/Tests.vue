<template>
    <div class="container welcome form">
        <welcome-menu></welcome-menu>
        <h1>Доступные анкеты и тесты</h1>
        <div class="form tests-list">
            <div class="row" v-for="item in items" :key="item.id" @click="getTest(item.id)">
                <div class="col-sm-3 clearfix">{{ item.datetime }}</div>
                <div class="col-sm-9 clearfix">{{ item.name }}</div>
                <div class="col-sm-12 clearfix">{{ item.description }}</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        data() {
            return {
                items: [ ],
            }
        },

        methods: {

            list() {
                
                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'frontTestsList', {
                    // ...
                }).then(function (response) {
                    self.$store.state.loader = false;
                    if(response.data.status == 'success') self.items = response.data.result;
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

            },

            getTest(id) {

                this.$router.push('/test/' + id);
            }

        },

        mounted() {
            this.list();
        },

    }
</script>