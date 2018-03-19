<template>
    <div class="image-gallery">
        <div :class="'image-item ' + 'col-' + images.length" v-for="(image, i) in images">
            <button @click="show(i)"><img :src="image.path" data-toggle="modal" data-target="#imageGalleryModal"/></button>
        </div>

        <!-- Модальное окно -->
        <div class="modal fade bd-example-modal-lg" id="imageGalleryModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">                        
                        <button class="prev" @click="prev()"><span class="glyphicon glyphicon-chevron-left"></span></button>
                        <button class="next" @click="next()"><span class="glyphicon glyphicon-chevron-right"></span></button>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <img :src="images[current].path" style="width:100%"/>
                    </div>
                </div>
            </div>
        </div>
        <!--End of modal-->

    </div>
</template>

<script>
    export default {

        props: ['value'],

        data() {
            return {
                images  : this.value,
                current : 0,
            }
        },

        methods: {
            
            show(index) {
                this.current = index;
            },

            next() {
                let i = this.current + 1;
                if(i == this.images.length) i = 0;
                this.current = i;
            },

            prev() {
                let i = this.current - 1;
                if(i == -1) i = this.images.length - 1;
                this.current = i;
            }
        }

    }
</script>