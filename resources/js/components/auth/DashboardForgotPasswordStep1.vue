<template>
    <div>
        <fieldset>
            <legend><span class="fieldset-legend">Forgot password</span></legend>
            <input type="hidden" name="step" value="1">
            <div class="row">
                <div class="col-12">
                    <div class="form-row pt-3">
                        <div class="form-group col-12">
                            <label for="edit-last-name">Last Name <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" :class="errors.lastname != undefined ? 'is-invalid' : ''" id="edit-last-name" name="lastname" size="60" maxlength="50" :value="old['lastname']" required>
                        </div>

                        <div class="form-group col-12">
                            <label for="edit-social-insurance-number">Social Insurance Number <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" :class="errors.socialinsnum != undefined ? 'is-invalid' : ''" id="edit-social-insurance-number" name="socialinsnum" size="60" maxlength="11" autocomplete="off" required>
                        </div>

                        <div class="form-group col-12 m-0">
                            <label for="edit-month">Date of Birth (Month, DD, YYYY)</label>
                        </div>
                        <div class="form-group col-md-4">
                            <select class="form-control" :class="errors.month != undefined ? 'is-invalid' : ''" id="edit-month" name="month" :value="old['month']">
                                <option value="">- Select -</option>
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
                            <input type="text" class="form-control" :class="errors.day != undefined ? 'is-invalid' : ''" id="edit-day" name="day" size="60" maxlength="2" :value="old['day']" placeholder="Day" required>
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" class="form-control" :class="errors.year != undefined ? 'is-invalid' : ''" id="edit-year" name="year" size="60" maxlength="4" :value="old['year']" placeholder="Year" required>
                        </div>
                    </div>
                    <div class="form-row pt-3 pb-3">
                        <div class="col-md-6">
                            <a href="/dashboard" class="btn btn-link text-left">Cancel</a>
                        </div>
                        <div class="col-md-6">
                            <button type="button" @click="goNext" class="btn btn-block btn-success">Next</button>
                        </div>
                    </div>

                </div>
            </div>
        </fieldset>

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
    //import axios from 'axios

    export default {
        filters: {

        },
        props: ['errors', 'old'],
        data: () => ({
            loading: false,
            loadingError: false,
        }),
        methods: {
            goNext: function(){
                this.$emit('submitform');
            }

        },

        created() {

        },
        mounted: function () {

        },
        watch: {

        }

    }

</script>
