<template>
    <fieldset v-if="questionPool != '' && questionPool != undefined">
        <legend><span class="fieldset-legend"><span class="icon-uniF013 text-danger"></span> In case you forget your User ID / Password</span></legend>
        <div class="row">
            <div class="col-md-8">

                <div class="form-row pt-3">
                    <div class="form-group col-md-6">
                        <label for="edit-question1">Challenge Question #1 <span class="form-required" title="This field is required.">*</span></label>
                        <select class="form-control" :class="validationErrors.question1 != undefined ? 'is-invalid' : ''" id="edit-question1" name="question1" required v-model="parseOld['question1']">
                            <option value="">-- Please select a security question --</option>
                            <option v-for="(q,key,index) in questionPool.pool1" :value="key">{{q}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="edit-answer1">Enter your answer <span class="form-required" title="This field is required.">*</span></label>
                        <input type="password" class="form-control" :class="validationErrors.answer1 != undefined ? 'is-invalid' : ''" id="edit-answer1" name="answer1" size="60" maxlength="128" v-model="parseOld['answer1']">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="edit-question2">Challenge Question #2 <span class="form-required" title="This field is required.">*</span></label>
                        <select class="form-control" :class="validationErrors.question2 != undefined ? 'is-invalid' : ''" id="edit-question2" name="question2" required v-model="parseOld['question2']">
                            <option value="">-- Please select a security question --</option>
                            <option v-for="(q,key,index) in questionPool.pool2" :value="key">{{q}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="edit-answer2">Enter your answer <span class="form-required" title="This field is required.">*</span></label>
                        <input type="password" class="form-control" :class="validationErrors.answer2 != undefined ? 'is-invalid' : ''" id="edit-answer2" name="answer2" size="60" maxlength="128" v-model="parseOld['answer2']">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="edit-question3">Challenge Question #3 <span class="form-required" title="This field is required.">*</span></label>
                        <select class="form-control" :class="validationErrors.question3 != undefined ? 'is-invalid' : ''" id="edit-question3" name="question3" required v-model="parseOld['question3']">
                            <option value="">-- Please select a security question --</option>
                            <option v-for="(q,key,index) in questionPool.pool3" :value="key">{{q}}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="edit-answer3">Enter your answer <span class="form-required" title="This field is required.">*</span></label>
                        <input type="password" class="form-control" :class="validationErrors.answer3 != undefined ? 'is-invalid' : ''" id="edit-answer3" name="answer3" size="60" maxlength="128" v-model="parseOld['answer3']">
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <h6 class="text-uppercase text-danger"><strong>Important</strong></h6>
                <p>Please choose question/answer combinations you will remember. You will need this information if you forget your user ID and/or password in the future.</p>
            </div>

        </div>




    </fieldset>
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
    import axios from 'axios';

    export default {
        filters: {

            formatAppNumber: function(value){
                let year = value.slice(0, 4);
                let extra = value.slice(4);

                return year + '-' + extra;
            }
        },

        data: () => ({

            loading: true,
            loadingError: false,
            questionPool: '',

        }),
        props: ['parseOld', 'validationErrors'],
        methods: {
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/get-challenge-questions',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                //axios.get( '/fetch-dashboard' )
                    .then(function (response) {
                        vm.loading = false;
                        vm.questionPool = response.data.questions;
                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        console.log(error);
                    });
            },

            stripSlashes: function(str) {

                return (str + '').replace(/\\(.?)/g, function (s, n1) {
                    switch (n1) {
                        case '\\':
                            return '\\';
                        case '0':
                            return '\u0000';
                        case '':
                            return '';
                        default:
                            return n1;
                    }
                });
            }
        },
        computed: {

        },
        created() {

        },
        mounted: function () {
            this.fetchData();
        },
        watch: {

        }

    }

</script>
