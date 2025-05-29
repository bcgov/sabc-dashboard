<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">

            <div v-if="loading == true && load_msg == ''" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == true && loadingError == false && load_msg != ''" class="jumbotron text-center h4">{{load_msg}}</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">

                <form
                @submit="submitFom"
                :action="'/dashboard/appendix-submit-checklist/APPENDIX2/' + program_year + '/' + app_id + '/' + document_guid"
                method="post" id="sabc-form-builder" accept-charset="UTF-8">
                <input type="hidden" name="_token" :value="csrf">
                <input type="hidden" name="hpot" value="">
                <input type="hidden" name="decRequired" :value="parsedDecl.inkSignatureRequired">
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

                <fieldset>
                    <div class="form-row pt-3">
                        <div class="form-group col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="verification_confirmation" name="verification_confirmation" required>
                                <label class="form-check-label" for="verification_confirmation">
                                    I certify that all information in my <strong>appendix 2</strong> is complete and accurate, and I understand that it is subject to verification and audit.
                                    <span class="form-required" title="This field is required.">*</span>
                                </label>
                            </div>

                            <em>You can view/print a copy of your appendix 2 from your dashboard once the appendix 2 has been submitted.</em>
                        </div>
                    </div>
                </fieldset>

                <template v-if="parsedDecl.inkSignatureRequired == 'E'">
                    <fieldset>
                        <div class="form-row pt-3">
                            <div class="form-group col-12">
                                <!-- AGREEMENT GOES HERE -->
                                <div class="p-4 mb-5" style="overflow-y:scroll; height:200px;" v-html="parsedFields.field_consent_req_agreement_text.value"></div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="declaration_confirmation" name="declaration_confirmation" required>
                                    <label class="form-check-label" for="declaration_confirmation">
                                        I agree to the terms and conditions of the StudentAid BC declaration form.
                                        <span class="form-required" title="This field is required.">*</span>
                                    </label>
                                </div>

                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="form-row pt-3">
                            <div class="form-group col-12">
                                <!-- AGREEMENT 2 GOES HERE -->
                                <div class="p-4 mb-5" style="overflow-y:scroll; height:200px;" v-html="parsedFields.field_consent2_req_agreement.value"></div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="declaration_cra_confirmation" name="declaration_cra_confirmation" required>
                                    <label class="form-check-label" for="declaration_cra_confirmation">
                                        I agree to the Canada Revenue Agency (CRA) consent form.
                                        <span class="form-required" title="This field is required.">*</span>
                                    </label>
                                </div>

                            </div>
                        </div>
                    </fieldset>
                </template>

                <fieldset v-else>
                    <div class="form-row pt-3">
                        <div class="form-group col-12">
                            <!-- AGREEMENT 3 GOES HERE -->
                            <div v-if="parsedFields.field_sig_req_agreement_text != undefined" class="p-4 mb-5" style="overflow-y:scroll; height:200px;" v-html="parsedFields.field_sig_req_agreement_text.value"></div>
                            <div v-if="parsedFields.field_new_dec_req_agreement_text != undefined" class="p-4 mb-5" style="overflow-y:scroll; height:200px;" v-html="parsedFields.field_new_dec_req_agreement_text.value"></div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="declaration_confirmation" name="declaration_confirmation" required>
                                <label class="form-check-label" for="declaration_confirmation">
                                    I agree to the terms and conditions of the StudentAid BC declaration form and to the Canada Revenue Agency (CRA) consent form.
                                    <span class="form-required" title="This field is required.">*</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset v-if="parsedDecl.inkSignatureRequired == 'Y' || parsedDecl.inkSignatureRequired == 'X'">
                    <div class="form-row pt-3">
                        <div class="form-group col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="consent_confirmation" name="consent_confirmation" required>
                                <label class="form-check-label" for="consent_confirmation">
                                    I will print, sign and mail the Spouse Consent and Declaration page to StudentAid BC. I understand that my appendix 2 will <strong>NOT</strong> be processed unless I print, sign and mail my declaration page.
                                    <span class="form-required" title="This field is required.">*</span>
                                </label>
                            </div>

                        </div>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-md-6">
                        <a :href="'/dashboard/student-loans/check-appendix2-status/' + app_id" class="btn btn-link text-left">Cancel</a>
                    </div>
                    <div class="col-md-6">
                        <button v-if="submitting == true" type="button" class="btn btn-block btn-success disabled">
                            <small class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </small>
                        </button>
                        <template v-else>
                            <button type="submit" class="btn btn-block btn-success">Submit my Appendix 2</button>
                        </template>
                    </div>
                </div>

            </form>

            </template>
        </div>

    </div><!-- /.row -->
</template>
<style scoped>
    .alert-p{
        min-height: auto;
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

    export default {
        filters: {
        },

        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            validationErrors: '',
            maintenanceMode: false,

            parsedDecl: '',
            parsedList: '',
            parsedFields: {},

            submitting: false,

        }),

        props: ['program_year', 'app_id', 'document_guid', 'declaration', 'submit_status', 'submit_msg', 'errors', 'checklist', 'load_status', 'load_msg'],
        methods: {
            submitFom: function(){
                this.submitting = true;
            },
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },
        },
        created() {
            document.title = "StudentAidBC - Appendix 2 Submit Agreement";
        },
        mounted: function () {
            document.title = "StudentAidBC - Appendix 2 Submit Agreement";

            if(this.errors != "")
                this.validationErrors = JSON.parse(this.errors);
            this.parsedDecl = JSON.parse(this.declaration);
            this.parsedList = JSON.parse(this.checklist);
            for(var i=0; i<this.parsedList.fields.length; i++){
                this.parsedFields[this.parsedList.fields[i].field_id] = {label: this.parsedList.fields[i].field_label, value: this.parsedList.fields[i].field_value};
            }
            this.loading = false;

            document.title = "StudentAidBC - Appendix 2 Submit Agreement";

        },
        watch: {
        }

    }

</script>
