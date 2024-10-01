<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">
            <div class="taskbar">
                <h4>File Manager</h4>
                <hr class="mt-0"/>
            </div>


            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load your File Manager. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">
                <div class="row">
                    <div class="col-12">
                        <div class="alert  alert-primary text-white">
<!--                            <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg>-->
                            <p class="p-0">Please note: Uploaded files can't be deleted.</p>
                            <p class="p-0 m-0">Each individual file cannot exceed <strong>2MB</strong> and the total number of
                                files size cannot exceed <strong>8MB</strong><br>Accepted file types:
                                <strong>PDF, DOC, DOCX, XLS, JPG, GIF, PNG</strong><br>Make sure the file
                                name does not contain special characters (e.g. punctuation marks such as commas
                                and periods).</p>
                        </div>

                    </div>
                    <div class="col-lg-5">
                        <form enctype="multipart/form-data" id="dropzonewidget" class="dropzone" action="/dashboard/file-uploads" method="post" accept-charset="UTF-8">
                            <input type="hidden" name="_token" :value="csrf">
                            <input type="hidden" name="form_id" value="sabc_file_manager_block_form">
                            <div id="form-wrapper">
                                <div id="edit-form" class="control-group plain form-type-item form-item">
                                    <div class="controls">
                                        <div class="control-group plain form-type-select form-item-document-purpose form-group">
                                            <label for="edit-document-purpose">Document Purpose </label>
                                            <div class="controls">
                                                <select id="edit-document-purpose" name="document_purpose" class="form-control">
                                                    <option value="P000">Personal Identification</option>
                                                    <option value="P001">Appeals</option>
                                                    <option value="P002">Appendix 7 – Request for reassessment</option>
                                                    <option value="P003">Appendix 3 – School and Program Info</option>
                                                    <option value="P004">Part Time Studies</option>
                                                    <option value="P008">Appendix 8 – Permanent Disability Program Application</option>
                                                    <option value="P005">Appendix 5 – Transfer of schools</option>
                                                    <option value="P006">Verification</option>
                                                    <option value="P007">Other</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div
                                            class="control-group plain form-type-textfield form-item-attach-to-application form-group">
                                            <label for="appNumber">Application #(optional) </label>
                                            <div class="controls">
                                                <input id="appNumber" type="text" name="attach_to_application" value="" size="60" maxlength="11" class="form-control">
                                            </div>
                                        </div>
                                        <div id="document_notes">
                                            <div class="control-group plain form-type-textarea form-item-document-notes form-group">
                                                <label for="edit-document-notes">Comments (optional) </label>
                                                <div class="controls">
                                                    <div class="form-textarea-wrapper">
                                                        <textarea maxlength="1500"
                                                         id="edit-document-notes"
                                                         name="document_notes" cols="60"
                                                         rows="1" class="form-control"
                                                         style="overflow: hidden; overflow-wrap: break-word; resize: horizontal; height: 58px;" v-model="cmnt" @keyup="cmntCount"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="textarea_feedback">{{cmntRemain}} characters remaining</div>
                                        </div>
                                        <input type="hidden" name="nid" value="">

                                        <div class="card-body">
                                            <div class="dropzone-container" style="overflow-x: hidden;">
                                                <div id="actions" class="form-row actions">
                                                    <div class="form-group col-12">
                                                        <span class="btn btn-success fileinput-button dz-clickable">
                                                            <i class="glyphicon glyphicon-plus"></i>
                                                            <span>Add files...</span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="table table-striped files" id="previews">
                                                    <div id="template" class="file-row dz-image-preview">
                                                        <div>
                                                            <p class="name" data-dz-name></p>
                                                            <strong class="error text-danger" data-dz-errormessage></strong>
                                                        </div>
                                                        <div>
                                                            <p class="size" data-dz-size></p>
                                                        </div>
                                                        <div class="text-right">
                                                           <span class="float-right">
                                                               <button type="button" data-dz-remove class="btn btn-icon btn-secondary delete">
                                                                 <span aria-hidden="true">&times;</span>
                                                               </button>
                                                           </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 actions">
                                                        <div id="error"></div>
                                                    </div>
                                                </div>
                                                <div id="dropzone-files" style="display:none;">
                                                    <div class="form-row">
                                                        <div class="form-group col-12" style="padding-bottom:12px;">
                                                            <span class="fileupload-process">
                                                              <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                                                <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress="">&nbsp;</div>
                                                              </div>
                                                            </span>
                                                        </div>
                                                        <div class="form-group col-6 text-right actions">
                                                            <button class="btn btn-primary btn-sm start form-submit ajax-processed form-submit form-button-disabled" disabled="disabled" type="button" id="uploadSubmit" name="op" value="Upload">Upload</button>&nbsp;
                                                        </div>
                                                        <div class="form-group col-6 actions">
                                                            <button type="reset" class="btn btn-secondary btn-sm cancel">Cancel Upload</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                    <file-uploads-list></file-uploads-list>
                </div>
                <div class="row">
                    <div class="col-12 text-muted"><br><br><small><i><p><strong>Collection Notice</strong></p>
                        <p><strong>Collection, use and disclosure of information.</strong> Any information you submit is collected under Section 26(c) &amp; (e) of the Freedom of Information and Protection of Privacy Act. The information provided will be collected, used and disclosed for the purposes for which it is provided. If it is submitted to assist in confirming your identification for student financial assistance, that is why it will be collected, used and disclosed. If it is submitted to support a student financial assistance application, that is why it will be collected, used and disclosed, and the submitted information will become part of that application and subject to the terms and conditions found in your signed StudentAid BC Declaration.
                            If you have any questions about the collection, use and disclosure of this information, contact Director, StudentAid BC, Ministry of Post-Secondary Education and Future Skills, PO Box 9173, Stn Prov Govt, Victoria BC, V8W 9H7, telephone 1-800-561-1818 (toll-free in Canada/U.S.) or +1-778-309-4621 from outside North America.
                        </p></i></small>
                    </div>

                </div>


            </template>
        </div>

    </div><!-- /.row -->
