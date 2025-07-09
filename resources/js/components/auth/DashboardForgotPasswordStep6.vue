<template>
    <div class="row">
        <input type="hidden" name="step" value="6">
        <div class="col-12">
            <div class="jumbotron">
                <h2>Reset password</h2>
                <p>StudentAid BC has sent an e-mail to <strong>{{data.emailAddress}}</strong>. Click the link in the e-mail to reset your password.</p>
                <p>If you don’t see the e-mail, check other places it might be, like your junk, spam, or other folders. If you no longer have access to the e-mail address above please follow the De-activate Account process.</p>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="/dashboard/login" class="btn btn-block btn-success">Back to Login</a>
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
