<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">Alerts <a href="/dashboard/admin/alerts/new" class="btn btn-success float-right btn-sm">Create New</a></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body table-responsive">

                        <table v-if="alerts !== ''" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="fit" scope="col">Name</th>
                                    <th class="fit" scope="col">Type</th>
                                    <th class="fit" scope="col">Pages</th>
                                    <th class="fit" scope="col">Start Time</th>
                                    <th class="fit" scope="col">End Time</th>
                                    <th scope="col">Status</th>
                                    <th class="fit" scope="col">Disable Page</th>
                                    <th class="fit" scope="col">Last Modified</th>
                                    <th scope="col" style="width: 70px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="alert in alerts">
                                    <td class="fit" scope="row">{{alert.name}}</td>
                                    <td class="fit"><span class="alert" :class="'alert-' + alert.type">{{alertTypes[alert.type]}}</span></td>
                                    <td class="fit">{{pages[alert.pages]}}</td>
                                    <td class="fit">{{ formatDate(alert.start_time) }}</td>
                                    <td class="fit">{{ formatDate(alert.end_time) }}</td>
                                    <td>{{alert.status}}</td>
                                    <td v-if="alert.disable_page === false" class="fit">{{alert.disable_page}}</td>
                                    <td v-else class="fit"><span class="alert alert-danger">{{alert.disable_page}}</span></td>
                                    <td class="fit">{{ formatDate(alert.updated_at) }}</td>
                                    <td><a :href="'/dashboard/admin/alerts/edit/' + alert.id" class="btn btn-sm btn-primary">Edit</a></td>
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
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            alerts: '',
            alertTypes: {
                // "info": "Info - Light blue",
                "danger": "Error - Red",
                "success": "Success - Green",
                "warning": "Warning - Orange",
                // "primary": "Primary - Dark blue"
                "primary": "Info - Blue"
            },
            pages: {
                "login_register_pages": "Login/Register Pages",
                "auth_pages": "Authanticated Pages",
                "all": "All Pages"
                }
        }),
        props: [],
        methods: {
            formatDate: function (value) {
                if(value != undefined && value != ''){
                    var newValue = value.split("T");

                    return newValue[0];
                }
                return value;
            },
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/admin/fetch-alerts',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.alerts = response.data.alerts;

                        //switch the class for info to use primary instead ( admin uses a light colour for info that what dashboard uses )
                        for(var i=0; i<vm.alerts.length; i++){
                            if(vm.alerts[i].type == 'info'){
                                vm.alerts[i].type = 'primary';
                            }
                        }

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
            document.title = "StudentAidBC - Admin Alerts";
        },
        watch: {
        }

    }
</script>
