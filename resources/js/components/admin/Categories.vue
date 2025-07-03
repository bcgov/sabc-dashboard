<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">Categories <a href="/dashboard/admin/categories/new" class="btn btn-success float-right btn-sm">Create New</a></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table v-if="categories.length" class="table table-bordered">
                        <thead>
                                <tr>
                                    <th class="text-primary" scope="col" @click="sortBy('name')">Name</th>
                                    <th class="text-primary" scope="col" @click="sortBy('name')">Long Name</th>
                                    <th class="text-primary" scope="col" @click="sortBy('status')">Status</th>
                                    <th class="text-primary" scope="col" @click="sortBy('weight')">Order</th>
                                    <th class="fit text-primary" scope="col" @click="sortBy('updated_at')">Last Modified</th>
                                    <th scope="col" style="width: 70px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cat in categories">
                                    <td scope="row">{{cat.name}}</td>
                                    <td class="">{{cat.long_name}}</td>
                                    <td>
                                        <span v-if="cat.status === 1">Active</span>
                                        <span v-else>Draft</span>
                                    </td>
                                    <td class="fit"><input type="number" @keyup="updateWeight(cat)" v-model="cat.weight"/></td>
                                    <td class="fit">{{ formatDate(cat.updated_at) }}</td>
                                    <td><a :href="'/dashboard/admin/categories/edit/' + cat.id" class="btn btn-sm btn-primary">Edit</a></td>
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
            categories: [],
            sortType: 'asc',
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
            sortBy: function(clmn){
                if(this.sortType == 'asc'){
                    this.categories = this.categories.sort(function(a, b){ return (a[clmn] > b[clmn]) ? -1 : 1; });
                    this.sortType = 'desc';
                }else{
                    this.categories = this.categories.reverse(function(a, b){ return (a[clmn] > b[clmn]) ? -1 : 1; });
                    this.sortType = 'asc';
                }
            },
            updateWeight: function(cat){
                axios({
                    url: '/dashboard/admin/categories/edit-weight/' + cat.id + '/' + cat.weight,
                    method: 'get',
                    headers: {'Accept': 'application/json'}
                })
                    .then(function (response) {

                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/admin/categories/fetch',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.categories = response.data.categories;


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
            document.title = "StudentAidBC - Admin Categories";
        },
        watch: {
        }

    }
</script>
