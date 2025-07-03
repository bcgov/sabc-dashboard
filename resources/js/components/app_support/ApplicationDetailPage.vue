<template>
    <div class="container-fluid">
        <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
        <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>

        <div v-if="loading == false && loadingError == false" class="row">
            <div class="col-12" v-if="application !== ''">
                <div class="card" v-if="application.applicationProfile != undefined && Object.keys(application.applicationProfile).length > 0 ">
                    <div class="card-header"><h5>Application Profile</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3 text-right">Application No.</div>
                            <div class="col-sm-3">{{application.applicationNumber}}</div>
                            <div class="col-sm-3 text-right">Status</div>
                            <div class="col-sm-3">{{application.applicationStatus}}</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 text-right">First Name</div>
                            <div class="col-sm-3">{{application.applicationProfile.firstName}}</div>
                            <div class="col-sm-3 text-right">Last Name</div>
                            <div class="col-sm-3">{{application.applicationProfile.lastName}}</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 text-right">Start Date</div>
                            <div class="col-sm-3">{{ formatApplicationDate(application.applicationProfile.studySDate) }}</div>
                            <div class="col-sm-3 text-right">End Date</div>
                            <div class="col-sm-3">{{ formatApplicationDate(application.applicationProfile.studyEDate) }}</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 text-right">Birth Date</div>
                            <div class="col-sm-3">{{ formatApplicationDate(application.applicationProfile.birthDate) }}</div>
                            <div class="col-sm-3 text-right">Program Year</div>
                            <div class="col-sm-3">{{application.applicationProfile.programYear}}</div>
                        </div>
                        <template v-if="application.applicationProfile.institution != undefined && Object.keys(application.applicationProfile.institution).length > 0 ">
                            <div class="row">
                                <div class="col-sm-3 text-right">School Name</div>
                                <div class="col-sm-3">{{application.applicationProfile.institution.schoolName}}</div>
                                <div class="col-sm-3 text-right">Designation Status</div>
                                <div class="col-sm-3">{{application.applicationProfile.institution.designationStatus}}</div>
                            </div>
                        </template>

                    </div>
                </div>

                <div class="card" v-if="application.applicationTimeline != undefined && Object.keys(application.applicationTimeline).length > 0">
                    <div class="card-header"><h5>Timeline <small class="badge badge-info float-right">{{application.applicationTimeline.applicationTimelineCode}}</small> </h5></div>
                    <div class="card-body">
                        <div v-for="e in application.applicationTimeline.EventCategories.eventCategory" class="row mb-3">

                            <template v-if="e.eventItems != undefined && Object.keys(e.eventItems).length > 0">
                                <div class="col-sm-3 text-right">{{e.eventCategoryName}}</div>
                                <div class="col-sm-9">
                                    <h5>{{e.eventCategoryCode}}</h5>
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <template v-if="Array.isArray(e.eventItems.eventItem)">
                                                <div v-for="item in e.eventItems.eventItem" class="row">
                                                    <div class="col-sm-3">{{item.eventType}}</div>
                                                    <div class="col-sm-2">{{ formatApplicationDate(item.eventDate) }}</div>
                                                    <div class="col-sm-2">{{item.eventCode}}</div>
                                                    <div class="col-sm-5">{{item.eventDescription}}</div>
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


                <div class="card" v-if="application.pdfLetters != undefined && Object.keys(application.pdfLetters).length > 0">
                    <div class="card-header"><h5>PDF LETTERS</h5></div>
                    <div class="card-body">

                        <template v-for="(letter, key, index) in application.pdfLetters">
                            <template v-if="Array.isArray(letter)">
                                <div v-for="(value, name) in letter" class="row">
                                    <div class="col-md-4 text-right"><small>{{ formatApplicationDate(value.sendDate) }}</small></div>
                                    <div class="col-md-8">
                                        <a :href="'/dashboard/app_support/fetch-application-notification/' + value.letterID + '/' + (application.applicationStatus == 'SUBMPROC' ? 'T' : 'R') + ''" target="_blank">{{value.letterDescription}}</a>
                                    </div>
                                </div>
                            </template>
                            <div v-else class="row">
                                <div class="col-md-4 text-right"><small>{{ formatApplicationDate(letter.sendDate) }}</small></div>
                                <div class="col-md-8">
                                    <a :href="'/dashboard/app_support/fetch-application-notification/' + letter.letterID + '/' + (application.applicationStatus == 'SUBMPROC' ? 'T' : 'R') + ''" target="_blank">{{letter.letterDescription}}</a>
                                </div>
                            </div>
                        </template>

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
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            application: '',
        }),
        props: ['appnumber'],
        methods: {
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
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/app_support/fetch-application-detail/' + this.appnumber,
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.application = response.data.application.applicationDetails;


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
            document.title = "StudentAidBC - Applications App Support";
            // this.applications = JSON.parse(this.apps);
        },
        watch: {
        }

    }
</script>
