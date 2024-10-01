<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">

            <div v-if="loading == true && load_msg == ''" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == true && loadingError == false && load_msg != ''" class="jumbotron text-center h4">{{load_msg}}</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">

                <div class="jumbotron bg-white text-center mt-5 mb-5">
                    <p>Appendix submission is in progress. Please wait.</p>
                    <div class='progress'>
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role='progressbar' aria-valuemax="100" style="width: 0px;min-width:0px;" aria-valuemin="0" aria-valuenow="0">0%</div>
                    </div>
                </div>

            </template>
        </div>

    </div><!-- /.row -->
</template>
<style scoped>
.progress {
    width: 100%;
    height: 30px;
}
</style>
<script>

    export default {
        filters: {
        },

        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            validationErrors: '',
            maintenanceMode: false,
            progressValue: 0,

        }),

        props: ['app_id', 'appendix_type', 'document_guid', 'submit_status', 'submit_msg', 'errors', 'load_status', 'load_msg'],
        methods: {
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },
            myLoop: function (i){
                let vm = this;
                setTimeout(function () {
                    $(".progress-bar").html(vm.progressValue + '%');
                    // console.log(vm.progressValue);
                    vm.progressValue += 1;
                    $(".progress-bar").attr('aria-valuenow', vm.progressValue);
                    $(".progress-bar").css('width', vm.progressValue + '%');
                    if (--i) vm.myLoop(i);
                    if (vm.progressValue == 101) vm.endProgress();
                }, 350);
            },
            endProgress: function (){
                // console.log("REDIRECT");
                window.location.href = '/dashboard/appendix-submit-success-redirect/' + this.app_id + '/' + this.appendix_type + '/' + this.document_guid;
            }
        },
        created() {
            document.title = "StudentAidBC - Appendix Submit Success";
        },
        mounted: function () {
            this.loading = false;

            document.title = "StudentAidBC - Appendix Submit Success";

            let vm = this;
            //$(".progress").removeClass('hidden');
            setTimeout(function (){
                vm.myLoop(101);
            }, 1000);

        },
        watch: {
        }

    }

</script>
