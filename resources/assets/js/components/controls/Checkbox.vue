<template>
    <div class="easy-checkbox" v-bind:class="{ checked: isChecked, disabled: disabled }">
        <div class="easy-checkbox-icon" @click="click" v-bind:class="{ radio: count == 1 }">
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
                localChecked: this.checked
            }
        },

        computed: {
            isChecked() {
                return this.checked;
            },
        },

        methods: {
            click() {

                if(!this.isChecked && this.disabled) return;

                this.localChecked = !this.localChecked;
                this.$emit('update', this.index, this.localChecked);
            }
        }

    }
</script>