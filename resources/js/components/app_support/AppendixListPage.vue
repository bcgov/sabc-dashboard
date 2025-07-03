<template>
    <div class="container-fluid">
        <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
        <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>

        <div v-if="loading == false && loadingError == false" class="row">
            <div class="col-12" v-if="appendix_list !== ''">

                <template v-if="appendix_list != undefined && appendix_list.Appendix1 != undefined && Object.keys(appendix_list.Appendix1).length > 0">
                    <div class="card" v-if="appendix_list.Appendix1.NotSubmitted != undefined && Array.isArray(appendix_list.Appendix1.NotSubmitted) == false && Object.keys(appendix_list.Appendix1.NotSubmitted).length > 0">
                        <div class="card-header"><h4>Appendix 1 <span class="badge badge-warning">not submitted</span></h4></div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Application No.</th>
                                        <th class="fit" scope="col">Start Date</th>
                                        <th class="fit" scope="col">End Date</th>
                                        <th class="fit" scope="col">Sign. Req.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="app in appendix_list.Appendix1.NotSubmitted">
                                        <td scope="row"><a :href="'/dashboard/app_support/appendix_list/' + app[0].ApplicationNumber">{{app[0].ApplicationNumber}}</a></td>
                                        <td class="fit">{{app[0].StudyStartDate}}</td>
                                        <td class="fit">{{app[0].StudyEndDate}}</td>
                                        <td class="fit">{{app[0].InkSigRequired}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card" v-if="appendix_list.Appendix1.Submitted != undefined && Array.isArray(appendix_list.Appendix1.Submitted) == false && Object.keys(appendix_list.Appendix1.Submitted).length > 0">
                        <div class="card-header"><h4>Appendix 1 <span class="badge badge-success">submitted</span></h4></div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Application No.</th>
                                        <th class="fit" scope="col">Start Date</th>
                                        <th class="fit" scope="col">End Date</th>
                                        <th class="fit" scope="col">Sign. Req.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="app in appendix_list.Appendix1.Submitted">
                                        <td scope="row"><a :href="'/dashboard/app_support/appendix_list/' + app[0].ApplicationNumber">{{app[0].ApplicationNumber}}</a></td>
                                        <td class="fit">{{app[0].StudyStartDate}}</td>
                                        <td class="fit">{{app[0].StudyEndDate}}</td>
                                        <td class="fit">{{app[0].InkSigRequired}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
                <hr/>
                <template v-if="appendix_list != undefined && appendix_list.Appendix2 != undefined && Object.keys(appendix_list.Appendix2).length > 0">
                    <div class="card" v-if="appendix_list.Appendix2.NotSubmitted != undefined && Array.isArray(appendix_list.Appendix2.NotSubmitted) == false && Object.keys(appendix_list.Appendix2.NotSubmitted).length > 0">
                        <div class="card-header"><h4>Appendix 2 <span class="badge badge-warning">not submitted</span></h4></div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col">Application No.</th>
                                    <th class="fit" scope="col">Start Date</th>
                                    <th class="fit" scope="col">End Date</th>
                                    <th class="fit" scope="col">Sign. Req.</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="app in appendix_list.Appendix2.NotSubmitted">
                                    <td scope="row"><a :href="'/dashboard/app_support/appendix_list/' + app[0].ApplicationNumber">{{app[0].ApplicationNumber}}</a></td>
                                    <td class="fit">{{app[0].StudyStartDate}}</td>
                                    <td class="fit">{{app[0].StudyEndDate}}</td>
                                    <td class="fit">{{app[0].InkSigRequired}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card" v-if="appendix_list.Appendix1.Submitted != undefined && Array.isArray(appendix_list.Appendix2.Submitted) == false && Object.keys(appendix_list.Appendix2.Submitted).length > 0">
                        <div class="card-header"><h4>Appendix 2 <span class="badge badge-success">submitted</span></h4></div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col">Application No.</th>
                                    <th class="fit" scope="col">Start Date</th>
                                    <th class="fit" scope="col">End Date</th>
                                    <th class="fit" scope="col">Sign. Req.</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="app in appendix_list.Appendix2.Submitted">
                                    <td scope="row"><a :href="'/dashboard/app_support/appendix_list/' + app[0].ApplicationNumber">{{app[0].ApplicationNumber}}</a></td>
                                    <td class="fit">{{app[0].StudyStartDate}}</td>
                                    <td class="fit">{{app[0].StudyEndDate}}</td>
                                    <td class="fit">{{app[0].InkSigRequired}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>



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
            appendix_list: '',
        }),
        props: [],
        methods: {
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/app_support/fetch-appendix_list',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.appendix_list = response.data.appendix_list;


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
            document.title = "StudentAidBC - Appendix List App Support";
            // this.applications = JSON.parse(this.apps);
        },
        watch: {
        }

    }
</script>
