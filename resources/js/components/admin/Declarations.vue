<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">Declarations <a href="/dashboard/admin/declarations/new" class="btn btn-success float-right btn-sm">Create New</a></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <table v-if="declarations !== ''" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-primary" scope="col" @click="sortBy('name')">Name</th>
                                    <th class="fit text-primary" scope="col" @click="sortBy('type')">Type</th>
                                    <th class="fit text-primary" scope="col" @click="sortBy('type_id')">Type ID</th>
                                    <th class="fit text-primary" scope="col" @click="sortBy('program_year')">Program Year</th>
                                    <th class="text-primary" scope="col" @click="sortBy('status')">Status</th>
                                    <th class="fit text-primary" scope="col" @click="sortBy('updated_at')">Last Modified</th>
                                    <th scope="col" style="width: 70px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="declaration in declarations">
                                    <td scope="row">{{declaration.name}}</td>
                                    <td class="fit">{{declaration.type}}</td>
                                    <td class="fit">{{declaration.type_id}}</td>
                                    <td class="fit">{{declaration.program_year}}</td>
                                    <td>
                                        <span v-if="declaration.status === 1">Active</span>
                                        <span v-else>Draft</span>
                                    </td>
                                    <td class="fit">{{declaration.updated_at | formatDate}}</td>
                                    <td><a :href="'/dashboard/admin/declarations/edit/' + declaration.id" class="btn btn-sm btn-primary">Edit</a></td>
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
            declarations: '',
            sortType: 'asc',
        }),
        props: [],
        methods: {
            sortBy: function(clmn){
                if(this.sortType == 'asc'){
                    this.declarations = this.declarations.sort(function(a, b){ return (a[clmn] > b[clmn]) ? -1 : 1; });
                    this.sortType = 'desc';
                }else{
                    this.declarations = this.declarations.reverse(function(a, b){ return (a[clmn] > b[clmn]) ? -1 : 1; });
                    this.sortType = 'asc';
                }
            },
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/admin/fetch-declarations',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.declarations = response.data.declarations;


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
            document.title = "StudentAidBC - Admin Declarations";
        },
        watch: {
        }

    }
</script>
