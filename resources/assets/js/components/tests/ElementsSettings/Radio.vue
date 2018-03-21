<template>
    <div>
        <easy-radio :name="'radio_' + id" v-model="result" :data="setting.rows" v-on:update="resultUpdate"></easy-radio>              
    </div>
</template>

<script>
    export default {

        props: ['setting', 'oldResult', 'id'],

        computed: {
            result() {
                return this.oldResult;
            }
        },

        methods: {

            resultUpdate(index) {
                this.$emit('update', parseInt(index, 10));
            }
            
        },
    }
</script>