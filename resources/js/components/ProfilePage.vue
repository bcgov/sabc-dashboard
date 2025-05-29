<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">
            <div class="taskbar">
                <h4>Profile</h4>
                <hr class="mt-0"/>
            </div>

            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load your profile. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">

            <form :action="profile.userProfile.challengeQuestionPool != undefined ? '/dashboard/profile' : '/dashboard/bcsc-profile'" method="post" id="sabc-form-builder" accept-charset="UTF-8">
                <input type="hidden" name="_token" :value="csrf">
                <input type="hidden" name="hpot" value="">
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
                            </template>                        </div>

                    </div><!-- /.block -->
                </div>
                <div v-if="submit_status == true" class="row">
                    <div class="col-12">
                        <div class="alert alert-contextual alert-success">
                            <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#check"></use></svg>
                            <p>Profile updated successfully</p>
                        </div>

                    </div><!-- /.block -->
                </div>



                <fieldset>
                    <legend><span class="fieldset-legend"><span class="icon-webmail text-info"></span> Update My Address / Phone</span></legend>
                    <div class="form-row pt-3">
                        <div class="form-group col-12">
                            <input v-if="profile.userProfile.assuranceLevel != undefined" type="hidden" name="uguid" :value="profile.userProfile.userGUID">
                            <input v-if="profile.userProfile.assuranceLevel != undefined" type="hidden" name="assuranceLevel" :value="profile.userProfile.assuranceLevel">
                            <input v-if="profile.userProfile.assuranceLevel != undefined" type="hidden" name="last_name" :value="profile.userProfile.familyName">
                            <input v-if="profile.userProfile.assuranceLevel != undefined && profile.userProfile.firstName != undefined" type="hidden" name="first_name" :value="profile.userProfile.firstName">
                            <input v-if="profile.userProfile.assuranceLevel != undefined && profile.userProfile.middleName != undefined" type="hidden" name="middle_name" :value="profile.userProfile.middleName">
                            <label for="edit-fullname">Full Name</label>
                            <input type="text" class="form-control" id="edit-fullname" name="fullName" aria-describedby="nameHelp" size="60" maxlength="128" autocomplete="off" readonly="readonly" v-model="fullName">
                            <i v-if="profile.userProfile.challengeQuestionPool != undefined" id="nameHelp" class="form-text text-muted">Need to change your name, date of birth, or gender? <a href="/help-centre/applying-loans/how-do-i-change-my-namegenderdate-birth" target="_blank">Find out how.</a></i>
                            <i v-else id="nameHelp" class="form-text text-muted">Need to change your name, date of birth, or gender? <a href="https://www2.gov.bc.ca/gov/content/governments/government-id/bc-services-card/your-card/change-personal-information" target="_blank">Find out how.</a></i>
                        </div>
                    </div>
                    <div class="form-row mb-4">
                        <div class="form-group col-md-6">
                            <label v-if="profile.userProfile.gender != undefined" for="edit-gender">Gender</label>
                            <label v-else for="edit-gender">Gender <span class="form-required" title="This field is required.">*</span></label>
