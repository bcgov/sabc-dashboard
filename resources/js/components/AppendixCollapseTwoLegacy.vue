<template>
    <div>

    <!--    ## SPOUSE Submit Appendix Declaration box-->
<!--    $s .= '<li>';-->

<!--    $cid = 'get_application_dec'.rand().''.$app->applicationNumber;-->
<!--    $common = new aeit();-->
<!--    $common->WSDL = fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');-->

<!--    $GUID = $common->fnDecrypt($GLOBALS['user']->name);-->
<!--    $isWebDeclarationInkSignatureRequired = $common->fnRequest('isWebDeclarationInkSignatureRequired', array('applicationNumber' => $app->applicationNumber, 'role' => 'S', 'userGUID' => $GUID), $cid, 100);-->

<!--    //if isWebDeclarationInkSignatureRequired returns Y then show legacy-->
<!--    if($isWebDeclarationInkSignatureRequired->inkSignatureRequired == 'Y'){-->
<!--    $s .= fetchSpouseLegacyCode($events);-->

<!--    //else isWebDeclarationInkSignatureRequired returns N|E then show new logic-->
<!--    }else{-->
<!--    // IF APPENDIX IS IN PROGRESS-->
<!--    if($events['submitStatus'] == false) {-->

<!--    //SHOW E-CONSENT if user is authenticated-->
<!--    if($isWebDeclarationInkSignatureRequired->inkSignatureRequired == 'E') {-->
<!--    $s .= '<span class="icon icon-uniF47C text-' . fnStatusClass($events['Submit Appendix Declaration']['eventCode']) . '"></span>';-->
<!--    $s .= '<strong>E-Consent Not Submitted</strong><br>';-->
<!--    }else{-->
<!--    $s .= fetchSpouseLegacyCode($events);-->
<!--    }-->

<!--    //else it was submitted-->
<!--    }else{-->
<!--    list($processing_consent, $show_econsent) = fetchGetWebDeclaration($common, $app, $GUID);-->

<!--    //if $processing_consent is true, attempt to wait 30s-->
<!--    if($processing_consent == true) {-->
<!--    $wait_init = 0;-->
<!--    $wait_limit = 30;-->
<!--    while ($wait_init < $wait_limit) {-->
<!--    list($processing_consent, $show_econsent) = fetchGetWebDeclaration($common, $app, $GUID);-->
<!--    if($processing_consent == false) {-->
<!--    break;-->
<!--    }-->
<!--    sleep(1);-->
<!--    $wait_init++;-->
<!--    }-->
<!--    }-->

<!--    if($processing_consent == true){-->
<!--    $s .= '<span class="icon icon-uniF47C text-warning"></span>';-->
<!--    $s .= '<p class="alert alert-warning">If unable to view your declaration please contact SABC at 1-800-5611818</p>';-->
<!--    }else{-->
<!--    //if the declaration was an econsent (CSXS), show e-consent view button-->
<!--    if($show_econsent == true) {-->
<!--    $s .= '<span class="icon icon-uniF47C text-success"></span>';-->
<!--    $s .= '<strong>E-Consent</strong><br>';-->
<!--    $s .= '<a class="btn btn-block" target="_blank" href="/dashboard/declaration/econsent/spouse/' . $app->applicationNumber . '">View Declaration</a><br>';-->

<!--    //if it was submitted before rollout then show legacy declaration button (Re)Print and sign ...-->
<!--    }else{-->
<!--    $s .= fetchSpouseLegacyCode($events);-->
<!--    }-->
<!--    }-->

<!--    }-->
<!--    }-->
        <!-- LEGACY CODE -->

        <span class="icon" :class="fnStatusClass(eventItems['Submit Appendix Declaration'], 'icon-uniF47C')"></span>

        <div class="card rounded-0">
            <div class="card-header" id="headingTwo">
                <h2 class="mb-0">
                    <button @click="toggleAccordion()" class="btn btn-link btn-block text-left" type="button">
                        Submit Appendix Declaration
                        <i class="float-right icon" :class="accordion === true ? ' icon-circleup' : ' icon-circledown'"></i>
                    </button>
                </h2>
            </div>
            <div id="collapseTwo" class="collapse" :class="accordion === true ? 'show' : 'hide'">
                <div class="card-body">
                    <p v-if="eventItems['Submit Appendix Declaration'].eventDate == undefined || eventItems['Submit Appendix Declaration'].eventDate == false">{{eventItems['Submit Appendix Declaration'].eventDescription}}</p>
                    <div v-else class="row">
                        <div class="col-5"><small>{{eventItems['Submit Appendix Declaration'].eventDate}}</small></div>
                        <div class="col-7" v-html="eventItems['Submit Appendix Declaration'].eventDescription"></div>
                    </div>

                    <div class="row">
                        <template v-if="eventItems['webDecStatus'] != undefined && eventItems['webDecStatus'] != false">
                            <!--                // if app has not been submitted then show print declaration button-->
                            <div class="col-12">
                                <div v-if="eventItems['webDecStatus'] == 't'" v-html="eventItems['Submit Appendix Declaration'].webDecState"></div>
                                <template v-else>
                                    <div v-if="eventItems['webDecStatus'] == 'e'" class="alert alert-contextual alert-info">
                                        <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg>
                                        <p>Web declaration no longer available on-line, if you require a copy please contact StudentAidBC at 1-800-561-1818</p>
                                    </div>
                                    <div v-else-if="eventItems['webDecStatus'] == 'f' || eventItems['webDecStatus'] == 'f1' || eventItems['webDecStatus'] == 'f2'">
                                        <br>
                                        <div v-html="eventItems['Submit Appendix Declaration'].webDecState"></div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>


                </div>
            </div>
        </div>


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
            //you cannot update arrays directly in JS. The DOM won't see these changes.
            //https://stackoverflow.com/questions/51412250/vue-v-if-cant-access-booleans-in-arrays
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
            if(
                (this.eventItems['Submit Appendix Declaration'].eventDate == undefined || this.eventItems['Submit Appendix Declaration'].eventDate == false)
                && (this.eventItems['webDecStatus'] != undefined && this.eventItems['webDecStatus'] != false)
                && (this.eventItems['webDecStatus'] == 'f' || this.eventItems['webDecStatus'] == 'f1' || this.eventItems['webDecStatus'] == 'f2')
            ){
                this.accordion = true;
            }
        },
        watch: {

        }

    }

</script>
