<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="page-title-box">Edit Declaration</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div v-if="errors != ''" class="row">
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <template v-for="(error, i) in validationErrors" :key="i">
                                        <template v-for="(e, j) in error" :key="j">
                                            <p class="alert-p">
                                                <span v-html="e"></span>
                                                <br v-if="i === 1" />
                                            </p>
                                        </template>
                                    </template>                                      </div>
                            </div><!-- /.block -->
                        </div>
                        <form method="post" :action="'/dashboard/admin/declarations/edit/' + declarationData.id" enctype="multipart/form-data">
                            <input type="hidden" name="_token" :value="csrf">

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="name" name="name" required v-model="parsedOld['type']">
                                <input v-else type="text" class="form-control" id="name" name="name" required v-model="declarationData.name">
                            </div>
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select v-if="parsedOld != ''" class="form-control" id="type" name="type" required v-model="parsedOld['type']">
                                    <option value="Applicant - Signature Required">Applicant - Signature Required</option>
                                    <option value="Applicant - No Signature Required">Applicant - No Signature Required</option>
                                    <option value="Parent - Signature Required">Parent - Signature Required</option>
                                    <option value="Parent - No Signature Required">Parent - No Signature Required</option>
                                    <option value="Spouse - Signature Required">Spouse - Signature Required</option>
                                    <option value="Spouse - No Signature Required">Spouse - No Signature Required</option>
                                    <option value="Applicant - E-Consent">Applicant - E-Consent</option>
                                    <option value="Spouse - E-Consent">Spouse - E-Consent</option>
                                    <hr>
                                    <option value="Applicant Declaration 2013 - Signature Required">Applicant Declaration 2013 - Signature Required</option>
                                    <option value="Applicant Declaration 2013 - No Signature Required">Applicant Declaration 2013 - No Signature Required</option>
                                    <option value="Applicant Declaration 2012 - Signature Required">Applicant Declaration 2012 - Signature Required</option>
                                    <option value="Applicant Declaration 2012 - No Signature Required">Applicant Declaration 2012 - No Signature Required</option>
                                </select>
                                <select v-else class="form-control" id="type" name="type" required v-model="declarationData.type">
                                    <option value="Applicant - Signature Required">Applicant - Signature Required</option>
                                    <option value="Applicant - No Signature Required">Applicant - No Signature Required</option>
                                    <option value="Parent - Signature Required">Parent - Signature Required</option>
                                    <option value="Parent - No Signature Required">Parent - No Signature Required</option>
                                    <option value="Spouse - Signature Required">Spouse - Signature Required</option>
                                    <option value="Spouse - No Signature Required">Spouse - No Signature Required</option>
                                    <option value="Applicant - E-Consent">Applicant - E-Consent</option>
                                    <option value="Spouse - E-Consent">Spouse - E-Consent</option>
                                    <hr>
                                    <option value="Applicant Declaration 2013 - Signature Required">Applicant Declaration 2013 - Signature Required</option>
                                    <option value="Applicant Declaration 2013 - No Signature Required">Applicant Declaration 2013 - No Signature Required</option>
                                    <option value="Applicant Declaration 2012 - Signature Required">Applicant Declaration 2012 - Signature Required</option>
                                    <option value="Applicant Declaration 2012 - No Signature Required">Applicant Declaration 2012 - No Signature Required</option>
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="type_id">Type ID</label>
                                <select v-if="parsedOld != ''" class="form-control" id="type_id" name="type_id" required v-model="parsedOld['type_id']">
                                    <option value="sabc_new_dec">New Declaration (post PY 2019/2020)</option>
                                    <option value="sabc_econsent">E-Consent</option>
                                    <option value="sabc_declaration">Old Declaration</option>
                                </select>
                                <select v-else class="form-control" id="type_id" name="type_id" required v-model="declarationData.type_id">
                                    <option value="sabc_new_dec">New Declaration (post PY 2019/2020)</option>
                                    <option value="sabc_econsent">E-Consent</option>
                                    <option value="sabc_declaration">Old Declaration</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="program_year">Program Year</label>
                                <input v-if="parsedOld != ''" type="text" class="form-control" id="program_year" name="program_year" required v-model="parsedOld['program_year']">
                                <input v-else type="text" class="form-control" id="program_year" name="program_year" required v-model="declarationData.program_year">
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select v-if="parsedOld != ''" class="form-control" id="status" name="status" required v-model="parsedOld['status']">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                </select>
                                <select class="form-control" id="status" name="status" required v-model="declarationStatus">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                </select>
                            </div>
                            <fieldset class="m-3">
                                <legend>Form Declaration/Concent Data:</legend>
                                <div v-for="(row, i) in rows" class="form-row">
                                    <div class="col-md-3 form-group">
                                        <input type="text" class="form-control" name="field_ids[]" placeholder="Field ID" v-model="fields[i].field_id" required>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <input type="text" class="form-control" name="field_labels[]" placeholder="Field Label" v-model="fields[i].field_label" required>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <input type="text" class="form-control" name="field_weights[]" placeholder="Weight/Order on page" v-model="fields[i].field_weight" required>
                                    </div>
                                    <div class=" col-md-3 form-group">
                                        <textarea class="form-control" name="field_data[]" rows="3" placeholder="Field Data" v-model="fields[i].field_value" required>{{ fields[i].data }}</textarea>
                                    </div>
                                    <div class=" col-md-1 form-group">
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteRow(i)">X</button>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-sm btn-info" @click="addRow">+ Add Field</button>
                                    </div>
                                </div>

                            </fieldset>


                            <button type="submit" class="btn btn-primary">Update</button>
                            <button @click="deleteDecl" type="button" class="btn btn-danger float-right">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
    fieldset {
        min-width: initial;
        padding: initial;
        margin: initial;
        border: initial;
    }
    legend {
        display: initial;
        width: initial;
        max-width: initial;
        padding: initial;
        margin-bottom: initial;
        font-size: initial;
        line-height: initial;
        color: initial;
        white-space: initial;
    }
