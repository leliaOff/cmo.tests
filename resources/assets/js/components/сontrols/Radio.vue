<template>
    <div class="easy-radio-group">
        <div class="easy-radio" v-for="(row, i) in data" @click="click(i)" v-bind:class="{ checked: (i == result) }">
            <div class="easy-radio-icon">
                <span class="easy-radio-icon-point"></span>
                <input type="radio" :name="name" :value="i" v-model="result" />
            </div><div class="easy-radio-label">
                {{ row.value }}
            </div>
        </div>
    </div>
</template>

<!--input type="radio" :name="'radio_' + id" :value="i" v-model="result" @click="resultUpdate" />
                    {{ row.value }}-->

<script>
    export default {
        
        name:   'easyRadio',

        props: ['name', 'model', 'data'],

        data() {
            return {
                result: this.model
            }
        },

        methods: {
            click(i) {
                this.result = i;
                this.$emit('update', i);
            }
        }

    }
</script>