<template>
    <div>
        <fieldset>
            <legend><span class="fieldset-legend">Answer your challenge question</span></legend>
            <div class="mt-3">

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-contextual alert-info" role="alert">
                            <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg>
                            <p>You have <strong>three chances</strong> to answer your challenge question correctly. If the third attempt is incorrect you will be provided with other options to recover your User ID/Password.</p>
                        </div><!-- /.alert -->
                    </div><!-- /.block -->
                </div>

            </div>
            <input type="hidden" name="step" value="2">
            <input type="hidden" name="questionPool" :value="data.questionPoolNumber">
            <input type="hidden" name="questionNumber" :value="data.questionNumber">
            <input type="hidden" name="userGUID" :value="data.guid">
            <input type="hidden" name="emailAddress" :value="data.emailAddress">
            <input type="hidden" name="challengeQuestion" :value="data.challengeQuestion">
            <div class="row">
                <div class="col-12">
                    <div class="form-row pt-3">
                        <div class="form-group col-12">
                            <label for="edit-answer">{{data.challengeQuestion}} <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" :class="errors.answer != undefined ? 'is-invalid' : ''" id="edit-answer" name="answer" size="60" maxlength="128" required>
                        </div>
                    </div>
                    <div class="form-row pt-3 pb-3">
                        <div class="col-md-6">
                            <a href="/dashboard" class="btn btn-link text-left">Cancel</a>
                        </div>
                        <div class="col-md-6">
                            <button type="button" @click="goNext" class="btn btn-block btn-success">Next</button>
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
            goNext: function(){
                this.$emit('submitform');
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
