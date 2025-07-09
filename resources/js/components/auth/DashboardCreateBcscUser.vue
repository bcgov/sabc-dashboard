<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">

            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">

                <form ref="form" v-if="parsedSaml != ''" action="/dashboard/create-bcsc" method="post" id="sabc-form-builder" accept-charset="UTF-8">
                    <input type="hidden" name="_token" :value="csrf">
                    <input type="hidden" name="hpot" value="">
                    <div v-if="errors != ''" class="row">
                        <div class="col-12">
                            <div class="alert alert-contextual alert-danger" role="alert">
                                <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#stopsign-alert"></use></svg>
                                <template v-for="(error, i) in validationErrors" :key="i">
                                    <template v-for="(e, j) in error" :key="j">
                                        <p class="alert-p">
                                            <span v-html="e"></span>
                                            <br v-if="i === 1" />
                                        </p>
                                    </template>
                                </template>                            </div><!-- /.alert -->

                        </div><!-- /.block -->
                    </div>


                    <fieldset>
                        <legend><span class="fieldset-legend"><span class="icon-user text-info"></span> About You</span></legend>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-row pt-3">
                                    <div class="form-group col-12">
                                        <input type="hidden" name="assuranceLevel" v-model="parsedSaml.identityassurancelevel">
                                        <input type="hidden" name="userGUID" v-model="parsedSaml.DID">
                                        <input type="hidden" name="hpot" value="">
                                        <label for="edit-first-name">First Name</label>
                                        <input type="text" class="form-control" id="edit-first-name" name="first_name" size="60" maxlength="15" readonly="readonly" v-model="parsedSaml.firstName">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-middle-name">Middle Name</label>
                                        <input type="text" class="form-control" id="edit-middle-name" name="middle_name" size="60" maxlength="15" readonly="readonly" v-model="parsedSaml.middleName">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-last-name">Last Name</label>
                                        <input type="text" class="form-control" id="edit-last-name" name="last_name" size="60" maxlength="25" readonly="readonly" v-model="parsedSaml.lastName">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-email">Email Address <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.email != undefined ? 'is-invalid' : ''" id="edit-email" name="email" autocomplete="off" aria-describedby="emailHelp" size="60" maxlength="40" required v-model="parseOld['email']">
                                        <i id="emailHelp" class="form-text text-muted">We will send you important emails about the status of your application</i>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-confirm-email">Confirm E-mail Address <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.confirm_email != undefined ? 'is-invalid' : ''" id="edit-confirm-email" autocomplete="off" name="confirm_email" size="60" maxlength="40" required v-model="confirmEmailAddress" @paste.prevent>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="edit-gender-select">Gender <span class="form-required" title="This field is required.">*</span></label>
