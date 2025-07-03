<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">

            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load this page. Please try again later.</div>
            <template v-if="loading == false && loadingError == false">

                <div class="card">
                    <div class="card-header">Appendix #{{ formatAppNumber(app_id) }}</div>
                    <div class="card-body">
                        <div class="alert alert-contextual alert-info mb-3">
                            <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg>
                            <p>Your {{ appendix_type === 'APPENDIX1' ? 'Appendix 1' : 'Appendix 2'}} has been submitted. <br>Please print your declaration below and fill out all required fields. Your declaration must be signed and mailed to StudentAid BC.</p>
                        </div>
                        <a class="btn btn-light btn-block mb-3" target="_blank" :href="'/dashboard/declaration/signature/' + (appendix_type === 'APPENDIX1' ? 'parent' : 'spouse') + '/' + app_id">(Re)Print and Sign Appendix Declaration</a>
                    </div>
                </div>
            </template>
        </div>

    </div><!-- /.row -->
</template>
<style scoped>
    .alert-p{
        min-height: auto;
    }

    span.form-required {
        color: red;
    }
    legend{
        display: block;
        padding: 0 15px;
        background: #ffffff;
        margin: 0;
        font-size: 21px;
        font-family: "Questrial", "Century Gothic", Arial, Helvetica, sans-serif;
        line-height: 24px;
        color: #869198;
        width: auto;
    }
    fieldset{
        border: solid 2px #e6e9ea;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        padding: 0 25px;
        margin: 24px 0;
    }
</style>
<script>

    export default {
        data: () => ({
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            loading: true,
            loadingError: false,
            maintenanceMode: false,
        }),

        props: ['app_id', 'document_guid', 'appendix_type'],
        methods: {
            formatAppNumber: function(value){
                let year = value.slice(0, 4);
                let extra = value.slice(4);

                return year + '-' + extra;
            },
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },
        },
        created() {
            document.title = "StudentAidBC - Download Declaration";
        },
        mounted: function () {
            this.loading = false;
            document.title = "StudentAidBC - Download Declaration";
        },
        watch: {
        }

    }

</script>
