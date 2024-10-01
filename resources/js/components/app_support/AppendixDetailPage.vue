<template>
    <div class="container-fluid">
        <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
        <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>

        <div v-if="loading == false && loadingError == false" class="row">
            <div class="col-12" v-if="appendix !== '' && appendix != false">
                <div class="card">
                    <div class="card-header"><h5>Appendix Details</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3 text-right">Appendix Type</div>
                            <div class="col-sm-3">{{appendix.formType}}</div>
                            <div class="col-sm-3 text-right">Status</div>
                            <div class="col-sm-3">{{appendix.apxStatus}}</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 text-right">First Name</div>
                            <div class="col-sm-3">{{appendix.firstName}}</div>
                            <div class="col-sm-3 text-right">Last Name</div>
                            <div class="col-sm-3">{{appendix.lastName}}</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 text-right">Start Date</div>
                            <div class="col-sm-3">{{appendix.studyStartDate | formatApplicationDate}}</div>
                            <div class="col-sm-3 text-right">End Date</div>
                            <div class="col-sm-3">{{appendix.studyEndDate | formatApplicationDate}}</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 text-right">Doc. ID</div>
                            <div class="col-sm-3">{{appendix.documentID}}</div>
                            <div class="col-sm-3 text-right">Program Year</div>
                            <div class="col-sm-3">{{appendix.programYear}}</div>
                        </div>


                    </div>
                </div>

                <div class="card" v-if="appendix.appendixTimeline != undefined && Object.keys(appendix.appendixTimeline).length > 0">
                    <div class="card-header"><h5>Timeline <small class="badge badge-info float-right">{{appendix.appendixTimeline.appendixTimelineCode}}</small> </h5></div>
                    <div class="card-body">
                        <div v-for="e in appendix.appendixTimeline.EventCategories.eventCategory" class="row mb-3">

                            <template v-if="e.eventItems != undefined && Object.keys(e.eventItems).length > 0">
                                <div class="col-sm-4 text-right">{{e.eventCategoryName}}</div>
                                <div class="col-sm-8">
                                    <h5>{{e.eventCategoryCode}}</h5>
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <template v-if="Array.isArray(e.eventItems.eventItem)">
                                                <div v-for="item in e.eventItems.eventItem" class="row">
                                                    <div class="col-sm-3">{{item.eventDate | formatApplicationDate}}</div>
                                                    <div class="col-sm-3">{{item.eventCode}}</div>
                                                    <div class="col-sm-6">{{item.eventDescription}}</div>
                                                </div>
                                            </template>
                                            <div v-else class="row">
                                                <div class="col-sm-3">{{e.eventItems.eventItem.eventCode}}</div>
                                                <div class="col-sm-9">{{e.eventItems.eventItem.eventDescription}}</div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </template>

                        </div>

                    </div>
                </div>


                <div class="card" v-if="details.submitStatus != undefined && details.submitStatus != false">
                    <div class="card-header"><h5>PDF LETTERS</h5></div>
                    <div class="card-body">

                            <div v-if="(appendix.documentID != undefined && appendix.documentID != false) || (appendix.formGUID != undefined && appendix.formGUID != false)" class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    <a :href="'/dashboard/app_support/fetch-application-notification/' + (appendix.documentID != undefined && appendix.documentID != false ? appendix.documentID : appendix.formGUID) + '/' + (appendix.documentID != undefined && appendix.documentID != false ? 'R' : 'T') + '/appendix'" target="_blank">{{appendix.formType}}</a>
                                </div>
                            </div>

                    </div>
                </div>


            </div>
        </div>
    </div>
</template>
<style scoped>
    .table td.fit,
    .table th.fit {
        white-space: nowrap;
        /*width: 1%;*/
    }
</style>
<script>
    import axios from 'axios';

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

        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            appendix: '',
            details: '',
        }),
        props: ['appnumber'],
        methods: {
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/app_support/fetch-appendix-detail/' + this.appnumber,
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.appendix = response.data.application;
                        vm.details = response.data.details;


                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        console.log(error);
                    });
            },
        },
        computed: {
            fullName: function(){
                // let name = "";
                // if(this.profile != ''){
                //     if(this.profile.userProfile.middleName != undefined)
                //         name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.middleName + " " + this.profile.userProfile.familyName);
                //     else
                //         name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.familyName);
                // }
                // return name.toUpperCase();
            },

        },
        created() {

        },
        mounted: function () {
            this.fetchData();
            document.title = "StudentAidBC - Appendix App Support";
            // this.applications = JSON.parse(this.apps);
        },
        watch: {
        }

    }
</script>
