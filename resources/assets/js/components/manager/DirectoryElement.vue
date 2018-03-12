<template>
    <div>
        <div class="modal fade bs-example-modal-lg" id="elementsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Справочник</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Алиас: *</label></div>
                            <div class="col-sm-10 clearfix"><input type="text" placeholder="name" v-model="data.alias"/>
                                <span class="input-validator alias" v-if="validation.alias != undefined">{{ validation.alias[0] }}</span></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Наименование: *</label></div>
                            <div class="col-sm-10 clearfix"><input type="text" placeholder="наименование" v-model="data.title"/>
                                <span class="input-validator title" v-if="validation.title != undefined">{{ validation.title[0] }}</span></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Описание:</label></div>
                            <div class="col-sm-10 clearfix"><textarea type="text" placeholder="описание"
                                                                v-model="data.description"></textarea></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" @click="update">Сохранить</button>
                        <button type="button" class="btn btn-primary" @click="update(true)">Сохранить и закрыть</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {

        data() {
            return {
                validation: { },
            }
        },

        computed: {
            data() {
                return this.$store.state.current
            }
        },

        methods: {
            
            update(isCloseWindow = false) {
                
                let self = this;
                self.validation = {};
                self.$store.state.loader = true;
                
                let data = {
                    
                    alias: this.data.alias,
                    title: this.data.title,
                    description: this.data.description,

                };
                var url = this.data.id == 0 ? window.baseurl + 'directoryInsert' : window.baseurl + 'directoryUpdate';

                axios.post(url, {
                    
                    data: data, id: this.data.id
                    
                }).then(function (response) {

                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');
                    
                    if(response.data.status == 'fail') {
                        
                        self.validation = response.data.error;

                    } else if(response.data.status == 'success') {

                        if(self.data.id == 0) self.data.id = response.data.result;
                        self.$emit('update');
                        if(isCloseWindow == true) $('#elementsModal').modal('hide');

                    }
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });
            }
            
        }
    }
</script>