<!--                                        <input v-if="parsedSaml.DID != undefined && (initGender == 'M' || initGender == 'F')" type="text" class="form-control" :class="validationErrors.gender != undefined ? 'is-invalid' : ''" id="edit-gender-select" name="gender_select" size="60" maxlength="128" readonly="readonly" v-model="gender">-->
                                        <select class="form-control" id="edit-gender-select" name="gender_select" v-model="parsedSaml.gender" required>
                                            <option>- Select -</option>
                                            <option value="M">Man</option>
                                            <option value="F">Woman</option>
                                            <option value="X">Non-Binary</option>
                                            <option value="U">Prefer Not to Answer</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-12">
                                        <label for="edit-month">Date of Birth (Month, DD, YYYY)</label>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input type="text" class="form-control" :class="validationErrors.month != undefined ? 'is-invalid' : ''" id="edit-month" autocomplete="off" name="month" required v-model="birthDate[1]" readonly="readonly" >
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input type="text" class="form-control" :class="validationErrors.day != undefined ? 'is-invalid' : ''" id="edit-day" name="day" size="60" maxlength="2" readonly="readonly" v-model="birthDate[2]">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input type="text" class="form-control" :class="validationErrors.year != undefined ? 'is-invalid' : ''" id="edit-year" name="year" size="60" maxlength="4" readonly="readonly" v-model="birthDate[0]">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-social-insurance-number">Social Insurance Number <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.social_insurance_number != undefined ? 'is-invalid' : ''" id="edit-social-insurance-number" name="social_insurance_number" size="60" maxlength="11" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div v-if="parsedSaml.DID == undefined">
                                    <h6 class="text-uppercase text-danger"><strong>Important</strong></h6>
                                    <p>Please ensure you enter your FIRST name, MIDDLE name (if you have one) and LAST name in the correct fields as they appear on your Social Insurance Number card / letter.</p>
                                    <p><img src="/dashboard/img/sincard.gif" /></p>
                                    <p>Your identity will be verified through Employment and Social Development Canada using your Social Insurance Number before a student loan application, Appendix 1, or Appendix 2 is processed.</p>
                                </div>
                                <p v-else>Your identity will be verified through Employment and Social Development Canada using your Social Insurance Number before a student loan application, Appendix 1, or Appendix 2 is processed.</p>
                            </div>

                        </div>
                    </fieldset>

                    <fieldset>
                        <legend><span class="fieldset-legend"><span class="icon-webmail text-info"></span> Contact Information</span></legend>
                        <div class="row">
                            <div class="col-md-9">

                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <label for="edit-street1">Address Line 1 <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.Street1 != undefined ? 'is-invalid' : ''" id="edit-street1" name="Street1" size="60" maxlength="35" required v-model="parsedSaml.Street1">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-street2">Address Line 2</label>
                                        <input type="text" class="form-control" :class="validationErrors.Street2 != undefined ? 'is-invalid' : ''" id="edit-street2" name="Street2" size="60" maxlength="35" v-model="parsedSaml.Street2">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="edit-country">Country</label>
                                        <select class="form-control" :class="validationErrors.Country != undefined ? 'is-invalid' : ''" id="edit-country" name="Country" v-model="parsedSaml.Country">
                                            <option v-for="(cntry, key, index) in countries" :value="key">{{cntry}}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="edit-city">City / Town <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.City != undefined ? 'is-invalid' : ''" id="edit-city" name="City" size="60" maxlength="25" required v-model="parsedSaml.City">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="edit-prov">Province</label>
                                        <select class="form-control" :class="validationErrors.Prov != undefined ? 'is-invalid' : ''" id="edit-prov" name="Prov" required v-model="parsedSaml.Province">
                                            <option v-for="(prov,key,index) in provinces" :value="key">{{prov}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="edit-postzip">Postal / ZIP Code <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.PostZip != undefined ? 'is-invalid' : ''" id="edit-postzip" name="PostZip" size="60" maxlength="16" required v-model="parsedSaml.PostZip">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="phonenumber">Phone Number <span class="form-required" title="This field is required.">*</span></label>
                                        <input type="text" class="form-control" :class="validationErrors.Phone != undefined ? 'is-invalid' : ''" id="phonenumber" name="Phone" size="60" maxlength="14" required v-model="formatPhone" @keyup="formatElements" @change="formatElements" placeholder="(555) 555-5555">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-uppercase text-danger"><strong>Important</strong></h6>
                                <p>Please make sure your address is kept up to date - we may send important info on your application status and file activity by mail.</p>
                            </div>

                        </div>

                    </fieldset>

                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-contextual alert-warning m-3" role="alert">
                                <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#warning"></use></svg>
                                <p>If you have an existing StudentAid BC ID, it will be deactivated and any unsubmitted applications or appendices will be lost. This action cannot be undone.</p>
                            </div><!-- /.alert -->


                        </div>
                        <div class="col-md-6">
                            <a href="/dashboard" class="btn btn-link text-left">Cancel</a>
                        </div>
                        <div class="col-md-6">
                            <button type="button" @click="submitForm" class="btn btn-block btn-success">Create Account</button>
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
    import axios from 'axios';

    export default {
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            countries: '',
            provinces: '',
            states: '',
            loading: false,
            loadingError: false,
            parsedSaml: '',
            initGender: '',
            confirmEmailAddress: '',
            validationErrors: '',
            maintenanceMode: false,
            formatPhone: '',

            selectedCountry: '',
            parseOld: [],
        }),

        props: ['submit_status', 'submit_msg', 'errors', 'saml', 'role', 'old'],
        methods: {
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
            // formatElements: function () {
            //     setTimeout(function(){
            //         new Formatter(document.getElementById('phonenumber'), {
            //             'pattern': '({{999}}) {{999}}-{{9999}}',
            //             'persistent': true
            //         });
            //     }, 1500);
            // },
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
                        vm.parsedSaml = vm.parsedSaml.data;
                        vm.initGender = vm.parsedSaml.gender;

                        vm.countries = response.data.countries;
                        vm.provinces = response.data.provinces;
                        vm.states = response.data.states;
                        vm.loading = false;

                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        console.log(error);
                    });
            }
        },
        computed: {
            fullName: function(){
                let name = "";
                if(this.profile != ''){
                    if(this.profile.userProfile.middleName != undefined)
                        name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.middleName + " " + this.profile.userProfile.familyName);
                    else
                        name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.familyName);
                }
                return name.toUpperCase();
            },
            gender: function(){
                if(this.parsedSaml != '' && this.parsedSaml.DID != undefined){
                    let g = this.parsedSaml.gender;
                    if(g == "M")
                        return "Man";
                    if(g == "F")
                        return "Woman";
                    if(g == "X")
                        return "Non-Binary";
                    if(g == "U")
                        return "Prefer Not to Answer";
                    return "UNKNOWN";
                }
                return "";
            },

            //return array(YYYY, MM, DD)
            birthDate: function () {
                if(this.parsedSaml != '' && this.parsedSaml.DID != undefined){
                    let value = this.parsedSaml.birthdate;
                    return value.split('-');
                }
                return "";
            },

        },
        created() {
            document.title = "StudentAidBC - Create BCSC Account";

        },
        mounted: function () {
            this.parsedSaml = JSON.parse(this.saml);

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
        },
        watch: {
        }

    }

</script>
