<template>
    <div>
        <div class="actions">
            <button class="btn btn-text" @click="getResult">Загрузить результаты</button>
        </div>
        <div class="table-container" v-if="stat.length != 0">
            <table>
                <tr>
                    <td class="row-item"></td>
                    <td v-for="(col, i) in setting.cols">{{ col.value }}</td>
                </tr>
                <tr v-for="(row, i) in setting.rows">
                    <td class="row-item">{{ row.value }}</td>
                    <td v-for="(col, j) in setting.cols">
                        <div class="stat-item" v-if="stat[i][j].count != 0">
                            <div class="row">
                                <div class="col-sm-4 clearfix"><span class="glyphicon glyphicon-user"></span></div>
                                <div class="col-sm-8 clearfix">{{ stat[i][j].count == 0 ? '' : stat[i][j].count }}</div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 clearfix"><span class="glyphicon glyphicon-stats"></span></div>
                                <div class="col-sm-8 clearfix">{{ stat[i][j].count == 0 ? '' : stat[i][j].percent + '%' }}</div>
                            </div>
                            <div class="percent-graf"><div :style="{ width: stat[i][j].percent + '%' }"></div></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>        
    </div>
</template>

<script>
    export default {

        props: ['setting', 'item'],

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
            getResult() { //table

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