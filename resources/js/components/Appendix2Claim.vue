<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">
            <div class="taskbar">
                <h4>Applicant Overview Info</h4>
                <hr class="mt-0"/>
            </div>

            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load your profile. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">

                <form action="/dashboard/appendix/claim/APPENDIX2" method="post" id="sabc-form-builder" accept-charset="UTF-8">
                    <input type="hidden" name="_token" :value="csrf">
                    <input type="hidden" name="hpot" value="">
                    <input type="hidden" name="form_update" value="FALSE">
                    <input type="hidden" name="consent" :value="userConsent">
                    <input type="hidden" name="middle_name" :value="middleName">
                    <input v-if="profile.userProfile.challengeQuestionPool != undefined" type="hidden" name="question1" :value="profile.userProfile.challengeQuestionPool.challengeQuestion[0].questionNumber">
                    <input v-if="profile.userProfile.challengeQuestionPool != undefined" type="hidden" name="answer1" :value="profile.userProfile.challengeQuestionPool.challengeQuestion[0].answer">
                    <input v-if="profile.userProfile.challengeQuestionPool != undefined" type="hidden" name="question2" :value="profile.userProfile.challengeQuestionPool.challengeQuestion[1].questionNumber">
                    <input v-if="profile.userProfile.challengeQuestionPool != undefined" type="hidden" name="answer2" :value="profile.userProfile.challengeQuestionPool.challengeQuestion[1].answer">
                    <input v-if="profile.userProfile.challengeQuestionPool != undefined" type="hidden" name="question3" :value="profile.userProfile.challengeQuestionPool.challengeQuestion[2].questionNumber">
                    <input v-if="profile.userProfile.challengeQuestionPool != undefined" type="hidden" name="answer3" :value="profile.userProfile.challengeQuestionPool.challengeQuestion[2].answer">

                    <div v-if="submit_msg != ''" class="row">
                        <div class="col-12">
                            <div class="alert alert-contextual alert-danger">
                                <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#stopsign-alert"></use></svg>
                                <p v-html="submit_msg"></p>
                            </div>

                        </div><!-- /.block -->
                    </div>
                    <div v-if="errors != ''" class="row">
                        <div class="col-12">
                            <div class="alert alert-contextual alert-danger">
                                <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#stopsign-alert"></use></svg>
                                <template v-for="(error, i) in validationErrors" :key="i">
                                    <template v-for="(e, j) in error" :key="j">
                                        <p class="alert-p">
                                            <span v-html="e"></span>
                                            <br v-if="i === 1" />
                                        </p>
                                    </template>
                                </template>
                            </div>

                        </div><!-- /.block -->
                    </div>
                    <div class="alert alert-contextual alert-info">
                        <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg>
                        <p>You are about to claim this Appendix 2 as <strong>{{fullName}}</strong>.  If this is not the right account please log out and log in as the right user before claiming this Appendix 2 access code.</p>
                    </div>
                    <fieldset v-if="access_code == '' || access_code == undefined">
                        <legend><span class="fieldset-legend"><span class="icon-sslmanager text-info"></span> Appendix Access Code</span></legend>
                        <div class="form-row pt-3">
                            <div class="form-group col-12">
                                <p>Why am I being asked to complete an <a target="_blank" :href="'/help-centre/loan-application-instructions-' + fullCurrentYear + '/appendix-2/' + shortCurrentYear">Appendix 2</a>?</p>
                                <label for="access_code">Appendix Access Code <span class="form-required" title="This field is required.">*</span></label>
                                <input type="text" class="form-control" id="access_code" name="access_code" aria-describedby="accessCodeHelp" size="60" maxlength="9" required>
                                <p id="accessCodeHelp" class="form-text text-muted"><a href="/help-centre/troubleshooting/appendix-two" target="_blank">Don‘t have an Appendix Access Code</a></p>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="appendix_type">Appendix Type</label>
                                <input type="text" class="form-control" id="appendix_type" name="appendix_type" disabled="disabled" value="Appendix 2">
                            </div>
                        </div>
                    </fieldset>
                    <template v-else>
                        <input type="hidden" name="appendix_type" value="APPENDIX2">
                        <input type="hidden" name="access_code" :value="access_code">
                    </template>

                    <fieldset id="edit-change-address">
                        <legend><span class="fieldset-legend"><span class="icon-webmail text-info"></span> Verify Contact Information</span></legend>
                        <div class="form-row pt-3">
                            <div class="form-group col-12">
                                <input v-if="profile.userProfile.assuranceLevel != undefined" type="hidden" name="assuranceLevel" :value="profile.userProfile.assuranceLevel">
                                <input v-if="profile.userProfile.assuranceLevel != undefined" type="hidden" name="first_name" :value="profile.userProfile.firstName">
                                <input v-if="profile.userProfile.assuranceLevel != undefined" type="hidden" name="last_name" :value="profile.userProfile.familyName">
                                <input v-if="profile.userProfile.assuranceLevel != undefined && profile.userProfile.middleName != undefined" type="hidden" name="middle_name" :value="profile.userProfile.middleName">
                                <label for="edit-fullname">Full Name</label>
                                <input type="text" class="form-control" id="edit-fullname" name="fullName" aria-describedby="nameHelp" size="60" maxlength="128" readonly="readonly" v-model="fullName">
                                <p v-if="profile.userProfile.challengeQuestionPool != undefined" id="nameHelp" class="form-text text-muted">Need to change your name, date of birth, or gender? <a href="/help-centre/applying-loans/how-do-i-change-my-namegenderdate-birth" target="_blank">Find out how.</a></p>
                                <p v-else id="nameHelp" class="form-text text-muted">Need to change your name, date of birth, or gender? <a href="https://www2.gov.bc.ca/gov/content/governments/government-id/bc-services-card/change-personal-information" target="_blank">Find out how.</a></p>
                            </div>
                        </div>
                        <div class="form-row mb-4">
                            <div class="form-group col-md-6">
                                <label v-if="profile.userProfile.gender != undefined" for="edit-gender">Gender</label>
                                <label v-else for="edit-gender">Gender <span class="form-required" title="This field is required.">*</span></label>
                                <input v-if="profile.userProfile.gender != undefined" type="text" class="form-control" id="edit-gender" name="gender" size="60" maxlength="128" readonly="readonly" v-model="gender">
                                <select v-else class="form-control" id="edit-gender" name="gender" v-model="profile.userProfile.gender" required>
                                    <option value="M">Man</option>
                                    <option value="F">Woman</option>
                                    <option value="X">Non-Binary</option>
                                    <option value="U">Prefer Not to Answer</option>
                                </select>
                            </div>
                            <div v-if="profile.userProfile.dateOfBirth != undefined" class="form-group col-md-6">
                                <label for="edit-dateofbirth">Date of Birth</label>
                                <input type="text" class="form-control" id="edit-dateofbirth" name="dateOfBirth" size="60" maxlength="128" readonly="readonly" v-model="birthDate">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="edit-street1">Address Line 1 <span class="form-required" title="This field is required.">*</span></label>
                                <input type="text" class="form-control" id="edit-street1" name="Street1" size="60" maxlength="35" required v-model="profile.userProfile.addressLine1">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="edit-street2">Address Line 2</label>
                                <input type="text" class="form-control" id="edit-street2" name="Street2" size="60" maxlength="35" v-model="profile.userProfile.addressLine2">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="edit-country">Country</label>
                                <select class="form-control" id="edit-country" name="Country" v-model="profile.userProfile.country">
                                    <option v-for="(cntry, key, index) in countries" :value="key">{{cntry}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="edit-city">City / Town <span class="form-required" title="This field is required.">*</span></label>
                                <input type="text" class="form-control" id="edit-city" name="City" size="60" maxlength="25" required v-model="profile.userProfile.city">
                            </div>
                            <div v-if="profile.userProfile.country == 'CAN'" class="form-group col-md-4">
                                <label for="edit-prov">Province</label>
                                <select class="form-control" id="edit-prov" name="Prov" required v-model="profile.userProfile.province">
                                    <option v-for="(prov,key,index) in provinces" :value="key">{{prov}}</option>
                                </select>
                            </div>
                            <div v-if="profile.userProfile.country == 'USA'" class="form-group col-md-4">
                                <label for="edit-state">State</label>
                                <select class="form-control" id="edit-state" name="State" required v-model="profile.userProfile.province">
                                    <option v-for="(state,key,index) in states" :value="key">{{state}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="edit-postzip">Postal / ZIP Code <span class="form-required" title="This field is required.">*</span></label>
                                <input type="text" class="form-control" id="edit-postzip" name="PostZip" size="60" maxlength="16" required v-model="profile.userProfile.postalCode">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="phonenumber">Phone Number <span class="form-required" title="This field is required.">*</span></label>
                                <input type="text" class="form-control" id="phonenumber" name="Phone" size="60" maxlength="14" required v-model="profile.userProfile.phoneNumber" @keyup="formatElements" @change="formatElements">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="edit-email">Email Address <span class="form-required" title="This field is required.">*</span></label>
                                <input type="text" class="form-control" id="edit-email" name="email" autocomplete="off" size="60" maxlength="40" required v-model="profile.userProfile.emailAddress">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="edit-confirm-email">Confirm E-mail Address <span class="form-required" title="This field is required.">*</span></label>
                                <input type="text" class="form-control" id="edit-confirm-email" autocomplete="off" name="confirm_email" size="60" maxlength="40" required v-model="confirmEmailAddress">
                            </div>
                        </div>

                    </fieldset>

                    <div class="row">
                        <div class="col-md-6">
                            <a href="/dashboard" class="btn btn-link text-left">Cancel</a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-block btn-success">Save and Continue</button>
                        </div>
                    </div>
                </form>

            </template>
        </div>

    </div><!-- /.row -->
</template>
<style scoped>
.taskbar h4 {
    line-height: 50px;
    margin: 0;
    padding: 0;
    font-family: "Questrial", "Century Gothic", Arial, Helvetica, sans-serif;
    font-weight: normal;
    font-size: 16.1px;
}
    .alertbody-alert-error :nth-child(2) {
        padding-top: 31px;
        min-height: auto !important;
    }
    .alertbody-alert-error :last-child {
        padding-bottom: 31px;
    }
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
    import ProfileChallengeQuestions from "./ProfileChallengeQuestions";

    export default {
        filters: {

            formatAppNumber: function(value){
                let year = value.slice(0, 4);
                let extra = value.slice(4);

                return year + '-' + extra;
            }
        },

        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            countries: '',
            provinces: '',
            states: '',
            loading: true,
            loadingError: false,
            profile: '',
            confirmEmailAddress: '',
            validationErrors: '',
            maintenanceMode: false,

            parsedProgramYears: '',
            fullCurrentYear: '',
            shortCurrentYear: '',

        }),
        props: ['access_code', 'submit_status', 'submit_msg', 'errors', 'program_years'],
        methods: {
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },
            getCurrentYear: function(){
                let lastYear = 0;
                let thisYear = 0;
                for(let i=0; i<this.parsedProgramYears.length; i++) {
                    if (typeof (this.parsedProgramYears[i]) === 'object') {
                        let startDate = parseInt(this.parsedProgramYears[i].lastYear);
                        if(startDate > lastYear){
                            lastYear = startDate;
                            thisYear = parseInt(this.parsedProgramYears[i].thisYear);
                        }
                    }
                }
                this.fullCurrentYear = lastYear + "" + thisYear;
                lastYear = lastYear + "";
                thisYear = thisYear + "";
                this.shortCurrentYear = lastYear.substr(2,2) + thisYear.substr(2,2);
            },
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/fetch-appendix-claim',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.profile = response.data.profile;
                        vm.countries = response.data.countries;
                        vm.provinces = response.data.provinces;
                        vm.states = response.data.states;
                        vm.confirmEmailAddress = response.data.profile.userProfile.emailAddress;
                        vm.formatElements();

                        if(vm.profile.userProfile.province == "ON"){
                            vm.profile.userProfile.province = "'ON'";
                        }
                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        //console.log(error);
                    });
            },
            formatElements: function () {
                if(this.profile != undefined && this.profile.userProfile != undefined) {
                    var x = this.profile.userProfile.phoneNumber.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                    this.profile.userProfile.phoneNumber = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
                }
                setTimeout(function(){
                    $('#edit-change-address input, #edit-change-address select').on('keyup change', function(){
                        $('input[name="form_update"]').val("TRUE");
                    });
                }, 2000);

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
            middleName: function(){
                let name = "";
                if(this.profile != ''){
                    if(this.profile.userProfile.middleName != undefined)
                        name = this.stripSlashes(this.profile.userProfile.middleName);
                }
                return name.toUpperCase();
            },
            gender: function(){
                if(this.profile != ''){
                    let g = this.profile.userProfile.gender;
                    if(g == "M")
                        return "MAN";
                    if(g == "F")
                        return "WOMAN";
                    if(g == "X")
                        return "NON-BINARY";
                    if(g == "U")
                        return "PREFER NOT TO ANSWER";
                    return "UNKNOWN";
                }
                return "";
            },
            birthDate: function () {
                let value = "";
                if(this.profile != ''){
                    value = this.profile.userProfile.dateOfBirth;
                    let year = value.slice(0, 4);
                    let month = value.slice(4, 6);
                    let day = value.slice(6, 8);

                    let date = new Date(year + "-" + month + "-" + day + "T19:30:00.000Z");
                    let formatted = date.toDateString().split(" ");
                    return formatted[1] + " " +  formatted[2] + ", " + formatted[3];
                }
                return value;
            },
            userConsent: function () {
                let consent = "";
                if(this.profile != ''){
                    consent = this.profile.userProfile.userConsent == undefined || this.profile.userProfile.userConsent == null || this.profile.userProfile.userConsent == "" ? "" : this.profile.userProfile.userConsent;
                }
                return consent;
            }

        },
        created() {

        },
        mounted: function () {
            this.fetchData();
            document.title = "StudentAidBC - Applicant Overview Info";

            if(this.errors != "")
                this.validationErrors = JSON.parse(this.errors);
            this.parsedProgramYears = JSON.parse(this.program_years);
            if(typeof(this.parsedProgramYears) === 'object'){

                // this.parsedProgramYears = Object.values(this.parsedProgramYears); //NOT SUPPORTED IN IE11

                let vm = this;
                //use this instead
                this.parsedProgramYears = Object.keys(this.parsedProgramYears).map(function(itm) { return vm.parsedProgramYears[itm]; });

                this.getCurrentYear();
            }

        },
        watch: {
        }

    }

</script>
