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

        <form action="#" id="sabc-show-appeal" method="post" accept-charset="UTF-8">

            <fieldset>
                <legend><span class="fieldset-legend">SECTION 1 - STUDENT INFORMATION</span></legend>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="application_number">STUDENT'S APPLICATION NUMBER</label>
                        <div class="input-group mb-3 pr-5">
                            <input type="text" class="form-control" id="application_number" name="application_number_v" maxlength="10" v-model="parsedAppeal.data.application_number_v" disabled>
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
                            <input type="checkbox" class="custom-control-input" id="required_documentation_1" name="required_documentation_1" v-model="parsedAppeal.data.required_documentation_1" disabled>
                            <label class="custom-control-label" for="required_documentation_1"><strong>A letter</strong> explaining the circumstances that meet one or more of the Appeal Criteria (see above).
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="required_documentation_2" name="required_documentation_2" v-model="parsedAppeal.data.required_documentation_2" disabled>
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
                            <input type="text" class="form-control" id="mortgage_rent" name="mortgage_rent_v" maxlength="5" v-model="parsedAppeal.data.mortgage_rent_v" disabled>
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
                            <input type="text" class="form-control" id="second_mortgage" name="second_mortgage_v" maxlength="5" v-model="parsedAppeal.data.second_mortgage_v" disabled>
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
                            <input type="text" class="form-control" id="food" name="food_v" maxlength="5" v-model="parsedAppeal.data.food_v" disabled>
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
                            <input type="text" class="form-control" id="medical" name="medical_v" maxlength="5" v-model="parsedAppeal.data.medical_v" disabled>
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
                            <input type="text" class="form-control" id="dental" name="dental_v" maxlength="5" v-model="parsedAppeal.data.dental_v" disabled>
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
                            <input type="text" class="form-control" id="hydro" name="hydro_v" maxlength="5" v-model="parsedAppeal.data.hydro_v" disabled>
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
                            <input type="text" class="form-control" id="cable" name="cable_v" maxlength="5" v-model="parsedAppeal.data.cable_v" disabled>
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
                            <input type="text" class="form-control" id="water" name="water_v" maxlength="5" v-model="parsedAppeal.data.water_v" disabled>
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
                            <input type="text" class="form-control" id="heat" name="heat_v" maxlength="5" v-model="parsedAppeal.data.heat_v" disabled>
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
                            <input type="text" class="form-control" id="phone" name="phone_v" maxlength="5" v-model="parsedAppeal.data.phone_v" disabled>
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
                            <input type="text" class="form-control" id="daycare" name="daycare_v" maxlength="5" v-model="parsedAppeal.data.daycare_v" disabled>
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
                            <input type="text" class="form-control" id="transportation" name="transportation_v" maxlength="5" v-model="parsedAppeal.data.transportation_v" disabled>
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
                            <input type="text" class="form-control" id="vehicle_payment_1" name="vehicle_payment_1_v" maxlength="5" v-model="parsedAppeal.data.vehicle_payment_1_v" disabled>
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
                            <input type="text" class="form-control" id="vehicle_payment_2" name="vehicle_payment_2_v" maxlength="5" v-model="parsedAppeal.data.vehicle_payment_2_v" disabled>
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
                            <input type="text" class="form-control" id="vehicle_insurance" name="vehicle_insurance_v" maxlength="5" v-model="parsedAppeal.data.vehicle_insurance_v" disabled>
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
                            <input type="text" class="form-control" id="vehicle_upkeep" name="vehicle_upkeep_v" maxlength="5" v-model="parsedAppeal.data.vehicle_upkeep_v" disabled>
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
                            <input type="text" class="form-control" id="gas" name="gas_v" maxlength="5" v-model="parsedAppeal.data.gas_v" disabled>
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
                            <input type="text" class="form-control" id="other*" name="other*_v" maxlength="5" v-model="parsedAppeal.data['other*_v']" disabled>
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
                            <textarea class="form-control" id="itemize_other" name="itemize_other_v" rows="3" disabled>{{parsedAppeal.data.itemize_other_v}}</textarea>
                        </div>
                    </div><!-- /.col-md-4 -->
                </div><!-- /.form-row -->

                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <label for="document_notes">Comments (optional):</label>
                            <textarea class="form-control" id="document_notes" name="document_notes_v" rows="3" disabled>{{parsedAppeal.data.document_notes_v}}</textarea>
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
                                    <input type="text" class="form-control" id="total_monthly_expenses" name="total_monthly_expenses_v" maxlength="5" v-model="parsedAppeal.data.total_monthly_expenses_v" disabled>
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
                                    <input type="text" class="form-control" id="total_monthly_net_income" name="total_monthly_net_income_v" maxlength="5" v-model="parsedAppeal.data.total_monthly_net_income_v" disabled>
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
                            <p>DECLARATION AGREED TO AND SUBMITTED</p>

                            <small><strong>Collection and use of information:</strong> The information included in this form and authorized above is collected under Sections 26(c) and 26(e) of the Freedom of Information and Protection of Privacy Act, and under the authority of the Canada Student Financial Assistance Act, R.S.C. 1994, Chapter C-28 and StudentAid BC. The information provided will be used to determine eligibility for a benefit through StudentAid BC and for statistical and evaluation purposes. If you have any questions about the collection and use of this information, contact the Director, StudentAid BC, Ministry of Post-Secondary Education and Future Skills, PO Box 9173, Stn Prov Govt, Victoria B.C., V8W 9H7, telephone 1-800-561-1818 (toll-free in Canada/U.S.) or (250)-387-6100 from outside North America.</small>

                        </div><!-- /.col-md-4 -->
                    </div><!-- /.form-row -->


            </fieldset>


            <div class="row d-print-none">
                <div class="col-12">
                    <button @click="printForm" type="button" class="btn btn-block btn-success">Print Now</button>
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
        filters: {


        },
        props: ['appeal', 'errors'],
        data: () => ({
            parsedAppeal: ''
        }),
        methods: {

            printForm: function(){
                window.print();
            },
        },
        computed: {


        },
        created() {

        },
        mounted: function () {
            if(this.appeal != "")
                this.parsedAppeal = JSON.parse(this.appeal);

        },
        watch: {

        }

    }

</script>
