<template>

    <div class="printing-area">
        <h4>WHAT IS AN APPEAL?</h4>
        <p>An appeal is the process by which you have the opportunity to request a reconsideration of your assessed award. If you have had an exceptional circumstance that meets one or more of the criteria listed below and you have not received the maximum amount of funding for the application study period, you may submit an appeal request. Once evaluated, the appeal request may result in a change to your StudentAid BC award.</p>
        <br/>

        <h4>APPEAL CRITERIA</h4>
        <p>You may submit an appeal request if one or more of the following criteria caused you to not meet the required submission deadline to receive funding before your study period end date.</p>
        <ul>
            <li>Medical illness or injury</li>
            <li>Family emergency (e.g. death, injury)</li>
            <li>Natural disaster</li>
            <li>Layoff, strike, lockout, or other reduction in earnings beyond your control</li>
            <li>Other exceptional circumstances</li>
        </ul>
        <br/>
        <h4>APPEAL INSTRUCTIONS</h4>
        <ol>
            <li>Talk to a Financial Aid Officer at your school, they can help you with the appeal process. If you are unable to contact a Financial
                Aid Officer, contact <a href="https://studentaidbc.ca/contact-information" target="_blank">StudentAid BC</a>.</li>
            <li>Review the Appeal Criteria.</li>
            <li>Complete Sections 1-5.</li>
            <li>Upload your completed Appeal Request Form and all required documentation to your StudentAid BC Dashboard</li>
        </ol>
        <p class="text-center"><small>Refer to the StudentAid BC <a href="https://studentaidbc.ca/institution-officials" target="_blank">Policy Manual</a> for more information on appeals.</small></p>

        <form action="/dashboard/appeal-forms/create-appeal" method="post" id="sabc-create-appeal" accept-charset="UTF-8">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="hpot" value="">
            <input type="hidden" name="form_name" value="APPEAL REQUEST FOR FUNDING AFTER END DATE">
            <input type="hidden" name="py" value="2021/2022">
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
                        </template>                    </div>

                </div><!-- /.block -->
            </div>

            <fieldset>
                <legend><span class="fieldset-legend">SECTION 1 - STUDENT INFORMATION</span></legend>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="application_number">STUDENT'S APPLICATION NUMBER</label>
                        <div class="input-group mb-3 pr-5">
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="application_number" name="application_number_v" maxlength="10">
                            <input type="hidden" class="form-control" name="application_number_l" value="STUDENT'S APPLICATION NUMBER">
                        </div>
                    </div><!-- /.col-md-4 -->
                </div>

            </fieldset>

            <fieldset>
                <legend><span class="fieldset-legend">SECTION 2 - REQUIRED DOCUMENTATION</span></legend>
                <div class="form-row pt-3">
                    <div class="form-group col-12">
                        <p>You must submit all of the following documentation to your StudentAid BC Dashboard to support your appeal request:</p>
                        <div class="custom-control custom-checkbox">
                            <input v-model="requiredDocumentation1" type="checkbox" class="custom-control-input" id="required_documentation_1" name="required_documentation_1">
                            <label class="custom-control-label" for="required_documentation_1"><strong>A letter</strong> explaining the circumstances that meet one or more of the Appeal Criteria (see above).
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input v-model="requiredDocumentation2" type="checkbox" class="custom-control-input" id="required_documentation_2" name="required_documentation_2">
                            <label class="custom-control-label" for="required_documentation_2"><strong>All relevant supporting documentation.</strong>
                            </label>
                        </div>
                        <p class="text-danger">YOUR ASSESSMENT WILL BE DELAYED OR DENIED IF YOU DO NOT SUBMIT ALL REQUIRED DOCUMENTATION.</p>

                    </div>
                </div>

            </fieldset>


            <fieldset>
                <legend><span class="fieldset-legend">SECTION 3 - MONTHLY EXPENSES</span></legend>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="mortgage_rent">MORTGAGE/RENT</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="mortgage_rent" name="mortgage_rent_v" maxlength="5">
                            <input type="hidden" class="form-control" name="mortgage_rent_l" value="MORTGAGE/RENT">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="second_mortgage">SECOND MORTGAGE</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="second_mortgage" name="second_mortgage_v" maxlength="5">
                            <input type="hidden" class="form-control" name="second_mortgage_l" value="SECOND MORTGAGE">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="food">FOOD</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="food" name="food_v" maxlength="5">
                            <input type="hidden" class="form-control" name="food_l" value="FOOD">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                </div><!-- /.form-row -->

                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="medical">MEDICAL</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="medical" name="medical_v" maxlength="5">
                            <input type="hidden" class="form-control" name="medical_l" value="MEDICAL">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="dental">DENTAL</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="dental" name="dental_v" maxlength="5">
                            <input type="hidden" class="form-control" name="dental_l" value="DENTAL">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="hydro">HYDRO</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="hydro" name="hydro_v" maxlength="5">
                            <input type="hidden" class="form-control" name="hydro_l" value="HYDRO">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                </div><!-- /.form-row -->

                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="cable">CABLE</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="cable" name="cable_v" maxlength="5">
                            <input type="hidden" class="form-control" name="cable_l" value="CABLE">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="water">WATER</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="water" name="water_v" maxlength="5">
                            <input type="hidden" class="form-control" name="water_l" value="WATER">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="heat">HEAT</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="heat" name="heat_v" maxlength="5">
                            <input type="hidden" class="form-control" name="heat_l" value="HEAT">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                </div><!-- /.form-row -->


                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="phone">PHONE</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="phone" name="phone_v" maxlength="5">
                            <input type="hidden" class="form-control" name="phone_l" value="PHONE">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="daycare">DAYCARE</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="daycare" name="daycare_v" maxlength="5">
                            <input type="hidden" class="form-control" name="daycare_l" value="DAYCARE">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="transportation">TRANSPORTATION</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="transportation" name="transportation_v" maxlength="5">
                            <input type="hidden" class="form-control" name="transportation_l" value="TRANSPORTATION">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                </div><!-- /.form-row -->


                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="vehicle_payment_1">VEHICLE PAYMENT 1</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="vehicle_payment_1" name="vehicle_payment_1_v" maxlength="5">
                            <input type="hidden" class="form-control" name="vehicle_payment_1_l" value="VEHICLE PAYMENT 1">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="vehicle_payment_2">VEHICLE PAYMENT 2</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="vehicle_payment_2" name="vehicle_payment_2_v" maxlength="5">
                            <input type="hidden" class="form-control" name="vehicle_payment_2_l" value="VEHICLE PAYMENT 2">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="vehicle_insurance">VEHICLE INSURANCE</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="vehicle_insurance" name="vehicle_insurance_v" maxlength="5">
                            <input type="hidden" class="form-control" name="vehicle_insurance_l" value="VEHICLE INSURANCE">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                </div><!-- /.form-row -->


                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="vehicle_upkeep">VEHICLE UPKEEP</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="vehicle_upkeep" name="vehicle_upkeep_v" maxlength="5">
                            <input type="hidden" class="form-control" name="vehicle_upkeep_l" value="VEHICLE UPKEEP">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="gas">GAS</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="gas" name="gas_v" maxlength="5">
                            <input type="hidden" class="form-control" name="gas_l" value="GAS">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-4 mb-3">
                        <label for="other*">OTHER*</label>
                        <div class="input-group mb-3 pr-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input @keypress="isNumber($event)" type="text" class="form-control" id="other*" name="other*_v" maxlength="5">
                            <input type="hidden" class="form-control" name="other*_l" value="OTHER*">
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </div><!-- /.col-md-4 -->
                </div><!-- /.form-row -->


                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <label for="itemize_other">*Itemize other expenses below:</label>
                            <textarea class="form-control" id="itemize_other" name="itemize_other_v" rows="3" v-model="itemizeOther" @keyup="cmntCount('itemizeOther')" maxlength="1500"></textarea>
                            <input type="hidden" class="form-control" name="itemize_other_l" value="*Itemize other expenses below:">
                            <div>{{cmntLimit - itemizeOther.length}} characters remaining</div>

                        </div>
                    </div><!-- /.col-md-4 -->
                </div><!-- /.form-row -->

                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <label for="document_notes">Comments (optional):</label>
                            <textarea class="form-control" id="document_notes" name="document_notes_v" rows="3" v-model="cmnt" @keyup="cmntCount('cmnt')" maxlength="1500"></textarea>
                            <input type="hidden" class="form-control" name="document_notes_l" value="Comments (optional):">
                            <div>{{cmntLimit - cmnt.length}} characters remaining</div>

                        </div>
                    </div><!-- /.col-md-4 -->
                </div><!-- /.form-row -->
            </fieldset>


            <fieldset>
                <legend><span class="fieldset-legend">SECTION 4 - TOTAL EXPENSES</span></legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 mb-3 pr-5">
                                <label for="total_monthly_expenses">TOTAL MONTHLY EXPENSES</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input @keypress="isNumber($event)" type="text" class="form-control" id="total_monthly_expenses" name="total_monthly_expenses_v" maxlength="5">
                                    <input type="hidden" class="form-control" name="total_monthly_expenses_l" value="TOTAL MONTHLY EXPENSES">
                                    <div class="input-group-append">
                                        <span class="input-group-text">.00</span>
                                    </div>
                                </div>
                            </div><!-- /.col-md-4 -->
                            <div class="col-12 mb-3">
                                <label for="total_monthly_net_income">TOTAL MONTHLY NET INCOME</label>
                                <div class="input-group mb-3 pr-5">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input @keypress="isNumber($event)" type="text" class="form-control" id="total_monthly_net_income" name="total_monthly_net_income_v" maxlength="5">
                                    <input type="hidden" class="form-control" name="total_monthly_net_income_l" value="TOTAL MONTHLY NET INCOME">
                                    <div class="input-group-append">
                                        <span class="input-group-text">.00</span>
                                    </div>
                                </div>
                            </div><!-- /.col-md-4 -->
                        </div><!-- /.form-row -->
                    </div>
                    <div class="col-md-6">
                        <p class="text-center">All information is subject to verification and could result in an overaward if information is misreported.</p>
                    </div>
                </div>

            </fieldset>


            <fieldset>
                <legend><span class="fieldset-legend">SECTION 5 - DECLARATION</span></legend>
                    <div class="form-row">
                        <div class="col-12 mb-3">
                            <p>By submitting this request for an appeal, I understand that:</p>
                            <ul>
                                <li>All terms agreed to on my application will remain in force.</li>
                                <li>StudentAid BC may consider information from prior applications in my appeal request.</li>
                            </ul>

                            <div class="form-check mb-2 mr-sm-2">
                                <input v-model="agree" class="form-check-input" type="checkbox" id="declaration_agree" name="declaration_agree">
                                <label class="form-check-label" for="declaration_agree">I certify that information provided with this request is accurate and correct.</label>
                            </div>

                            <small><strong>Collection and use of information:</strong> The information included in this form and authorized above is collected under Sections 26(c) and 26(e) of the Freedom of Information and Protection of Privacy Act, and under the authority of the Canada Student Financial Assistance Act, R.S.C. 1994, Chapter C-28 and StudentAid BC. The information provided will be used to determine eligibility for a benefit through StudentAid BC and for statistical and evaluation purposes. If you have any questions about the collection and use of this information, contact the Director, StudentAid BC, Ministry of Post-Secondary Education and Future Skills, PO Box 9173, Stn Prov Govt, Victoria B.C., V8W 9H7, telephone 1-800-561-1818 (toll-free in Canada/U.S.) or (250)-387-6100 from outside North America.</small>

                        </div><!-- /.col-md-4 -->
                    </div><!-- /.form-row -->


            </fieldset>


            <div class="row d-print-none">
                <div class="col-12">
                    <button v-if="submitting == true" type="button" class="btn btn-block btn-success disabled">
                        <small class="spinner-border spinner-border-sm" role="status">
                            <span class="sr-only">Loading...</span>
                        </small>
                    </button>
                    <template v-else>
                        <button @click="submitForm" v-if="printReady == false" type="button" class="btn btn-block btn-success" :class="(agree === false || requiredDocumentation1 === false || requiredDocumentation2 === false) ? 'disabled' : ''" :disabled="agree === false || requiredDocumentation1 === false || requiredDocumentation2 === false">Submit & Print</button>
                        <button @click="printForm" v-if="printReady == true" type="button" class="btn btn-block btn-success">Print Now</button>
                    </template>

                </div>
            </div>

        </form>

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
        margin-bottom: 15px;
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
        props: ['submit_status', 'submit_msg', 'errors'],
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            validationErrors: '',
            agree: false,
            requiredDocumentation1: false,
            requiredDocumentation2: false,
            submitting: false,
            printReady: false,

            cmntLimit: 1500,
            cmnt: "",
            itemizeOther: "",

        }),
        methods: {
            cmntCount: function (id) {
                if(id == 'cmnt'){
                    if(this.cmnt.length > this.cmntLimit){
                        this.cmnt = this.cmnt.substr(0, this.cmntLimit);
                    }
                }
                if(id == 'itemizeOther'){
                    if(this.itemizeOther.length > this.cmntLimit){
                        this.itemizeOther = this.itemizeOther.substr(0, this.cmntLimit);
                    }
                }

            },
            submitForm: function(){
                $(document).ready(function(){
                    $("#sabc-create-appeal :input").prop("disabled", true);
                });

                this.submitting = true;
                var vm = this;
                let myForm = document.getElementById('sabc-create-appeal');
                let formData = new FormData(myForm);
                formData.append('_token', this.csrf);

                axios({
                    url: '/dashboard/appeal-forms/create-appeal',
                    data: formData,
                    method: 'post',
                    headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    // headers: {'Accept': 'application/json'}
                })
                    .then(function (response) {
                        vm.submitting = false;
                        vm.printReady = true;
                        $("#sabc-create-appeal button").prop("disabled", false);

                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            printForm: function(){
                window.print();
            },

            fetchData: function(){

            },
            isNumber: function(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                // console.log(charCode);
                if ((charCode > 31 && (charCode < 48 || charCode > 57))) {
                    evt.preventDefault();
                } else {
                    return true;
                }
            }
        },
        computed: {


        },
        created() {

        },
        mounted: function () {
            if(this.errors != "")
                this.validationErrors = JSON.parse(this.errors);

        },
        watch: {

        }

    }

</script>
