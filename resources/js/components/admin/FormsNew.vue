<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">New Form</h4>
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
                        <form method="post" action="/dashboard/admin/forms/new" enctype="multipart/form-data">
                            <input type="hidden" name="_token" :value="csrf">

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required v-model="parsedOld['name']">
                            </div>

                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-control" id="category_id" name="category_id" required v-model="parsedOld['category_id']">
                                    <option v-for="cat in categories" :value="cat.id">{{cat.name}}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="program_year">Program Year</label>
                                <input type="text" class="form-control" id="program_year" name="program_year" required v-model="parsedOld['program_year']">
                            </div>
                            <div class="form-group">
                                <label for="form_body">Form Body</label>
                                <textarea rows="20" class="form-control" id="form_body" name="form_body" required v-model="parsedOld['form_body']"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required v-model="parsedOld['status']">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="publish">Publish</label>
                                <select class="form-control" id="publish" name="publish" required v-model="parsedOld['publish']">
                                    <option value="unpublished">Not Published</option>
                                    <option value="published">Published</option>
                                </select>
                            </div>



                            <button type="submit" class="btn btn-primary">Submit</button>
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
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            validationErrors: '',
            parsedOld: '',
            categories: [],
        }),
        props: ['errors', 'old'],
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

            document.title = "StudentAidBC - Admin Pages - Create New Form";
            if(this.errors != ""){
                this.validationErrors = JSON.parse(this.errors);
                this.parsedOld = JSON.parse(this.old);
            }
        },
        watch: {
        }

    }
</script>
