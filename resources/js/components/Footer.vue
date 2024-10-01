<template>
    <div class="">
        <div class="fixed-bottom d-none d-md-block bg-white">
            <div class="row pr-2 pl-2">
                <div class="col-md-4">
                    <a class="mr-3" href="/dashboard" title="My Dashboard">
                        <span class="icon-uniF006 text-muted"></span>
                    </a>
                    <a class="mr-3" href="/dashboard/profile" title="Update Profile">
                        <span class="icon-webmail text-muted"></span>
                    </a>
                    <a class="mr-3" href="/help-centre" title="Help Centre">
                        <span class="icon-uniF014 text-muted"></span>
                    </a>
                    <a class="mr-3" href="/dashboard/logout?destination=dashboard" title="Logout">
                        <span class="icon-power text-muted"></span>
                    </a>
                </div>
                <div class="col-sm-12 col-md-8 text-right">
                    <a href="/" title="Go to StudentAid BC">
                        <small>Go to StudentAid BC <span class="icon-uniF473 text-muted"></span></small>
                    </a>
                </div>
            </div>
        </div>
        <div class="d-sm-block d-md-none bg-dark">
            <ul class="list-group list-group-flush">
                <a href="/dashboard" class="list-group-item text-white">
                    <li>
                        <div class="float-left">My Dashboard</div>
                        <div class="float-right">
                            <span class="icon-uniF006 icon-1x text-muted"></span>
                        </div>
                    </li>
                </a>
                <a href="/dashboard/profile" class="list-group-item text-white">
                    <li>
                        <div class="float-left">Update Profile</div>
                        <div class="float-right"><span class="icon-webmail icon-1x text-muted"></span></div>
                    </li>
                </a>
                <a href="/help-centre" target="_blank" class="list-group-item text-white">
                    <li>
                        <div class="float-left">Help Centre</div>
                        <div class="float-right"><span class="icon-uniF014 icon-1x text-muted"></span></div>
                    </li>
                </a>
                <a href="/dashboard/logout?destination=dashboard" class="list-group-item mb-3 text-white">
                    <li>
                        <div class="float-left">Logout</div>
                        <div class="float-right"><span class="icon-power icon-1x text-muted"></span></div>
                    </li>
                </a>
                <a href="/" class="list-group-item text-white">
                    <li>
                        <div class="float-left">Go to StudentAid BC</div>
                        <div class="float-right"><span class="icon-uniF473 icon-1x text-muted"></span></div>
                    </li>
                </a>

            </ul>
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
            dashboard: "",
        }),
        props: [],
        methods: {
            // fetchData: function(){
            //     var vm = this;
            //     axios.get( '/dashbaord' )
            //         .then(function (response) {
            //
            //             vm.dashboard = response.data.dashboard;
            //
            //         })
            //         .catch(function (error) {
            //             console.log(error);
            //         });
            // }

        },
        created() {

        },
        mounted: function () {
            // document.title = "StudentAidBC - Dashboard";
        },
        computed: {

            },
        watch: {

        }

    }

</script>
