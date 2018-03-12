<template>
    <div>
        <div class="actions">
            <button class="btn btn-text" @click="getResult">Загрузить результаты</button>
        </div>
        <div class="table-container" v-if="stat.length != 0">
            <table>
                <tr v-for="(row, i) in setting.rows" v-if="param.nr == false || stat[i].count != 0">
                    <td class="row-item">{{ row.value }}</td>
                    <td>
                        <div class="stat-item" v-if="stat[i].count != 0">
                            <div class="row">
                                <div class="col-sm-8 clearfix"><label>Количество респондентов:</label></div>
                                <div class="col-sm-4 clearfix"><label>{{ stat[i].count }}</label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-8 clearfix"><label>Процент респондентов:</label></div>
                                <div class="col-sm-4 clearfix"><label>{{ stat[i].count == 0 ? '' : stat[i].percent + '%' }}</label></div>
                            </div>
                            <div class="percent-graf"><div :style="{ width: stat[i].percent + '%' }"></div></div>
                        </div>
                    </td>
                </tr>
            </table>
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
                let incisions = Object.assign({}, this.$store.state.incisions);
                return incisions;
            }
        },

        watch: {
            incisions: function (incisions) {
                if(this.is_load) this.getResult();
            },
        },

        methods: {
            getResult() { //radio

                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'resultsByAnswer', {
                    item: self.item, incisions: self.incisions
                }).then(function (response) {
                    self.$store.state.loader = false;
                    self.stat = response.data;
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

                this.is_load = true;

            }
        },
    }
</script>