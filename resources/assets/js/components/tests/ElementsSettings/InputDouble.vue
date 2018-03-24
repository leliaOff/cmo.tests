<template>
    <div>
        <input type="number" step="0.1" :min="setting.min" :max="setting.max" class="form-control" v-model.number="result" />
    </div>
</template>

<script>

    /* Произвольное целочисленное */
    export default {

        props: ['setting', 'oldResult'],

        data() {
            return {
                result: this.oldResult != undefined ? this.oldResult : ''
            }
        },

        watch: {
            result: function(value) {
                this.$emit('update', this.result);
            }
        },
    }
</script>