</style>
<script>
    import axios from 'axios';

    export default {
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            validationErrors: '',
            declarationData: '',
            declarationStatus: 'draft',
            rows: 0,
            fields: [],
            parsedOld: '',

        }),
        props: ['errors', 'declaration', 'old'],
        methods: {
            deleteDecl: function(){
                var check = confirm('Are you sure you want to delete this declaration?');
                if(check === true){
                    this.loading = true;
                    var vm = this;
                    axios({
                        url: '/dashboard/admin/declarations/delete/' + declarationData.id,
                        //data: formData,
                        method: 'get',
                        //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                        headers: {'Accept': 'application/json'}
                    })

                        //axios.get( '/fetch-dashboard' )
                        .then(function (response) {

                            vm.loading = false;
                            //vm.pages = response.data.pages;
                            window.location.href = '/dashboard/admin/declarations';

                        })
                        .catch(function (error) {
                            vm.loading = false;
                            vm.loadingError = true;
                            console.log(error);
                        });
                }
                return true;
            },
            addRow: function () {
                this.fields.push({'field_id': '', 'field_label': '', 'field_weight': '', 'field_value': ''});
                this.rows += 1;
            },
            deleteRow: function (i) {
//                alert('Delete row ' + i);
                let conf = confirm('Are you sure you want to remove this row?');
                if(conf === true){
                    let newArray = [];
                    for(var x = 0; x<this.fields.length; x++){
                        if(x !== i){
                            newArray.push(this.fields[x]);
                        }
                    }
                    this.fields = undefined;
                    this.fields = newArray;
                    this.rows -= 1;

                }
            }
        },
        computed: {
            fullName: function(){
                // let name = "";
                // if(this.profile != ''){
                //     if(this.profile.userProfile.middleName != undefined)
                //         name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.middleName + " " + this.profile.userProfile.familyName);
                //     else
                //         name = this.stripSlashes(this.profile.userProfile.firstName + " " + this.profile.userProfile.familyName);
                // }
                // return name.toUpperCase();
            },

        },
        created() {

        },
        mounted: function () {
            document.title = "StudentAidBC - Admin Pages - Edit Declaration";
            if(this.errors != ""){
                this.validationErrors = JSON.parse(this.errors);
                this.parsedOld = JSON.parse(this.old);
            }
            this.declarationData = JSON.parse(this.declaration);

            this.rows = this.declarationData.fields.length;
            this.fields = this.declarationData.fields;

            this.declarationStatus = this.declarationData.status === 1 ? 'active' : 'draft';
        },
        watch: {
        }

    }
</script>
