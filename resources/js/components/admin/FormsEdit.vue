<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">Edit Appeal</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div v-if="errors != ''" class="row">
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <template v-for="(error, i) in validationErrors" :key="i">
                                        <template v-for="(e, j) in error" :key="j">
                                            <p class="alert-p">
                                                <span v-html="e"></span>
                                                <br v-if="i === 1" />
                                            </p>
                                        </template>
                                    </template>                                </div>
                            </div><!-- /.block -->
                        </div>
                        <form method="post" :action="'/dashboard/admin/forms/edit/' + formData.id" enctype="multipart/form-data">
                            <input type="hidden" name="_token" :value="csrf">

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="name" name="name" required v-model="parsedOld['name']">
                                <input v-else type="text" class="form-control" id="name" name="name" required v-model="formData.name">
                            </div>

                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select v-if="parsedOld != ''" class="form-control" id="category_id" name="category_id" required v-model="parsedOld['category_id']">
                                    <option v-for="cat in categories" :value="cat.id">{{cat.name}}</option>
                                </select>
                                <select v-else class="form-control" id="category_id" name="category_id" required v-model="formData.category_id">
                                    <option v-for="cat in categories" :value="cat.id">{{cat.name}}</option>
                                </select>
                            </div>


                            <div class="form-group">
                                <label for="program_year">Program Year</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="program_year" name="program_year" required v-model="parsedOld['program_year']">
                                <input v-else type="text" class="form-control" id="program_year" name="program_year" required v-model="formData.program_year">
                            </div>
                            <div class="form-group">
                                <label for="form_body">Form Body</label>
                                <textarea v-if="parsedOld != ''" rows="20" class="form-control" id="form_body" name="form_body" required v-model="parsedOld['form_body']"></textarea>
                                <textarea v-else rows="20" class="form-control" id="form_body" name="form_body" required v-model="formData.form_body"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select v-if="parsedOld != ''" class="form-control" id="status" name="status" required v-model="parsedOld['status']">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                </select>
                                <select v-else class="form-control" id="status" name="status" required v-model="formStatus">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="publish">Publish</label>
                                <select v-if="parsedOld != ''" class="form-control" id="publish" name="publish" required v-model="parsedOld['publish']">
                                    <option value="unpublished">Not Published</option>
                                    <option value="published">Published</option>
                                </select>
                                <select v-else class="form-control" id="publish" name="publish" required v-model="formPublish">
                                    <option value="unpublished">Not Published</option>
                                    <option value="published">Published</option>
                                </select>
                            </div>



                            <button type="submit" class="btn btn-primary">Update</button>
                            <button @click="deleteForm" type="button" class="btn btn-danger float-right">Delete</button>
                        </form>
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
            formData: '',
            formStatus: 'draft',
            formPublish: 'unpublished',
            rows: 0,
            fields: [],
            categories: [],
        }),
        props: ['errors', 'form', 'old'],
        methods: {
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/admin/categories/fetch',
                    method: 'get',
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {
                        vm.categories = response.data.categories;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            deleteForm: function(){
                var check = confirm('Are you sure you want to delete this form?');
                if(check == true){
                    this.loading = true;
                    var vm = this;
                    axios({
                        url: '/dashboard/admin/forms/delete/' + this.formData.id,
                        //data: formData,
                        method: 'get',
                        //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                        headers: {'Accept': 'application/json'}
                    })

                        //axios.get( '/fetch-dashboard' )
                        .then(function (response) {

                            vm.loading = false;
                            //vm.pages = response.data.pages;
                            window.location.href = '/dashboard/admin/forms';

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
            this.fetchData();
            document.title = "StudentAidBC - Admin Pages - Edit Form";
            if(this.errors != ""){
                this.validationErrors = JSON.parse(this.errors);
                this.parsedOld = JSON.parse(this.old);
            }
            this.formData = JSON.parse(this.form);


            this.formStatus = this.formData.status === 1 ? 'active' : 'draft';
            this.formPublish = this.formData.publish === 1 ? 'published' : 'unpublished';
        },
        watch: {
        }

    }
</script>
