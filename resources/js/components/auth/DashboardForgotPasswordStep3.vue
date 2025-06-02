<template>
    <div class="row">
        <input type="hidden" name="step" value="3">
        <div class="col-md-6 mb-5">
            <div class="jumbotron">
                <div class="text-center text-muted pb-4"><span class="icon icon-webmail icon-4x"></span></div>
                <h2>Reset password</h2>
                <p>Email a password reset to <strong>{{data.emailAddress}}</strong></p>
                <p><small><strong>Not your email?</strong> Select Cancel to return to the Login Page or De-Activate Account if you want to create a new account.</small></p>

            </div>
            <div class="row">
                <div class="col-12">
                    <input type="hidden" name="userGUID" :value="data.guid">
                    <a href="/dashboard" class="btn col-2 btn-link text-left">Cancel</a>
                    <button type="button" @click="goNext('reset')" class="btn col-9 btn-light">Reset Password</button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="jumbotron">
                <div class="text-center text-muted pb-4"><span class="icon icon-sslmanager icon-4x"></span></div>
                <h2>De-Activate account</h2>
                <p>Can't access your account? De-Activate it and create a new one.</p>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" @click="goNext('del')" class="btn btn-block btn-danger">De-Activate Account</button>
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
