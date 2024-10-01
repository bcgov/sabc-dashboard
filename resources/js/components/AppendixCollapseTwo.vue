<template>
    <div>
<!--    ## SPOUSE Submit Appendix Declaration box-->

        <!-- LEGACY CODE -->
        <!--    //if isWebDeclarationInkSignatureRequired returns Y then show legacy-->
        <template v-if="isInkSignReq != undefined && isInkSignReq.inkSignatureRequired == 'Y'">
            <appendix-collapse-two-legacy :app="app" :eventItems="eventItems" :isInkSignReq="isInkSignReq" :processingConsent="processingConsent" :showEconsent="showEconsent"></appendix-collapse-two-legacy>
        </template>

<!--        e-consent code-->
        <template v-else>
            <!--    // IF APPENDIX IS IN PROGRESS-->
            <template v-if="eventItems['submitStatus'] == false">
                <!--        //SHOW E-CONSENT if user is authenticated-->
                <template v-if="isInkSignReq != undefined && isInkSignReq.inkSignatureRequired == 'E'">
                    <span class="icon" :class="fnStatusClass(eventItems['Submit Appendix Declaration'], 'icon-uniF47C')"></span>
                    <div class="card rounded-0">
                        <div class="card-header" id="headingTwo">
                            <h2 class="mb-0">
                                <button @click="toggleAccordion(1)" class="btn btn-link btn-block text-left" type="button">
                                    E-Consent Not Submitted
    <!--                                <i class="float-right icon" :class="accordion[1] === true ? ' icon-circleup' : ' icon-circledown'"></i>-->
                                </button>
                            </h2>
                        </div>
                    </div>
                </template>
                <appendix-collapse-two-legacy v-else :app="app" :eventItems="eventItems" :isInkSignReq="isInkSignReq" :processingConsent="processingConsent" :showEconsent="showEconsent"></appendix-collapse-two-legacy>

            </template>

            <!--        //else it was submitted-->
            <template v-else>
                <template v-if="processingConsent == true">
                    <span class="icon icon-uniF47C text-warning"></span>
                    <div class="card rounded-0">
                        <div class="card-body">
                            <div class="alert alert-contextual alert-warning">
                                <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#warning"></use></svg>
                                <p>If unable to view your declaration please contact SABC at 1-800-5611818</p>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
<!--                    //if the declaration was an econsent (CSXS), show e-consent view button-->
                    <template v-if="showEconsent == true || (isInkSignReq != null && isInkSignReq.inkSignatureRequired == 'E')">
                        <span class="icon icon-uniF47C text-success"></span>
                        <div class="card rounded-0">

                            <div class="card-header" id="headingTwo">
                                <h2 class="mb-0">
                                    <button @click="toggleAccordion()" class="btn btn-link btn-block text-left" type="button">
                                        E-Consent
                                        <i class="float-right icon" :class="accordion === true ? ' icon-circleup' : ' icon-circledown'"></i>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseTwo" class="collapse" :class="accordion === true ? 'show' : 'hide'">
                                <div class="card-body">
                                    <a class="btn btn-block btn-light" target="_blank" :href="'/dashboard/declaration/econsent/spouse/' + app.applicationNumber">View Declaration</a><br>
                                </div>
                            </div>
                        </div>
                    </template>
<!--                    //if it was submitted before rollout then show legacy declaration button (Re)Print and sign ...-->
                    <template v-else>
                        <appendix-collapse-two-legacy :app="app" :eventItems="eventItems" :isInkSignReq="isInkSignReq" :processingConsent="processingConsent" :showEconsent="showEconsent"></appendix-collapse-two-legacy>
                    </template>
                </template>
            </template>

        </template>

    </div>
</template>
<style scoped>
    /*    to pass css to v-html child */
    div.row >>> span.label{
        cursor: default !important;
        text-transform: uppercase;
    }
    .card{
        border: none;
    }
    .card-header{
        background: white;
    }
    div.timeline{
        border-left: 5px solid #ccc;
    }
    .timeline ul{
        list-style: none;
        padding: 0;
    }
    .timeline ul li{
        position: relative;
    }
    .timeline ul li i.icon{
        position: absolute;
        top: 20px;
        right: 0;
    }
    .timeline ul li span.icon{
        position: absolute;
        background: #fff;
        top: 18px;
        left: -20px;
        font-weight: 300;
        font-size: 28px;
        z-index: 11;
        color: #7f818c;
        line-height: 28px;
    }
    .skinny{
        font-weight: 100;
    }
    .status-left{
        border-left: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
    }
    .status-right{
        border-right: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
    }
    .status-bar{
        position: relative;
        color: #ffffff;
        background: #3f414a;
        padding-top: 12px;
        padding-bottom: 12px;
    }
    .badge{
        border-radius: 50px;
    }
    .taskbar h4 {
        line-height: 50px;
        margin: 0;
        padding: 0;
        font-family: "Questrial", "Century Gothic", Arial, Helvetica, sans-serif;
        font-weight: normal;
    }

    h4 {
        font-size: 16.1px;
        font-weight: 700;
    }
    h2.slick{
        font-weight: 100;
        line-height: 15px;
        margin-bottom: 0;
    }
    h2>button.btn{
        color: initial;
        text-decoration: none;
        font-weight: 500;
    }
</style>
<script>
    //import axios from 'axios

    export default {
        filters: {
            formatDate: function (value) {
                if(value != undefined && value != ''){
                    var newValue = value.split(",");

                    return newValue[0];
                }
                return '-';
            },
            formatApplicationDate: function (value) {
                if(value != undefined && value != '' && value.length == 8){
                    //var newValue = value.split(" ");
                    let year = value.slice(0, 4);
                    let month = value.slice(4, 6);
                    let day = value.slice(6, 8);

                    let date = new Date(year + "-" + month + "-" + day + "T19:30:00.000Z");
                    let formatted = date.toDateString().split(" ");
                    return formatted[1] + " " +  formatted[2] + ", " + formatted[3];
                }
                return '-';
            },
        },
        props: ['app', 'eventItems', 'isInkSignReq', 'processingConsent', 'showEconsent'],
        data: () => ({
            accordion: false,
        }),
        methods: {
            toggleAccordion: function(){
                this.accordion = !this.accordion;
            },
            fnStatusClass: function(e, defaultClass){
                // alert(e);
                // alert(defaultClass);
                //if(e.eventCategoryCode != undefined && e.eventCategoryCode != null) {
                let fnClass = e.eventCode != undefined && e.eventCode == 'Complete' ? 'text-success' : '';
                return defaultClass + ' ' + fnClass;
                //return this.statusFlags[this.app.applicationDetails.applicationTimeline.applicationTimelineCode].class;
                //return (e != undefined && e != null ? $default.' text-'.$statusFlags[$e['eventCategoryCode']]['class'] : $default;

                //}
                //return defaultClass;
            }


        },
        computed: {
        },
        created() {

        },
        mounted: function () {
            if(this.showEconsent == true){
                this.accordion = true;
            }
        },
        watch: {

        }

    }

</script>
