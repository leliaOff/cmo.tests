<template>
    <div>
        
        <!-- Минимум -->
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Минимальное значение:</label></div>
            <div class="col-sm-9 clearfix"><input class="form-control" type="number" step="1" :max="setting.max" placeholder="3" v-model="setting.min" /></div>
        </div>

        <!-- Максимум -->
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Максимальное значение:</label></div>
            <div class="col-sm-9 clearfix"><input class="form-control" type="number" step="1" :min="setting.min" placeholder="18" v-model="setting.max" /></div>
        </div>

        <!-- Изображения -->
        <div class="row">
            <div class="col-sm-3 clearfix"><label class="input-title">Изображения:</label></div>
            <div class="col-sm-9 clearfix"><images-gallery v-model="files"></images-gallery></div>
        </div>

        <!-- Условия -->
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

            /* Изменили список условий */
            onChangeConditions(conditions) {
                this.$emit('onChangeConditions', conditions);
            }
            
        },
    }
</script>