<template>
    <div class="easy-radio-group">
        <div class="easy-radio" v-for="(row, i) in data" @click="click(i)" v-bind:class="{ checked: (i == valueLocal), full: (row.value == 'arbitrary') }">
            
            <div class="easy-radio-icon">
                <span class="easy-radio-icon-point"></span>
                <input type="radio" :name="name" :value="i" v-model="valueLocal" />
            </div><!--
            --><div class="easy-radio-label" v-if="row.value == 'arbitrary'">
                <textarea class="arbitrary" placeholder="введите свой вариант ответа" v-model="arbitraryText" v-on:keyup="arbitraryUpdate"></textarea>
            </div><div class="easy-radio-label" v-else>
                {{ row.value }}
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        
        name:   'easyRadio',

        props: ['name', 'value', 'data'],

        data () {
            return {
                valueLocal      : this.value,
                arbitraryText   : ''
            }
        },

        methods: {
            click(i) {
                this.valueLocal = i;
                this.$emit('update', i);
            },
            arbitraryUpdate() {
                this.$emit('update', '_' + this.arbitraryText);
            }
        }

    }
</script>