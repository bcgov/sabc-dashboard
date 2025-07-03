<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div id="new_form_page" v-if="maintenanceMode === false" class="col-12">
            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">
                <div v-if="submit_msg != ''" class="row">
                    <div class="col-12">
                        <div class="alert alert-contextual alert-danger">
                            <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#stopsign-alert"></use></svg>
                            <p v-html="submit_msg"></p>
                        </div>

                    </div><!-- /.block -->
                </div>
                <div v-if="errors != ''" class="row">
                    <div class="col-12">
                        <div class="alert alert-contextual alert-danger">
                            <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#stopsign-alert"></use></svg>
                            <template v-for="(error, i) in validationErrors" :key="i">
                                <template v-for="(e, j) in error" :key="j">
                                    <p class="alert-p">
                                        <span v-html="e"></span>
                                        <br v-if="i === 1" />
                                    </p>
                                </template>
                            </template>                        </div>

                    </div><!-- /.block -->
                </div>


                <div v-if="form != ''" v-html="form.form_body"></div>

            </template>
        </div>


    </div><!-- /.row -->
</template>
<style scoped>

    span.form-required {
        color: red;
    }
    legend{
        display: block;
        padding: 0 15px;
        background: #ffffff;
        margin: 0;
        font-size: 21px;
        font-family: "Questrial", "Century Gothic", Arial, Helvetica, sans-serif;
        line-height: 24px;
        color: #869198;
        width: auto;
        margin-bottom: 15px;
    }
    fieldset{
        border: solid 2px #e6e9ea;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        padding: 0 25px;
        margin: 24px 0;
    }
</style>
<script>
    import axios from 'axios';

    export default {
        props: ['errors', 'submit_msg', 'submit_status', 'uuid'],
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

            loading: false,
            loadingError: false,
            maintenanceMode: false,
            validationErrors: '',
            form: '',
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
                    url: '/dashboard/fetch-published-forms-list/' + this.uuid,
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {
                        vm.form = response.data.form;
                        vm.loading = false;
                        setTimeout(function (){
                            $("#new_form_page form input[name='_token']").val(vm.csrf);
                            $("#new_form_page form").append('<input type="hidden" name="form_uuid" value="' + vm.uuid + '"/>');
                        }, 2000);
                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        // console.log(error);
                    });
            }

        },
        computed: {


        },
        created() {

        },
        mounted: function () {
            document.title = "StudentAidBC - Start New Form";
            if(this.errors != "")
                this.validationErrors = JSON.parse(this.errors);

            this.fetchData();
        },
        watch: {

        }

    }

</script>
