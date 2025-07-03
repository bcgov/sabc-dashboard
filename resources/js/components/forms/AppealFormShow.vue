<template>

    <div id="show_form_page" class="printing-area">
        <div v-if="loading === true" class="jumbotron text-center">Loading ...</div>
        <div v-if="loading === false && loadingError === true" class="jumbotron text-center">Sorry we could not load your form. Error #99831</div>
        <div v-if="loading === false && loadingError === false" v-html="parsedData.form_body"></div>

        <div v-if="parsedData != ''" class="row d-print-none">
            <div class="col-12">
                <button @click="printForm" type="button" class="btn btn-block btn-success ">Print</button>
            </div>
        </div>
    </div>


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
        props: ['form', 'errors'],
        data: () => ({
            parsedData: '',
            parsedForm: '',
            loading: true,
            loadingError: false,
        }),
        methods: {

            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/fetch-published-forms-list/' + this.parsedForm.uuid,
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {
                        vm.parsedData = response.data.form;
                        vm.loading = false;
                        setTimeout(function (){
                            // $("#show_form_page form input[name='_token']").val(vm.csrf);
                            // $("#show_form_page form").append('<input type="hidden" name="form_uuid" value="' + vm.uuid + '"/>');

                            vm.populateForm();
                        }, 2000);
                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        // console.log(error);
                    });
            },
            populateForm: function(){
                for (const [key, value] of Object.entries(this.parsedForm.data)) {
                    //console.log(`${key}: ${value}`);
                    var check = key.split('___x23y_');
                    if(check.length === 2){
                        if(check[1] === 'checkbox') {
                            $("#" + check[0]).prop('checked', true);
                        }
                        if(check[1] === 'v'){
                            $("#" + check[0]).val(value);
                        }
                    }
                    $("#show_form_page button[type='submit']").parent().parent().hide();
                    $("#show_form_page input, #show_form_page select, #show_form_page textarea").prop('readonly', true);
                    $("#show_form_page input, #show_form_page select, #show_form_page textarea").prop('disabled', true);
                }
            },
            printForm: function(){
                window.print();
            },
        },
        computed: {


        },
        created() {

        },
        mounted: function () {
            if(this.form != "") {
                this.parsedForm = JSON.parse(this.form);
                this.fetchData();
            }

        },
        watch: {

        }

    }

</script>
