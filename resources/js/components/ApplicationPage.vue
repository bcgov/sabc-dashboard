<template>
    <div class="row">
        <dashboard-alerts @disable="disablePage" pagesTarget="auth_pages"></dashboard-alerts>
        <div v-if="maintenanceMode === false" class="col-12">
            <div class="taskbar">
                <h4>Check application status</h4>
                <hr class="mt-0"/>
            </div>
            <div v-if="loading == true" class="jumbotron text-center h4">Loading ...</div>
            <div v-if="loading == false && loadingError == true" class="jumbotron text-center h4">Sorry! We could not load your application. Please try again later.</div>
            <template v-if="loading == false && loadingError == false && app !='' && eventItems !=''">

                <div v-if="eventItems.appSubmitted == false && eventItems.documentGUID != '' && eventItems.documentGUID != undefined" class="row mb-4">
                    <div v-if="resenderror != ''" class="col-12">
                        <div class="alert alert-contextual alert-danger">
                            <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#stopsign-alert"></use></svg>
                            <p>{{resenderror}}</p>
                        </div>

                    </div><!-- end resend error message -->
                    <div v-if="resendsuccess != ''" class="col-12">
                        <div class="alert alert-contextual alert-success">
                            <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#check"></use></svg>
                            <p>{{resendsuccess}}</p>
                        </div>

                    </div><!-- end resend error message -->

                    <application-action-buttons :app="app" :items="eventItems"></application-action-buttons>
                </div>
                <div class="row p-3">
                    <div class="col-12 status-bar">
                        <div class="">
                            <span>Application #{{app.applicationDetails.applicationNumber | formatAppNumber}}</span>
                            <span class="btn btn-sm float-right" :class="'btn-' + appStatusClass">{{appStatus}}</span>
                        </div>
                    </div>
                    <div class="col-md-6 status-left pt-3 pb-3 bg-white">
                        <div>
                            <h6 class="skinny text-uppercase">funding details</h6>
                            <div class="row">
                                <div class="d-block d-md-none col-md-4"><strong>Total Award</strong></div>
                                <div class="d-none d-md-block col-md-4 text-right"><strong>Total Award</strong></div>
                                <div v-if="eventItems.totalAward != '--' && eventItems.totalAward != ''" class="col-md-8">
                                    <h2 class="slick text-success">{{eventItems.totalAward}}</h2><br>
                                    <small><i class="icon-supportrequest muted"></i><a href="#" class="togglepanel muted"> How was this calculated?</a></small>

                                </div>
                                <div v-else class="col-md-8 text-success">
                                    <span>--</span><br>
                                    <small><i class="icon-supportrequest muted"></i><button type="button" class="btn btn-link text-muted" data-toggle="modal" data-target="#exampleModal"> How was this calculated?</button></small>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div>
                            <h6 class="skinny text-uppercase">application details</h6>
                            <div v-if="app.applicationDetails.applicationProfile == null || app.applicationDetails.applicationProfile == undefined || app.applicationDetails.applicationProfile == ''" class="row">
                                <div class="col-12">N/A</div>
                            </div>
                            <template v-else>
                                <div class="row">
                                    <div class="d-block d-md-none col-md-4"><strong>Start Date</strong></div>
                                    <div class="d-none d-md-block col-md-4 text-right"><strong>Start Date</strong></div>
                                    <div class="col-md-8">{{app.applicationDetails.applicationProfile.studySDate | formatApplicationDate}}</div>
                                </div>
                                <div class="row">
                                    <div class="d-block d-md-none col-md-4"><strong>End Date</strong></div>
                                    <div class="d-none d-md-block col-md-4 text-right"><strong>End Date</strong></div>
                                    <div class="col-md-8">{{app.applicationDetails.applicationProfile.studyEDate | formatApplicationDate}}</div>
                                </div>
                                <div class="row">
                                    <div class="d-block d-md-none col-md-4"><strong>Institution</strong></div>
                                    <div class="d-none d-md-block col-md-4 text-right"><strong>Institution</strong></div>
                                    <div class="col-md-8">{{app.applicationDetails.applicationProfile.institution.schoolName | cleanText}}</div>
                                </div>
                                <div class="row">
                                    <div class="d-block d-md-none col-md-4"><strong>Program</strong></div>
                                    <div class="d-none d-md-block col-md-4 text-right"><strong>Program</strong></div>
                                    <div class="col-md-8">{{app.applicationDetails.applicationProfile.program | cleanText}}</div>
                                </div>
                            </template>
                        </div>
                        <hr/>
                        <div v-if="app.applicationDetails.pdfLetters != undefined && app.applicationDetails.pdfLetters != null">
                            <h6 class="skinny text-uppercase">document centre</h6>
                            <template v-for="(letter, key, index) in app.applicationDetails.pdfLetters">
                                <template v-if="Array.isArray(letter)">
                                    <div v-for="(value, name) in letter" class="row">
                                        <div class="d-block d-md-none col-md-4"><small>{{value.sendDate | formatLetterDate}}</small></div>
                                        <div class="d-none d-md-block col-md-4 text-right"><small>{{value.sendDate | formatLetterDate}}</small></div>
                                        <div class="col-md-8">
                                            <a :href="'/dashboard/notifications/' + value.letterID + '/' + (app.applicationDetails.applicationStatus == 'SUBMPROC' ? 'T' : 'R') + ''" target="_blank">{{value.letterDescription}}</a>
                                        </div>
                                    </div>
                                </template>
                                <div v-else class="row">
                                    <div class="d-block d-md-none col-md-4"><small>{{letter.sendDate | formatLetterDate}}</small></div>
                                    <div class="d-none d-md-block col-md-4 text-right"><small>{{letter.sendDate | formatLetterDate}}</small></div>
                                    <div class="col-md-8">
                                        <a :href="'/dashboard/notifications/' + letter.letterID + '/' + (app.applicationDetails.applicationStatus == 'SUBMPROC' ? 'T' : 'R') + ''" target="_blank">{{letter.letterDescription}}</a>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>
                    <div class="col-md-6 status-right pb-3 pr-0 pl-0">
                        <div class="timeline">
                            <ul class="accordion" id="accordionExample">
                                <li>
                                    <span v-if="eventItems['Start/Submit Application'].eventCategoryCode == 'Complete'" class="icon icon-uniF479 text-success"></span>
                                    <span v-else class="icon" :class="fnStatusClass(eventItems['Start/Submit Application'].eventCategoryCode, 'icon-addtolist')"></span>
                                <div class="card rounded-0">
                                    <div class="card-header" id="headingOne">
                                        <h2 class="mb-0">
                                            <button @click="toggleAccordion(0)" class="btn btn-link btn-block text-left" type="button">
                                                Start/Submit Application
                                                <i class="float-right icon pr-3" :class="accordion[0] === true ? 'icon-circleup' : 'icon-circledown'"></i>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseOne" class="collapse" :class="accordion[0] === true ? 'show' : 'hide'">
                                        <div class="card-body">
