<template>
    <div>

    </div>
</template>
<style scoped>
    /*    to pass css to v-html child */
    div.row :deep(span.label) {
        cursor: default !important;
        text-transform: uppercase;
    }
    .card{
        border: none;
    }
    .card-header{
        background: white;
    }
    div.timeline{
        border-left: 5px solid #ccc;
    }
    .timeline ul{
        list-style: none;
        padding: 0;
    }
    .timeline ul li{
        position: relative;
    }
    .timeline ul li i.icon{
        position: absolute;
        top: 20px;
        right: 0;
    }
    .timeline ul li span.icon{
        position: absolute;
        background: #fff;
        top: 18px;
        left: -20px;
        font-weight: 300;
        font-size: 28px;
        z-index: 11;
        color: #7f818c;
        line-height: 28px;
    }
    .skinny{
        font-weight: 100;
    }
    .status-left{
        border-left: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
    }
    .status-right{
        border-right: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
    }
    .status-bar{
        position: relative;
        color: #ffffff;
        background: #3f414a;
        padding-top: 12px;
        padding-bottom: 12px;
    }
    .badge{
        border-radius: 50px;
    }
    .taskbar h4 {
        line-height: 50px;
        margin: 0;
        padding: 0;
        font-family: "Questrial", "Century Gothic", Arial, Helvetica, sans-serif;
        font-weight: normal;
    }

    h4 {
        font-size: 16.1px;
        font-weight: 700;
    }
    h2.slick{
        font-weight: 100;
        line-height: 15px;
        margin-bottom: 0;
    }
    h2>button.btn{
        color: initial;
        text-decoration: none;
        font-weight: 500;
    }
</style>
<script>
    //import axios from 'axios

    export default {
        props: ['app', 'eventItems', 'isInkSignReq', 'processingConsent', 'showEconsent'],
        data: () => ({
            accordion: false,
        }),
        methods: {
            formatDate: function (value) {
                if(value != undefined && value != ''){
                    var newValue = value.split(",");

                    return newValue[0];
                }
                return '-';
            },
            formatApplicationDate: function (value) {
                if(value != undefined && value != '' && value.length == 8){
                    //var newValue = value.split(" ");
                    let year = value.slice(0, 4);
                    let month = value.slice(4, 6);
                    let day = value.slice(6, 8);

                    let date = new Date(year + "-" + month + "-" + day + "T19:30:00.000Z");
                    let formatted = date.toDateString().split(" ");
                    return formatted[1] + " " +  formatted[2] + ", " + formatted[3];
                }
                return '-';
            },
            //you cannot update arrays directly in JS. The DOM won't see these changes.
            //https://stackoverflow.com/questions/51412250/vue-v-if-cant-access-booleans-in-arrays
            toggleAccordion: function(){
                this.accordion = !this.accordion;
            },
            fnStatusClass: function(e, defaultClass){
                // alert(e);
                // alert(defaultClass);
                //if(e.eventCategoryCode != undefined && e.eventCategoryCode != null) {
                let fnClass = e.eventCode != undefined && e.eventCode == 'Complete' ? 'text-success' : '';
                return defaultClass + ' ' + fnClass;
                //return this.statusFlags[this.app.applicationDetails.applicationTimeline.applicationTimelineCode].class;
                //return (e != undefined && e != null ? $default.' text-'.$statusFlags[$e['eventCategoryCode']]['class'] : $default;

                //}
                //return defaultClass;
            }


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
