<template>
    <div>
        <easy-checkbox v-for="(row, i) in setting.rows" :key="i"
            :checked="result[i]" :index="i" :label="row.value"
            :disabled="(result[i] == false && countResult == setting.count)" 
            v-on:update="resultUpdate"></easy-checkbox>  

        <!-- Произвольный ответ -->
        <div v-if="arbitrary" class="arbitrary-group">
            <easy-checkbox :index="setting.rows.length" label=""
                :disabled="(result[setting.rows.length] == '' && countResult == setting.count)" 
                v-on:update="arbitraryUpdate" :checked="arbitraryChecked"></easy-checkbox>
            <div class="arbitrary-container"><textarea class="arbitrary" placeholder="введите свой вариант ответа" v-model="arbitraryText" 
                v-on:keyup="arbitraryUpdate" :disabled="(result[setting.rows.length] == '' && countResult == setting.count)" ></textarea></div>
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
                    if(value == true || value != '') countResult++;
                    arbitraryText = value;
                });

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
                    if(value == true || value != '') this.countResult++;
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