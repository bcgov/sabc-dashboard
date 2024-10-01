<template>
    <!-- Page Content-->
    <div class="page-content">

    </div>
    <!-- end page content -->

</template>

<script>
    //import SettingsLayoutsComponent from "../SettingsLayoutsComponent";
    export default {
        //components: {SettingsLayoutsComponent},
        data: () => ({
            current_page: '',
        }),

        methods: {
          checkPagePath(path){
              return this.current_page.startsWith(path);
          }
        },
        mounted() {
            this.current_page = this.$route.path;
        },

        watch: {
          '$route' (to, from) {
            //console.log(to);
            this.current_page = to.path;
          }
        },
    }
</script>
