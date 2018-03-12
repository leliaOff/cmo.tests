<template>
    <div>
        <div class="table-container">
            <table>
                <tr v-for="(row, i) in setting.rows">
                    <td class="row-item">
                        <textarea v-model="row.value" v-on:keyup="rowUpdate" placeholder="введите значение"></textarea>
                        <button type="button" class="btn btn-default" v-if="i < (setting.rows.length - 1)" @click="rowRemove(i)"><span class="glyphicon glyphicon glyphicon-trash"></span></button>
                    </td>
                </tr>
            </table>
        </div>        
    </div>
</template>

<script>
    export default {

        props: ['setting'],

        methods: {

            rowUpdate() {

                if(this.setting.rows[this.setting.rows.length - 1].value != '') {
                    this.setting.rows.push({value: ''});
                }

            },

            rowRemove(index) {

                this.setting.rows.splice(index, 1);

            },
            
        },
    }
</script>