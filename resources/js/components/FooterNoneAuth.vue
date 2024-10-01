<template>
    <div class="pr-2 pl-2">
        <div class="fixed-bottom d-none d-md-block bg-white">
            <div class="row">
                <div class="col-md-4">
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
