<template>
    <div>
        <easy-checkbox v-for="(row, i) in setting.rows" :key="i"
            :checked="result[i]" :index="i" :label="row.value"
            :disabled="(result[i] == false && countResult == setting.count)" 
            v-on:update="resultUpdate"></easy-checkbox>  

        <!-- Произвольный ответ -->
        <div class="row" v-if="arbitrary">
            <div class="col-sm-1 clearfix">
                <easy-checkbox :index="setting.rows.length" label=""
                                v-on:update="arbitraryUpdate" :checked="arbitraryChecked"></easy-checkbox>
            </div>
            <div class="col-sm-11 clearfix">
                <textarea class="arbitrary" placeholder="введите свой вариант ответа" v-model="arbitraryText" v-on:keyup="arbitraryUpdate"></textarea>
            </div>
        </div>
            
    </div>
</template>

<script>
    export default {

        props: ['setting', 'oldResult'],

        mounted() {
            console.log(this.setting);
            console.log(this.oldResult);
        },

        data() {

            let self = this;
            
            //Хранилище результатов
            let result = [];
            //Количество
            let countResult = 0;
            //Если есть произвольный ответ
            let arbitraryText = '';

            if(self.oldResult == undefined || self.oldResult === false) {

                $.each(self.setting.rows, function(i, row) {
                    result[i] = false;
                });

                if(parseInt(this.setting.arbitrary) == 1) {
                    result[result.length] = '';
                }

            } else {

                //Хранилище результатов
                result = Object.assign({}, self.oldResult);

                //Пересчитываем количество ответов для строки
                $.each(result, function(i, value) {
                    if(value == true) countResult++;
                });

                arbitraryText = result[result.length - 1];

            }            

            return {
                result              : result,
                countResult         : countResult,
                arbitraryText       : arbitraryText,
            }
        },

        computed: {
            arbitrary() {
                if(this.setting.arbitrary == undefined) return false;
                if(parseInt(this.setting.arbitrary) == 0) return false;
                return true;
            },
            arbitraryChecked() {
                return this.arbitraryText.trim() == '' ? false : true;
            }
        },

        methods: {

            resultUpdate(i, state) {

                this.result[i] = state;

                //Пересчитываем количество ответов для строки
                this.countResult = 0;
                $.each(this.result, (i, value) => {
                    if(value == true) this.countResult++;
                });

                //Отправляем результат папочке
                this.$emit('update', this.result);

            },

            arbitraryUpdate() {
                this.resultUpdate(this.result.length - 1, this.arbitraryText);
            }
            
        },
    }
</script>