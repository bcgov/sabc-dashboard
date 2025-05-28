<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">
            <div class="taskbar">
                <h4>Check appendix status</h4>
                <hr class="mt-0"/>
            </div>
            <div id="region-header-alert">
                <div class="region region-header-alert" style="display: block;">
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
                </div><!-- /.region -->
            </div>

            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load your appendix. Please try again later.</div>
            <template v-if="loading == false && loadingError == false && app !='' && eventItems !=''">

                <div v-if="app.applicationNumber != undefined && app.applicationNumber != false && app.applicationNumber > 0 && app.appendixTimeline != undefined" class="row mb-4">
                    <appendix-action-buttons :app="app" :eventItems="eventItems"></appendix-action-buttons>
                </div>
                <div class="row p-3">
                    <div class="col-12 status-bar">
                        <div class="">
                            <span>Appendix #{{app.applicationNumber | formatAppNumber}}</span>
                        </div>
                    </div>

                    <div class="col-md-6 status-left pt-3 pb-3 bg-white">

                        <div>
                            <template>
                                <div class="row mb-3">
                                    <div class="d-block d-md-none col-md-4"><strong>For</strong></div>
                                    <div class="d-none d-md-block col-md-4 text-right"><strong>For</strong></div>
                                    <div class="col-md-8">{{ app.firstName + ' ' + app.lastName }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="d-block d-md-none col-md-4"><strong>App #</strong></div>
                                    <div class="d-none d-md-block col-md-4 text-right"><strong>App #</strong></div>
                                    <div class="col-md-8">{{app.applicationNumber}}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="d-block d-md-none col-md-4"><strong>Start Date</strong></div>
                                    <div class="d-none d-md-block col-md-4 text-right"><strong>Start Date</strong></div>
                                    <div class="col-md-8">{{progStartDate | formatLetterDate}}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="d-block d-md-none col-md-4"><strong>End Date</strong></div>
                                    <div class="d-none d-md-block col-md-4 text-right"><strong>End Date</strong></div>
                                    <div class="col-md-8">{{progEndDate | formatLetterDate}}</div>
                                </div>



<!--                                //don't show unless app has been submitted (in transition or received) &#45;&#45; needs to be changed to match application details!!!!-->
<!--                                if(!empty($events['submitStatus'])){-->
<!--                                $s .= '<hr class="small">';-->
<!--                                $s .= '<div class="application-info paddingB mo-padding">';-->
<!--                                $s .= '<h6 class="uppercase short">Document Centre</h6>';-->
<!--                                //DOCUMENT CENTER-->
<!--                                if(isset($app->documentID) && !empty($app->documentID) || isset($app->formGUID) && !empty($app->formGUID)){-->
<!--                                $appStatus = (!empty($app->documentID)) ? 'R' : 'T'; //"R" (received) : "T" (in transition)-->
<!--                                $docID = (!empty($app->documentID)) ? $app->documentID : $app->formGUID;-->
<!--                                $s .= '<dl class="dl-horizontal-med">';-->
<!--                                    $s .= '<dt>';-->
<!--                                        $s .= '<a href="/dashboard/notifications/'.$docID.'/'.$appStatus.'/Appendix" target="_blank">';-->
<!--                                            $s .= $app->formType;-->
<!--                                            $s .= '</a>';-->
<!--                                        $s .= '</dt>';-->
<!--                                    $s .= '</dl>';-->
<!--                                }-->

<!--                                $s .= '</div>';-->
<!--                                }-->
                                <template v-if="eventItems.submitStatus != undefined && eventItems.submitStatus != false">
                                    <hr/>
                                    <div>
                                        <h6 class="skinny text-uppercase">Document Centre</h6>
                                        <div v-if="(app.documentID != undefined && app.documentID != false) || (app.formGUID != undefined && app.formGUID != false)" class="row">
                                            <div class="col-md-8">
                                                <a :href="'/dashboard/notifications/' + (app.documentID != undefined && app.documentID != false ? app.documentID : app.formGUID) + '/' + (app.documentID != undefined && app.documentID != false ? 'R' : 'T') + '/appendix'" target="_blank">{{app.formType}}</a>
                                            </div>
                                        </div>
                                    </div>
                                </template>


                            </template>
                        </div>

                    </div>
                    <div class="col-md-6 status-right pb-3 pr-0 pl-0">
                        <div class="timeline">
                            <ul class="accordion" id="accordionExample">

                                <li>
                                    <span class="icon" :class="fnStatusClass(eventItems['Start/Submit Appendix'], 'icon-addtolist')"></span>

                                    <div class="card rounded-0">
                                        <div class="card-header" id="headingOne">
                                            <h2 class="mb-0">
                                                <button @click="toggleAccordion(0)" class="btn btn-link btn-block text-left" type="button">
                                                    Start/Submit Appendix
                                                    <i class="float-right icon" :class="accordion[0] === true ? ' icon-circleup' : ' icon-circledown'"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseOne" class="collapse" :class="accordion[0] === true ? 'show' : 'hide'">
                                            <div class="card-body">
<!--                                                if($app->reminderFlag == 'Y'){-->
                                                <p v-if="app.reminderFlag == 'Y'">
                                                    <span v-html="eventItems['Start/Submit Appendix'].eventDate"></span>&nbsp;&nbsp;<span v-html="eventItems['Start/Submit Appendix'].eventType"></span>
                                                    <br><br>
                                                    <button data-toggle="modal" data-remote="false" data-target="#delete-appendix-confirm" class="btn btn-danger">Delete Appendix</button>
                                                </p>
                                                <div v-else class="row">
                                                    <template v-if="eventItems['Start/Submit Appendix'].eventDate != undefined && eventItems['Start/Submit Appendix'].eventDate != false">
                                                        <div class="col-5"><small>{{eventItems['Start/Submit Appendix'].eventDate}}</small></div>
                                                        <div class="col-7" v-html="eventItems['Start/Submit Appendix'].eventType"></div>
                                                    </template>
                                                    <template v-else>
                                                        <p class="col-12" v-html="eventItems['Start/Submit Appendix'].eventType"></p>
                                                    </template>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <appendix-collapse-two :app="app" :eventItems="eventItems" :isInkSignReq="isInkSignReq" :processingConsent="processingConsent" :showEconsent="showEconsent"></appendix-collapse-two>
                                </li>

                            </ul>

                        </div>
                    </div>
                </div>

            </template>
        </div>


    </div><!-- /.row -->
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
    import axios from 'axios';

    export default {
        filters: {
            formatAppNumber: function(value){
                let year = value.slice(0, 4);
                let extra = value.slice(4);

                return year + '-' + extra;
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
                return 'confirming';
            },
            formatLetterDate: function (value) {
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
            cleanText: function(value){
                if(value == undefined) return '';
                let txt = value.trim();
                if(txt == ''){
                    return 'confirming';
                }
                return txt;
            }

        },
        props: ['errors', 'appno', 'formguid'],
        data: () => ({
            app: '',
            eventItems: '',
            loading: true,
            loadingError: false,
            accordion: [false, false, false, false, false],
            //TIMELINE EVENTS
            statusFlags: {
                'Start/Submit Appendix': {'title': 'Start/Submit Appendix', 'class': 'icon-addtolist'},
                'Submit Appendix Declaration': {'title': 'Submit Appendix Declaration', 'class': 'icon-uniF47C'}
            },
            statusFlagsClass: {
                'In Progress': {'status': 'In Progress', 'class': 'info'},
                'Complete': {'status': 'Complete', 'class': 'success'},
                'Waiting': {'status': 'Waiting', 'class': 'warning'},
                'Scheduled': {'status': 'Scheduled', 'class': 'info'},
                'Missing Info': {'status': 'Missing Info', 'class': 'important'},
                'Missing Information': {'status': 'Missing Information', 'class': 'important'},
                'Cancelled': {'status': 'Cancelled', 'class': 'danger'},
                'Not Required': {'status': 'Not Required Yet', 'class': ''}
            },

            isInkSignReq: null,
            processingConsent: null,
            showEconsent: null,
            econsent_rollout_date: 20200317, // 2020 Mar 17
            maintenanceMode: false,
            validationErrors: '',

        }),
        methods: {
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },
            //you cannot update arrays directly in JS. The DOM won't see these changes.
            //https://stackoverflow.com/questions/51412250/vue-v-if-cant-access-booleans-in-arrays
            toggleAccordion: function(index){
                this.$set(this.accordion, index, !this.accordion[index])
            },
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/student-loans/fetch-appendix-status?application_number=' + this.appno + '&form_guid=' + this.formguid,
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.app = response.data.application;
                        vm.eventItems = response.data.details;
                        vm.isInkSignReq = response.data.isWebDeclarationInkSignatureRequired;
                        vm.processingConsent = response.data.processing_consent;
                        vm.showEconsent = response.data.show_econsent;

                        //            if(isset($eventItems['Start/Submit Application']['eventCategoryCode']) && $eventItems['Start/Submit Application']['eventCategoryCode'] == 'Complete'){
                        //show collapse details of event if it is NOT complete
                        if(vm.eventItems['Start/Submit Appendix'].eventCategoryCode == null || vm.eventItems['Start/Submit Application'].eventCategoryCode != 'Complete'){
                            vm.accordion[0] = true;
                        }
                        //show collapse details of event if it is NOT complete
                        if(vm.eventItems['Submit Appendix Declaration'].eventCategoryCode != undefined && vm.eventItems['Submit Appendix Declaration'].eventCategoryCode != 'Complete'){
                            vm.accordion[1] = true;
                        }


                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        console.log(error);
                    });
            },
            fnStatusClass: function(e, defaultClass){
                // alert(e);
                // alert(defaultClass);
                //if(e.eventCategoryCode != undefined && e.eventCategoryCode != null) {
                let fnClass = e.eventCode != undefined ? 'text-' + this.statusFlagsClass[e.eventCode].class : '';
                return defaultClass + ' ' + fnClass;
                //return this.statusFlags[this.app.applicationDetails.applicationTimeline.applicationTimelineCode].class;
                //return (e != undefined && e != null ? $default.' text-'.$statusFlags[$e['eventCategoryCode']]['class'] : $default;

                //}
                //return defaultClass;
            }


        },
        computed: {
            progStartDate: function(){
                // $progStartDate = (isset($app->studyStartDate)) ?
                //     date('M d, Y', strtotime($app->studyStartDate)) : NULL;
                return app.studyStartDate != undefined ? app.studyStartDate : '';
            },
            progEndDate: function(){
                return app.studyEndDate != undefined ? app.studyEndDate : '';
            },
            //         $lettersArray = $this->fnLoadLetters($app->applicationDetails->pdfLetters);
            // foreach($lettersArray as $sendDate => $letters){
            //     foreach ($letters as $letter){
            //         if($letter['LetterDescript'] == "Application"){
            //             $submit_date = date("Ymd", $sendDate);
            //         }
            //     }
            // }

            appStatus: function(){
//                $status = $statusFlags[$app->applicationDetails->applicationTimeline->applicationTimelineCode]['status'];
//                $statusClass = $statusFlags[$app->applicationDetails->applicationTimeline->applicationTimelineCode]['class'];
//                 $statusFlags = array(
//                 'In Progress' => array('status' => 'In Progress', 'class' => 'info'),
//                 'Complete' => array('status' => 'Complete', 'class' => 'success'),
//                 'Waiting' => array('status' => 'Waiting', 'class' => 'warning'),
//                 'Scheduled' => array('status' => 'Scheduled', 'class' => 'info'),
//                 'Missing Info' => array('status' => 'Missing Info', 'class' => 'important'),
//                 'Missing Information' => array('status' => 'Missing Information', 'class' => 'important'),
//                 'Not Required' => array('status' => 'Not Required Yet', 'class' => ''));

                // return this.statusFlags[this.app.applicationDetails.applicationTimeline.applicationTimelineCode].status;
            },
            submitClass: function(){
                // return this.statusFlags[this.app.applicationDetails.applicationTimeline.applicationTimelineCode].class;
                return (this.app.eventItems.submitStatus != undefined && this.app.eventItems.submitStatus != null) ? 'btn-success' : ((this.app.apxStatus == 'RDY') ? 'btn-success' : 'disabled');
            },

        },
        created() {

        },
        mounted: function () {
            this.fetchData();
            document.title = "StudentAidBC - Check Appendix Status";

            if(this.errors != "")
                this.validationErrors = JSON.parse(this.errors);

        },
        watch: {

        }

    }

</script>
