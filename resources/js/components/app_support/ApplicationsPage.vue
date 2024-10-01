<template>
    <div class="container-fluid">
        <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
        <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>

        <div v-if="loading == false && loadingError == false" class="row">
            <div class="col-12" v-if="applications !== ''">
                <div class="card" v-if="applications.NotSubmitted != undefined && applications.NotSubmitted.length > 0">
                    <div class="card-header"><h5>Not Submitted</h5></div>
                    <div class="card-body">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Application No.</th>
                                    <th class="fit" scope="col">Start Date</th>
                                    <th class="fit" scope="col">End Date</th>
                                    <th class="fit" scope="col">Sign. Req.</th>
                                    <th class="fit" scope="col">Decl. Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="app in applications.NotSubmitted">
                                    <td scope="row"><a :href="'/dashboard/app_support/applications/' + app[0].ApplicationNumber">{{app[0].ApplicationNumber}}</a></td>
                                    <td class="fit">{{app[0].StudyStartDate}}</td>
                                    <td class="fit">{{app[0].StudyEndDate}}</td>
                                    <td class="fit">{{app[0].InkSigRequired}}</td>
                                    <td class="fit">{{app[0].DecStatus}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card" v-if="applications.Submitted != undefined && applications.Submitted.length > 0">
                    <div class="card-header"><h5>Submitted</h5></div>
                    <div class="card-body">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Application No.</th>
                                    <th class="fit" scope="col">Start Date</th>
                                    <th class="fit" scope="col">End Date</th>
                                    <th class="fit" scope="col">Sign. Req.</th>
                                    <th class="fit" scope="col">Decl. Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="app in applications.Submitted">
                                    <td scope="row"><a :href="'/dashboard/app_support/applications/' + app[0].ApplicationNumber">{{app[0].ApplicationNumber}}</a></td>
                                    <td class="fit">{{app[0].StudyStartDate}}</td>
                                    <td class="fit">{{app[0].StudyEndDate}}</td>
                                    <td class="fit">{{app[0].InkSigRequired}}</td>
                                    <td class="fit">{{app[0].DecStatus}}</td>
                                </tr>
                            </tbody>
                        </table>
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
                    var newValue = value.split("T");

                    return newValue[0];
                }
                return value;
            },

        },

        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            applications: '',
        }),
        props: [],
        methods: {
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/app_support/fetch-applications',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.applications = response.data.applications;


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
