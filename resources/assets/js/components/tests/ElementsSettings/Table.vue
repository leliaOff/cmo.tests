<template>
    <div>
        <div class="table-container">
            <table>
                <tr>
                    <td class="row-item"></td>
                    <td v-for="(col, i) in setting.cols"> {{ col.value }} </td>
                </tr>
                <tr v-for="(row, i) in setting.rows">
                    <td class="row-item"> {{ row.value }} </td>
                    <td v-for="(col, j) in setting.cols">
                        <easy-checkbox
                            :checked="result[i][j]" :index="[i, j]" 
                            :countRowResult="countRowResult[i]" :count="setting.count" 
                            :disabled="(result[i][j] == false && countRowResult[i] >= setting.count)" 
                            v-on:update="resultUpdate"></easy-checkbox>
                    </td>
                </tr>
            </table>
        </div>        
    </div>
</template>

<script>
    export default {

        props: ['setting', 'oldResult'],

        data() {
            
            let self = this;

            //Хранилище результатов
            let result = [];
            //Хранилище количества ответов в строк
            let countRowResult = [];

            if(self.oldResult == undefined) {

                $.each(self.setting.rows, function(i, row) {
                    result[i] = [];
                    countRowResult[i] = 0;
                    $.each(self.setting.cols, function(j, col) {
                        result[i][j] = false;
                    });
                });

            } else {

                //Хранилище результатов
                result = Object.assign({}, self.oldResult);

                //Пересчитываем количество ответов для строки
                $.each(result, function(i, cols) {
                    countRowResult[i] = 0;
                    $.each(cols, function(j, cell) {
                        if(cell == true) countRowResult[i]++;
                    });
                });

            }

            return {
                result: result,
                //countRowResult: countRowResult
            }
        },

        computed: {
            countRowResult() {                
                let result = [];
                //Пересчитываем количество ответов для строки
                $.each(this.result, function(i, cols) {
                    result[i] = 0;
                    $.each(cols, function(j, cell) {
                        if(cell == true) result[i]++;
                    });
                });
                return result;
            }
        },

        methods: {

            resultUpdate(index, state) {

                //Если не копировать из массива в массив - обновление countRowResult не происходит
                let result = Object.assign({}, this.result);
                result[index[0]][index[1]] = state;                
                this.result = Object.assign({}, result);

                //Отправляем результат папочке
                this.$emit('update', this.result);

            }
            
        },
        
    }
</script>