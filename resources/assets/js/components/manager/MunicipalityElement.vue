<template>
    <div>
        <div class="modal fade bs-example-modal-lg" id="elementsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Муниципалитет</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Код: *</label></div>
                            <div class="col-sm-10 clearfix"><input type="text" placeholder="код" v-model="data.code"/>
                                <span class="input-validator code" v-if="validation.code != undefined">{{ validation.code[0] }}</span></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Наименование: *</label></div>
                            <div class="col-sm-10 clearfix"><input type="text" placeholder="наименование" v-model="data.name"/>
                                <span class="input-validator name" v-if="validation.name != undefined">{{ validation.name[0] }}</span></div>
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
                    
                    code: this.data.code,
                    name: this.data.name

                };
                var url = this.data.id == 0 ? window.baseurl + 'municipalityInsert' : window.baseurl + 'municipalityUpdate';

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