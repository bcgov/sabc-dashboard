<template>
    <div class="d-none d-md-block col-md-12">
        <template v-if="app.reminderFlag == 'Y'">
            <button data-toggle="modal" data-remote="false" data-target="#delete-appendix-confirm"
               href="#"
               class="btn btn-danger left">Delete Appendix</button>

            <div class="modal fade" id="delete-appendix-confirm" tabindex="-1"
                 role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header border-bottom">
                            <h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete {{app.formType}} <strong>#{{app.applicationNumber}}</strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-secondary btn-sm" data-dismiss="modal">No, do NOT delete this appendix</a>
                            <a :href="'/dashboard/student-loans/remove-appendix/' + app.applicationNumber + '/' + app.formGUID + ''"
                               class="btn btn-danger btn-sm">Yes, delete this appendix</a>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>

<!--            //if(empty($events['submitStatus'])){-->
            <template v-if="eventItems.submitStatus == undefined || eventItems.submitStatus == false">
                <a v-if="submitClass == 'btn-success'"
                   :href="'/dashboard/appendix-submit-checklist/' + appType + '/' + app.programYear + '/' + app.applicationNumber + '/' + app.formGUID"
                   :class="'btn '  + submitClass + ' float-right'" data-style="expand-left">Submit Appendix</a>

                <template v-else>
                    <button type="button" data-toggle="modal" data-remote="false" data-target="#submit-appendix-incomplete" :class="'btn '  + submitClass + ' float-right'">Submit Appendix</button>

                    <div class="modal fade" id="submit-appendix-incomplete" tabindex="-1"
                         role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header border-bottom">
                                    <h4 class="modal-title" id="myModalLabel2">Submit Appendix</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-contextual alert-info">
                                        <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg>
                                        <small>Finished filling out your appendix and ready to submit? Press “Edit Appendix" and then “Submit” from inside the form.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="#" class="btn btn-secondary" data-dismiss="modal">Close</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </template>
                <a :href="'/dashboard/apply/appendix/' + appType + '/' + app.formGUID + '/' + app.programYear" class="btn btn-warning float-right ladda-button" data-style="expand-left" style="margin-right:4px;">Edit Appendix</a>

            </template>

        </template>

    </div>

</template>
<style scoped>
    .card{
        border: none;
    }
    .card-header{
        background: white;
    }
    div.timeline{
        border-left: 5px solid #ccc;
    }
    .timeline ul{
        list-style: none;
        padding: 0;
    }
    .timeline ul li{
        position: relative;
    }
    .timeline ul li i.icon{
        position: absolute;
        top: 20px;
        right: 0;
    }
    .timeline ul li span.icon{
        position: absolute;
        background: #fff;
        top: 18px;
        left: -20px;
        font-weight: 300;
        font-size: 28px;
        z-index: 11;
        color: #7f818c;
        line-height: 28px;
    }
    .skinny{
        font-weight: 100;
    }
    .status-left{
        border-left: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
    }
    .status-right{
        border-right: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
    }
    .status-bar{
        position: relative;
        color: #ffffff;
        background: #3f414a;
        padding-top: 12px;
        padding-bottom: 12px;
    }
.badge{
    border-radius: 50px;
}
.taskbar h4 {
    line-height: 50px;
    margin: 0;
    padding: 0;
    font-family: "Questrial", "Century Gothic", Arial, Helvetica, sans-serif;
    font-weight: normal;
}

