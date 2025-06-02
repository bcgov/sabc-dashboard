<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">


            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load your profile. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">

            <form :action="'/dashboard/appendix/login/' + py + '/APPENDIX1'" method="post" id="sabc-form-builder" accept-charset="UTF-8">
                <input type="hidden" name="_token" :value="csrf">
                <input type="hidden" name="hpot" value="">

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
                    <p> You are about to claim this Appendix 1 as parent without a SIN.</p>
                </div>

                <fieldset>
                    <legend><span class="fieldset-legend"><span class="icon-sslmanager text-info"></span> Appendix Access Code</span></legend>
                    <div class="form-row pt-3">
                        <div class="form-group col-12">
                            <p>Why am I being asked to complete an <a target="_blank" :href="'/help-centre/loan-application-instructions-' + fullCurrentYear + '/appendix-1/' + shortCurrentYear">Appendix 1</a>?</p>
                            <label for="access_code">Appendix Access Code <span class="form-required" title="This field is required.">*</span></label>
                            <input type="text" class="form-control" id="access_code" name="access_code" aria-describedby="accessCodeHelp" size="60" maxlength="9" required>
                            <p id="accessCodeHelp" class="form-text text-muted"><a href="/help-centre/troubleshooting/appendix-one" target="_blank">Don't have an Appendix Access Code</a></p>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="appendix_type">Appendix Type</label>
                            <input type="text" class="form-control" id="appendix_type" name="appendix_type" disabled="disabled" value="Appendix 1">
                        </div>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-block btn-success">Save and Continue</button>
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
    import ProfileChallengeQuestions from "./ProfileChallengeQuestions";

    export default {
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: false,
            loadingError: false,
            validationErrors: '',
            maintenanceMode: false,

            parsedProgramYears: '',
            fullCurrentYear: '',
            shortCurrentYear: '',

        }),
        props: ['py', 'submit_status', 'submit_msg', 'errors', 'program_years'],
        methods: {
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },

            fetchData: function(){

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
        },
        computed: {

        },
        created() {

        },
        mounted: function () {
            this.fetchData();
            document.title = "StudentAidBC - Appendix 1 Claim";

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
