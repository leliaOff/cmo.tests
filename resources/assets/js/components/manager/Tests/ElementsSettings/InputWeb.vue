<template>
    <div>
        
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