h4 {
    font-size: 16.1px;
    font-weight: 700;
}
</style>
<script>
    import axios from 'axios';

    export default {
        filters: {
            formatAppNumber: function(value){
                let year = value.slice(0, 4);
                let extra = value.slice(4);

                return year + '-' + extra;
            }
        },
        props: ['app', 'eventItems'],
        data: () => ({
            //app: '',
            // eventItems: '',
            loading: true,
            //submitClass: '',
            //accordion: [false, false, false, false, false],
        }),
        methods: {
            //you cannot update arrays directly in JS. The DOM won't see these changes.
            //https://stackoverflow.com/questions/51412250/vue-v-if-cant-access-booleans-in-arrays
            // toggleAccordion: function(index){
            //     this.$set(this.accordion, index, !this.accordion[index])
            // },
            // fetchData: function(){
            //     this.loading = true;
            //     var vm = this;
            //     axios({
            //         url: '/student-loans/fetch-application-status?application_number=' + this.appno,
            //         //data: formData,
            //         method: 'get',
            //         //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
            //         headers: {'Accept': 'application/json'}
            //     })
            //
            //     //axios.get( '/fetch-dashboard' )
            //         .then(function (response) {
            //
            //             vm.loading = false;
            //             vm.app = response.data.application;
            //             vm.eventItems = response.data.details;
            //
            //         })
            //         .catch(function (error) {
            //             vm.loading = false;
            //             vm.loadingError = true;
            //             console.log(error);
            //         });
            // }

        },
        computed: {
            appType: function(){
                var type = this.app.formType;
                type = type.toUpperCase();
                type = type.replace(/\s/g, "");
                return type;
            },
            submitClass: function(){
                //$submitClass = (isset($app->applicationDetails->applicationStatus) && $app->applicationDetails->applicationStatus == 'RDY') ? 'btn-success' : 'disabled';
                // $submitClass = (isset($events['submitStatus']) && !empty($events['submitStatus'])) ? 'btn-success' : (($app->apxStatus == 'RDY') ? 'btn-success' : 'disabled');
                return (this.eventItems.submitStatus != undefined && this.eventItems.submitStatus != false) ? 'btn-success' : ( (this.app.apxStatus == 'RDY') ? 'btn-success' : 'btn-success disabled' );
//                return this.app.applicationDetails.applicationStatus != null && this.app.applicationDetails.applicationStatus == 'RDY' ? 'btn-success' : 'disabled';
            },
            /*
            $eventIncomplete = array();
                    $index = 0;
                    foreach($appendices as $k => $event) {
                        if ($event['eventCode'] == "Waiting" || $event['eventCode'] == "In Progress") {
                            if ($onlineStatus == 'Y' ||
                                ($onlineStatus == 'N' && $event['appendixType'] != 'Appendix 3')) {
                                $eventIncomplete[$index] = $event['eventIncomplete'];
                                $index++;
                            }
                        }
                    }

                    $incompleteItems = "";
                    foreach($eventIncomplete as $k => $incomplete) {
                        $incompleteItems .= $incomplete;
                        if ($k < sizeof($eventIncomplete) - 1) {
                            $incompleteItems .= '<hr>';
                        }
                    }
             */
            onlineStatus: function(){
                //            $onlineStatus = $app->applicationDetails->applicationProfile->institution->onlineStatus;
                return this.app.applicationDetails.applicationProfile.institution.onlineStatus;
            },
            eventIncomplete: function () {
                //var index = 0;
                var arr = []; //$vm0.eventItems["Start/Submit Application"].eventCategoryCode

                var appendices = this.items['Start/Submit Application'].events.appendices;
                for(var i=0; i<appendices.length; i++){
                    if (appendices[i].eventCode == "Waiting" || appendices[i].eventCode == "In Progress") {
                        if (this.onlineStatus === 'Y' ||
                            (this.onlineStatus == 'N' && appendices[i].appendixType != 'Appendix 3')) {
                            //$eventIncomplete[$index] = $event['eventIncomplete'];
                            //$index++;
                            arr.push(appendices[i].eventIncomplete);
                        }
                    }
                }
                return arr;
            },
            incompleteItems: function () {
                var incompletes = this.eventIncomplete;
                var items = "";
                for(var i=0; i<incompletes.length; i++){
                    items += incompletes[i];
                }
                return items + "<hr>";
            }
        },
        created() {

        },
        mounted: function () {
            //this.fetchData();
            //document.title = "StudentAidBC - Check Application Status";
        },
        watch: {

        }

    }

</script>
