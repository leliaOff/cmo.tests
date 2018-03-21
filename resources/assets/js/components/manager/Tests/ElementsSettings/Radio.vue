<template>
    <div>
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Варианты ответов:</label></div>
            <div class="col-sm-9 clearfix">
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
        </div> 
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Изображения:</label></div>
            <div class="col-sm-9 clearfix"><images-gallery v-model="files"></images-gallery></div>
        </div>
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Условия появления:</label></div>
            <div class="col-sm-9 clearfix"><conditions v-model="conditions" :elementId="elementId" v-on:onChange="onChangeConditions"></conditions></div>
        </div>
    </div>
</template>

<script>

    import ImagesGallery    from '../ElementsControls/ImagesGallery.vue';
    import Conditions       from '../ElementsControls/Conditions.vue';

    export default {

        props: ['setting', 'files', 'conditions', 'elementId'],

        components: {
            ImagesGallery   : ImagesGallery,
            Conditions      : Conditions,
        },

        watch: {
            files: function() {
                this.$emit('onChangeFiles', this.files);
            }
        },

        methods: {

            rowUpdate() {
                if(this.setting.rows[this.setting.rows.length - 1].value != '') {
                    this.setting.rows.push({value: ''});
                }
            },

            rowRemove(index) {
                this.setting.rows.splice(index, 1);
            },

            /* Изменили список условий */
            onChangeConditions(conditions) {
                this.$emit('onChangeConditions', conditions);
            }
            
        },
    }
</script>