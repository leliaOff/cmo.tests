<template>
    <div>
        <input type="text" class="form-control" v-model="result" placeholder="номер телефона"/>
    </div>
</template>

<script>
    export default {

        props: ['setting', 'oldResult'],

        data() {
            return {
                result: this.oldResult != undefined ? this.oldResult : ''
            }
        },

        watch: {
            result: function() {
                this.$emit('update', this.result);
            }
        }
    }
</script>