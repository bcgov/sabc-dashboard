<template>
    <div class="row mb-3">
        <div class="col-sm-12 col-md-4">

            <div class="card">
                <div class="card-header text-muted">
                    My Account
                    <span class="float-right icon icon-user"></span>
                </div>
                <ul class="list-group list-group-flush">
                    <a href="/dashboard/notifications" class="list-group-item">
                        <li>
                            <div class="float-left">Message Centre</div>
                            <div class="float-right"><span class="icon-webmail icon-2x text-muted"></span></div>
                            <div v-if="msgs != undefined && msgs > 0" class="float-right"><span class="badge badge-danger">{{msgs}} new</span></div>
                        </li>
                    </a>
                    <a href="/dashboard/file-uploads" class="list-group-item">
                        <li>
                            <div class="float-left">File Uploads</div>
                            <div class="float-right">
                                <span class="icon-filemanager icon-2x text-muted"></span>
                            </div>
                        </li>
                    </a>
                    <a href="/dashboard/profile" class="list-group-item">
                        <li>
                            <div class="float-left">Update Profile</div>
                            <div class="float-right"><span class="icon-webmail icon-1x text-muted"></span></div>
                        </li>
                    </a>
                    <a href="/help-centre" target="_blank" class="list-group-item">
                        <li>
                            <div class="float-left">Help Centre</div>
                            <div class="float-right"><span class="icon-uniF014 icon-1x text-muted"></span></div>
                        </li>
                    </a>
                    <a href="/dashboard/logout?destination=dashboard" class="list-group-item">
                        <li>
                            <div class="float-left">Logout</div>
                            <div class="float-right"><span class="icon-power icon-1x text-muted"></span></div>
                        </li>
                    </a>
                </ul>
            </div>

        </div>
        <div class="d-none d-md-block col-md-8">

            <div class="card">
                <div class="card-header text-muted">
                    What would you like to do?
                    <span class="float-right icon icon-supportrequest"></span>
                </div>
                <ul class="list-group list-group-flush">
                    <a href="/dashboard/student-loans/apply" class="list-group-item">
                        <li>
                            <div class="float-left">Student</div>
                            <div class="float-right">Apply for a student loan</div>
                        </li>
                    </a>
                    <a href="/dashboard/student-loans/interest-free" class="list-group-item">
                        <li>
                            <div class="float-left">Student</div>
                            <div class="float-right">Apply for In-Study Payment-Free status</div>
                        </li>
                    </a>
                    <a href="/dashboard/appendix/claim/APPENDIX1/" class="list-group-item">
                        <li>
                            <div class="float-left">Parent</div>
                            <div class="float-right">Complete an Appendix 1</div>
                        </li>
                    </a>
                    <a href="/dashboard/appendix/claim/APPENDIX2/" class="list-group-item">
                        <li>
                            <div class="float-left">Spouse</div>
                            <div class="float-right">Complete an Appendix 2</div>
                        </li>
                    </a>
                </ul>
            </div>

        </div>
    </div>
</template>
<style scoped>
a li{
    list-style: none;
}
</style>
<script>
    import axios from 'axios';

    export default {
        data: () => ({

        }),
        props: ['msgs'],
        methods: {

        },
        created() {

        },
        mounted: function () {

        },
        computed: {

            },
        watch: {

        }

    }

</script>
