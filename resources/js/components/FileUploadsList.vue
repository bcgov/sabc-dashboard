<template>
    <div class="col-lg-7">
        <div v-if="uploads == ''" class="text-center">
            <img src="/dashboard/img/no-files-added.png"><br>
            <span>No Documents uploaded.</span>
        </div>
        <div v-else>
            <div class="card">
                <div class="card-header">Uploaded Documents</div>
                <div class="card-body p-0">
                    <ul class="list-group">
                        <li class="list-group-item" v-for="uploadedDoc in uploads">
                            <div class="float-left">
                                <a :href="'/dashboard/notifications/' + uploadedDoc.documentID" target="_blank">{{ uploadedDoc.fileName }}</a>
                            </div>
                            <div class="float-right">
                                <span class="badge badge-secondary">{{ uploadedDoc.dateUploaded }}</span>
                            </div>
                        </li>
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
            uploads: '',
            alertMsg: '',
        }),
        props: [],
        methods: {
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/fetch-uploads-list',
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
