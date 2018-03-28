<template>
    <div>
        <div class="actions">
            <button class="btn btn-text" @click="getResult">Загрузить результаты</button>
        </div>
        <div class="table-container">
            <table v-if="stat.length != 0">
                <thead>
                    <tr><td></td><td><span class="glyphicon glyphicon-user"></span></td>
                        <td><span class="glyphicon glyphicon-stats"></span></td><td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(value, i) in stat" v-if="param.nr == false || value.count != 0">
                        <td width="40%">{{ value.name }}</td>
                        <td width="10%">{{ value.count }}</td>
                        <td width="50%">
                            {{ value.count == 0 ? '' : value.percent + '%' }}
                            <div class="percent-graf" v-if="value.count != 0"><div :style="{ width: value.percent + '%' }"></div></div>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>        
    </div>
</template>

<script>
    export default {
        props: ['elementId', 'item', 'param'],

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
            getResult() { //directory

                this.$store.state.loader = true;

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
        }
    }
</script>