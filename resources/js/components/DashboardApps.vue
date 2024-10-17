<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">
            <div class="taskbar">
                <h4>My Dashboard</h4>
                <hr class="mt-0"/>
            </div>

            <div id="region-header-alert">
                <div class="region region-header-alert" style="display: block;">
                    <div v-if="errors != ''" class="row">
                        <div class="col-12">
                            <div class="alert alert-contextual alert-danger">
                                <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#stopsign-alert"></use></svg>
                                <template v-for="(error, i) in validationErrors"><p class="alert-p"v-for="e in error" v-html="e"><br v-if="i==1"></p></template>
                            </div>
                        </div><!-- /.block -->
                    </div>

                    <div v-if="loading == false && loadingError == true && errorMsg !== ''" class="alert alert-contextual alert-danger">
                        <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#stopsign-alert"></use></svg>
                        <p>{{errorMsg}}</p>
                    </div><!-- /.alert -->
                </div><!-- /.region -->
            </div>

            <dashboard-links :msgs="newMsgs"></dashboard-links>
            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load your dashboard. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">

                <div class="d-block d-sm-block d-md-none accordion" id="myAccordion">
                    <div class="card">
                        <div class="card-header p-0" id="accordion1">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseAccordion1" aria-expanded="true" aria-controls="collapseAccordion1">
                                    My Student Loan Applications <span v-if="numberApps > 0" class="badge badge-secondary">{{numberApps}}</span>
                                </button>
                            </h2>
                        </div>

                        <div id="collapseAccordion1" class="collapse show" aria-labelledby="accordion1" data-parent="#myAccordion">
                            <div class="card-body p-0">
                                <ul class="list-group">

                                    <template v-if="(notSubmittedApps == undefined || notSubmittedApps == '') && (submittedApps == undefined || submittedApps == '')">
                                        <li class="list-group-item">You have no applications</li>
                                    </template>
                                    <template v-if="notSubmittedApps != undefined && notSubmittedApps != ''">
                                        <li class="list-group-item list-group-item-warning text-dark">Application NOT Submitted</li>
                                        <a v-for="app in notSubmittedApps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-application-status/' + app[0].ApplicationNumber">
                                            <span class="btn btn-link">#{{ app[0].ApplicationNumber | formatAppNumber }}</span>
                                            <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app[0].StudyStartDate}}</span>
                                            <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app[0].StudyEndDate}}</span>
                                            <div class="float-right m-2">
                                                <span v-if="app[0].AppendixReminder == 'Y'" class="d-none d-md-inline mr-1">No longer required</span>
                                                <span class="badge badge-warning p-2">Edit</span>
                                            </div>
                                        </a>

                                    </template>

                                    <template v-if="submittedApps != undefined && submittedApps != ''">
                                        <li class="list-group-item list-group-item-success text-dark">Application Submitted</li>
                                        <a v-for="app in submittedApps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-application-status/' + app[0].ApplicationNumber">
                                            <span class="btn btn-link">#{{ app[0].ApplicationNumber | formatAppNumber }}</span>
                                            <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app[0].StudyStartDate}}</span>
                                            <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app[0].StudyEndDate}}</span>
                                            <div class="float-right m-2">
                                                <span v-if="app[0].InkSigRequired == 'Y'" class="d-none d-md-inline mr-1">Declaration Required</span>
                                                <span class="badge badge-success p-2">View</span>
                                            </div>
                                        </a>
                                    </template>

                                </ul>
                            </div><!-- /.card-body -->
                        </div><!-- /.collapse -->
                    </div><!-- /.card -->

                    <div class="card">
                        <div class="card-header p-0" id="accordion2">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseAccordion2" aria-expanded="true" aria-controls="collapseAccordion2">
                                    Appendix 1 <span v-if="appx != undefined && appx.Appendix1 != undefined && appx.Appendix1.total > 0" class="badge badge-secondary">{{appx.Appendix1.total}}</span>
                                </button>
                            </h2>
                        </div>

                        <div v-if="appx != undefined && appx.Appendix1 != undefined && appx.Appendix1.total > 0" id="collapseAccordion2" class="collapse" aria-labelledby="accordion2" data-parent="#myAccordion">
                            <div class="card-body p-0">
                                <ul class="list-group">
                                    <template v-if="(appx.Appendix1.NotSubmitted == undefined || appx.Appendix1.NotSubmitted == '') && (appx.Appendix1.Submitted == undefined || appx.Appendix1.Submitted == '')">
                                        <li class="list-group-item">You have no appendix 1</li>
                                    </template>

                                    <template v-if="appx.Appendix1.NotSubmitted != ''">
                                        <li class="list-group-item list-group-item-warning text-dark">Appendix 1 NOT Submitted</li>
                                        <template v-for="apps in appx.Appendix1.NotSubmitted">
                                            <!--                                            <a v-for="app in apps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-appendix-status/' + app.ApplicationNumber + '/' + app.FormGUID">-->
                                            <a v-for="app in apps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-appendix-status/' + app.ApplicationNumber">
                                                <span class="btn btn-link">#{{ app.ApplicationNumber | formatAppNumber }}</span>
                                                <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app.StudyStartDate}}</span>
                                                <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app.StudyEndDate}}</span>
                                                <div class="float-right m-2">
                                                    <span v-if="app.AppendixReminder == 'Y'" class="d-none d-md-inline mr-1">No longer required</span>
                                                    <span class="badge badge-warning p-2">Edit</span>
                                                </div>
                                            </a>
                                        </template>

                                    </template>

                                    <template v-if="appx.Appendix1.Submitted != ''">
                                        <li class="list-group-item list-group-item-success text-dark">Appendix 1 Submitted</li>
                                        <template v-for="apps in appx.Appendix1.Submitted">
                                            <a v-for="app in apps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-appendix-status/' + app.ApplicationNumber">
                                                <span class="btn btn-link">#{{ app.ApplicationNumber | formatAppNumber }}</span>
                                                <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app.StudyStartDate}}</span>
                                                <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app.StudyEndDate}}</span>
                                                <div class="float-right m-2">
                                                    <span class="badge badge-success p-2">View</span>
                                                </div>
                                            </a>
                                        </template>
                                    </template>
                                </ul>

                            </div><!-- /.card-body -->
                        </div><!-- /.collapse -->
                    </div><!-- /.card -->
                    <div class="card">
                        <div class="card-header p-0" id="accordion3">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseAccordion3" aria-expanded="true" aria-controls="collapseAccordion2">
                                    Appendix 2 <span v-if="appx != undefined && appx.Appendix2 != undefined && appx.Appendix2.total > 0" class="badge badge-secondary">{{appx.Appendix2.total}}</span>
                                </button>
                            </h2>
                        </div>

                        <div v-if="appx != undefined && appx.Appendix2 != undefined && appx.Appendix2.total > 0" id="collapseAccordion3" class="collapse" aria-labelledby="accordion3" data-parent="#myAccordion">
                            <div class="card-body p-0">
                                <ul class="list-group">
                                    <template v-if="appx.Appendix2.NotSubmitted != ''">
                                        <li class="list-group-item list-group-item-warning text-dark">Appendix 2 NOT Submitted</li>
                                        <template v-for="apps in appx.Appendix2.NotSubmitted">
                                            <a v-for="app in apps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-appendix-status/' + app.ApplicationNumber">
                                                <!--                                                <a v-for="app in apps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-appendix-status/' + app.ApplicationNumber + '/' + app.FormGUID">-->
                                                <span class="btn btn-link">#{{ app.ApplicationNumber | formatAppNumber }}</span>
                                                <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app.StudyStartDate}}</span>
                                                <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app.StudyEndDate}}</span>
                                                <div class="float-right m-2">
                                                    <span v-if="app.AppendixReminder == 'Y'" class="d-none d-md-inline mr-1">No longer required</span>
                                                    <span class="badge badge-warning p-2">Edit</span>
                                                </div>
                                            </a>
                                        </template>

                                    </template>

                                    <template v-if="appx.Appendix2.Submitted != ''">
                                        <li class="list-group-item list-group-item-success text-dark">Appendix 2 Submitted</li>
                                        <template v-for="apps in appx.Appendix2.Submitted">
                                            <a v-for="app in apps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-appendix-status/' + app.ApplicationNumber">
                                                <span class="btn btn-link">#{{ app.ApplicationNumber | formatAppNumber }}</span>
                                                <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app.StudyStartDate}}</span>
                                                <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app.StudyEndDate}}</span>
                                                <div class="float-right m-2">
                                                    <!--                                            <span v-if="app.AppendixReminder == 'N'" class="mr-1">No longer required</span>-->
                                                    <span class="badge badge-success p-2">View</span>
                                                </div>
                                            </a>
                                        </template>
                                    </template>
                                </ul>
                            </div><!-- /.card-body -->
                        </div><!-- /.collapse -->
                    </div><!-- /.card -->
                </div>


                <ul class="d-none d-md-flex nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="applications-tab" data-toggle="tab" href="#applications" role="tab" aria-controls="applications" aria-selected="true">My Student Loan Applications <span v-if="numberApps > 0" class="badge badge-secondary">{{numberApps}}</span></a>
                    </li>
                    <li v-if="appx != undefined && appx.Appendix1 != undefined && appx.Appendix1.total > 0" class="nav-item" role="presentation">
                        <a class="nav-link" id="appx1-tab" data-toggle="tab" href="#appx1" role="tab" aria-controls="appx1" aria-selected="false">Appendix 1 <span v-if="appx.Appendix1.total > 0" class="badge badge-secondary">{{appx.Appendix1.total}}</span></a>
                    </li>
                    <li v-if="appx != undefined && appx.Appendix2 != undefined && appx.Appendix2.total > 0" class="nav-item" role="presentation">
                        <a class="nav-link" id="appx2-tab" data-toggle="tab" href="#appx2" role="tab" aria-controls="contact" aria-selected="false">Appendix 2 <span v-if="appx.Appendix2.total > 0" class="badge badge-secondary">{{appx.Appendix2.total}}</span></a>
                    </li>
                </ul>
                <div class="d-none d-md-block tab-content bg-white" id="myTabContent">
                    <div class="tab-pane fade show active" id="applications" role="tabpanel" aria-labelledby="applications-tab">
                        <ul class="list-group">

                            <template v-if="(notSubmittedApps == undefined || notSubmittedApps == '') && (submittedApps == undefined || submittedApps == '')">
                                <li class="list-group-item">You have no applications</li>
                            </template>
                            <template v-if="notSubmittedApps != undefined && notSubmittedApps != ''">
                                <li class="list-group-item list-group-item-warning text-dark">Application NOT Submitted</li>
                                <a v-for="app in notSubmittedApps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-application-status/' + app[0].ApplicationNumber">
                                    <span class="btn btn-link">#{{ app[0].ApplicationNumber | formatAppNumber }}</span>
                                    <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app[0].StudyStartDate}}</span>
                                    <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app[0].StudyEndDate}}</span>
                                    <div class="float-right m-2">
                                        <span v-if="app[0].AppendixReminder == 'Y'" class="d-none d-md-inline mr-1">No longer required</span>
                                        <span class="badge badge-warning p-2">Edit</span>
                                    </div>
                                </a>

                            </template>

                            <template v-if="submittedApps != undefined && submittedApps != ''">
                                <li class="list-group-item list-group-item-success text-dark">Application Submitted</li>
                                <a v-for="app in submittedApps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-application-status/' + app[0].ApplicationNumber">
                                    <span class="btn btn-link">#{{ app[0].ApplicationNumber | formatAppNumber }}</span>
                                    <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app[0].StudyStartDate}}</span>
                                    <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app[0].StudyEndDate}}</span>
                                    <div class="float-right m-2">
                                        <span v-if="app[0].InkSigRequired == 'Y'" class="d-none d-md-inline mr-1">Declaration Required</span>
                                        <span class="badge badge-success p-2">View</span>
                                    </div>
                                </a>
                            </template>

                        </ul>
                    </div>
                    <div v-if="appx != undefined && appx != '' && appx.Appendix1 != null" class="tab-pane fade" id="appx1" role="tabpanel" aria-labelledby="appx1-tab">
                        <ul class="list-group">
                            <template v-if="(appx.Appendix1.NotSubmitted == undefined || appx.Appendix1.NotSubmitted == '') && (appx.Appendix1.Submitted == undefined || appx.Appendix1.Submitted == '')">
                                <li class="list-group-item">You have no appendix 1</li>
                            </template>

                            <template v-if="appx.Appendix1.NotSubmitted != ''">
                                <li class="list-group-item list-group-item-warning text-dark">Appendix 1 NOT Submitted</li>
                                <template v-for="apps in appx.Appendix1.NotSubmitted">
                                    <a v-for="app in apps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-appendix-status/' + app.ApplicationNumber + '/' + app.FormGUID">
                                        <span class="btn btn-link">#{{ app.ApplicationNumber | formatAppNumber }}</span>
                                        <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app.StudyStartDate}}</span>
                                        <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app.StudyEndDate}}</span>
                                        <div class="float-right m-2">
                                            <span v-if="app.AppendixReminder == 'Y'" class="d-none d-md-inline mr-1">No longer required</span>
                                            <span class="badge badge-warning p-2">Edit</span>
                                        </div>
                                    </a>
                                </template>

                            </template>

                            <template v-if="appx.Appendix1.Submitted != ''">
                                <li class="list-group-item list-group-item-success text-dark">Appendix 1 Submitted</li>
                                <template v-for="apps in appx.Appendix1.Submitted">
                                    <a v-for="app in apps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-appendix-status/' + app.ApplicationNumber">
                                        <span class="btn btn-link">#{{ app.ApplicationNumber | formatAppNumber }}</span>
                                        <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app.StudyStartDate}}</span>
                                        <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app.StudyEndDate}}</span>
                                        <div class="float-right m-2">
                                            <span class="badge badge-success p-2">View</span>
                                        </div>
                                    </a>
                                </template>
                            </template>
                        </ul>
                    </div>
                    <div v-if="appx != undefined && appx != '' && appx.Appendix2 != null" class="tab-pane fade" id="appx2" role="tabpanel" aria-labelledby="appx2-tab">
                        <ul class="list-group">
                            <template v-if="appx.Appendix2.NotSubmitted != ''">
                                <li class="list-group-item list-group-item-warning text-dark">Appendix 2 NOT Submitted</li>
                                <template v-for="apps in appx.Appendix2.NotSubmitted">
                                    <a v-for="app in apps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-appendix-status/' + app.ApplicationNumber">
                                        <!--                                        <a v-for="app in apps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-appendix-status/' + app.ApplicationNumber + '/' + app.FormGUID">-->
                                        <span class="btn btn-link">#{{ app.ApplicationNumber | formatAppNumber }}</span>
                                        <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app.StudyStartDate}}</span>
                                        <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app.StudyEndDate}}</span>
                                        <div class="float-right m-2">
                                            <span v-if="app.AppendixReminder == 'Y'" class="d-none d-md-inline mr-1">No longer required</span>
                                            <span class="badge badge-warning p-2">Edit</span>
                                        </div>
                                    </a>
                                </template>

                            </template>

                            <template v-if="appx.Appendix2.Submitted != ''">
                                <li class="list-group-item list-group-item-success text-dark">Appendix 2 Submitted</li>
                                <template v-for="apps in appx.Appendix2.Submitted">
                                    <a v-for="app in apps" class="list-group-item list-group-item-action" :href="'/dashboard/student-loans/check-appendix-status/' + app.ApplicationNumber">
                                        <span class="btn btn-link">#{{ app.ApplicationNumber | formatAppNumber }}</span>
                                        <strong class="d-none d-md-inline ml-4">Start: </strong><span class="d-none d-md-inline">{{ app.StudyStartDate}}</span>
                                        <strong class="d-none d-md-inline ml-4">End: </strong><span class="d-none d-md-inline">{{ app.StudyEndDate}}</span>
                                        <div class="float-right m-2">
                                            <!--                                            <span v-if="app.AppendixReminder == 'N'" class="mr-1">No longer required</span>-->
                                            <span class="badge badge-success p-2">View</span>
                                        </div>
                                    </a>
                                </template>
                            </template>
                        </ul>
                    </div>

                </div>
            </template>
        </div>
        <div class="modal fade in" id="require_bcsc" tabindex="-1" role="dialog" aria-hidden="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
