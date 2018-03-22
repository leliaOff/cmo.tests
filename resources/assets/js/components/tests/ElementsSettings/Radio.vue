<template>
    <div>
        <easy-radio :name="'radio_' + id" v-model="result" :data="settingRows" v-on:update="resultUpdate"></easy-radio>
    </div>
</template>

<script>
    export default {

        props: ['setting', 'oldResult', 'id'],

        data() {

            let rows = this.setting.rows;
            if(parseInt(this.setting.arbitrary) == 1) {
                rows.push({value: 'arbitrary'});
            }            

            return {
                settingRows     : rows
            }
        },

        computed: {
            result() {
                return this.oldResult;
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