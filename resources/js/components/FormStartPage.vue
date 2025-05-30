<template>
    <div>
        <div class="taskbar">
            <h4>START A NEW FORM
                <a href="/dashboard/appeal-forms" class="btn btn-link btn-sm float-right mt-2">Back to Forms &amp; Appeals</a>

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
                <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>
                <template v-if="loading == false && loadingError == false">

                    <div class="row">
                        <div class="col-md-4">
                            <ul class="list-group list-group-flush">
                                <li v-for="(cat, i) in categories" class="list-group-item"><a :href="'#forms_' + i">{{cat.name}}</a></li>
                            </ul>
                        </div>
                        <div class="col-md-8">
                            <div v-for="(cat, i) in categories" class="card mb-3">
                                <div class="card-header bg-dark text-white" v-html="cat.long_name" :id="'forms_' + i"></div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush p-0">
                                        <li v-for="(form, j) in cat.forms" class="list-group-item"><a  :href="'/dashboard/appeal-forms/new-form/' + form.uuid">{{form.name}}</a></li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>

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
            categories: [],
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
                    url: '/dashboard/categories/fetch-all',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {
                        vm.categories = response.data.categories;
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
            document.title = "StudentAidBC - Start Forms Page";
            this.fetchData();
        },
        watch: {

        }

    }

</script>
