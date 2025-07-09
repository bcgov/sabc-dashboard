<template>
    <div>
        <div class="taskbar">
            <h4>APPEALS &amp; Forms
                <a href="/dashboard/appeal-forms/new" type="button" class="btn btn-success btn-sm mt-2 float-right">Start New Appeal/Form</a>

<!--                <div class="btn-group float-right" role="group">-->
<!--                    <button id="btnGroupDropForms" type="button" class="btn btn-success btn-sm dropdown-toggle mt-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Start New Appeal/Form</button>-->
<!--                    <div id="divGroupDropForms" class="dropdown-menu" aria-labelledby="btnGroupDropForms" x-placement="bottom-start">-->
<!--                        <a v-for="form in forms" class="dropdown-item btn-sm" :href="'/dashboard/appeal-forms/new-form/' + form.uuid">{{form.name}} <small>({{form.program_year}})</small></a>-->

<!--                        <a class="dropdown-item btn-sm" href="#">...</a>-->
<!--                    </div>-->
<!--                </div>-->
            </h4>
            <hr class="mt-0"/>
        </div>

        <div class="row">
            <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
            <div v-if="maintenanceMode === false" class="col-12">
                <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
                <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load your appeal forms. Please try again later.</div>
                <template v-if="loading == false && loadingError == false">

                    <forms-list></forms-list>

                </template>
            </div>


        </div><!-- /.row -->
    </div>
</template>
<style>
    div#divGroupDropAppeals {
        left: -64px !important;
    }
</style>
<script>
    import axios from 'axios';

    export default {
        props: ['errors'],
        data: () => ({

            loading: false,
            loadingError: false,
            maintenanceMode: false,
            forms: [],
        }),
        methods: {
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },

            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/fetch-published-forms-list',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {
                        vm.forms = response.data.forms;
                        vm.loading = false;

                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        console.log(error);
                    });
            }

        },
        computed: {


        },
        created() {

        },
        mounted: function () {
            document.title = "StudentAidBC - Forms Page";

            $(document).ready(function(){
                $("#btnGroupDropForms").click(function(){
                    $("#divGroupDropForms").toggle();
                });
            });
            this.fetchData();
        },
        watch: {

        }

    }

</script>
