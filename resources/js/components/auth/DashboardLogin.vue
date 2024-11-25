<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 mt-3">
                <dashboard-alerts @disable="disablePage" pagesTarget="login_register_pages"></dashboard-alerts>
                <p v-if="passwordreset == true" class="alert alert-success">Password Reset was successful!</p>

                <h2>Student Accounts &amp; Application</h2>
                <p>Please select the option below that best applies to you.</p>
                <div v-if="maintenanceMode === false" class="card mb-3">
                    <div class="card-header text-dark bg-light">
                        <span class="text-primary text-left p-0">Full-Time Student</span>
                    </div>
                    <div class="collapse show">
                        <div class="card-body">

                            <div class="form-group row mb-0">
                                <div class="col-12">
                                    <p>A student who is enrolled in a minimum of 60 percent of a full course load (40
                                        percent for students with a permanent disability, or a persistent or prolonged
                                        disability) at a designated public or private post-secondary institution.</p>

                                    <div class="accordion mb-2" id="accordionA">
                                        <div class="card">
                                            <a :href="'https://id' + BcscEnv[0] + '.gov.bc.ca/login/saml2sso?TARGET=urn:aved:sabc:' + BcscEnv[1]"
                                               class="btn btn-primary btn-block text-center p-3">Log in with a BC
                                                Services Card account</a>
                                        </div>
                                    </div>


                                    <div class="accordion mb-2" id="accordionC">
                                        <div class="card">
                                            <a href="https://id.gov.bc.ca/account/"
                                               class="btn btn-link btn-block text-center p-3" target="_blank">Learn how
                                                to use a BC Services Card account to log in</a>
                                        </div>
                                    </div>

                                    <div class="accordion mb-2" id="accordionB">
                                        <div class="card">
                                            <div class="card-header p-2" id="headingB">
                                                <h2 class="mb-0">
                                                    <button @click="toggleCollapse('collapseB')"
                                                            class="btn btn-link btn-block text-center" type="button">
                                                        I’m not eligible for a BC Services Card Account. What can I do?
                                                    </button>
                                                </h2>
                                            </div>

                                            <div id="collapseB" class="collapse" aria-labelledby="headingB"
                                                 data-parent="#accordionB">
                                                <div class="card-body">
                                                    <p>Using a BC Services Card account is the easiest, most secure way
                                                        to apply for student financial aid. However, we understand not
                                                        everyone is eligible for the BC Services Card account.<br/>To
                                                        inquire about alternate login options, please email a request to
                                                        <a href="mailto:StudentAidBC@gov.bc.ca" target="_blank">StudentAidBC@gov.bc.ca</a>
                                                        and provide a detailed explanation why you are not eligible for
                                                        a BC Services Card account. StudentAid BC will review your
                                                        request and provide further instructions via email.</p>
                                                    <label class="mb-3 mt-3">Log in with StudentAid BC User ID</label>
                                                    <form method="POST" action="/dashboard/login">
                                                        <input type="hidden" name="_token" :value="csrf">
                                                        <input type="hidden" name="trap" value="">
                                                        <div v-if="errors != ''" class="row">
                                                            <div class="col-12">
                                                                <div class="alert alert-contextual alert-danger"
                                                                     role="alert">
                                                                    <svg class="alert-icon icon-lg colorRed100"
                                                                         aria-hidden="true" focusable="false">
                                                                        <use
                                                                            xlink:href="/dashboard/assets/sprite/icons.svg#alert"></use>
                                                                    </svg>
                                                                    <template v-for="(error, i) in validationErrors"><p
                                                                        v-for="e in error" v-html="e"></p></template>

                                                                </div><!-- /.alert -->
                                                            </div><!-- /.block -->
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="user_id" class="">User ID <span
                                                                class="form-required"
                                                                title="This field is required.">*</span></label>
                                                            <input id="user_id" type="text" class="form-control"
                                                                   :class="(errors !== '' && validationErrors.username != undefined) ? 'is-invalid' : ''"
                                                                   name="user_id" :value="userid" required
                                                                   autocomplete="user_id" autofocus>
                                                            <span
                                                                v-if="errors !== '' && validationErrors.username != undefined"
                                                                class="invalid-feedback" role="alert">
                                            <strong>{{ validationErrors.username[0] }}</strong><br>
                                        </span>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="password">Password <span class="form-required"
                                                                                                 title="This field is required.">*</span></label>
                                                            <input id="password" type="password" class="form-control"
                                                                   :class="(errors !== '' && validationErrors.password != undefined) ? 'is-invalid' : ''"
                                                                   name="password" required
                                                                   autocomplete="current-password">

                                                            <span
                                                                v-if="errors !== '' && validationErrors.password != undefined"
                                                                class="invalid-feedback" role="alert">
                                            <strong>{{ validationErrors.password[0] }}</strong><br>
                                        </span>
                                                        </div>

                                                        <div class="form-group row mb-0">
                                                            <div class="col-12 text-center">
                                                                <button type="submit"
                                                                        class="btn btn-primary btn-block mb-3">Login
                                                                    with StudentAid BC User ID
                                                                </button>

                                                                <a v-if="accountLocked === false" class="btn btn-link"
                                                                   href="/dashboard/forgot/password">Forgot your
                                                                    StudentAid BC User ID/Password?</a>
                                                                <br/>
                                                                <a v-if="accountLocked === false" class="btn btn-link"
                                                                   href="/dashboard/create">Register</a>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div v-if="maintenanceMode === false" class="card mb-3">
                    <div class="card-header text-dark bg-light">
                        <span class="text-primary text-left p-0">Part-Time Student</span>
                    </div>
                    <div class="collapse show">
                        <div class="card-body">

                            <div class="form-group row mb-0">
                                <div class="col-12">
                                    <p>A student who is enrolled in at least 20 to 59 percent of a full course load at a
                                        designated public or private post-secondary institution.</p>

                                    <div class="accordion mb-2" id="accordionA2">
                                        <div class="card">
                                            <a href="https://sims.studentaidbc.ca/student/login"
                                               class="btn btn-primary btn-block text-center p-3">Part-Time Students -
                                                Log in / Create Account</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div v-if="maintenanceMode === false" class="p-4">
                    <p>
                        <strong>Minimum system requirements.</strong>
                        <br/>
                        You can access our full-time student account using a desktop computer or mobile device with a
                        current web browser, including Google Chrome, Safari, Firefox, or Edge. However, to complete the
                        full-time application, you must use a desktop computer with the latest version of Adobe Reader.
                        <br/>
                        The student account for part-time students works best on a desktop computer with up-to-date web
                        browsers, including Google Chrome, Safari, Firefox, or Edge.
                    </p>
                </div>
                <div v-if="maintenanceMode === false" class="p-4">
                    <small>Collection and Use of Information. The information included in this form is collected under
                        ss. 26(c) and 26(e) of the Freedom of Information and Protection of Privacy Act, R.S.B.C. 1996,
                        c. 165. Upon pressing the “Submit” button you are confirming that you have reviewed this
                        statement. The information you provide will be used in confirming your identity. If you have any
                        questions about the collection and use of this information, contact the Executive Director,
                        StudentAid BC, Ministry of Post-Secondary Education and Future Skills, PO Box 9173, Stn Prov
                        Govt, Victoria BC, V8W 9H7, telephone 1-800-561-1818 (toll-free in Canada/U.S.) or
                        +1-778-309-4621 from outside North America.</small>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
