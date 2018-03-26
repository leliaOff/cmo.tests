<template>
    <div>
        <easy-radio :name="'radio_' + id" :isArbitrary="setting.arbitrary" v-model="result" :data="settingRows" v-on:update="resultUpdate"></easy-radio>
    </div>
</template>

<script>
    export default {

        props: ['setting', 'index', 'id'],

        data() {     

            return {
                settingRows     : this.setting.rows
            }
        },

        computed: {
            result() {
                let a = this.$store.state.results[this.index] == undefined ? -1 : this.$store.state.results[this.index].result;
                return this.$store.state.results[this.index] == undefined ? -1 : this.$store.state.results[this.index].result;
            },
            arbitrary() {
                if(this.setting.arbitrary == undefined) return false;
                if(parseInt(this.setting.arbitrary) == 0) return false;
                return true;
            },
        },

        methods: {

            resultUpdate(index) {
                this.$emit('update', index);
            },
            
        },
    }
</script>