</template>
<style scoped>
.taskbar h4 {
    line-height: 50px;
    margin: 0;
    padding: 0;
    font-family: "Questrial", "Century Gothic", Arial, Helvetica, sans-serif;
    font-weight: normal;
    font-size: 16.1px;
}

</style>
<script>
    import axios from 'axios';
    //import ProfileChallengeQuestions from "./ProfileChallengeQuestions";

    export default {
        filters: {

            // formatAppNumber: function(value){
            //     let year = value.slice(0, 4);
            //     let extra = value.slice(3);
            //
            //     return year + '-' + extra;
            // }
        },

        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            files: '',
            maintenanceMode: false,

            cmntLimit: 1500,
            cmntRemain: 1500,
            cmnt: "",

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
                    url: '/dashboard/fetch-uploads',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.files = response.data.files;
                        vm.loadCustomCode();

                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        console.log(error);
                    });
            },
            loadCustomCode: function () {
                setTimeout(function () {
                    let s = document.createElement( 'script' );
                    s.setAttribute( 'src', "/dashboard/js/dropzone_extra.js?qitny6" );
                    document.body.appendChild( s );
                }, 1000);
            },
            cmntCount: function () {
                if(this.cmnt.length <= this.cmntLimit){
                    this.cmntRemain = this.cmntLimit - this.cmnt.length;
                }else{
                    this.cmntRemain = 0;
                    this.cmnt = this.cmnt.substr(0,this.cmntLimit-1);
                }
            }

        },
        computed: {
            // fullName: function(){
            //     let name = "";
            //   if(this.profile != ''){
            //       if(this.profile.userProfile.middleName != undefined)
            //         name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.middleName + " " + this.profile.userProfile.familyName);
            //         else
            //             name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.familyName);
            //   }
            //   return name.toUpperCase();
            // },
            //
        },
        created() {

        },
        mounted: function () {
            this.fetchData();
            document.title = "StudentAidBC - File Manager";

        },
        watch: {
        }

    }

</script>