button.btn-link {
    box-shadow: none;
}

span.form-required {
    color: red;
}

</style>
<script>
//import axios from 'axios

export default {
    filters: {},
    props: ['errors', 'userid', 'env', 'passwordreset'],
    data: () => ({
        validationErrors: '',
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        maintenanceMode: false,
    }),
    methods: {
        toggleCollapse: function (id) {
            $('#' + id).collapse('toggle');
        },
        disablePage: function (e) {
            if (e === true)
                this.maintenanceMode = true;
        }
    },
    computed: {
        BcscEnv: function () {
            if (this.env == 'prod') {
                return ['', 'prd'];
            }

            return ['test', this.env];
        },
        accountLocked: function () {
            //User ID and Password combination entered is not valid. Your account has been temporarily locked. Please try again later or click "Forgot ID/Password" to attempt to recover your account information.
            if (this.errors !== '' && this.validationErrors.username != undefined) {
                if (this.validationErrors.username[0].indexOf('account has been temporarily locked') > 0) {
                    return true;
                }
            }
            if (this.errors !== '' && this.validationErrors.name != undefined) {
                if (this.validationErrors.name[0].indexOf('many login attempts') > 0) {
                    return true;
                }
            }

            return false;
        }
    },
    created() {
        if (this.errors != "") {
            this.validationErrors = JSON.parse(this.errors);
        }
        if (this.validationErrors != '' && this.validationErrors.username[0].indexOf('account has been temporarily locked') > 0) {
            this.validationErrors.username[0] = "Access to this account has been locked for 15 minutes. Please try again later.";
        }

    },
    mounted: function () {
        document.title = "StudentAidBC - Dashboard Login";
        let vm = this;
        setTimeout(function () {
            if (vm.errors != "") {
                vm.toggleCollapse();
            }
        }, 1500);

    },
    watch: {}

}

</script>
