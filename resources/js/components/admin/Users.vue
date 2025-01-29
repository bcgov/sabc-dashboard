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

                        <table v-if="users !== ''" class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="fit" scope="col">
                                    <span @dblclick="filterTbl('username')" v-show="tblFilter != 'username'">Username</span>
                                    <input @keyup.esc="clear" @keyup.enter="search" v-show="tblFilter == 'username'" :focus="tblFilter == 'username'" type="text" class="form-control" placeholder="Search by username" v-model="searchUsername">
                                </th>
                                <th scope="col">
                                    <span @dblclick="filterTbl('status')" v-show="tblFilter != 'status'">Status</span>
                                    <select v-show="tblFilter == 'status'" class="form-control" @change="filterStatus" v-model="searchStatus">
                                        <option value="all">-- CLEAR --</option>
                                        <option value="1">Active</option>
                                        <option value="0">Disabled</option>
                                    </select>
                                </th>
                                <th class="fit" scope="col">
                                    <span @dblclick="filterTbl('role')" v-show="tblFilter != 'role'">Roles</span>
                                    <select v-show="tblFilter == 'role'" class="form-control" @change="filterRole" v-model="searchRole">
                                        <option value="clear">-- CLEAR --</option>
                                        <option value="administrator">Administrator</option>
                                        <option value="app_support">App Support</option>
                                        <option value="student">Student</option>
                                        <option value="spouse">Spouse</option>
                                        <option value="parent">Parent</option>
                                        <option value="parent_no_sin">Parent No Sin</option>
                                        <option value="spouse_no_sin">Spouse No Sin</option>
                                        <option value="bcsc_student">BCSC Sstudent</option>
                                        <option value="bcsc_spouse">BCSC Spouse</option>
                                        <option value="bcsc_parent">BCSC Parent</option>
                                        <option value="reviewer">Reviewer</option>
                                        <option value="editor">Editor</option>
                                    </select>
                                </th>
                                <th class="fit" scope="col">Last Access</th>
                                <th scope="col" style="width: 70px;"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="user in users">
                                <td class="fit" scope="row">{{user.name}}</td>
                                <td>{{user.status | formatStatus}}</td>
                                <td class="fit">
                                    <ul v-if="Array.isArray(user.roles) && user.roles.length > 0">
                                        <li v-for="role in user.roles">{{role.name}}</li>
                                    </ul>
                                </td>
                                <td class="fit">{{user.updated_at | formatDate}}</td>
                                <td><a :href="'/dashboard/admin/users/edit/' + user.uid" class="btn btn-sm btn-primary">Edit</a></td>
                            </tr>
                            </tbody>
                        </table>

                        <nav v-if="paginateObj != ''" aria-label="...">
                            <ul class="pagination">
                                <li class="page-item" :class="page == 1 ? 'disabled' : ''">
                                    <button @click="goTo('previous')" class="page-link" type="button" tabindex="-1" :aria-disabled="page == 1 ? 'true' : 'false'">Previous</button>
                                </li>
                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                <li class="page-item active" aria-current="page">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item" :class="page == paginateObj.last_page ? 'disabled' : ''">
                                    <button @click="goTo('next')" class="page-link" type="button" tabindex="-1" :aria-disabled="page == paginateObj.last_page ? 'true' : 'false'">Next</button>
                                </li>
                            </ul>
                        </nav>

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