<!--                                            $s .= '<dl class="dl-horizontal-med">';-->
<!--                                            if(isset($event['eventDate']) && !empty($event['eventDate'])){-->
<!--                                            $s .= '<dt class="left">';-->
<!--                                                $s .= '<small>';-->
<!--                                                    $s .= $event['eventDate'];-->
<!--                                                    $s .= '</small>';-->
<!--                                                $s .= '</dt>';-->
<!--                                            $s .= '<dd>'. $event['eventDescription'] .'</dd>';-->
<!--                                            }-->
<!--                                            else-->
<!--                                            {-->
<!--                                            $s .= (!empty($event['eventDescription'])) ? '<p>- '.$event['eventDescription'].'</p>' : NULL;-->
<!--                                            }-->

<!--                                            $s .= '</dl>';-->
                                            <div v-for="event in eventItems['Start/Submit Application'].events" v-if="Array.isArray(event) == false" class="row">
                                                <template v-if="event.eventDate != undefined && event.eventDate != null">
                                                    <div class="col-5"><small>{{event.eventDate}}</small></div>
                                                    <div class="col-7">{{event.eventDescription}}</div>
                                                </template>
                                                <template v-else>
                                                    <p class="col-12" v-if="event.eventDescription != undefined && event.eventDescription != null">- {{event.eventDescription}}</p>
                                                </template>
                                            </div>

                                            <div v-for="event in eventItems['Start/Submit Application'].events.appendices">
