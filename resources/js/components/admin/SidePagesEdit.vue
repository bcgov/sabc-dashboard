<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">Edit Side Region</h4>
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
                        <form method="post" :action="'/dashboard/admin/side-pages/edit/' + pageData.id" enctype="multipart/form-data">
                            <input type="hidden" name="_token" :value="csrf">

                            <div class="form-group">
                                <label for="page_name">Page Name</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="page_name" name="page_name" v-model="parsedOld['page_name']" required>
                                <input v-else type="text" class="form-control" id="page_name" name="page_name" v-model="pageData.name" required>
                            </div>
                            <div class="form-group">
                                <label for="page_path">Page Path</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="page_path" name="page_path" v-model="parsedOld['page_path']" aria-describedby="page_path_help">
                                <input v-else type="text" class="form-control" id="page_path" name="page_path" v-model="pageData.url" aria-describedby="page_path_help">
                                <small id="page_path_help" class="form-text text-muted">Do not include https://www.studentaidbc.ca <strong>include only the path ie /dashboard</strong></small>
                            </div>
                            <div class="form-group">
                                <label for="left_side">Left Side</label>
                                <template v-if="parsedOld != ''">
                                    <textarea class="form-control" id="left_side" name="left_side" rows="10" v-model="parsedOld['left_side']"></textarea>
                                </template>
                                <template v-else>
                                    <textarea v-if="pageData.status === 'draft'" class="form-control" id="left_side" name="left_side" rows="10" v-model="pageData.left_side_draft"></textarea>
                                    <textarea v-else class="form-control" id="left_side" name="left_side" rows="10" v-model="pageData.left_side"></textarea>
                                </template>
                            </div>
                            <div class="form-group">
                                <label for="right_side">Right Side</label>
                                <template v-if="parsedOld != ''">
                                    <textarea class="form-control" id="right_side" name="right_side" rows="10" v-model="parsedOld['right_side']"></textarea>
                                </template>
                                <template v-else>
                                    <textarea v-if="pageData.status === 'draft'" class="form-control" id="right_side" name="right_side" rows="10" v-model="pageData.right_side_draft"></textarea>
                                    <textarea v-else class="form-control" id="right_side" name="right_side" rows="10" v-model="pageData.right_side"></textarea>
                                </template>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <template v-if="parsedOld != ''">
                                    <select class="form-control" id="status" name="status" v-model="parsedOld['status']">
                                        <option value="draft">Draft</option>
                                        <option value="active">Active</option>
                                    </select>
                                </template>
                                <template v-else>
                                    <select class="form-control" id="status" name="status" v-model="pageData.status">
                                        <option value="draft">Draft</option>
                                        <option value="active">Active</option>
                                    </select>
                                </template>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>


</style>
<script>
    import axios from 'axios';

    export default {
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
            pages: '',
            validationErrors: '',
            pageData: '',
            parsedOld: '',

        }),
        props: ['errors', 'page', 'old'],
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
            document.title = "StudentAidBC - Admin Pages - Edit Side Page";
            if(this.errors != ""){
                this.validationErrors = JSON.parse(this.errors);
                this.parsedOld = JSON.parse(this.old);
            }
            this.pageData = JSON.parse(this.page);
        },
        watch: {
        }

    }
</script>
