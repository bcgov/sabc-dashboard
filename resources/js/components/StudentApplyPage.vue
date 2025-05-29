<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">
            <div class="taskbar">
                <h4>Student Apply</h4>
                <hr class="mt-0"/>
            </div>

            <div class="card">
                <div class="card-header">Select the institution you want to attend</div>
                <div class="card-body">

                    <div class="">
                        <div class="">
                            <div class="">
                                <table id="institution" class="table table-bordered">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th>Institution<span class="icon-sortbynamedescending"></span><span
                                            class="icon-sortbynameascending"></span><span class="icon-sync2"></span>
                                        </th>
                                        <th>City<span class="icon-sortbynamedescending"></span><span
                                            class="icon-sortbynameascending"></span><span class="icon-sync2"></span>
                                        </th>
                                        <th>Province<span class="icon-sortbynamedescending"></span><span
                                            class="icon-sortbynameascending"></span><span class="icon-sync2"></span>
                                        </th>
                                        <th>Country<span class="icon-sortbynamedescending"></span><span
                                            class="icon-sortbynameascending"></span><span class="icon-sync2"></span>
                                        </th>
                                        <th>Designated<span class="icon-sortbynamedescending"></span><span
                                            class="icon-sortbynameascending"></span><span class="icon-sync2"></span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <noscript>JavaScript is disabled in your browser. Please enable it to use this feature.</noscript>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- /.card -->
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
<style >
    #institution_wrapper .btn-light {
        color: #212529;
        background-color: #e2e2e2;
        border-color: #c3c3c3;
        font-size: 13px;
        padding: 6px;
    }
    #institution_wrapper .alphabet-filter-widgets{

    }
    #institution_filter{
        padding: 0;
    }

    #institution_filter label{
        width: 100%;
    }
    #institution_filter input{
        display: block;
        height: 42px;
        line-height: 40px;
        padding: 8px 15px;
        margin-bottom: 12px;
        color: #34495e;
        border-radius: 3px;
        vertical-align: middle;
        width: 100%;
        margin-left: 0;
    }
    #institution_wrapper div#institution_processing{
        position: relative;
        top: initial;
        left: initial;
        width: initial;
        margin-left: initial;
        margin-top: initial;
        text-align: initial;
        padding: initial;
    }
    #institution_wrapper table#institution{
        width: 100% !important;
        font-size: 13px !important;
    }

</style>
<script>
    import axios from 'axios';
    import ProfileChallengeQuestions from "./ProfileChallengeQuestions";

    export default {
        // components: {ProfileChallengeQuestions},
        filters: {

            // formatAppNumber: function(value){
            //     let year = value.slice(0, 4);
            //     let extra = value.slice(3);
            //
            //     return year + '-' + extra;
            // }
        },

        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            profile: '',
            maintenanceMode: false,

        }),
        props: [],
        methods: {
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },
            // fetchData: function(){
            //     this.loading = true;
            //     var vm = this;
            //     axios({
            //         url: '/fetch-profile',
            //         //data: formData,
            //         method: 'get',
            //         //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
            //         headers: {'Accept': 'application/json'}
            //     })
            //
            //     //axios.get( '/fetch-dashboard' )
            //         .then(function (response) {
            //
            //             vm.loading = false;
            //             vm.profile = response.data.profile;
            //             vm.countries = response.data.countries;
            //             vm.provinces = response.data.provinces;
            //             vm.states = response.data.states;
            //             vm.confirmEmailAddress = response.data.profile.userProfile.emailAddress;
            //             vm.formatElements();
            //
            //         })
            //         .catch(function (error) {
            //             vm.loading = false;
            //             vm.loadingError = true;
            //             console.log(error);
            //         });
            // },
        },
        computed: {
            // fullName: function(){
            //     let name = "";
            //   if(this.profile != ''){
            //       if(this.profile.userProfile.middleName != undefined)
            //         name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.middleName + " " + this.profile.userProfile.familyName);
            //         else
            //             name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.familyName);
            //   }
            //   return name.toUpperCase();
            // },

        },
        created() {
            // window.vueDocument.title = "StudentAidBC - Apply for a student loan";

        },
        mounted: function () {
            //this.fetchData();
            // window.vueDocument.title = "StudentAidBC - Apply for a student loan";
            let s0 = document.createElement( 'link' );
            s0.setAttribute( 'rel', "stylesheet" );
            s0.setAttribute( 'type', "text/css" );
            s0.setAttribute( 'href', "/dashboard/css/sabc_institution.css?qitny6" );
            document.head.appendChild( s0 );

            setTimeout(function () {
                let s1 = document.createElement( 'script' );
                s1.setAttribute( 'src', "/dashboard/js/jquery.dataTables.min.js?qitny6" );
                document.body.appendChild( s1 );
            }, 1000);
            setTimeout(function () {
                let s2 = document.createElement( 'script' );
                s2.setAttribute( 'src', "/dashboard/js/sabc_institution.js?qitny6" );
                document.body.appendChild( s2 );

                document.title = "StudentAidBC - Apply for a student loan";
            }, 1500);

        },
        watch: {
            '$route' (to, from) {
                // document.title = 'StudentAidBC - Apply for a student loan'
            }
        },

    }

</script>
