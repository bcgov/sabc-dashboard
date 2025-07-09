<template>
    <div>
        <fieldset>
            <legend><span class="fieldset-legend">Create a New Password</span></legend>
            <div class="mt-3">
                <div class="alert alert-contextual alert-info" role="alert">
                    <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg>
                    <p>Resetting password for <strong>{{data.userID}}</strong></p>
                </div><!-- /.alert -->
            </div>
            <input type="hidden" name="step" value="7">
            <input type="hidden" name="userID" :value="data.userGUID">
            <input type="hidden" name="username" :value="data.userID">
            <div class="row">
                <div class="col-12">

                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="edit-password-pass1">Password <span class="form-required" title="This field is required.">*</span></label>
                            <input type="password" class="form-control" :class="errors.password != undefined ? 'is-invalid' : ''" id="edit-password-pass1" name="password" size="60" maxlength="128" required autocomplete="off">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="edit-password-pass2">Confirm password <span class="form-required" title="This field is required.">*</span></label>
                            <input type="password" class="form-control" :class="errors.confirm_password != undefined ? 'is-invalid' : ''" id="edit-password-pass2" name="confirm_password" size="60" maxlength="128" required autocomplete="off">
                        </div>
                    </div>
                    <div class="form-row pt-3 pb-3">
                        <div class="col-md-6">
                            <a href="/dashboard" class="btn btn-link text-left">Cancel</a>
                        </div>
                        <div class="col-md-6">
                            <button type="button" @click="goNext" class="btn btn-block btn-success">Reset Password</button>
                        </div>
                    </div>

                </div>
            </div>
        </fieldset>

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
