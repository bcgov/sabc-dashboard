<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">Edit Alert</h4>
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
                        <form method="post" :action="'/dashboard/admin/alerts/edit/' + alertData.id" enctype="multipart/form-data">
                            <input type="hidden" name="_token" :value="csrf">

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="name" name="name" v-model="parsedOld['name']" required>
                                <input v-else type="text" class="form-control" id="name" name="name" v-model="alertData.name" required>
                            </div>
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select v-if="parsedOld != ''" class="form-control" id="type" name="type" v-model="parsedOld['type']">
                                    <option value="info">Info - Light blue</option>
                                    <option value="danger">Error - Red</option>
                                    <option value="success">Success - Green</option>
                                    <option value="warning">Warning - Orange</option>
                                    <!--                                    <option value="primary">Primary - Dark blue</option>-->
                                </select>
                                <select v-else class="form-control" id="type" name="type" v-model="alertData.type">
                                    <option value="info">Info - Light blue</option>
                                    <option value="danger">Error - Red</option>
                                    <option value="success">Success - Green</option>
                                    <option value="warning">Warning - Orange</option>
                                    <!--                                    <option value="primary">Primary - Dark blue</option>-->
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="pages">Pages</label>
                                <select v-if="parsedOld != ''" class="form-control" id="pages" name="pages" v-model="parsedOld['pages']">
                                    <option value="login_register_pages">Login/Register Pages</option>
                                    <option value="auth_pages">Logged in Pages</option>
                                    <option value="all">All Pages</option>
                                </select>
                                <select v-else class="form-control" id="pages" name="pages" v-model="alertData.pages">
                                    <option value="login_register_pages">Login/Register Pages</option>
                                    <option value="auth_pages">Logged in Pages</option>
                                    <option value="all">All Pages</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="body">Body</label>
                                <textarea v-if="parsedOld != ''" class="form-control" id="body" name="body" rows="6" required v-model="parsedOld['body']"></textarea>
                                <textarea v-else class="form-control" id="body" name="body" rows="6" required v-model="alertData.body"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="start_time">Start Time</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="start_time" name="start_time" v-model="parsedOld['start_time']" required>
                                <input v-else type="text" class="form-control" id="start_time" name="start_time" v-model="alertData.start_time" required>
                            </div>
                            <div class="form-group">
                                <label for="end_time">End Time</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="end_time" name="end_time" v-model="parsedOld['end_time']">
                                <input v-else type="text" class="form-control" id="end_time" name="end_time" v-model="alertData.end_time">
                            </div>
                            <div class="form-group">
                                <label for="disable_page">Disable Page</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="disable_page" id="disable_page" value="true" v-model="alertData.disable_page">
                                    <label class="form-check-label" for="disable_page">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="disable_page" id="disable_page2" value="false" v-model="alertData.disable_page">
                                    <label class="form-check-label" for="disable_page2">
                                        No
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dismissible1">Dismissible</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="dismissible" id="dismissible1" value="true" v-model="alertData.dismissible">
                                    <label class="form-check-label" for="dismissible1">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="dismissible" id="dismissible2" value="false" v-model="alertData.dismissible">
                                    <label class="form-check-label" for="dismissible2">
                                        No
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="order_index">Order on Page</label>
                                <input v-if="parsedOld != ''" type="number" class="form-control" id="order_index" name="order_index" v-model="parsedOld['order_index']">
                                <input v-else type="number" class="form-control" id="order_index" name="order_index" v-model="alertData.order_index">
                            </div>


                            <div class="form-group">
                                <label for="status">Status</label>
                                <select v-if="parsedOld != ''" class="form-control" id="status" name="status" v-model="parsedOld['status']">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                </select>
                                <select v-else class="form-control" id="status" name="status" v-model="alertData.status">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                </select>
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

            formatAppNumber: function(value){
                let year = value.slice(0, 4);
                let extra = value.slice(4);

                return year + '-' + extra;
            }
        },

        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            alerts: '',
            validationErrors: '',
            parsedOld: '',
            alertData: '',
        }),
        props: ['errors', 'alert', 'old'],
        methods: {

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
            document.title = "StudentAidBC - Admin Pages - Edit Alert";
            if(this.errors != ""){
                this.validationErrors = JSON.parse(this.errors);
                this.parsedOld = JSON.parse(this.old);
            }
            this.alertData = JSON.parse(this.alert);

        },
        watch: {
        }

    }
</script>
