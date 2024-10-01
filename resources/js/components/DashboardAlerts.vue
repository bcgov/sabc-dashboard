<template>
    <div v-if="alerts != ''" class="col-12 mt-3">
        <div v-for="(alert, i) in alerts" class="alert alert-contextual alert-dismissible fade show" :class="'alertbox' + i + ' alert-' + alert.type" role="alert">
                <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use :xlink:href="'/dashboard/assets/sprite/icons.svg#' + alertIcons[alert.type] "></use></svg>

                <button v-if="alert.dismissible == true" type="button" :id="'alertBtnClose' + i" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                <p v-html="alert.body"></p>
        </div><!-- /.alert -->
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        filters: {

        },

        data: () => ({
            alerts: "",
            disablePage: false,
            alertIcons: {
                'success': 'check',
                'danger': 'stopsign-alert',
                'info': 'info',
                'warning': 'warning',
                'primary': 'favorite',
            },
            pages: 'all', //options are 'all|login_register_pages|auth_pages
        }),
        props: ['pagesTarget'],
        methods: {
            fetchData: function(){
                this.loading = true;
                let vm = this;
                axios({
                    url: '/dashboard/fetch-alerts?target=' + this.pages,
                    method: 'get',
                    headers: {'Accept': 'application/json'}
                })

                //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.alerts = response.data.alerts;
                        for(var i=0; i<vm.alerts.length; i++){
                            if(vm.alerts[i].disable_page == true){
                                vm.disablePage = true;
                                break;
                            }
                        }

                        //tell the parent page to disable the page
                        if(vm.disablePage === true){
                            vm.$emit('disable', true);
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }

        },
        computed: {
        },
        created() {
        },
        mounted: function () {
            if(this.pagesTarget != undefined && this.pagesTarget != ''){
                this.pages = this.pagesTarget;
            }
            this.fetchData();
        },
        watch: {
        }
    }
</script>
