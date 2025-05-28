<template>
    <div class="row mt-3">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">
            <div class="taskbar">
                <h4>BCSC Temporary Access</h4>
                <hr class="mt-0"/>
            </div>

            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">
                <form ref="form" :action="'/dashboard/temporary/step-' + step" method="post" id="sabc-form-builder" accept-charset="UTF-8">
                    <input type="hidden" name="_token" :value="csrf">
                    <input type="hidden" name="hpot" value="">

                    <div v-if="errors != ''" class="row">
                        <div class="col-12">
                            <div class="alert alert-contextual alert-danger" role="alert">
                                <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#stopsign-alert"></use></svg>
                                <template v-for="(error, i) in validationErrors" :key="i">
                                    <template v-for="(e, j) in error" :key="j">
                                        <p class="alert-p">
                                            <span v-html="e"></span>
                                            <br v-if="i === 1" />
                                        </p>
                                    </template>
                                </template>                            </div><!-- /.alert -->

                        </div><!-- /.block -->
                    </div>

                    <dashboard-bcsc-forgot-step-1 v-if="step == 1" @submitform="handleSubmit" :errors="validationErrors" :old="parseOld"></dashboard-bcsc-forgot-step-1>
                    <dashboard-bcsc-forgot-step-2 v-if="step == 2" @submitform="handleSubmit" :errors="validationErrors" :old="parseOld" :data="parseData"></dashboard-bcsc-forgot-step-2>
                    <dashboard-bcsc-forgot-step-3 v-if="step == 3" @submitform="handleSubmit" :errors="validationErrors" :old="parseOld" :data="parseData"></dashboard-bcsc-forgot-step-3>

                    <input v-if="action != ''" type="hidden" name="action" :value="action">
                </form>
            </template>
        </div>

    </div>
</template>
<style scoped>

</style>
<script>
    //import axios from 'axios

    export default {
        filters: {

        },
        props: ['errors', 'old', 'data', 'step'],
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: false,
            loadingError: false,

            validationErrors: '',
            maintenanceMode: false,
            parseOld: '',
            parseData: '',
            action: '',
        }),
        methods: {
            handleSubmit: function(e){
                console.log(e);
                if(e == 'del' || e == 'reset' || e == 'confirm-delete')
                    this.action = e;
                var vm = this;
                setTimeout(function () {
                    vm.$refs.form.submit();
                }, 1000);
            },
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },


        },

        created() {

        },
        mounted: function () {
            if(this.errors != "")
                this.validationErrors = JSON.parse(this.errors);
            if(this.data != "")
                this.parseData = JSON.parse(this.data);

            if(this.old != ""){
                this.parseOld = JSON.parse(this.old);
            }

            document.title = "StudentAidBC - Forgot User ID/Password";

        },
        watch: {

        }

    }

</script>
