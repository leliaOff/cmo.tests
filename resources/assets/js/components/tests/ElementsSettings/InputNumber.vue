<template>
    <div>
        <input type="number" :min="setting.min" :max="setting.max" class="form-control" v-model.number="result" />
        <span class="input-validation" v-if="!isValid">Введите целое число от {{ this.setting.min }} до {{ this.setting.max }} включительно</span>
    </div>
</template>

<script>

    /* Произвольное целочисленное */
    export default {

        props: ['setting', 'oldResult'],

        data() {
            return {
                result  : this.oldResult != undefined ? this.oldResult : '',
                isValid : true,
            }
        },

        watch: {
            result: function(value) {
                this.isValid = this.validation(value);
                if(this.isValid) {
                    this.$emit('update', this.result);
                } else {
                    this.$emit('update', '');
                }
            }
        },

        methods: {

            validation(value) {
                
                if(value >= this.setting.min && value <= this.setting.max) {
                    return true;
                }
                return false;

            },

        }
    }
</script>