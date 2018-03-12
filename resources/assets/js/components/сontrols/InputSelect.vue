<template>
    <div class="input-select">
        <input type="text" placeholder="введите текст для поиска" v-model="input" @click="openList" />
        <button @click="clean" class="clean"><span class="glyphicon glyphicon-remove"></span></button>
        <ul class="select close">
            <li v-for="item in items" @click="select(item.name, item.id)">{{ item.name }}</li>
        </ul>
    </div>
</template>

<script>
    export default {

        props: ['list', 'oldResult'],

        data() {
            return {
                input: '',
                result: this.oldResult == undefined ? 0 : this.oldResult
            }
        },

        computed: {
            items() {
                let self = this;
                return this.list.filter(function (value) {
                    if(parseInt(self.result, 10) == value.id) {
                        self.input = value.name;
                    }
                    if(self.input == '' || value.name.indexOf(self.input) != -1) {
                        return value;
                    }
                    return false;
                });
            },
        },

        methods: {
            clean() {
                this.input = '';
                this.result = undefined;
                this.$emit('update', undefined);
            },
            openList() {
                $('.input-select ul').toggleClass('close');
            },
            select(name, id) {
                $('.input-select ul').addClass('close');
                this.input = name;
                this.$emit('update', id);
            }
        }

    }
</script>