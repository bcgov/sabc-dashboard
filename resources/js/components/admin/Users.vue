<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">Users <a href="/dashboard/admin/users/new" class="btn btn-success float-right btn-sm">Create New</a></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body table-responsive">

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
            formatStatus: function (value) {
                if(value != undefined && value != ''){
                    return value == 1 ? 'Active' : 'Disabled';
                }
                return '-';
            },

        },

        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            users: '',
            page: 1,
            paginateObj: '',

            searchUsername: '',
            searchStatus: '',
            searchRole: '',
            tblFilter: '',

        }),
        props: [],
        methods: {
            filterTbl: function(clmn){
              this.tblFilter = clmn;
            },
            search: function(){
                this.fetchData();
            },
            filterStatus: function(){
                if(this.searchStatus == 'all'){
                    this.clear();
                }else{
                    this.fetchData();
                }
            },
            filterRole: function(){
                if(this.searchRole == 'clear'){
                    this.clear();
                }else{
                    this.fetchData();
                }
            },
            clear: function(){
                this.tblFilter = '';
                this.searchUsername = '';
                this.searchStatus = '';
                this.searchRole = '';
                this.fetchData();

            },
            goTo: function(to){
                if(to == 'previous'){
                    if(this.page > 1)
                        this.page -= 1;
                }
                if(to == 'next'){
                    if(this.page < 5)
                        this.page += 1;
                }
                if(to != 'previous' && to != 'next'){
                    this.page = to;
                }
                this.fetchData();
            },
            fetchData: function(){
                var url = '/dashboard/admin/fetch-users?page=' + this.page;
                if(this.tblFilter == 'username'){
                    url += '&username=' + this.searchUsername;
                }
                if(this.tblFilter == 'status'){
                    url += '&status=' + this.searchStatus;
                }
                if(this.tblFilter == 'role'){
                    url += '&role=' + this.searchRole;
                }
                this.loading = true;
                var vm = this;
                axios({
                    url: url,
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.paginateObj = response.data.users;
                        vm.users = response.data.users.data;

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
            document.title = "StudentAidBC - Admin Access - Users";
        },
        watch: {
        }

    }
</script>
