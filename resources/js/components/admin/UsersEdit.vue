<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">Edit User</h4>
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
                        <form method="post" :action="'/dashboard/admin/users/edit/' + userData.uid" enctype="multipart/form-data">
                            <input type="hidden" name="_token" :value="csrf">

                            <div class="form-group">
                                <label for="name">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" v-model="userData.name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="email" name="email" v-model="userData.email">
                            </div>

                            <div class="form-group">
                                <label for="password">New Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>



                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status1" value="0" :checked="userData.status == 0">
                                    <label class="form-check-label" for="status1">Disabled</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status2" value="1" :checked="userData.status == 1">
                                    <label class="form-check-label" for="status2">Active</label>
                                </div>
                            </div>

                            <div v-for="(role, i) in roles" class="form-check">
                                <input class="form-check-input" type="checkbox" :value="role.value" :id="'role' + i" name="roles[]" :checked="role.active == 1">
                                <label class="form-check-label" :for="'role' + i">{{role.label}}</label>
                            </div>


                            <button type="submit" class="btn btn-primary mt-4">Update</button>
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
            users: '',
            validationErrors: '',
            userData: '',

            roles: [
                {label: 'Administrator', value: 'administrator', active: 0},
                {label: 'App Support', value: 'app_support', active: 0},
                {label: 'Student', value: 'student', active: 0},
                {label: 'Spouse', value: 'spouse', active: 0},
                {label: 'Parent', value: 'parent', active: 0},
                {label: 'Parent No Sin', value: 'parent_no_sin', active: 0},
                {label: 'Spouse No Sin', value: 'spouse_no_sin', active: 0},
                {label: 'BCSC Sstudent', value: 'bcsc_student', active: 0},
                {label: 'BCSC Spouse', value: 'bcsc_spouse', active: 0},
                {label: 'BCSC Parent', value: 'bcsc_parent', active: 0},
                {label: 'Reviewer', value: 'reviewer', active: 0},
                {label: 'Editor', value: 'editor', active: 0}
            ],
        }),
        props: ['errors', 'user'],
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
            document.title = "StudentAidBC - Admin Pages - Edit User";
            if(this.errors != "")
                this.validationErrors = JSON.parse(this.errors);
            this.userData = JSON.parse(this.user);

            for(var i=0; i<this.userData.roles.length; i++){
                for(var j=0; j<this.roles.length; j++){
                    if(this.userData.roles[i].name == this.roles[j].value){
                        this.roles[j].active = 1;
                    }
                }
            }

        },
        watch: {
        }

    }
</script>
