<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">Edit Category</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div v-if="errors != ''" class="row">
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <template v-for="(error, i) in validationErrors"><p v-for="e in error" v-html="e"><br v-if="i==1"></p></template>
                                </div>
                            </div><!-- /.block -->
                        </div>
                        <form method="post" :action="'/dashboard/admin/categories/edit/' + categoryData.id" enctype="multipart/form-data">
                            <input type="hidden" name="_token" :value="csrf">
                            <input type="hidden" name="weight" :value="categoryData.weight">

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="name" name="name" required v-model="parsedOld['name']">
                                <input v-else type="text" class="form-control" id="name" name="name" required v-model="categoryData.name">
                            </div>

                            <div class="form-group">
                                <label for="name">Long Name</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="long_name" name="long_name" required v-model="parsedOld['long_name']">
                                <input v-else type="text" class="form-control" id="long_name" name="long_name" required v-model="categoryData.long_name">
                            </div>


                            <div class="form-group">
                                <label for="program_year">Program Year</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="program_year" name="program_year" v-model="parsedOld['program_year']">
                                <input v-else type="text" class="form-control" id="program_year" name="program_year" v-model="categoryData.program_year">
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select v-if="parsedOld != ''" class="form-control" id="status" name="status" required v-model="parsedOld['status']">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                </select>
                                <select v-else class="form-control" id="status" name="status" required v-model="categoryStatus">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                </select>
                            </div>



                            <button type="submit" class="btn btn-primary">Update</button>
                            <button @click="deleteCategory" type="button" class="btn btn-danger float-right">Delete</button>
                        </form>
                        <hr/>
                        <div v-if="categoryData.forms != null" class="card">
                            <div class="card-header">Forms</div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="text-primary" scope="col">Name</th>
                                        <th class="fit text-primary" scope="col">Program Year</th>
                                        <th class="text-primary" scope="col">Status</th>
                                        <th class="text-primary" scope="col">Publish</th>
                                        <th class="fit text-primary" scope="col">Order</th>
                                        <th scope="col" style="width: 70px;"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="form in categoryData.forms">
                                        <td scope="row">{{form.name}}</td>
                                        <td class="fit">{{form.program_year}}</td>
                                        <td>
                                            <span v-if="form.status === 1">Active</span>
                                            <span v-else>Draft</span>
                                        </td>
                                        <td>
                                            <span v-if="form.publish === 1">Published</span>
                                            <span v-else>Not Published</span>
                                        </td>
                                        <td class="fit"><input type="number" @keyup="updateWeight(form)" v-model="form.weight"></td>
                                        <td><a :href="'/dashboard/admin/forms/edit/' + form.id" class="btn btn-sm btn-primary">Edit</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
    fieldset {
        min-width: initial;
        padding: initial;
        margin: initial;
        border: initial;
    }
    legend {
        display: initial;
        width: initial;
        max-width: initial;
        padding: initial;
        margin-bottom: initial;
        font-size: initial;
        line-height: initial;
        color: initial;
        white-space: initial;
    }
</style>
<script>
    import axios from 'axios';

    export default {
        filters: {

        },

        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            validationErrors: '',
            parsedOld: '',
            categoryData: '',
            categoryStatus: 'draft',
        }),
        props: ['errors', 'category', 'old'],
        methods: {
            updateWeight: function(form){
                axios({
                    url: '/dashboard/admin/forms/edit-weight/' + form.id + '/' + form.weight,
                    method: 'get',
                    headers: {'Accept': 'application/json'}
                })
                    .then(function (response) {

                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            deleteCategory: function(){
                var check = confirm('Are you sure you want to delete this category? This category will be remove from all attached forms.');
                if(check == true){
                    this.loading = true;
                    var vm = this;
                    axios({
                        url: '/dashboard/admin/categories/delete/' + this.categoryData.id,
                        //data: formData,
                        method: 'get',
                        //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                        headers: {'Accept': 'application/json'}
                    })

                        //axios.get( '/fetch-dashboard' )
                        .then(function (response) {

                            vm.loading = false;
                            //vm.pages = response.data.pages;
                            window.location.href = '/dashboard/admin/categories';

                        })
                        .catch(function (error) {
                            vm.loading = false;
                            vm.loadingError = true;
                            console.log(error);
                        });
                }
                return true;
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
            document.title = "StudentAidBC - Admin Pages - Edit Category";
            if(this.errors != ""){
                this.validationErrors = JSON.parse(this.errors);
                this.parsedOld = JSON.parse(this.old);
            }
            this.categoryData = JSON.parse(this.category);


            this.categoryStatus = this.categoryData.status === 1 ? 'active' : 'draft';
        },
        watch: {
        }

    }
</script>
