<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12 container">
            <div class="taskbar">
                <h4>Account Verification Required</h4>
                <hr class="mt-0"/>
            </div>

            <div class="card">
                <div class="card-header alert-primary text-white">
                    <div class="float-left">BC Services Card account required</div>
                    <span class="icon icon-notes float-right"></span>
                </div>
                <div class="card-body">

                    <p>As an applicant to the full-time student loan program, you will need to use a BC Services Card account to access the student loan application via the StudentAid BC dashboard.</p>
                    <p><a href="https://id.gov.bc.ca/account/" target="_blank">Learn how to set up a BC Services Card account</a></p>

                    <p>Once you have completed set up of a BC Services Card account, you will use it to log into your StudentAid BC Dashboard to access the student loan application.</p>
                    <p><a href="https://www2.gov.bc.ca/gov/content?id=B2B3A21E797A421A8FD39EEA86E245D6" target="_blank">Learn more about a BC Services Card account</a></p>

                    <blockquote>
                        <p class="">
                            <b>How do I get a BC Services Card account if I don’t live in British Columbia and I don’t have a BC Services Card? </b><br/>
                            If you don’t live in B.C. but have Canadian ID, like a driver’s licence, passport, or immigration document, you can set up a BC Services Card account without a BC Services Card. <a href="https://id.gov.bc.ca/account/" target="_blank">Visit the BC Services Card account website</a> for a list of <a href="https://www2.gov.bc.ca/gov/content?id=5F8782A92E464066885149242E050814" target="_blank">accepted ID</a> you can use to set up your account.
                            <br/>
                            <br/>
                            If you are not eligible for a BC Services Card, and do not have a Canadian issued ID, please contact StudentAid BC:
                            <br/>1-800-561-1818 toll-free from anywhere in Canada or the U.S.
                            <br/>+1-778-309-4621 from outside North America

                        </p>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
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
</style>
<script>
export default {

    data: () => ({
        maintenanceMode: false,
    }),
    methods: {
        disablePage: function(e){
            if(e === true)
                this.maintenanceMode = true;
        },
    },
    mounted: function () {
        document.title = "StudentAidBC - Account Verification Required";
    },
    watch: {
        '$route' (to, from) {
            // document.title = 'StudentAidBC - Apply for a student loan'
        }
    },
}
</script>