<!--                                                if(isset($event['eventDate']) && !empty($event['eventDate'])){-->
<!--                                                $s .= '<dt class="left">';-->
<!--                                                $s .= '<small>';-->
<!--                                                    $s .= $event['eventDate'];-->
<!--                                                    $s .= '</small>';-->
<!--                                                $s .= '</dt>';-->
<!--                                                $s .= '<dd>'.$event['eventType'].'</dd>';-->
<!--                                                }-->
<!--                                                else-->
<!--                                                {-->
<!--                                                $s .= '<dd style="margin-left:0;">'.$event['eventType'].'</dd>';-->
<!--                                                }-->
                                                <template v-if="event.eventDate != undefined && event.eventDate != null">
                                                    <div class="row mt-3">
                                                        <div class="col-5"><small>{{event.eventDate}}</small></div>
                                                        <div class="col-7">{{event.eventType}}</div>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div v-html="event.eventType" class="row mt-3"></div>
                                                </template>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                </li>

                                <li>
                                    <span v-if="app.applicationDetails.applicationStatus == 'INPR' || app.applicationDetails.applicationStatus == 'RDY'" class="icon icon-uniF47C"></span>
                                    <template v-else>
                                        <span v-if="enableEconsent == true" class="icon icon-uniF479 text-success"></span>
                                        <span v-else-if="eventItems['Submit Declaration'] != undefined && eventItems['Submit Declaration'].eventCategoryCode == 'Complete'" class="icon icon-uniF479 text-success"></span>
                                        <span v-else class="icon" :class="fnStatusClass(eventItems['Submit Declaration'].eventCategoryCode, 'icon-uniF47C')"></span>
                                    </template>

                                    <div class="card rounded-0">
                                    <div class="card-header" id="headingTwo">
                                        <h2 class="mb-0">
                                            <button v-if="app.applicationDetails.applicationStatus == 'INPR' || app.applicationDetails.applicationStatus == 'RDY'" @click="toggleAccordion(1)" class="btn btn-link btn-block text-left" type="button">
                                                E-Consent Not Submitted
                                                <i class="float-right icon pr-3" :class="accordion[1] === true ? ' icon-circleup' : ' icon-circledown'"></i>
                                            </button>
                                            <template v-else>
                                                <button v-if="enableEconsent == true" @click="toggleAccordion(1)" class="btn btn-link btn-block text-left" type="button">
                                                    E-Consent
                                                    <i class="float-right icon pr-3" :class="accordion[1] === true ? ' icon-circleup' : ' icon-circledown'"></i>
                                                </button>
                                                <template v-else>
                                                    <button v-if="eventItems['Submit Declaration'].eventCategoryCode != undefined && eventItems['Submit Declaration'].eventCategoryCode == 'Complete'" @click="toggleAccordion(1)" class="btn btn-link btn-block text-left" type="button">
                                                        Submit Declaration
                                                        <i class="float-right icon pr-3" :class="accordion[1] === true ? ' icon-circleup' : ' icon-circledown'"></i>
                                                    </button>
                                                    <button v-else-if="eventItems['Submit Declaration'].eventCategoryCode != undefined" @click="toggleAccordion(1)" class="btn btn-link btn-block text-left" type="button">
                                                        Submit Declaration
                                                        <i class="float-right icon pr-3" :class="accordion[1] === true ? ' icon-circleup' : ' icon-circledown'"></i>
                                                    </button>
                                                    <button v-else @click="toggleAccordion(1)" class="btn btn-link btn-block text-left" type="button">
                                                        E-Consent
                                                        <i class="float-right icon pr-3" :class="accordion[1] === true ? ' icon-circleup' : ' icon-circledown'"></i>
                                                    </button>

                                                </template>

                                            </template>

                                        </h2>
                                    </div>
                                    <div id="collapseTwo" class="collapse" :class="accordion[1] === true ? 'show' : 'hide'">
                                        <div class="card-body">

                                            <template v-if="app.applicationDetails.applicationStatus == 'INPR' || app.applicationDetails.applicationStatus == 'RDY'"></template>
                                            <template v-else>

                                                <a v-if="enableEconsent == true" class="btn btn-light btn-block" target="_blank" :href="'/dashboard/declaration/econsent/applicant/' + app.applicationDetails.applicationNumber">View Declaration</a>
                                                <template v-else>

                                                    <div v-for="event in eventItems['Submit Declaration'].events">
                                                        <template v-if="event.eventDate != undefined && event.eventDate != null">
                                                            <template v-if="eventItems['webDecStatus'] != undefined">
                                                                <div v-if="eventItems['webDecStatus'] == 't'" class="row mt-3">
                                                                    <div class="col-5"><small>{{event.eventDate}}</small></div>
                                                                    <div class="col-7">{{event.eventDescription}}</div>
                                                                </div>
                                                                <template v-else>
                                                                    <div v-if="eventItems['webDecStatus'] == 'e'" class="row mt-3">
                                                                        <div class="col-12"><p class="alert alert-contextual alert-info"><i class="icon icon-info">&nbsp;</i> Web declaration no longer available on-line, if you require a copy please contact StudentAidBC at 1-800-561-1818</p></div>
                                                                        <div class="col-7">{{event.eventDescription}}</div>
                                                                    </div>
                                                                    <div v-else-if="eventItems['webDecStatus'] == 'f' || eventItems['webDecStatus'] == 'f1' || eventItems['webDecStatus'] == 'f2'" class="row mt-3">
                                                                        <div class="col-5"><small>{{event.eventDate}}</small></div>
                                                                        <div class="col-7">{{event.eventDescription}}</div>
                                                                        <div class="col-12">{{event.webDecState}}</div>
                                                                    </div>
                                                                    <div v-else class="row mt-3">
                                                                        <div class="col-12">{{event.webDecState}}</div>
                                                                    </div>
                                                                </template>
                                                            </template>

                                                            <div v-else class="row mt-3">
                                                                <div class="col-5"><small>{{event.eventDate}}</small></div>
                                                                <div class="col-7">{{event.eventDescription}}</div>
                                                            </div>
                                                        </template>
                                                        <template v-else>
