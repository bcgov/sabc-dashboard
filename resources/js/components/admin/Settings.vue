<template>
    <div class="container-fluid">
        <div class="card mt-3 mb-3">
            <div class="card-header">Update text in Declarations</div>
            <div class="card-body">
                <form id="settings-update-declarations">
                    <div class="row">
                        <div class="col">
                            <input @keyup="resetDec" name="dec_old_text" type="text" class="form-control" placeholder="Old text" aria-label="Old text" v-model="decOldText">
                        </div>
                        <div class="col">
                            <input name="dec_new_text" type="text" class="form-control" placeholder="New text" aria-label="New text" v-model="decNewText" :disabled="findsDeclaration.length > 0">
                        </div>
                        <div class="col">
                            <select name="dec_year" class="form-control" aria-label="Year" v-model="decYear" :disabled="findsDeclaration.length > 0">
                                <option value="2020/2021">2020/2021</option>
                                <option value="2021/2022">2021/2022</option>
                                <option value="2022/2023">2022/2023</option>
                                <option value="2023/2024">2023/2024</option>
                                <option value="2024/2025">2024/2025</option>
                            </select>
                        </div>
                        <div class="col">
                            <button @click="fetchData('declarations')" type="button" class="btn btn-primary" :disabled="findsDeclaration.length > 0">Find</button>
                        </div>
                    </div>
                </form>

                <table v-if="findsDeclaration.length > 0" class="mt-3 table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-primary" scope="col">Name</th>
                        <th class="fit text-primary" scope="col">Type</th>
                        <th class="fit text-primary" scope="col">Text Body</th>
                        <th scope="col" style="width: 70px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(dec, i) in findsDeclaration">
                        <td class="fit" scope="row">{{dec.name}}</td>
                        <td class="fit">{{dec.type}}</td>
                        <td class="fit">{{dec.field_text}}</td>
                        <td><button @click="updateDec(i, dec.field_id)" type="button" class="btn btn-sm btn-primary dec-update-button">Update</button></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">Update text in Side Pages</div>
            <div class="card-body">
                <form id="settings-update-side-pages">
                    <div class="row">
                        <div class="col">
                            <input @keyup="resetSidePage" name="side_page_old_text" type="text" class="form-control" placeholder="Old text" aria-label="Old text" v-model="sidePageOldText">
                        </div>
                        <div class="col">
                            <input name="side_page_new_text" type="text" class="form-control" placeholder="New text" aria-label="New text" v-model="sidePageNewText" :disabled="findsSidePages.length > 0">
                        </div>
                        <div class="col">
                            <button @click="fetchData('side-pages')" type="button" class="btn btn-primary" :disabled="findsSidePages.length > 0">Find</button>
                        </div>
                    </div>
                </form>

                <table v-if="findsSidePages.length > 0" class="mt-3 table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-primary" scope="col">Page</th>
                        <th class="fit text-primary" scope="col">Page Section</th>
                        <th class="fit text-primary" scope="col">Text Body</th>
                        <th scope="col" style="width: 70px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(sidePage, i) in findsSidePages">
                        <td class="fit" scope="row">{{sidePage.page.name}}</td>
                        <td class="fit">{{sidePage.section}}</td>
                        <td class="fit">{{sidePage.page[sidePage.ref]}}</td>
                        <td><button @click="updateSidePage(sidePage)" type="button" class="btn btn-sm btn-primary side-page-update-button">Update</button></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="submitSuccess !== null">
            <div v-if="submitSuccess === true" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                <div id="updateSuccessAlert" class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="">
                        <div class="toast-body">{{ submitMsg }}</div>
                    </div>
                </div>
            </div>
            <div v-if="submitSuccess === false" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                <div id="updateFailAlert" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="">
                        <div class="toast-body">{{ submitMsg }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.me-2 {
    margin-right: 0.5rem!important;
}
.m-auto {
    margin: auto!important;
}
.p-3 {
    padding: 1rem!important;
}
.end-0 {
    right: 0!important;
}
.bottom-0 {
    bottom: 0!important;
}
.position-fixed {
    position: fixed!important;
}
</style>
<script>
    import axios from 'axios';

    export default {

        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: false,
            submitSuccess: null,
            submitMsg: '',
            findsDeclaration: [],
            findsSidePages: [],
            decOldText: '',
            decNewText: '',
            decYear: '',
            sidePageOldText: '',
            sidePageNewText: '',

        }),
        props: ['counter'],
        methods: {
            resetDec: function (){
                this.findsDeclaration = [];
            },
            resetSidePage: function (){
                this.findsSidePages = [];
            },
            updateDec: function (index, id) {
                $(".dec-update-button").prop("disabled", true);
                let vm = this;
                let formData = new FormData();
                formData.append('_token', this.csrf);
                formData.append('old_text', this.decOldText);
                formData.append('new_text', this.decNewText);
                formData.append('year', this.decYear);

                axios({
                    url: '/dashboard/admin/settings-dec-update/' + id,
                    data: formData,
                    method: 'post',
                    headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                })
                    .then(function (response) {
                        $(".dec-update-button").prop("disabled", false);
                        this.fetchData('declarations');
                    })
                    .catch(function (error) {
                        $(".dec-update-button").prop("disabled", false);
                        vm.submitSuccess = false;
                        if(error.response !== undefined && error.response.data.message != null){
                            vm.submitMsg = error.response.data.message;
                        }else{
                            console.log(error);
                        }
                        setTimeout(function(){
                            vm.submitSuccess = null;
                        }, 3000);

                    });
            },
            updateSidePage: function (sidePage) {
                $(".side-page-update-button").prop("disabled", true);
                let vm = this;
                let formData = new FormData();
                formData.append('_token', this.csrf);
                formData.append('old_text', this.sidePageOldText);
                formData.append('new_text', this.sidePageNewText);
                formData.append('ref', sidePage.ref);

                axios({
                    url: '/dashboard/admin/settings-side-page-update/' + sidePage.page.id,
                    data: formData,
                    method: 'post',
                    headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                })
                    .then(function (response) {
                        $(".side-page-update-button").prop("disabled", false);
                        this.fetchData('side-pages');
                    })
                    .catch(function (error) {
                        $(".side-page-update-button").prop("disabled", false);
                        vm.submitSuccess = false;
                        if(error.response !== undefined && error.response.data.message != null){
                            vm.submitMsg = error.response.data.message;
                        }else{
                            console.log(error);
                        }
                        setTimeout(function(){
                            vm.submitSuccess = null;
                        }, 3000);

                    });
            },
            fetchData: function(type){
                this.findsDeclaration = [];
                this.findsSidePages = [];
                $("#settings-update-declarations button").prop("disabled", true);
                $("#settings-update-side-pages button").prop("disabled", true);
                this.loading = true;
                let vm = this;
                let name = 'settings-update-' + type;
                let myForm = document.getElementById(name);
                let formData = new FormData(myForm);
                formData.append('_token', this.csrf);

                axios({
                    url: '/dashboard/admin/settings-fetch/' + type,
                    data: formData,
                    method: 'post',
                    headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                })
                    .then(function (response) {

                        vm.loading = false;
                        $("#settings-update-declarations button").prop("disabled", false);
                        $("#settings-update-side-pages button").prop("disabled", false);
                        if(response.data.finds.length === 0) {
                            vm.submitSuccess = false;
                            vm.submitMsg = "No records were found!";
                        }else{
                            vm.submitSuccess = true;
                            vm.submitMsg = response.data.finds.length + " records were found.";
                        }
                        if(type === 'declarations') {
                            vm.findsDeclaration = response.data.finds;
                        }
                        if(type === 'side-pages') {
                            vm.findsSidePages = response.data.finds;
                        }
                        setTimeout(function(){
                            vm.submitSuccess = null;
                        }, 3000);

                    })
                    .catch(function (error) {
                        vm.submitSuccess = false;
                        $("#settings-update-declarations button").prop("disabled", false);
                        $("#settings-update-side-pages button").prop("disabled", false);
                        if(error.response !== undefined && error.response.data.message != null){
                            vm.submitMsg = error.response.data.message;
                        }else{
                            console.log(error);
                        }
                        setTimeout(function(){
                            vm.submitSuccess = null;
                        }, 3000);

                    });
            },
        },

        mounted: function () {
            document.title = "StudentAidBC - Admin Settings";
        },

    }
</script>
