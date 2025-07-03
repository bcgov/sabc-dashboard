<template>
    <div class="col-lg-12">
        <div v-if="uploads == ''" class="text-center">
            <img src="/dashboard/img/no-files-added.png"><br>
            <span>No Documents uploaded.</span>
        </div>
        <div v-else>
            <div class="card">
<!--                <div class="card-header">Appeals</div>-->
                <div class="card-body p-0">
                    <ul class="list-group">
                        <template  v-for="(uploadedDoc, i) in uploads">
                            <li class="list-group-item" :class="loadingItem === i ? 'disabled' : ''">
                                <div class="float-left">
                                    <small v-if="loadingItem === i" class="spinner-border spinner-border-sm m-1" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </small>
                                    <a @click="loadItem(i)" :href="'/dashboard/appeal-forms/' + uploadedDoc.documentID">{{ onlyName(uploadedDoc.fileName) }}</a>
                                </div>
                                <div class="float-right">
                                    <span class="badge badge-secondary">{{ uploadedDoc.dateUploaded }}</span>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>

    </div><!-- /.row -->
</template>
<style scoped>
a{
    overflow-wrap: break-word;
    word-break: break-all;
}
</style>
<script>
    import axios from 'axios';
    import ProfileChallengeQuestions from "./ProfileChallengeQuestions";

    export default {
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            uploads: '',
            alertMsg: '',
            loadingItem: '',
        }),
        props: [],
        methods: {
            onlyName: function (value) {
                var name = value.split('-_-');
                if(name[1] == undefined)
                    return value;
                return name[1];
            },
            loadItem: function(i){
                this.loadingItem = i;
            },
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/fetch-forms-list',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.uploads = response.data.uploads;
                        vm.alertMsg = response.data.alertMsg;

                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        console.log(error);
                    });
            },
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
        },
        watch: {
        }

    }

</script>