<!--                                                            $s .= (!empty($event['eventDescription'])) ? '<p>- '.$event['eventDescription'].'</p>' : NULL;-->

<!--                                                            if(isset($eventItems['webDecStatus'])){-->
<!--                                                            // if app has not been submitted then show print declaration button-->
<!--                                                            if($eventItems['webDecStatus'] == 't'){-->
<!--                                                            $s .= $event['webDecState'];-->
<!--                                                            }-->
<!--                                                            else-->
<!--                                                            {-->
<!--                                                            if($eventItems['webDecStatus'] == 'e'){-->
<!--                                                            $s .= '<p>';-->
<!--                                                            $s .= '<div class="alert alert-info"><i class="icon icon-info">&nbsp;</i> Web declaration no longer available on-line, if you require a copy please contact StudentAidBC at 1-800-561-1818</div>';-->
<!--                                                            $s .= '</p>';-->
<!--                                                            }-->
<!--                                                            else if($eventItems['webDecStatus'] == 'f' || $eventItems['webDecStatus'] == 'f1' || $eventItems['webDecStatus'] == 'f2'){-->
<!--                                                            $s .= '<br>';-->
<!--                                                            $s .= $event['webDecState'];-->
<!--                                                            }-->
<!--                                                            else {-->
<!--                                                            $s .= $event['webDecState'];-->
<!--                                                            }-->
<!--                                                            }-->
<!--                                                            }-->
                                                            <p v-if="event.eventDescription != null && event.eventDescription != ''">- {{event.eventDescription}}</p>
                                                            <template v-if="eventItems['webDecStatus'] != undefined && eventItems['webDecStatus'] != null">
                                                                <p v-if="eventItems['webDecStatus'] == 't'" v-html="event.webDecState"></p>
                                                                <div v-else class="row mt-3">
                                                                    <p v-if="eventItems['webDecStatus'] == 'e'" class="col-12 alert alert-contextual alert-info"><i class="icon icon-info">&nbsp;</i> Web declaration no longer available on-line, if you require a copy please contact StudentAidBC at 1-800-561-1818</p>
                                                                    <p v-else-if="eventItems['webDecStatus'] == 'f' || eventItems['webDecStatus'] == 'f1' || eventItems['webDecStatus'] == 'f2'" class="col-12" v-html="event.webDecState"></p>
                                                                    <p v-else class="col-12" v-html="event.webDecState"></p>


                                                                </div>
                                                            </template>
                                                        </template>

                                                    </div>
                                                </template>

                                            </template>
                                        </div>
                                    </div>
                                </div>
                                </li>

                                <li>
                                    <span v-if="eventItems['Application Review'] != undefined && eventItems['Application Review'].eventCategoryCode == 'Complete'" class="icon icon-uniF479 text-success"></span>
                                    <span v-else class="icon" :class="fnStatusClass(eventItems['Application Review'].eventCategoryCode, 'icon-eye2')"></span>
                                    <div class="card rounded-0">
                                        <div class="card-header" id="headingThree">
                                            <h2 class="mb-0">
                                                <button @click="toggleAccordion(2)" class="btn btn-link btn-block text-left" type="button">
                                                    Application Review
                                                    <i class="float-right icon pr-3" :class="accordion[2] === true ? ' icon-circleup' : ' icon-circledown'"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseThree" class="collapse" :class="accordion[2] === true ? 'show' : 'hide'">
                                            <div class="card-body">
                                                <div v-for="event in eventItems['Application Review'].events" class="row">
                                                    <template v-if="event.eventIncomplete != undefined && event.eventIncomplete != null">
                                                        <div class="col-12"><p class="alert alert-contextual alert-danger">{{event.eventIncomplete}}</p></div>
                                                    </template>

                                                    <template v-else-if="event.eventDate != undefined && event.eventDate != null">
                                                        <div class="col-5"><small>{{event.eventDate}}</small></div>
                                                        <div class="col-7">{{event.eventDescription}}</div>
                                                    </template>
                                                    <template v-else>
                                                        <p class="col-12" v-if="event.eventDescription != undefined && event.eventDescription != null">- {{event.eventDescription}}</p>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <span v-if="fundingDecisionInProgress == false" class="icon icon-uniF479 text-success"></span>
                                    <span v-else class="icon" :class="fnStatusClass(eventItems['Funding Decision'].eventCategoryCode, 'icon-cash')"></span>

                                    <div class="card rounded-0">
                                        <div class="card-header" id="headingFour">
                                            <h2 class="mb-0">
                                                <button @click="toggleAccordion(3)" class="btn btn-link btn-block text-left" type="button">
                                                    Funding Decision
                                                    <i class="float-right icon pr-3" :class="accordion[3] === true ? ' icon-circleup' : ' icon-circledown'"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseFour" class="collapse" :class="accordion[3] === true ? 'show' : 'hide'">
                                            <div class="card-body">
                                                <div v-for="event in eventItems['Funding Decision'].events" class="row">
                                                    <template v-if="event.eventDate != undefined && event.eventDate != null">
                                                        <div class="col-5"><small>{{event.eventDate}}</small></div>
                                                        <div class="col-7">{{event.eventDescription}}</div>
                                                    </template>
                                                    <template v-else>
                                                        <p class="col-12" v-if="event.eventDescription != undefined && event.eventDescription != null">- {{event.eventDescription}}</p>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <span v-if="eventItems['Confirmation of Enrollment and Funding Disbursement'] != undefined && eventItems['Confirmation of Enrollment and Funding Disbursement'].eventCategoryCode != undefined && eventItems['Confirmation of Enrollment and Funding Disbursement'].eventCategoryCode == 'Complete'" class="icon icon-uniF479 text-success"></span>
                                    <span v-else class="icon icon-wallet"></span>

                                    <div class="card rounded-0">
                                        <div class="card-header" id="headingFive">
                                            <h2 class="mb-0">
                                                <button @click="toggleAccordion(4)" class="btn btn-link btn-block text-left" type="button">
                                                    Confirmation of Enrollment and Funding Disbursement
                                                    <i class="float-right icon pr-3" :class="accordion[4] === true ? ' icon-circleup' : ' icon-circledown'"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseFive" class="collapse" :class="accordion[4] === true ? 'show' : 'hide'">
                                            <div class="card-body">
                                                <application-collapse-five :e="eventItems['Confirmation of Enrollment and Funding Disbursement']" :app="app"></application-collapse-five>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                            </ul>

                        </div>
                    </div>
                </div>


                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">How was this calculated?</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <template class="modal-body">


                                <!--                        if(isset($app->applicationDetails->assessedCosts)){-->
                                <template v-if="app.applicationDetails.assessedCosts != undefined">
                                    <table class="table" width="100%">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>
                                                <div class="text-right">Costs</div>
                                            </th>
                                            <th></th>
                                            <th>
                                                <div class="text-left">Resources</div>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <!--                        foreach($app->applicationDetails->assessedCosts as $k => $calculation){-->
                                        <template v-for="(calculation, k) in app.applicationDetails.assessedCosts">
                                            <!--                        if($k == 'AssessedTotals'){-->
                                            <template v-if="k == 'assessedTotals'">
                                                <tr class="highlight">
                                                    <td>Assessed Totals</td>
                                                    <td>
                                                        <div class="text-right">
                                                            <span class="text-success">{{calculation.assessedTotal[0].assessedTotalValue}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-center large">-</div>
                                                    </td>
                                                    <td>
                                                <span
                                                    class="text-error">{{calculation.assessedTotal[1].assessedTotalValue}}</span>
                                                    </td>
                                                </tr>

                                                <tr class="subheading">
                                                    <td>Assessed Need</td>
                                                    <td></td>
                                                    <td>
                                                        <div class="text-center large">=</div>
                                                    </td>
                                                    <td>
                                                        <div class="large">
                                                            {{calculation.assessedTotal[2].assessedTotalValue}}
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <template v-for="costs in calculation">
                                                    <template v-for="cost in costs">
                                                        <tr>

                                                            <!--                            if(!empty($cost->Value)){-->
                                                            <!--                            $desc = (isset($cost->CostType)) ? $cost->CostType : $cost->ResourceType;-->
                                                            <td v-if="cost.Value != undefined" width="50%">'. {{cost.CostType != undefined ? cost.CostType : cost.ResourceType}}
                                                            </td>
                                                            <!--                            }-->

                                                            <!--                            if(isset($cost->CostType) && !empty($cost->Value)){-->
                                                            <td v-if="cost.CostType != undefined && cost.Value != '' && cost.Value != undefined"
                                                                width="24%">
                                                                <div class="text-right">
                                                                    <strong class="text-success">
                                                                        {{cost.Value}}
                                                                    </strong>
                                                                </div>
                                                            </td>
                                                            <td width="2%"></td>
                                                            <td width="24%">
                                                                <div class="text-right">
                                                                    <strong class="text-success">
                                                                        &nbsp;
                                                                    </strong>
                                                                </div>
                                                            </td>
                                                            <!--                            }-->


                                                            <!--                            if(isset($cost->ResourceType) && !empty($cost->Value)){-->
                                                            <td v-if="cost.ResourceType != undefined && cost.Value != undefined && cost.Value != ''"
                                                                width="24%">
                                                                <div class="text-right">
                                                                    <strong class="text-success">
                                                                        &nbsp;
                                                                    </strong>
                                                                </div>
                                                            </td>
                                                            <td width="2%"></td>
                                                            <td width="24%">
                                                                <strong class="text-error">
                                                                    {{cost.Value}}
                                                                </strong>
                                                            </td>
                                                            <!--                            }-->

                                                        </tr>

                                                    </template>
                                                </template>
                                            </template>
                                        </template>
                                        </tbody>
                                    </table>
                                    <p>Your <strong>assessed need</strong> is compared with the <a
                                        href="/help-centre/applying-loans/your-financial-need" target="_blank">maximum weekly
                                        funding limit</a> allowed for your study period. The lesser of these two amounts is what
                                        you are eligible to receive.</p>
                                </template>
                                <p v-else>No assessment information available</p>
                            </template>


                        </div>
                        <!--                    <div class="modal-footer">-->
                        <!--                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
                        <!--                        <button type="button" class="btn btn-primary">Save changes</button>-->
                        <!--                    </div>-->
                    </div>
                </div>


</template>
        </div>

    </div><!-- /.row -->
</template>
<style scoped>
    /*    to pass css to v-html child */
    div.row >>> span.label{
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
        top: 26px;
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
    import axios from 'axios';

    export default {
        filters: {
            formatAppNumber: function(value){
                let year = value.slice(0, 4);
                let extra = value.slice(4);

                return year + '-' + extra;
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
                return 'confirming';
            },
            formatLetterDate: function (value) {
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
            cleanText: function(value){
                if(value == undefined) return '';
                let txt = value.trim();
                if(txt == ''){
                    return 'confirming';
                }
                return txt;
            }

        },
        props: ['appno', 'resendsuccess', 'resenderror'],
        data: () => ({
            app: '',
            eventItems: '',
            loading: true,
            loadingError: false,
            accordion: [false, false, false, false, false],
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
            submit_date: 0,
            econsent_rollout_date: 20200317, // 2020 Mar 17
            maintenanceMode: false,

    }),
        methods: {
            disablePage: function(e){
                if(e === true)
                    this.maintenanceMode = true;
            },
            //you cannot update arrays directly in JS. The DOM won't see these changes.
            //https://stackoverflow.com/questions/51412250/vue-v-if-cant-access-booleans-in-arrays
            toggleAccordion: function(index){
                this.$set(this.accordion, index, !this.accordion[index])
            },
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/student-loans/fetch-application-status?application_number=' + this.appno,
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                //axios.get( '/fetch-dashboard' )
                    .then(function (response) {

                        vm.loading = false;
                        vm.app = response.data.application;
                        vm.eventItems = response.data.details;

        //            if(isset($eventItems['Start/Submit Application']['eventCategoryCode']) && $eventItems['Start/Submit Application']['eventCategoryCode'] == 'Complete'){
                        //show collapse details of event if it is NOT complete
                        if(vm.eventItems['Start/Submit Application'].eventCategoryCode == null || vm.eventItems['Start/Submit Application'].eventCategoryCode != 'Complete'){
                            vm.accordion[0] = true;
                        }
                        //show collapse details of event if it is NOT complete
                        if(vm.eventItems['Submit Declaration'].eventCategoryCode != undefined && vm.eventItems['Submit Declaration'].eventCategoryCode != 'Complete'){
                            vm.accordion[1] = true;
                        }

                        //show collapse details of event if it is NOT complete
                        if(vm.eventItems['Application Review'].eventCategoryCode != undefined && vm.eventItems['Application Review'].eventCategoryCode != 'Complete'){
                            vm.accordion[2] = true;
                        }

                        if(vm.fundingDecisionInProgress == true){
                            vm.accordion[3] = true;
                        }

                    })
                    .catch(function (error) {
                        vm.loading = false;
                        vm.loadingError = true;
                        //console.log(error);
                    });
            },
            fnStatusClass: function(e, defaultClass){
                // alert(e);
                // alert(defaultClass);
                if(e != undefined && e != null) {

                    return defaultClass + ' text-' + this.statusFlags[e].class;
                    // return defaultClass + ' text-' + this.statusFlags[this.app.applicationDetails.applicationTimeline.applicationTimelineCode].class;
                    //return this.statusFlags[this.app.applicationDetails.applicationTimeline.applicationTimelineCode].class;
                    //return (e != undefined && e != null ? $default.' text-'.$statusFlags[$e['eventCategoryCode']]['class'] : $default;

                }
                return defaultClass;
            }


        },
        computed: {
    //         $lettersArray = $this->fnLoadLetters($app->applicationDetails->pdfLetters);
    // foreach($lettersArray as $sendDate => $letters){
    //     foreach ($letters as $letter){
    //         if($letter['LetterDescript'] == "Application"){
    //             $submit_date = date("Ymd", $sendDate);
    //         }
    //     }
    // }

            enableEconsent: function(){
                if(this.app.applicationDetails.pdfLetters == undefined)
                    return false;

                if(typeof(this.app.applicationDetails.pdfLetters.pdfLetter) === 'object' && Array.isArray(this.app.applicationDetails.pdfLetters.pdfLetter) === false){
                    this.submit_date = parseInt(this.app.applicationDetails.pdfLetters.pdfLetter.sendDate);
                }

                if(Array.isArray(this.app.applicationDetails.pdfLetters.pdfLetter)){
                    for(let i=0; i<this.app.applicationDetails.pdfLetters.pdfLetter.length; i++){
                        //for(let j=0; j<this.app.applicationDetails.pdfLetters[i]; j++){
                        if(this.app.applicationDetails.pdfLetters.pdfLetter[i].letterDescription === "Application"){
                            this.submit_date = parseInt(this.app.applicationDetails.pdfLetters.pdfLetter[i].sendDate);
                        }
                        //}
                    }
                }

                if(this.submit_date >= this.econsent_rollout_date){
                    return true;
                }
                return false;
            },
            appStatus: function(){
//                $status = $statusFlags[$app->applicationDetails->applicationTimeline->applicationTimelineCode]['status'];
//                $statusClass = $statusFlags[$app->applicationDetails->applicationTimeline->applicationTimelineCode]['class'];
//                 $statusFlags = array(
//                 'In Progress' => array('status' => 'In Progress', 'class' => 'info'),
//                 'Complete' => array('status' => 'Complete', 'class' => 'success'),
//                 'Waiting' => array('status' => 'Waiting', 'class' => 'warning'),
//                 'Scheduled' => array('status' => 'Scheduled', 'class' => 'info'),
//                 'Missing Info' => array('status' => 'Missing Info', 'class' => 'important'),
//                 'Missing Information' => array('status' => 'Missing Information', 'class' => 'important'),
//                 'Not Required' => array('status' => 'Not Required Yet', 'class' => '')
//                 'Cancelled' => array('status' => 'Cancelled', 'class' => 'danger'));

                return this.statusFlags[this.app.applicationDetails.applicationTimeline.applicationTimelineCode].status;
            },
            appStatusClass: function(){
                return this.statusFlags[this.app.applicationDetails.applicationTimeline.applicationTimelineCode].class;
            },
            fundingDecisionInProgress: function(){
                if(this.eventItems['Funding Decision'] != undefined && Array.isArray(this.eventItems['Funding Decision'].events) ){
                    let in_progress = false;
                    for(let i=0; i<this.eventItems['Funding Decision'].events.length; i++){
                        if(this.eventItems['Funding Decision'].events[i].eventDescription == "The review of your application is in progress"){
                            in_progress = true;
                            break;
                        }
                    }
                    return in_progress;
                }
                return false;
            }

        },
        created() {

        },
        mounted: function () {
            this.fetchData();
            document.title = "StudentAidBC - Check Application Status";
        },
        watch: {

        }

    }

</script>
