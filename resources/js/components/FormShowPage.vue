<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">
            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load your form. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">

                <appeal-form-show :errors="errors" :form="form"></appeal-form-show>

            </template>
        </div>


    </div><!-- /.row -->
</template>
<style scoped>

</style>
<script>
    import axios from 'axios';

    export default {
        filters: {


        },
        props: ['errors', 'form'],
        data: () => ({

            loading: true,
            loadingError: false,
            maintenanceMode: false,
            // parsedForm: ''
        }),
        methods: {
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },



        },
        computed: {


        },
        created() {

        },
        mounted: function () {
            document.title = "StudentAidBC - Form Page";
            // if(this.form != "")
            //     this.parsedForm = JSON.parse(this.form);
            this.loading = false;
        },
        watch: {

        }

    }

</script>
