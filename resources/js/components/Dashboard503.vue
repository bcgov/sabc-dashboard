<template>
    <div class="row justify-content-md-center">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div class="col-8">


            <div class="text-center p-5">

                <img src="/dashboard/img/logo-mo@2x.png">
                <img class="img-fluid" src="/dashboard/img/sabc-503.jpg">
<!--                <br/>-->

<!--                <h2 class="mt-4 mb-2">We are currently close for maintenance</h2>-->
<!--                <p>Thanks for your patience.</p>-->
            </div>

        </div>

    </div><!-- /.row -->
</template>
<style scoped>

</style>
<script>
    import axios from 'axios';
    import ProfileChallengeQuestions from "./ProfileChallengeQuestions";

    export default {
        filters: {

        },

        data: () => ({

        }),
        props: [],
        methods: {
            disablePage: function(e){},
        },
        computed: {

        },
        created() {

        },
        mounted: function () {

        },
        watch: {
        }

    }

</script>
