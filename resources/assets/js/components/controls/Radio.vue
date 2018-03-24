<template>
    <div class="easy-radio-group">
        <div class="easy-radio" v-for="(row, i) in data" @click="click(i)" v-bind:class="{ checked: (i == valueLocal) }">
            
            <div class="easy-radio-icon">
                <span class="easy-radio-icon-point"></span>
                <input type="radio" :name="name" :value="i" v-model="valueLocal" />
            </div><!--
            --><div class="easy-radio-label">
                {{ row.value }}
            </div>

        </div>

        <div class="easy-radio" v-if="parseInt(isArbitrary) == 1" v-bind:class="{ checked: (arbitraryText != ''), full: true }">
            
            <div class="easy-radio-icon">
                <span class="easy-radio-icon-point"></span>
                <input type="radio" :name="name" :value="'_' + arbitraryText" v-model="valueLocal" />
            </div><!--
            --><div class="easy-radio-label">
                <textarea class="arbitrary" placeholder="введите свой вариант ответа" v-model="arbitraryText" v-on:keyup="arbitraryUpdate"></textarea>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        
        name:   'easyRadio',

        props: ['name', 'value', 'data', 'isArbitrary'],

        data () {

            let arbitraryText = '';
            if(this.value != undefined && typeof(this.value) == 'string' && this.value != '') {
                arbitraryText = this.value.substring(1);
            }

            return {
                valueLocal      : this.value,
                arbitraryText   : arbitraryText
            }
        },

        methods: {
            click(i) {
                this.valueLocal = i;
                this.$emit('update', i);
            },
            arbitraryUpdate() {
                this.valueLocal = '_' + this.arbitraryText;
                this.$emit('update', '_' + this.arbitraryText);
            }
        }

    }
</script>