<!--                    <div class="modal-header">-->
<!--                        <h4 class="modal-title">Your StudentAid BC Dashboard login access is changing</h4>-->
<!--                        &lt;!&ndash;                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>&ndash;&gt;-->
<!--                    </div>-->
                    <div class="modal-body">
<!--                        <p>Do you know you can use a BC Services Card account to log in and access StudentAid BC programs and services via the dashboard. Setting up the BC Services Card app is safe, quick, and easy.</p>-->
                        <ul class="">
                            <li class="mb-3">All users (students, parents, and partners) should use a BC Services Card account to log in to StudentAid BC. </li>
                            <li class="mb-3">Soon, StudentAid BC will stop permitting users to log in using the old user ID and password, and you may not be able to access your StudentAid BC account or our online application. </li>
                            <li class="mb-3">To avoid potential delays in accessing your account and submitting your application, we recommend that you <a href="https://id.gov.bc.ca/account/" target="_blank">set up a BC Services Card account</a> as soon as possible.</li>
                        </ul>
<!--                        <p><a href="https://www2.gov.bc.ca/gov/content?id=034E906146794F84A00535A3F9354913" target="_blank">Learn how to set up a BC Services Card account</a></p>-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal" @click="openModal(0)">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /.row -->
</template>
<style scoped>
.accordion .card-header{
    background-color: rgba(0,0,0,.03);
    border-bottom: 1px solid rgb(255 255 255);
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
</style>
<script>
import axios from 'axios';

export default {
    filters: {
        formatAppNumber: function(value){
            let year = value.slice(0, 4);
            let extra = value.slice(4);

            return year + '-' + extra;
        }
    },
    props: ['errors'],
    data: () => ({
        dashboard: "",
        apps: '',
        submittedApps: '',
        notSubmittedApps: '',
        appx: '',
        newMsgs: 0,
        loading: true,
        loadingError: false,
        errorMsg: '',
        maintenanceMode: false,
        validationErrors: '',
    }),
    methods: {
        disablePage: function(e){
            if(e === true)
                this.maintenanceMode = true;
        },

        openModal: function(which){
            if(which === 1) $('#require_bcsc').modal('toggle');
        },
        fetchData: function(){
            this.loading = true;
            var vm = this;
            axios({
                url: '/dashboard/fetch-dashboard',
                //data: formData,
                method: 'get',
                //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                headers: {'Accept': 'application/json'}
            })

                //axios.get( '/fetch-dashboard' )
                .then(function (response) {

                    vm.loading = false;

                    if(response.data.profile.status != undefined && response.data.profile.status === false){
                        vm.loadingError = true;
                        vm.errorMsg = response.data.profile.msg;
                    }else{
                        vm.apps = response.data.apps;
                        vm.submittedApps = response.data.apps.Submitted;
                        vm.notSubmittedApps = response.data.apps.NotSubmitted;
                        vm.appx = response.data.appx;
                        vm.newMsgs = response.data.new_messages;
                    }

                    if(response.data.profile.userProfile.require_bcsc_account == true){
                        vm.openModal(1);
                    }
                })
                .catch(function (error) {
                    vm.loading = false;
                    vm.loadingError = true;
                    console.log(error);
                });
        }

    },
    computed: {
        numberApps: function(){
            if(this.apps.constructor === Object && Object.keys(this.apps).length > 0){
                let notSubmitted = this.apps.NotSubmitted == undefined ? 0 : Object.keys(this.apps.NotSubmitted).length;
                let submitted = this.apps.Submitted == undefined ? 0 : Object.keys(this.apps.Submitted).length;
                return notSubmitted + submitted;
            }
            return 0;
        },
        numberAppx1: function(){
            if(this.appx.constructor === Object && this.appx.Appendix1 != undefined){
                return this.appx.Appendix1.total;
            }
            return 0;
        },
        numberAppx2: function(){
            if(this.appx.constructor === Object && this.appx.Appendix2 != undefined){
                return this.appx.Appendix2.total;
            }
            return 0;
        },
    },
    created() {

    },
    mounted: function () {
        this.fetchData();
        document.title = "StudentAidBC - Dashboard";

        if(this.errors != "")
            this.validationErrors = JSON.parse(this.errors);

    },
    watch: {

    }

}

</script>
