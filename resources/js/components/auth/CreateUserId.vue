<template>
    <fieldset v-if="questionPool != ''">
        <legend><span class="fieldset-legend"><span class="icon-sslmanager text-warning"></span> Create User ID and Password</span></legend>
        <div class="row">
            <div class="col-md-8">

                <div class="form-row pt-3">
                    <div class="form-group col-12">
                        <label for="edit-user-id">Desired User ID <span class="form-required" title="This field is required.">*</span></label>
                        <input type="text" class="form-control" :class="validationErrors.user_id != undefined ? 'is-invalid' : ''" id="edit-user-id" name="user_id" size="60" minlength="8" maxlength="40" required autocomplete="off" v-model="parseOld['user_id']">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="edit-password-pass1">Password <span class="form-required" title="This field is required.">*</span></label>
                        <password-strength :validation="validationErrors" :eleId="'edit-password-pass1'" :eleName="'password'" :req="true"></password-strength>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="edit-password-pass2">Confirm password <span class="form-required" title="This field is required.">*</span></label>
                        <input type="password" class="form-control" :class="validationErrors.confirm_password != undefined ? 'is-invalid' : ''" id="edit-password-pass2" name="confirm_password" size="60" maxlength="128" required autocomplete="off">
                    </div>
                </div>


            </div>
            <div class="col-md-4">
                <h6 class="text-uppercase text-danger"><strong>Important</strong></h6>
                <p>Please do not share your StudentAid BC User ID and password with anyone, including your parents, spouse, financial assistance officers and institution officials. This is to protect the privacy of your personal information.</p>
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
        data: () => ({

            loading: true,
            loadingError: false,
            questionPool: '',

        }),
        props: ['parseOld', 'validationErrors'],
        methods: {
            formatAppNumber: function(value){
                let year = value.slice(0, 4);
                let extra = value.slice(4);

                return year + '-' + extra;
            },
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

                        //prevent password to autofill
                        // $("input[type='password']").prop('type', 'number');
                        // setTimeout(function () {
                        //     $("input[type='number']").prop('type', 'password');
                        // }, 500);

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
