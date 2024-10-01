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
<div class="printing-area">
    <h4>WHAT IS AN APPEAL?</h4>
    <p>An appeal is the process by which you have the opportunity to request a reconsideration of your assessed award.
        If you have had an exceptional circumstance that meets one or more of the criteria listed below and you have not
        received the maximum amount of funding for the application study period, you may submit an appeal request. Once
        evaluated, the appeal request may result in a change to your StudentAid BC award.</p> <br>
    <h4>APPEAL CRITERIA</h4>
    <p>You may submit an appeal request if one or more of the following criteria caused you to not meet the required
        submission deadline to receive funding before your study period end date.</p>
    <ul>
        <li>Medical illness or injury</li>
        <li>Family emergency (e.g. death, injury)</li>
        <li>Natural disaster</li>
        <li>Layoff, strike, lockout, or other reduction in earnings beyond your control</li>
        <li>Other exceptional circumstances</li>
    </ul>
    <br>
    <h4>APPEAL INSTRUCTIONS</h4>
    <ol>
        <li>Talk to a Financial Aid Officer at your school, they can help you with the appeal process. If you are unable
            to contact a Financial
            Aid Officer, contact <a href="https://studentaidbc.ca/contact-information" target="_blank">StudentAid BC</a>.
        </li>
        <li>Review the Appeal Criteria.</li>
        <li>Complete Sections 1-5.</li>
        <li>Upload your completed Appeal Request Form and all required documentation to your StudentAid BC Dashboard
        </li>
    </ol>
    <p class="text-center"><small>Refer to the StudentAid BC <a href="https://studentaidbc.ca/institution-officials"
                                                                target="_blank">Policy Manual</a> for more information
        on appeals.</small></p>
    <form action="/dashboard/appeal-forms/create-appeal" method="post" id="sabc-create-appeal" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="">
        <input type="hidden" name="hpot" value="">
        <input type="hidden" name="form_name" value="APPEAL REQUEST FOR FUNDING AFTER END DATE">
        <input type="hidden" name="py" value="2021/2022">
        <!---->
        <fieldset>
            <legend><span class="fieldset-legend">SECTION 1 - STUDENT INFORMATION</span></legend>
            <div class="form-row">
                <div class="col-md-12 mb-3"><label for="application_number">STUDENT'S APPLICATION NUMBER</label>
                    <div class="input-group mb-3">
                        <input type="text" id="application_number" name="application_number___x23y_v" maxlength="10"
                               class="form-control">
                        <input type="hidden" name="application_number___x23y_l" value="STUDENT'S APPLICATION NUMBER"
                               class="form-control"></div>
                </div>
                <div class="col-md-4 mb-3"><label for="student_last_name">STUDENT'S LAST NAME</label>
                    <div class="input-group mb-3">
                        <input type="text" id="student_last_name" name="student_last_name___x23y_v" class="form-control">
                        <input type="hidden" name="student_last_name___x23y_l" value="STUDENT'S LAST NAME"
                               class="form-control"></div>
                </div>
                <div class="col-md-4 mb-3"><label for="student_first_name">STUDENT'S FIRST NAME <span class="form-required">*</span></label>
                    <div class="input-group mb-3">
                        <input type="text" id="student_first_name" name="student_first_name___x23y_v" class="form-control" required>
                        <input type="hidden" name="student_first_name___x23y_l" value="STUDENT'S FIRST NAME"
                               class="form-control"></div>
                </div>
                <div class="col-md-4 mb-3"><label for="middle_initial">MIDDLE INITIAL</label>
                    <div class="input-group mb-3">
                        <input type="text" id="middle_initial" name="middle_initial___x23y_v" class="form-control">
                        <input type="hidden" name="middle_initial___x23y_l" value="STUDENT'S LAST NAME"
                               class="form-control"></div>
                </div>

            </div>
        </fieldset>
        <fieldset>
            <legend><span class="fieldset-legend">SECTION 2 - REQUIRED DOCUMENTATION</span></legend>
            <div class="form-row pt-3">
                <div class="form-group col-12">
                    <p>You must submit all of the following documentation to your StudentAid BC Dashboard to support
                        your appeal request:</p>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="required_documentation_1" name="required_documentation_1___x23y_checkbox"
                               class="custom-control-input">
                        <label for="required_documentation_1" class="custom-control-label"><strong>A letter</strong> explaining the circumstances that meet one or more of the Appeal
                            Criteria (see above).
                        </label></div>
                    <div class="custom-control custom-checkbox"><input type="checkbox" id="required_documentation_2"
                                                                       name="required_documentation_2___x23y_checkbox"
                                                                       class="custom-control-input"> <label
                        for="required_documentation_2" class="custom-control-label"><strong>All relevant supporting
                        documentation.</strong></label></div>
                    <p class="text-danger">YOUR ASSESSMENT WILL BE DELAYED OR DENIED IF YOU DO NOT SUBMIT ALL REQUIRED
                        DOCUMENTATION.</p>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend><span class="fieldset-legend">SECTION 3 - MONTHLY EXPENSES</span></legend>
            <div class="form-row">
                <div class="col-md-4 mb-3"><label for="mortgage_rent">MORTGAGE/RENT</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="mortgage_rent" name="mortgage_rent___x23y_v" maxlength="5" class="form-control">
                        <input type="hidden" name="mortgage_rent___x23y_l" value="MORTGAGE/RENT" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="second_mortgage">SECOND MORTGAGE</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="second_mortgage" name="second_mortgage___x23y_v" maxlength="5"
                               class="form-control"> <input type="hidden" name="second_mortgage___x23y_l"
                                                            value="SECOND MORTGAGE" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="food">FOOD</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="food" name="food___x23y_v" maxlength="5" class="form-control"> <input
                        type="hidden" name="food___x23y_l" value="FOOD" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3"><label for="medical">MEDICAL</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="medical" name="medical___x23y_v" maxlength="5" class="form-control"> <input
                        type="hidden" name="medical___x23y_l" value="MEDICAL" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="dental">DENTAL</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="dental" name="dental___x23y_v" maxlength="5" class="form-control"> <input
                        type="hidden" name="dental___x23y_l" value="DENTAL" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="hydro">HYDRO</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="hydro" name="hydro___x23y_v" maxlength="5" class="form-control"> <input
                        type="hidden" name="hydro___x23y_l" value="HYDRO" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3"><label for="cable">CABLE</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="cable" name="cable___x23y_v" maxlength="5" class="form-control">
                        <input type="hidden" name="cable___x23y_l" value="CABLE" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="water">WATER</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="water" name="water___x23y_v" maxlength="5" class="form-control">
                        <input type="hidden" name="water___x23y_l" value="WATER" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="heat">HEAT</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="heat" name="heat___x23y_v" maxlength="5" class="form-control">
                        <input type="hidden" name="heat___x23y_l" value="HEAT" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3"><label for="phone">PHONE</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="phone" name="phone___x23y_v" maxlength="5" class="form-control">
                        <input type="hidden" name="phone___x23y_l" value="PHONE" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="daycare">DAYCARE</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="daycare" name="daycare___x23y_v" maxlength="5" class="form-control">
                        <input type="hidden" name="daycare___x23y_l" value="DAYCARE" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="transportation">TRANSPORTATION</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="transportation" name="transportation___x23y_v" maxlength="5"
                               class="form-control">
                        <input type="hidden" name="transportation___x23y_l" value="TRANSPORTATION"
                                                            class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3"><label for="vehicle_payment_1">VEHICLE PAYMENT 1</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="vehicle_payment_1" name="vehicle_payment_1___x23y_v" maxlength="5"
                               class="form-control">
                        <input type="hidden" name="vehicle_payment_1___x23y_l"
                                                            value="VEHICLE PAYMENT 1" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="vehicle_payment_2">VEHICLE PAYMENT 2</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="vehicle_payment_2" name="vehicle_payment_2___x23y_v" maxlength="5"
                               class="form-control">
                        <input type="hidden" name="vehicle_payment_2___x23y_l"
                                                            value="VEHICLE PAYMENT 2" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="vehicle_insurance">VEHICLE INSURANCE</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="vehicle_insurance" name="vehicle_insurance___x23y_v" maxlength="5"
                               class="form-control">
                        <input type="hidden" name="vehicle_insurance___x23y_l"
                                                            value="VEHICLE INSURANCE" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3"><label for="vehicle_upkeep">VEHICLE UPKEEP</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="vehicle_upkeep" name="vehicle_upkeep___x23y_v" maxlength="5"
                               class="form-control"> <input type="hidden" name="vehicle_upkeep___x23y_l" value="VEHICLE UPKEEP"
                                                            class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="gas">GAS</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="gas" name="gas___x23y_v" maxlength="5" class="form-control"> <input
                        type="hidden" name="gas___x23y_l" value="GAS" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3"><label for="other*">OTHER*</label>
                    <div class="input-group mb-3 pr-5">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" id="other*" name="other*___x23y_v" maxlength="5" class="form-control"> <input
                        type="hidden" name="other*___x23y_l" value="OTHER*" class="form-control">
                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="itemize_other">*Itemize other expenses below:</label>
                        <textarea
                        id="itemize_other" name="itemize_other___x23y_v" rows="3" maxlength="1500"
                        class="form-control"></textarea> <input type="hidden" name="itemize_other___x23y_l"
                                                                value="*Itemize other expenses below:"
                                                                class="form-control">
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="document_notes">Comments (optional):</label>
                        <textarea
                        id="document_notes" name="document_notes___x23y_v" rows="3" maxlength="1500"
                        class="form-control"></textarea>
                        <input type="hidden" name="document_notes___x23y_l"
                                                                value="Comments (optional):" class="form-control">
                    </div>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend><span class="fieldset-legend">SECTION 4 - TOTAL EXPENSES</span></legend>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-row">
                        <div class="col-12 mb-3 pr-5"><label for="total_monthly_expenses">TOTAL MONTHLY EXPENSES</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="text" id="total_monthly_expenses" name="total_monthly_expenses___x23y_v"
                                       maxlength="5" class="form-control"> <input type="hidden"
                                                                                  name="total_monthly_expenses___x23y_l"
                                                                                  value="TOTAL MONTHLY EXPENSES"
                                                                                  class="form-control">
                                <div class="input-group-append"><span class="input-group-text">.00</span></div>
                            </div>
                        </div>
                        <div class="col-12 mb-3"><label for="total_monthly_net_income">TOTAL MONTHLY NET INCOME</label>
                            <div class="input-group mb-3 pr-5">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="text" id="total_monthly_net_income" name="total_monthly_net_income___x23y_v"
                                       maxlength="5" class="form-control"> <input type="hidden"
                                                                                  name="total_monthly_net_income___x23y_l"
                                                                                  value="TOTAL MONTHLY NET INCOME"
                                                                                  class="form-control">
                                <div class="input-group-append"><span class="input-group-text">.00</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <p class="text-center">All information is subject to verification and could result in an overaward
                        if information is misreported.</p>
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
                        <input type="checkbox" id="declaration_agree"
                                                                name="declaration_agree___x23y_checkbox" class="form-check-input">
                        <label for="declaration_agree" class="form-check-label">I certify that information provided with
                            this request is accurate and correct.</label>
                    </div>
                    <small><strong>Collection and use of information:</strong> The information included in this form and
                        authorized above is collected under Sections 26(c) and 26(e) of the Freedom of Information and
                        Protection of Privacy Act, and under the authority of the Canada Student Financial Assistance
                        Act, R.S.C. 1994, Chapter C-28 and StudentAid BC. The information provided will be used to
                        determine eligibility for a benefit through StudentAid BC and for statistical and evaluation
                        purposes. If you have any questions about the collection and use of this information, contact
                        the Director, StudentAid BC, Ministry of Post-Secondary Education and Future Skills, PO Box 9173,
                        Stn Prov Govt, Victoria B.C., V8W 9H7, telephone 1-800-561-1818 (toll-free in Canada/U.S.) or
                        (250)-387-6100 from outside North America.</small>
                </div>
            </div>
        </fieldset>
        <div class="row d-print-none">
            <div class="col-12">
                <button type="submit" class="btn btn-block btn-success ">Submit</button>
            </div>
        </div>
    </form>
</div>
