<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">
            <div class="taskbar">
                <h4>Notifications</h4>
                <hr class="mt-0"/>
            </div>

            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load your notifications. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">
                <ul class="list-group" v-if="notifications.documents != undefined && notifications.documents != ''">
                    <template v-for="note in notifications.documents">
                        <li v-for="e in note[1]" class="list-group-item" :class="e.Marked">
                            <div class="float-left">
                                <a :href="'/dashboard/notifications/' + e.DocumentID + ''" target="_blank">
                                    <span class="icon-circle" :class="e.Marked == 'unread' ? 'text-danger' : 'text-muted'"></span>
                                    {{e.Title}}
                                </a>
                            </div>
                            <div class="float-right">
                                <span class="badge badge-secondary">{{e.Created}}</span>
                            </div>
                        </li>
                    </template>
                </ul>

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
    import ProfileChallengeQuestions from "./ProfileChallengeQuestions";

    export default {
        filters: {

            formatAppNumber: function(value){
                let year = value.slice(0, 4);
                let extra = value.slice(4);

                return year + '-' + extra;
            }
        },

        data: () => ({
            loading: true,
            loadingError: false,
            notifications: '',
            maintenanceMode: false,
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
                    url: '/dashboard/fetch-notifications',
                    //data: formData,
                    method: 'get',
                    headers: {'Accept': 'application/json'}
                })

                    .then(function (response) {
                        vm.loading = false;
                        vm.notifications = response.data.notifications;
                        if(vm.notifications.documents != null){
                            let alertsArray = Object.entries(vm.notifications.documents);

                            // Sort the array by key (in descending order)
                            alertsArray.sort(([keyA], [keyB]) => keyB - keyA);

                            // Assign the sorted alerts to vm.notifications
                            vm.notifications.documents = alertsArray;
                        }
                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        console.log(error);
                    });
            },

        },
        computed: {

        },
        created() {

        },
        mounted: function () {
            this.fetchData();
            document.title = "StudentAidBC - Message Centre";


        },
        watch: {

        }

    }

</script>
