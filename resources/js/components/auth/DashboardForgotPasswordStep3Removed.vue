<template>
    <div class="row">
        <div class="col-12">
            <div class="jumbotron pt-4 pb-4">
                <h2>Account De-activated</h2>
                <p></p>
            </div>
                <div class="form-row pt-3 pb-3">
                    <div class="col-md-6">
                        <a href="/dashboard" class="btn btn-link text-left">Home</a>
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
