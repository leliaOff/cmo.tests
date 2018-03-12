<template>
    <div class="easy-checkbox" v-bind:class="{ checked: isChecked, disabled: disabled }" @click="click">
        <div class="easy-checkbox-icon" v-bind:class="{ radio: count == 1 }">
            <span class="glyphicon glyphicon-ok"></span>
            <input type="checkbox" v-model="isChecked" :disabled="disabled" />
        </div><div class="easy-checkbox-label" v-if="label != undefined">
            {{ label }}
        </div>
    </div>
</template>

<script>
    export default {
        
        name:   'easyCheckbox',

        props: ['checked', 'disabled', 'index', 'label', 'count'],

        data() {
            return {
                isChecked: this.checked
            }
        },

        methods: {
            click() {

                if(!this.isChecked && this.disabled) return;

                this.isChecked = !this.isChecked;
                this.$emit('update', this.index, this.isChecked);
            }
        }

    }
</script>