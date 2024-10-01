<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">Side Regions <a href="/dashboard/admin/side-pages/new" class="btn btn-success float-right btn-sm">Create New</a></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body table-responsive">

                        <table v-if="pages !== ''" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="fit" scope="col">Name</th>
                                    <th class="fit" scope="col">URL</th>
                                    <th scope="col">Status</th>
                                    <th class="fit" scope="col">Modified By</th>
                                    <th class="fit" scope="col">Last Modified</th>
                                    <th scope="col" style="width: 70px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="page in pages">
                                    <td class="fit" scope="row">{{page.name}}</td>
                                    <td class="fit">{{page.url}}</td>
                                    <td>{{page.status}}</td>
                                    <td class="fit">{{page.modified_by}}</td>
                                    <td class="fit">{{page.updated_at | formatDate}}</td>
                                    <td><a :href="'/dashboard/admin/side-pages/edit/' + page.id" class="btn btn-sm btn-primary">Edit</a></td>
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
            pages: '',

        }),
        props: [],
        methods: {
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/admin/fetch-side-pages',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.pages = response.data.pages;

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
            document.title = "StudentAidBC - Admin Pages";
        },
        watch: {
        }

    }
</script>