<!--                            <input v-if="profile.userProfile.gender != undefined" type="text" class="form-control" id="edit-gender" name="gender" autocomplete="off" size="60" maxlength="128" readonly="readonly" v-model="gender">-->
                            <select class="form-control" id="edit-gender" name="gender" autocomplete="off" v-model="profile.userProfile.gender" required>
                                <option value="M">Man</option>
                                <option value="F">Woman</option>
                                <option value="X">Non-Binary</option>
                                <option value="U">Prefer Not to Answer</option>
                            </select>
                        </div>
                        <div v-if="profile.userProfile.dateOfBirth != undefined" class="form-group col-md-6">
                            <label for="edit-dateofbirth">Date of Birth</label>
                            <input type="text" class="form-control" id="edit-dateofbirth" name="dateOfBirth" size="60" maxlength="128" autocomplete="off" readonly="readonly" v-model="birthDate">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="edit-street1">Address Line 1 <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" id="edit-street1" name="Street1" autocomplete="off" size="60" maxlength="35" required v-model="profile.userProfile.addressLine1">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="edit-street2">Address Line 2</label>
                            <input type="text" class="form-control" id="edit-street2" name="Street2" autocomplete="off" size="60" maxlength="35" v-model="profile.userProfile.addressLine2">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="edit-country">Country</label>
                            <select class="form-control" id="edit-country" name="Country" autocomplete="off" v-model="profile.userProfile.country">
                                <option v-for="(cntry, key, index) in countries" :value="key">{{cntry}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="edit-city">City / Town <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" id="edit-city" name="City" autocomplete="off" size="60" maxlength="25" required v-model="profile.userProfile.city">
                        </div>
                        <div v-if="profile.userProfile.country == 'CAN'" class="form-group col-md-6">
                            <label for="edit-prov">Province</label>
                            <select class="form-control" id="edit-prov" name="Prov" autocomplete="off" required v-model="profile.userProfile.province">
                                <option v-for="(prov,key,index) in provinces" :value="key">{{prov}}</option>
                            </select>
                        </div>
                        <div v-if="profile.userProfile.country == 'USA'" class="form-group col-md-6">
                            <label for="edit-state">State</label>
                            <select class="form-control" id="edit-state" name="State" autocomplete="off" required v-model="profile.userProfile.province">
                                <option v-for="(state,key,index) in states" :value="key">{{state}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="edit-postzip">Postal / ZIP Code <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" id="edit-postzip" name="PostZip" autocomplete="off" size="60" maxlength="16" required v-model="profile.userProfile.postalCode">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="phonenumber">Phone Number <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" id="phonenumber" name="Phone" autocomplete="off" size="60" maxlength="14" required v-model="profile.userProfile.phoneNumber" @keyup="formatElements" @change="formatElements">
                        </div>
                    </div>
                </fieldset>

                <fieldset v-if="profile.userProfile.challengeQuestionPool != undefined">
                    <legend><span class="fieldset-legend"><span class="icon-sslmanager text-warning"></span> Update Email / Password</span></legend>
                    <div class="form-row pt-3">
                        <div class="form-group col-12">
                            <label for="edit-email">Email Address <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" id="edit-email" name="email" autocomplete="off" aria-describedby="emailHelp" size="60" maxlength="40" required v-model="profile.userProfile.emailAddress">
                            <p id="emailHelp" class="form-text text-muted">We will send you important emails about the status of your application</p>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="edit-confirm-email">Confirm E-mail Address <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" id="edit-confirm-email" name="confirm_email" autocomplete="off" size="60" maxlength="40" required :value="confirmEmailAddress" @paste.prevent>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="edit-new-password-pass1">Password</label>
                            <password-strength :validation="undefined" :eleId="'edit-password-pass1'" :eleName="'new_password'" :req="false"></password-strength>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="edit-new-password-pass2">Confirm Password</label>
                            <input type="password" class="form-control" id="edit-new-password-pass2" name="confirm_new_password" autocomplete="off" size="60" maxlength="128">
                        </div>
                    </div>

                </fieldset>
                <fieldset v-else>
                    <legend><span class="fieldset-legend"><span class="icon-sslmanager text-warning"></span> Update Email</span></legend>
                    <div class="form-row pt-3">
                        <div class="form-group col-12">
                            <label for="edit-email">Email Address <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" id="edit-email" name="email" autocomplete="off" aria-describedby="emailHelp" size="60" maxlength="40" required v-model="profile.userProfile.emailAddress">
                            <p id="emailHelp" class="form-text text-muted">We will send you important emails about the status of your application</p>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="edit-confirm-email">Confirm E-mail Address <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" id="edit-confirm-email" autocomplete="off" name="confirm_email" size="60" maxlength="40" required :value="confirmEmailAddress">
                        </div>
                    </div>

                </fieldset>


                <profile-challenge-questions v-if="profile.userProfile.challengeQuestionPool != undefined" :profile="profile"></profile-challenge-questions>

                <div class="row">
                    <div class="col-md-6">
                        <a href="/dashboard" class="btn btn-link text-left">Cancel</a>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-block btn-success">Save</button>
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

    span.form-required {
        color: red;
    }
    legend{
        display: block;
        padding: 0 15px;
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
        components: {ProfileChallengeQuestions},
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
        }),

        props: ['submit_status', 'submit_msg', 'errors'],
        methods: {
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/fetch-profile',
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
                        console.log(error);
                    });
            },
            formatElements: function () {
                if(this.profile != undefined && this.profile.userProfile != undefined) {
                    var x = this.profile.userProfile.phoneNumber.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                    this.profile.userProfile.phoneNumber = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
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
            }
        },
        computed: {
            fullName: function(){
                let name = "";
              if(this.profile != ''){
                  if(this.profile.userProfile.firstName != undefined)
                      name = this.profile.userProfile.firstName;
                  if(this.profile.userProfile.middleName != undefined)
                      name += ' ' + this.profile.userProfile.middleName;

                  if(name != '')
                      name += ' ';

                  name += this.profile.userProfile.familyName;
                  name = this.stripSlashes(name);
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
                if(this.profile != ''){
                    let value = this.profile.userProfile.dateOfBirth;
                    let year = value.slice(0, 4);
                    let month = value.slice(4, 6);
                    let day = value.slice(6, 8);

                    let date = new Date(year + "-" + month + "-" + day + "T19:30:00.000Z");
                    let formatted = date.toDateString().split(" ");
                    return formatted[1] + " " +  formatted[2] + ", " + formatted[3];
                }
                return value;
            },

        },
        created() {
            document.title = "StudentAidBC - Update Profile";
        },
        mounted: function () {
            this.fetchData();

            if(this.errors != "")
                this.validationErrors = JSON.parse(this.errors);


        },
        watch: {
        }

    }

</script>
