<template>
    <div>
        <div class="modal fade bs-example-modal-lg" id="elementsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Пользователь</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-3 clearfix"><label class="input-title">Email: *</label></div>
                            <div class="col-sm-9 clearfix"><input type="text" name="email" placeholder="user@mail.ru" v-model="data.email"/>
                                <span class="input-validator email" v-if="validation.email != undefined">{{ validation.email[0] }}</span></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 clearfix"><label class="input-title">Роль: *</label></div>
                            <div class="col-sm-9 clearfix"><select name="role" v-model="data.role">
                                    <option value="user" selected>Пользователь</option>
                                    <option value="admin">Администратор</option>
                                </select></div>
                        </div>
                        <div class="alert alert-info" v-if="data.id != 0">Если необходимо изменить пароль, введите его два раза. Пароль не будет изменен, если оставить поле пустым</div>
                        <div class="row">
                            <div class="col-sm-3 clearfix"><label class="input-title">Пароль:<span v-if="data.id == 0"> *</span></label></div>
                            <div class="col-sm-9 clearfix"><input type="password" name="password" v-model="data.password"/>
                                <span class="input-validator password" v-if="validation.password != undefined">{{ validation.password[0] }}</span></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 clearfix"><label class="input-title">Повторите пароль:<span v-if="data.id == 0"> *</span></label></div>
                            <div class="col-sm-9 clearfix"><input type="password" name="confirmation" v-model="data.confirmation"/></div>
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
                    email: this.data.email,
                    role: this.data.role
                };
                if(this.data.id == 0 || this.data.password != '') {
                    data['password'] = this.data.password;
                    data['confirmation'] = this.data.confirmation;
                }
                var url = this.data.id == 0 ? window.baseurl + 'userInsert' : window.baseurl + 'userUpdate';

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