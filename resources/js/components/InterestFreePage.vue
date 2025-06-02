<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>

        <div class="col-12 taskbar">
            <h4>Apply for In-Study Payment-Free status</h4>
            <hr class="mt-0"/>
        </div>

        <div v-if="maintenanceMode === false" class="col-md-6">

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <span class="icon icon-notes float-right"></span>
                    <div class="">Am I eligible to apply for In-Study Payment-Free status?</div>
                </div>
                <div class="card-body">
                    <h4 class="text-primary">You can apply online if you:</h4>

                    <ul class="nice p-0">
                        <li><span class="bullet icon-uniF479"></span> are attending a <a data-original-title="" href="#" class="designated_school" data-html="true" data-toggle="tooltip">designated</a> B.C. public post-secondary school full-time</li>
                        <li><span class="bullet icon-uniF479"></span> have started (or are within 30 days of starting), or have not finished your study period </li>
                        <li><span class="bullet icon-uniF479"></span> have not received funding for your current study period</li>
                        <li><span class="bullet icon-uniF479"></span> have student loans in good-standing</li>
                    </ul>


                    <div class="pt-1">
                        <a class="btn btn-success btn-md btn-block" href="/sfas-bin/phcgi?PH_APP=SFASAPP&PH_QKC=phw_int_free00.QKC&PH_HTML=phw_int_free00.HTM" target="_blank"><span class="icon-flag"></span> Start a new In-Study Payment-Free Status application</a>
                    </div>

                    <hr />
                    <p class="text-muted">If you are <strong>not attending a B.C. public</strong> post-secondary school, you will need to confirm your enrolment with the <a href="https://www.csnpe-nslsc.canada.ca/en/home" target="_blank">National Student Loans Service Centre</a>.</p>

                    <p class="text-muted">If you have a <strong>grant overaward</strong>, you will need to complete a paper  <a href="/sites/all/files/form-library/cert3.pdf">Continuation of In-Study Payment-Free Status for Grant Overawards - Certificate 3</a> which will help you stay payment-free while in school.</p>

                </div>
            </div>
        </div><!-- /.col-md-6-->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <span class="icon icon-monitor float-right text-muted"></span>
                    <div class="">What happens once I submit this application?</div>
                </div>
                <div class="card-body">
                    <p>Once your application is approved, you won't need to begin payments on your loan until six months after your studies end. </p>
                    <ol class="nice p-0">
                        <li><span class="number">1</span> Your application will be sent electronically <strong>to your school</strong> for confirmation of full-time enrolment.</li>
                        <li><span class="number">2</span>  Your application will be sent electronically <strong>to your loan providers</strong> to confirm that your student loans are in good standing (not in default).</li>
                        <li><span class="number">3</span>  You will receive confirmation from the <a href="https://www.csnpe-nslsc.canada.ca/en/home" target="_blank">National Student Loan Service Centre</a> and any lending institution (if applicable) that your application has been approved. </li>
                    </ol>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-light">
                    <span class="icon icon-security float-right text-muted"></span>
                    <div class="">Is my information secure?</div>
                </div>
                <div class="card-body">
                    <p><strong>Yes.</strong> The online student loan application uses a secure environment to keep your information safe.</p>
                    <p>By completing this application, you consent to sharing your information with your school and with the <a href="https://www.csnpe-nslsc.canada.ca/en/home" target="_blank">National Student Loan Service Centre</a> (for your Canada student loans), and any lending institution (bank or credit union) that holds any of your previous student loans.</p>
                </div>
            </div>
        </div><!-- /.col-md-6-->

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
    ul.nice, ol.nice {
        margin: 12px 0;
        list-style: none;
    }
    ul.nice li, ol.nice li {
        padding-left: 24px;
        position: relative;
        line-height: 26.4px;
        font-size: 14px;
        margin-bottom: 4.8px;
        color: #7f818c;
    }
    ul.nice li span.bullet, ol.nice li span.bullet, ul.nice li span.number, ol.nice li span.number {
        position: absolute;
        left: 0;
        top: 0;
        font-weight: 300;
        font-size: 17.5px;
    }
    ul.nice li span.bullet, ol.nice li span.bullet {
        left: -3px;
    }
    ul.nice li span.number, ol.nice li span.number {
        color: #bbc1d1;
        font-weight: 700;
    }
    .card hr {
        margin-top: 1rem;
        margin-bottom: 1rem;
    }
</style>
<script>
    import axios from 'axios';

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
        computed: {

        },
        created() {
        },
        mounted: function () {
            document.title = "StudentAidBC - Apply for In-Study Payment-Free status";
        },
        watch: {
            '$route' (to, from) {
                // document.title = 'StudentAidBC - Apply for a student loan'
            }
        },

    }

</script>
