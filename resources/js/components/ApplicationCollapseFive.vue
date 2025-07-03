<template>
        <div class="row">

            <template v-if="e.eventCategoryCode != undefined && e.eventCategoryCode != null">
                <div class="col-12">
                    <p class="alert alert-contextual alert-info">
                        <small class="text-danger mr-1">Important</small>
                        Funding can only be released once you’ve completed your Master Student Financial Assistance Agreement (MSFAA) and your school has confirmed your enrolment. We will initiate the Confirmation of Enrolment process shortly before the disbursement date. Once confirmed, you can expect to receive your funding within 7 business days of the disbursement date.
                    </p>
                    <hr>
                    <template v-if="Object.keys(app.applicationDetails.fundingDetails).length > 0">
                        <h6 class="text-uppercase">Disbursement Details</h6>
                        <div v-if="Array.isArray(app.applicationDetails.fundingDetails.disbursementGroup) == true" class="row">

                            <table v-for="value in app.applicationDetails.fundingDetails.disbursementGroup" width="100%" class="table small table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th width="55%">
                                        <div class="text-left">Type of funding</div>
                                        </th>
                                    <th width="15%">
                                        <div class="text-center">Amount</div>
                                        </th>
                                    <th width="15%">
                                        <div class="text-center">To be paid to school</div>
                                        </th>
                                    <th width="15%">
                                        <div class="text-center">Confirmation of Enrolment</div>
                                        </th>
                                    </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <td colspan="2"><strong>Amount Paid to School:</strong></td>
                                    <td colspan="2" class="text-right">${{value.totalFunds.paidToSchool}}</td>
                                    </tr>
                                <tr>
                                    <td colspan="2"><strong>Est. Earliest Disbursement:</strong></td>
                                    <td colspan="2" class="text-right">{{ formatApplicationDate(value.totalFunds.earliestDate) }}</td>
                                    </tr>
                                <tr>
                                    <td colspan="2"><strong>Total Funding:</strong></td>
                                    <td colspan="2" class="text-right">${{value.totalFunds.amount}}</td>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    <template v-for="fund in value.funds">
                                        <template v-if="Array.isArray(fund)">
                                            <tr v-for="f in fund">
                                                <td>{{f.typeOfFunding}}</td>
                                                <td>
                                                    <div class="text-center">${{f.amount}}</div>
                                                    </td>
                                                <td>
                                                    <div class="text-center">${{f.paidToSchool}}</div>
                                                    </td>
                                                <td>
                                                    <div v-if="e.events[f.disbursementID].eventCode == 'Complete'" class="text-right">
                                                        {{ formatDate(e.events[f.disbursementID].eventDate) }}
                                                        <i class="icon icon-uniF479 text-success" alt="Enrolment Confirmed" title="Enrolment Confirmed"></i>
                                                    </div>
                                                    <div v-else class="text-right">
                                                        <span class="badge" :class="'badge-' + statusFlags[e.events[f.disbursementID].eventCode]['class'] + ''">
                                                        {{statusFlags[e.events[f.disbursementID].eventCode]['status']}}
                                                        </span>
                                                    </div>

                                                </td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr>
                                                <td>{{fund.typeOfFunding}}</td>
                                                <td>
                                                    <div class="text-center">${{fund.amount}}</div>
                                                    </td>
                                                <td>
                                                    <div class="text-center">${{fund.paidToSchool}}</div>
                                                    </td>
                                                <td>
                                                    <div v-if="e.events[fund.disbursementID].eventCode == 'Complete'" class="text-right">
                                                        {{ formatDate(e.events[f.disbursementID].eventDate) }}
                                                        <i class="icon icon-uniF479 text-success" alt="Enrolment Confirmed" title="Enrolment Confirmed"></i>
                                                        </div>
                                                    <div v-else class="text-right">
                                                        <span class="badge" :class="'badge-' + statusFlags[e.events[fund.disbursementID].eventCode]['class'] + ''">
                                                        {{statusFlags[e.events[fund.disbursementID].eventCode]['status']}}
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div v-else-if="typeof (app.applicationDetails.fundingDetails.disbursementGroup) === 'object'" class="row">

                            <table width="100%" class="table small table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th width="55%">
                                    <div class="text-left">Type of funding</div>
                                    </th>
                                <th width="15%">
                                    <div class="text-center">Amount</div>
                                    </th>
                                <th width="15%">
                                    <div class="text-center">To be paid to school</div>
                                    </th>
                                <th width="15%">
                                    <div class="text-center">Confirmation of Enrolment</div>
                                    </th>
                                </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <td colspan="2"><strong>Amount Paid to School:</strong></td>
                                <td colspan="2" class="text-right">${{app.applicationDetails.fundingDetails.disbursementGroup.totalFunds.paidToSchool}}</td>
                                </tr>
                            <tr>
                                <td colspan="2"><strong>Est. Earliest Disbursement:</strong></td>
                                <td colspan="2" class="text-right">{{ formatApplicationDate(app.applicationDetails.fundingDetails.disbursementGroup.totalFunds.earliestDate) }}</td>
                                </tr>
                            <tr>
                                <td colspan="2"><strong>Total Funding:</strong></td>
                                <td colspan="2" class="text-right">${{app.applicationDetails.fundingDetails.disbursementGroup.totalFunds.amount}}</td>
                                </tr>
                            </tfoot>
                            <tbody>
                                <template v-for="fund in app.applicationDetails.fundingDetails.disbursementGroup.funds">
                                    <template v-if="Array.isArray(fund)">
                                        <tr v-for="f in fund">
                                            <td>{{f.typeOfFunding}}</td>
                                            <td>
                                                <div class="text-center">${{f.amount}}</div>
                                                </td>
                                            <td>
                                                <div class="text-center">${{f.paidToSchool}}</div>
                                                </td>
                                            <td>
                                                <div v-if="e.events[f.disbursementID].eventCode == 'Complete'" class="text-right">
                                                    {{ formatDate(e.events[f.disbursementID].eventDate) }}
                                                    <i class="icon icon-uniF479 text-success" alt="Enrolment Confirmed" title="Enrolment Confirmed"></i>
                                                </div>
                                                <div v-else class="text-right">
                                                    <span class="badge" :class="'badge-' + statusFlags[e.events[f.disbursementID].eventCode]['class'] + ''">
                                                    {{statusFlags[e.events[f.disbursementID].eventCode]['status']}}
                                                    </span>
                                                </div>

                                            </td>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr>
                                            <td>{{fund.typeOfFunding}}</td>
                                            <td>
                                                <div class="text-center">${{fund.amount}}</div>
                                                </td>
                                            <td>
                                                <div class="text-center">${{fund.paidToSchool}}</div>
                                                </td>
                                            <td>
                                                <div v-if="e.events[f.disbursementID].eventCode == 'Complete'" class="text-right">
                                                    {{ formatDate(e.events[f.disbursementID].eventDate) }}
                                                    <i class="icon icon-uniF479 text-success" alt="Enrolment Confirmed" title="Enrolment Confirmed"></i>
                                                </div>
                                                <div v-else class="text-right">
                                                    <span class="badge" :class="'badge-' + statusFlags[e.events[f.disbursementID].eventCode]['class'] + ''">
                                                    {{statusFlags[e.events[f.disbursementID].eventCode]['status']}}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>


                        </div>

                    </template>
                </div>
<!--                <div class="col-12">-->
<!--                    <div id="panel">-->
<!--                        <hr class="print-only" />-->
<!--                        <div class="content">-->
<!--                            <div class="arow taskbar">-->
<!--                                <div class="inner">-->
<!--                                    <a href="#" class="badge togglepanel" aria-hidden="true">Close</a>&nbsp;&nbsp;<h3>Calculation of Assessment</h3>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
            </template>

        </div>
</template>
<style scoped>

</style>
<script>
    //import axios from 'axios

    export default {
        props: ['app', 'e'],
        data: () => ({

            statusFlags: {
                'In Progress': {'status': 'In Progress', 'class': 'info'},
                'Complete': {'status': 'Complete', 'class': 'success'},
                'Waiting': {'status': 'Waiting', 'class': 'warning'},
                'Scheduled': {'status': 'Scheduled', 'class': 'info'},
                'Missing Info': {'status': 'Missing Info', 'class': 'important'},
                'Missing Information': {'status': 'Missing Information', 'class': 'important'},
                'Cancelled': {'status': 'Cancelled', 'class': 'danger'},
                'Not Required': {'status': 'Not Required Yet', 'class': ''}

            },

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
