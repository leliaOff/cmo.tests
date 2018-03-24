<template>
    <div>
        <datepicker v-model="result" 
            format="dd.MM.yyyy" :first-day-of-week="1" lang="ru" :shortcuts="shortcuts"
            :not-before="setting.min" :not-after="setting.max">
        </datepicker>
    </div>
</template>

<script>

    /* Произвольное целочисленное */
    export default {

        props: ['setting', 'oldResult'],

        data() {
            return {
                result: this.oldResult != undefined ? this.oldResult : '',
            }
        },

        watch: {
            result: function(value) {
                this.$emit('update', this.result);
            }
        },
    }
</script>