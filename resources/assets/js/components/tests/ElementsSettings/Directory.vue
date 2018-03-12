<template>
    <div>
        <!--select v-model="result" @change="choice">
            <option v-for="item in items" :value="item.id">{{ (item.name != undefined ? item.name : item.title) }}</option>
        </select-->
        <input-select :oldResult="result" :list="items" v-on:update="choice"></input-select>
    </div>
</template>

<script>
    export default {

        props: ['setting', 'oldResult'],

        data() {
            return {
                items: [],
                result: this.oldResult
            }
        },

        methods: {

            list() {

                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'frontDirectoryData', {
                    alias: self.setting.alias
                }).then(function (response) {
                    self.$store.state.loader = false;
                    self.items = response.data.result;
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

            },

            choice(id) {

                this.result = id;
                //Отправляем результат папочке
                this.$emit('update', this.result);

            }
            
        },

        mounted() {
            this.list();
        },
    }
</script>