<template>
    <div class="row">
        <input type="hidden" name="step" value="5">
        <input type="hidden" name="userGUID" :value="data.guid">
        <div class="col-12">
            <div class="jumbotron pt-4 pb-4">
                <h2>De-Activate account</h2>
                <p>If you can't access your account through the challenge question or e-mail recovery process, you must de-activate your account and create a new account.
                    When you create the new account:
                </p>
                <ul class="disc">
                    <li>all Not Submitted (Draft) applications and/or appendices in your de-activated account will be <strong>deleted</strong></li>
                    <li>you will have access to previously submitted applications and/or appendices from the dashboard</li>
                </ul>
                <p>Please note that de-activating a user account does not eliminate your applicable student loan repayment obligations.</p>
            </div>
                <div class="form-row pt-3 pb-3">
                    <div class="col-md-6">
                        <a href="/dashboard" class="btn btn-link text-left">Cancel</a>
                    </div>
                    <div class="col-md-6">
                        <button id="edit-check" type="button" class="btn btn-block btn-danger" data-toggle="modal" data-target="#modal-deactivate">De-Activate Account</button>
                    </div>
                </div>
        </div>
        <div class="modal fade" aria-labelledby="deactivate-modal-label" aria-hidden="true" id="modal-deactivate">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="deactivate-modal-label">De-Activate Account</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to de-activate your account?</p>
                    </div>
                    <div class="modal-footer form-wrapper">
                        <button @click="goNext('confirm-delete')" class="btn btn-large btn-block btn-danger" id="edit-deactivate" name="deactivate">Yes. De-activate my account</button>
                        <button @click="hideModal" class="btn btn-large btn-block btn-light" type="button" id="edit-cancel--2" name="cancel">No. Do NOT de-activate my account</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>

    span.form-required {
        color: red;
    }
    legend{
        display: block;
        padding: 0 15px;
        background: #ffffff;
        margin: 0;
        font-size: 21px;
        font-family: "Questrial", "Century Gothic", Arial, Helvetica, sans-serif;
        line-height: 24px;
        color: #869198;
        width: auto;
    }
    fieldset{
        border: solid 2px #e6e9ea;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        padding: 0 25px;
        margin: 24px 0;
    }
</style>
<script>
    //import axios from 'axios

    export default {
        props: ['errors', 'old', 'data'],
        data: () => ({
            loading: false,
            loadingError: false,
        }),
        methods: {
            goNext: function(e = null){
                this.$emit('submitform', e);
            },
            hideModal: function(){
                $('#modal-deactivate').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            }

        },

        created() {

        },
        mounted: function () {

        },
        watch: {

        }

    }

</script>
