<template>
    <div class="row mt-3">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">
            <div class="taskbar">
                <h4>Create Account</h4>
                <hr class="mt-0"/>
            </div>

            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">

                <form @submit.prevent="submitForm" ref="form" action="/dashboard/create" method="post" id="sabc-register" accept-charset="UTF-8">
                    <input type="hidden" name="_token" :value="csrf">
                    <input type="hidden" name="hpot" value="">
                    <div v-if="errors != ''" class="row">
                        <div class="col-12">
                            <div class="alert alert-contextual alert-danger" role="alert">
                                <svg class="alert-icon icon-lg colorRed100" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#alert"></use></svg>
                                <template v-for="(error, i) in validationErrors" :key="i">
                                    <template v-for="(e, j) in error" :key="j">
                                        <p class="alert-p">
                                            <span v-html="e"></span>
                                            <br v-if="i === 1" />
                                        </p>
                                    </template>
                                </template>
                            </div><!-- /.alert -->

                        </div><!-- /.block -->
                    </div>


                    <fieldset>
                        <legend><span class="fieldset-legend"><span class="icon-user text-info"></span> About You</span></legend>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-row pt-3">
                                    <div class="form-group col-12">
                                        <label for="edit-first-name">First Name <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.first_name != undefined ? 'is-invalid' : ''" id="edit-first-name" name="first_name" :autocomplete="randomStr" size="60" maxlength="30" v-model="parseOld['first_name']" required>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-middle-name">Middle Name</label>
                                        <input type="text" class="form-control" id="edit-middle-name" name="middle_name" size="60" maxlength="30" v-model="parseOld['middle_name']">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-last-name">Last Name <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.last_name != undefined ? 'is-invalid' : ''" id="edit-last-name" name="last_name" :autocomplete="randomStr" size="60" maxlength="50" v-model="parseOld['last_name']" required>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-email">Email Address <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.email != undefined ? 'is-invalid' : ''" id="edit-email" name="email" :autocomplete="randomStr" aria-describedby="emailHelp" size="60" maxlength="40" v-model="parseOld['email']" required>
                                        <i id="emailHelp" class="form-text text-muted">We will send you important emails about the status of your application</i>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-confirm-email">Confirm E-mail Address <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.confirm_email != undefined ? 'is-invalid' : ''" id="edit-confirm-email" :autocomplete="randomStr" name="confirm_email" size="60" maxlength="40" required v-model="confirmEmailAddress" @paste.prevent>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="edit-gender">Gender <span class="form-required" title="This field is required.">*</span></label>
                                        <select class="form-control" :class="validationErrors.gender != undefined ? 'is-invalid' : ''" id="edit-gender" name="gender" :autocomplete="randomStr" v-model="parseOld['gender']" required>
                                            <option>- Select -</option>
                                            <option value="M">Man</option>
                                            <option value="F">Woman</option>
                                            <option value="X">Non-Binary</option>
                                            <option value="U">Prefer Not to Answer</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-12 m-0">
                                        <label for="edit-month">Date of Birth (Month, DD, YYYY) <span class="form-required" title="This field is required.">*</span></label>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <select class="form-control" :class="validationErrors.month != undefined ? 'is-invalid' : ''" id="edit-month" name="month" :autocomplete="randomStr" v-model="parseOld['month']">
                                            <option>- Select -</option>
                                            <option value="01">January</option>
                                            <option value="02">February</option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input type="text" class="form-control" :class="validationErrors.day != undefined ? 'is-invalid' : ''" id="edit-day" name="day" :autocomplete="randomStr" size="60" maxlength="2" v-model="parseOld['day']">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input type="text" class="form-control" :class="validationErrors.year != undefined ? 'is-invalid' : ''" id="edit-year" name="year" :autocomplete="randomStr" size="60" maxlength="4" v-model="parseOld['year']">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-social-insurance-number">Social Insurance Number <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.social_insurance_number != undefined ? 'is-invalid' : ''" id="edit-social-insurance-number" name="social_insurance_number" :autocomplete="randomStr" size="60" maxlength="11" required v-model="parseOld['social_insurance_number']">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div>
                                    <h6 class="text-uppercase text-danger"><strong>Important</strong></h6>
                                    <p>Please ensure you enter your FIRST name, MIDDLE name (if you have one) and LAST name in the correct fields as they appear on your Social Insurance Number card / letter.</p>
                                    <p><img src="/dashboard/img/sincard.gif" class="img-fluid"/></p>
                                    <p>Your identity will be verified through Employment and Social Development Canada using your Social Insurance Number before a student loan application, Appendix 1, or Appendix 2 is processed.</p>
                                </div>
                            </div>

                        </div>
                    </fieldset>

                    <create-user-id :parseOld="parseOld" :validationErrors="validationErrors"></create-user-id>
                    <create-challenge-questions :parseOld="parseOld" :validationErrors="validationErrors"></create-challenge-questions>


                    <fieldset>
                        <legend><span class="fieldset-legend"><span class="icon-webmail text-info"></span> Contact Information</span></legend>
                        <div class="row">
                            <div class="col-md-8">

                                <div class="form-row pt-3">
                                    <div class="form-group col-12">
                                        <label for="edit-street1">Address Line 1 <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.Street1 != undefined ? 'is-invalid' : ''" id="edit-street1" name="Street1" :autocomplete="randomStr" size="60" maxlength="35" required v-model="parseOld['Street1']">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-street2">Address Line 2</label>
                                        <input type="text" class="form-control" id="edit-street2" name="Street2" :autocomplete="randomStr" size="60" maxlength="35" v-model="parseOld['Street2']">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-country">Country <span class="form-required" title="This field is required.">*</span></label>
                                        <select class="form-control" :class="validationErrors.Country != undefined ? 'is-invalid' : ''" id="edit-country" name="Country" :autocomplete="randomStr" @change="updateCountry($event)" v-model="parseOld['Country']">
                                            <option value="" selected="selected"></option>
                                            <option v-for="(cntry, key, index) in countries" :value="key">{{cntry}}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="edit-city">City / Town <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.City != undefined ? 'is-invalid' : ''" id="edit-city" name="City" :autocomplete="randomStr" size="60" maxlength="25" required v-model="parseOld['City']">
                                    </div>
                                    <div v-if="selectedCountry == 'CAN'" class="form-group col-md-6">
                                        <label for="edit-prov">Province <span class="form-required" title="This field is required.">*</span></label>
                                        <select class="form-control" :class="validationErrors.Prov != undefined ? 'is-invalid' : ''" id="edit-prov" name="Prov" :autocomplete="randomStr" required v-model="parseOld['Prov']">
                                            <option v-for="(prov,key,index) in provinces" :value="key">{{prov}}</option>
                                        </select>
                                    </div>

                                    <div v-if="selectedCountry == 'USA'" class="form-group col-md-6">
                                        <label for="edit-state">State <span class="form-required" title="This field is required.">*</span></label>
                                        <select class="form-control" :class="validationErrors.State != undefined ? 'is-invalid' : ''" id="edit-state" name="State" :autocomplete="randomStr" required v-model="parseOld['State']">
                                            <option v-for="(state,key,index) in states" :value="key">{{state}}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="edit-postzip">Postal / ZIP Code <span v-if="selectedCountry == 'CAN' || selectedCountry == 'USA'" class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.PostZip != undefined ? 'is-invalid' : ''" id="edit-postzip" name="PostZip" :autocomplete="randomStr" size="60" maxlength="16" required v-model="parseOld['PostZip']">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="phonenumber">Phone Number <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.Phone != undefined ? 'is-invalid' : ''" id="phonenumber" name="Phone" :autocomplete="randomStr" size="60" maxlength="14" required v-model="formatPhone" @keyup="formatElements" @change="formatElements" placeholder="(555) 555-5555">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-uppercase text-danger"><strong>Important</strong></h6>
                                <p>Please make sure your address is kept up to date - we may send important info on your application status and file activity by mail.</p>
                            </div>

                        </div>

                    </fieldset>

                    <div class="row">
                        <div class="col-md-6">
                            <a href="/dashboard" class="btn btn-link text-left">Cancel</a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-block btn-success">Create Account</button>
                        </div>
                    </div>
                </form>

            </template>
        </div>

    </div><!-- /.row -->
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
    import CreateUserId from "./CreateUserId";
    import CreateChallengeQuestions from "./CreateChallengeQuestions";
    import axios from 'axios';

    export default {
        components: {CreateChallengeQuestions, CreateUserId},
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            selectedCountry: '',
            countries: '',
            provinces: '',
            states: '',
            loading: false,
            loadingError: false,

            confirmEmailAddress: '',
            validationErrors: '',
            maintenanceMode: false,
            formatPhone: '',
            parseOld: [],
        }),

        props: ['errors', 'old'],
        methods: {
            updateCountry: function(e){
                // console.log(e.target.value);
                this.selectedCountry = e.target.value;
            },
            submitForm: function(){
                $('[disabled]').removeAttr('disabled');
                this.$refs.form.submit();
            },
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },
            //
            formatElements: function () {
                if(this.formatPhone != undefined && this.formatPhone != '') {
                    var x = this.formatPhone.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                    this.formatPhone = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
                }
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
            },
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/fetch-countries',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {
                        vm.loading = false;
                        vm.loadingError = false;
                        vm.countries = response.data.countries;
                        vm.provinces = response.data.provinces;
                        vm.states = response.data.states;

                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        console.log(error);
                    });
            }
        },
        computed: {
            randomStr: function(){
                return Math.random();
            },
            fullName: function(){
                let name = "";
                // if(this.profile != ''){
                //     if(this.profile.userProfile.middleName != undefined)
                //         name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.middleName + " " + this.profile.userProfile.familyName);
                //     else
                //         name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.familyName);
                // }
                return name.toUpperCase();
            },
            gender: function(){
                // if(this.parsedSaml != '' && this.parsedSaml.DID != undefined){
                //     let g = this.parsedSaml.gender;
                //     if(g == "M")
                //         return "Male";
                //     if(g == "F")
                //         return "Female";
                //     return "UNKNOWN";
                // }
                return "";
            },

            //return array(YYYY, MM, DD)
            birthDate: function () {

                return value;
            },

        },
        created() {
        },
        mounted: function () {

            this.fetchData();
            if(this.errors != "")
                this.validationErrors = JSON.parse(this.errors);
            if(this.old != ""){
                this.parseOld = JSON.parse(this.old);
                this.confirmEmailAddress = this.parseOld['confirm_email'];
                this.formatPhone = this.parseOld['Phone'];
                if(this.parseOld['Country'] != undefined)
                    this.selectedCountry = this.parseOld['Country'];
            }

            document.title = "StudentAidBC - Create Account";

        },
        watch: {
        }

    }

</script>
