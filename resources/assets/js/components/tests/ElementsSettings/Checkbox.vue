<template>
    <div>
        <easy-checkbox v-for="(row, i) in setting.rows" :key="i"
            :checked="result[i]" :index="i" :label="row.value"
            :disabled="(result[i] == false && countResult == setting.count)" 
            v-on:update="resultUpdate"></easy-checkbox>      
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

            if(self.oldResult == undefined || self.oldResult === false) {

                $.each(self.setting.rows, function(i, row) {
                    result[i] = false;
                });

            } else {

                //Хранилище результатов
                result = Object.assign({}, self.oldResult);

                //Пересчитываем количество ответов для строки
                $.each(result, function(i, value) {
                    if(value == true) countResult++;
                });

            }

            return {
                result      : result,
                countResult : countResult,
            }
        },

        methods: {

            resultUpdate(i, state) {

                let self = this;
                self.result[i] = state;

                //Пересчитываем количество ответов для строки
                self.countResult = 0;
                $.each(self.result, function(i, value) {
                    if(value == true) self.countResult++;
                });

                //Отправляем результат папочке
                self.$emit('update', self.result);

            }
            
        },
    }
</script>