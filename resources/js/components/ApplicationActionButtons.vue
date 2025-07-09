<template>
    <div class="d-none d-md-block col-md-12">
        <a data-toggle="modal" data-remote="false" data-target="#delete-application-confirm"
           :href="'/dashboard/student-loans/confirm/delete-application/' + items.documentGUID + '/' + app.applicationDetails.applicationNumber + ''"
           class="btn btn-danger left">Delete Application</a>


<!--        <a v-if="submitClass == 'btn-success'"-->
<!--           :href="'/dashboard/application-submit-checklist/' + app.applicationDetails.applicationProfile.programYear + '/' + app.applicationDetails.applicationNumber + '/' + items.documentGUID + ''"-->
<!--           :class="'btn '  + submitClass + ' float-right'" data-style="expand-left">Submit Application</a>-->
<!--        <template v-else>-->
<!--            <button data-toggle="modal" data-remote="false" data-target="#submit-application-incomplete"-->
<!--                    :class="'btn ' + submitClass + ' float-right'">Submit Application-->
<!--            </button>-->

<!--            <div class="modal fade" id="submit-application-incomplete" tabindex="-1"-->
<!--                 role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">-->
<!--                <div class="modal-dialog">-->
<!--                    <div class="modal-content">-->
<!--                        <div class="modal-header border-bottom">-->
<!--                            <h4 class="modal-title" id="myModalLabel">-->
<!--                                <span v-if="eventIncomplete.length == 0">Submit Application</span>-->
<!--                                <span v-else>Application Incomplete</span>-->
<!--                            </h4>-->
<!--                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>-->
<!--                        </div>-->
<!--                        <div class="modal-body">-->
<!--                            <div class="alert alert-contextual alert-info">-->
<!--                                <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg>-->
<!--                                <p>-->
<!--                                    <small>-->
<!--                                        <span v-if="eventIncomplete.length == 0">Finished filling out your application and ready to submit? Press “Edit Application” and then “Submit” from inside the form.</span>-->
<!--                                        <span v-else>Your application cannot be submitted until the following issue<i-->
<!--                                            v-if="eventIncomplete.length > 1">s</i> ha<i v-if="eventIncomplete.length > 1">ve</i><i-->
<!--                                            v-else>s</i> been resolved.<br>The status must show as "Completed" before you can submit.</span>-->
<!--                                    </small>-->
<!--                                </p>-->

<!--                            </div>-->

<!--                            <p v-html="incompleteItems"></p>-->
<!--                        </div>-->
<!--                        <div class="modal-footer">-->
<!--                            <a href="#" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</a>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </template>-->
        <a v-if="submitting === false" @click="submitting=true" :href="'/dashboard/edit/application/' + items['documentGUID'] + '/' + app.applicationDetails.applicationProfile.programYear + ''" class="btn btn-warning float-right mr-1">Edit Application</a>
        <button v-if="submitting === true" type="button" class="btn btn-warning float-right mr-1 disabled">
            <small role="status" class="spinner-border spinner-border-sm mr-3"></small>
            <span>Edit Application</span>
        </button>

        <div class="modal fade" id="delete-application-confirm" tabindex="-1"
             role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h4 class="modal-title" id="myModalLabel2">Confirm Delete</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete application <strong>#
                            {{app.applicationDetails.applicationNumber}}</strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-secondary btn-sm" data-dismiss="modal">No, do NOT delete this application</a>
                        <a :href="'/dashboard/student-loans/delete-application/' + items.documentGUID + '/' + app.applicationDetails.applicationNumber + ''"
                           class="btn btn-danger btn-sm">Yes, delete this application</a>
                    </div>
                </div>
            </div>
        </div>

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
        props: ['app', 'items'],
        data: () => ({
            submitting: false,
            //app: '',
            eventItems: '',
            loading: true,
            //submitClass: '',
            //accordion: [false, false, false, false, false],
        }),
        methods: {
            formatAppNumber: function(value){
                let year = value.slice(0, 4);
                let extra = value.slice(4);

                return year + '-' + extra;
            }
        },
        computed: {
            submitClass: function(){
                //$submitClass = (isset($app->applicationDetails->applicationStatus) && $app->applicationDetails->applicationStatus == 'RDY') ? 'btn-success' : 'disabled';
                return this.app.applicationDetails.applicationStatus != null && this.app.applicationDetails.applicationStatus == 'RDY' ? 'btn-success' : 'btn-success disabled';
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
