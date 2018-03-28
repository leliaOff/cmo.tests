<template>
    <div>
        <div class="actions">
            <button class="btn btn-text" @click="getResult">Загрузить результаты</button>
        </div>
        <div class="table-container" v-if="stat.length != 0">
            <div class="row" v-for="(row, i) in setting.rows" v-if="param.nr == false || stat[i].count != 0">
                <div class="col-sm-4 clearfix"><b>{{ row.value }}</b></div>
                <div class="col-sm-8 clearfix">
                    <div class="stat-item" v-if="stat[i].count != 0">
                        <div class="row">
                            <div class="col-sm-8 clearfix"><label>Количество респондентов:</label></div>
                            <div class="col-sm-4 clearfix"><label>{{ stat[i].count }}</label></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 clearfix"><label>Процент респондентов:</label></div>
                            <div class="col-sm-4 clearfix"><label>{{ stat[i].count == 0 ? '' : stat[i].percent + '%' }}</label></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 clearfix"><div class="percent-graf"><div :style="{ width: stat[i].percent + '%' }"></div></div></div>
            </div>
        </div>        
    </div>
</template>

<script>
    export default {

        props: ['setting', 'item', 'param'],

        data() {
            return {
                stat: [],
                is_load: false
            }
        },

        computed: {
            incisions() {
                return Object.assign({}, this.$store.state.incisions);
            }
        },

        watch: {
            incisions: function (incisions) {
                if(this.is_load) this.getResult();
            },
        },

        methods: {
            getResult() { //radio

                this.$store.state.loader = true;

                //Если есть Другой ответ
                if(parseInt(this.item.data.arbitrary) === 1 && this.item.data.rows[this.item.data.rows.length - 1].value != 'Другое') {
                    this.item.data.rows.push({value: 'Другое'});
                }

                axios.post(window.baseurl + 'resultsByAnswer', {
                    item: this.item, incisions: this.$store.state.incisions
                }).then(response => {
                    this.$store.state.loader = false;
                    this.stat = response.data;
                }).catch(error => {
                    this.$store.state.loader = false;
                    console.log(error);
                });

                this.is_load = true;

            }
        },
    }
</script>