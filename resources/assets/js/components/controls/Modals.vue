<template>
    <div>
        <!-- Модальное окно - подтверждение действия -->
        <div class="modal fade" id="sureWindow" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{ $store.state.sureModal.title }}</h4>
                    </div>
                    <div class="modal-body">
                        <p>{{ $store.state.sureModal.text }}</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" @click="okClick" type="button" >Да</button>
                        <button class="btn btn-info" @click="canselClick" type="button" >Нет</button>
                    </div>
                </div>
            </div>
        </div>
        <!--End of modal-->
    </div>
</template>

<script>
    export default {
        name: 'modals-component',
        methods: {
            okClick() {
                $('#sureWindow').modal('hide');
                this.$store.state.sureModal.action();
            },
            canselClick() {
                $('#sureWindow').modal('hide');
            }
        }
    }
</script>