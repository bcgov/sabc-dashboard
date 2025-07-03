<template>
<!-- Sidenav -->
    <div>
        <small v-if="sidePage?.status === 'active' && section === 'left'" v-html="sidePage.left_side"></small>
        <small v-if="sidePage?.status === 'active' && section === 'right'" v-html="sidePage.right_side"></small>
    </div>
<!-- end sidenav-->
</template>
<style scoped>

</style>
<script>
    import axios from "axios";

    export default {
        props: ['section', 'customurl'],
        data: () => ({
            sidePage: null,
            loading: true,
            loadingError: false,
        }),
        methods:{
            formatAppNumber: function(value){
                let year = value.slice(0, 4);
                let extra = value.slice(4);
                return year + '-' + extra;
            },
            parseUrl: function (){
                if(this.customurl != undefined){
                    return this.customurl;
                }
                //replace any url that contains an application number with a wildcard
                let url = window.location.pathname;
                let regex = /\/\d+$/;
                return url.replace(regex, '/*');
            },
            fetchData: function(){
                let url = this.parseUrl();
                this.loading = true;
                let vm = this;
                axios({
                    url: '/dashboard/fetch-side-pages?path=' + url,
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.sidePage = response.data.sidePage || {};

                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        console.log(error);
                    });
            },
        },
        mounted() {
            this.fetchData();
        }
    }
</script>
