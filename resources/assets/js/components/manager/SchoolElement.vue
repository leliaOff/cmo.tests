<template>
    <div>
        <div class="modal fade bs-example-modal-lg" id="elementsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Образовательная организация</h4>
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
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">МО: *</label></div>
                            <div class="col-sm-10 clearfix"><select name="role" v-model="data.municipality_id">
                                    <option v-for="municipality in municipalities" :value="municipality.id">{{ municipality.name }}</option>
                                </select></div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Описание:</label></div>
                            <div class="col-sm-10 clearfix"><textarea type="text" name="description" placeholder="описание"
                                                                v-model="data.description"></textarea></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">ИНН:</label></div>
                            <div class="col-sm-10 clearfix"><input type="text" placeholder="наименование" v-model="data.INN"/>
                                <span class="input-validator INN" v-if="validation.INN != undefined">{{ validation.INN[0] }}</span></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Email:</label></div>
                            <div class="col-sm-10 clearfix"><input type="text" placeholder="наименование" v-model="data.email"/>
                                <span class="input-validator email" v-if="validation.email != undefined">{{ validation.email[0] }}</span></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 clearfix"><label class="input-title">Телефон:</label></div>
                            <div class="col-sm-10 clearfix"><input type="text" placeholder="наименование" v-model="data.phone"/>
                                <span class="input-validator phone" v-if="validation.phone != undefined">{{ validation.phone[0] }}</span></div>
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
                municipalities: []
            }
        },

        computed: {
            data() {
                return this.$store.state.current
            }
        },

        methods: {

            getMunicipalitiesList() {
                
                let self = this;
                self.$store.state.loader = true;

                axios.post(window.baseurl + 'municipalitiesList', {
                    // ...
                }).then(function (response) {
                    self.$store.state.loader = false;                    
                    if(response.data.status == 'relogin') self.$router.push('/');
                    if(response.data.status == 'success') self.municipalities = response.data.result;
                }).catch(function (error) {
                    self.$store.state.loader = false;
                    console.log(error);
                });

            },
            
            update(isCloseWindow = false) {
                
                let self = this;
                self.validation = {};
                self.$store.state.loader = true;
                
                let data = {
                    
                    code: this.data.code,
                    municipality_id: this.data.municipality_id,
                    name: this.data.name,
                    description: this.data.description,
                    INN: this.data.INN,
                    email: this.data.email,
                    phone: this.data.phone,

                };
                var url = this.data.id == 0 ? window.baseurl + 'schoolInsert' : window.baseurl + 'schoolUpdate';

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
            
        },

        mounted() {
            this.getMunicipalitiesList();
        },
    }
</script>