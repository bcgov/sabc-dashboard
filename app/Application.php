<?php

namespace App;

use App\Http\Controllers\Aeit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Application extends Aeit
{
    public function fnStatusClass($e, $default)
    {
        $statusFlags = ['In Progress' => ['status' => 'In Progress', 'class' => 'info'],
            'Complete' => ['status' => 'Complete', 'class' => 'success'],
            'Waiting' => ['status' => 'Waiting', 'class' => 'warning'],
            'Scheduled' => ['status' => 'Scheduled', 'class' => 'info'],
            'Missing Info' => ['status' => 'Missing Info', 'class' => 'important'],
            'Missing Information' => ['status' => 'Missing Information', 'class' => 'important'],
            'Cancelled' => ['status' => 'Cancelled', 'class' => 'danger'],
            'Not Required' => ['status' => 'Not Required Yet', 'class' => '']];

        if (isset($e['eventCategoryCode'])) {
            if ($e['eventCategoryCode'] == 'Complete') {
                return 'icon-uniF479 text-success';
            }

            return (! empty($e)) ? $default.' text-'.$statusFlags[$e['eventCategoryCode']]['class'] : $default;
        } elseif (in_array($e, $statusFlags)) {
            return $statusFlags[$e]['class'];
        } else {
            return $default;
        }
    }

    public function fundingDetails($f)
    {
        global $user;

        $fd = [];

        foreach ($f as $k => $v) {
            if (is_array($v) && ! empty($v)) {
                foreach ($v as $id => $funds) {
                    if (is_object($funds)) {
                        if (is_array($funds->Funds)) {
                            foreach ($funds->Funds as $fund) {
                                $ts = strtotime($fund->EarliestDate);

                                $fd[$ts]['Funds'][] = ['EarliestDate' => $fund->EarliestDate,
                                    'Amount' => number_format($fund->Amount),
                                    'PaidToSchool' => number_format($fund->PaidToSchool),
                                    'TypeOfFunding' => $fund->TypeOfFunding,
                                    'DisbursementID' => $fund->DisbursementID];
                            }
                        } else {
                            $ts = strtotime($funds->Funds->EarliestDate);

                            $disbursementID = (isset($funds->Funds->DisbursementID)) ? $funds->Funds->DisbursementID : null;

                            $fd[$ts]['Funds'][] = ['EarliestDate' => $funds->Funds->EarliestDate,
                                'Amount' => number_format($funds->Funds->Amount),
                                'PaidToSchool' => number_format($funds->Funds->PaidToSchool),
                                'TypeOfFunding' => $funds->Funds->TypeOfFunding,
                                'DisbursementID' => $disbursementID];
                        }

                        $fd[$ts]['TotalFunds'] = ['Amount' => number_format($funds->TotalFunds->Amount),
                            'PaidToSchool' => number_format($funds->TotalFunds->PaidToSchool),
                            'EarliestDate' => $funds->TotalFunds->EarliestDate];
                    }
                }
            } else {
                foreach ($v as $id => $funds) {
                    if ($id != 'TotalFunds') {
                        if (is_array($funds)) {
                            foreach ($funds as $fund) {
                                $ts = strtotime($fund->EarliestDate);

                                $fd[$ts]['Funds'][] = ['EarliestDate' => $fund->EarliestDate,
                                    'Amount' => number_format($fund->Amount),
                                    'PaidToSchool' => number_format($fund->PaidToSchool),
                                    'TypeOfFunding' => $fund->TypeOfFunding,
                                    'DisbursementID' => $fund->DisbursementID];
                            }
                        } else {
                            $ts = strtotime($funds->EarliestDate);

                            $disbursementID = (isset($funds->DisbursementID)) ? $funds->DisbursementID : null;

                            $fd[$ts]['Funds'][] = ['EarliestDate' => $funds->EarliestDate,
                                'Amount' => number_format($funds->Amount),
                                'PaidToSchool' => number_format($funds->PaidToSchool),
                                'TypeOfFunding' => $funds->TypeOfFunding,
                                'DisbursementID' => $disbursementID];
                        }
                    } else {
                        $fd[$ts]['TotalFunds'] = ['Amount' => number_format($funds->Amount),
                            'PaidToSchool' => number_format($funds->PaidToSchool),
                            'EarliestDate' => $funds->EarliestDate];
                    }
                }
            }
        }

        ksort($fd);

        return $fd;
    }

    // validates an application returning from Livecycle
    public function validateApplication($appID)
    {
        if (isset($appID) && $appID > 0) {
            drupal_goto('dashboard/student-loans/check-application-status/'.$appID.'/incomplete');
        } else {
            drupal_goto('dashboard');
        }
    }

    // loads an existing application
    public function loadApplication($appID)
    {
        $r = new application();
        if (isset($appID) && $appID > 0) {
            $app = $r->fnGetApplicationDetails($appID);

            return loadApplicationDetails($app);
        } else {
            $apps = $r->fnGetApplications();

            return loadApplications($apps);
        }
    }

    public function loadApplications($apps)
    {
        $widget = '<div class="twelve columns">';
        $widget .= '<div class="paddingL paddingT paddingR mo-paddingT">';
        $widget .= '<div class="box full-width nomargin">';
        $widget .= '<div class="heading">';
        $widget .= '<h2>My Student Loan Applications</h2>';
        $widget .= '<span class="icon icon-wallet"></span>';
        $widget .= '</div>';
        $widget .= '<ul class="quickview">';

        if (isset($apps['NotSubmitted'])) {
            $widget .= '<li class="warning">';
            $widget .= '<strong class="text-warning">NOT Submitted</strong>';
            $widget .= '</li>';

            foreach ($apps['NotSubmitted'] as $application) {
                //MAKE SURE APPLICATION RETURN IS NOT EMPTY
                foreach ($application as $app) {
                    $widget .= '<a href="/dashboard/view-application">';
                    $widget .= '<li class="clearfix">';
                    $widget .= '<div class="float-left">';
                    $widget .= '#'.$app['ApplicationNumber'];
                    $widget .= '<span class="action">';

                    $widget .= '<strong class="paddingL">Start:</strong> '.$app['StudyStartDate'];
                    $widget .= ' <strong class="paddingL">End:</strong> '.$app['StudyEndDate'];

                    $widget .= '</span>';
                    $widget .= '</div>';
                    $widget .= '<div class="float-right"><span class="badge badge-warning">Edit Application</span></div>';
                    $widget .= '</li>';
                    $widget .= '</a>';
                }
            }
        } else {
            $widget .= '<li class="warning">';
            $widget .= '<strong class="text-warning">NOT Submitted</strong>';
            $widget .= '</li>';
            $widget .= '<a href="#">';
            $widget .= '<li class="clearfix">';
            $widget .= '<div class="float-left">';
            $widget .= '(0) Not submitted applications';
            $widget .= '</div>';
            $widget .= '<div class="float-right"></div>';
            $widget .= '</li>';
            $widget .= '</a>';
        }

        if (isset($apps['Submitted'])) {
            $widget .= '<li class="success">';
            $widget .= '<strong class="text-success">Submitted</strong>';
            $widget .= '</li>';

            foreach ($apps['Submitted'] as $application) {
                //MAKE SURE APPLICATION RETURN IS NOT EMPTY
                foreach ($application as $app) {
                    if (isset($app['InTransition'])) {
                        $widget .= '<a href="/dashboard/ila-login" class="app_processing" data-toggle="tooltip">';
                    } else {
                        $widget .= '<a href="/dashboard/student-loans/check-application-status?appID='.$app['ApplicationNumber'].'">';
                    }
                    $widget .= '<li class="clearfix">';
                    $widget .= '<div class="float-left">';
                    $widget .= '#'.$app['ApplicationNumber'];
                    $widget .= '<span class="action">';

                    $widget .= '<strong class="paddingL">Start:</strong> '.$app['StudyStartDate'];
                    $widget .= ' <strong class="paddingL">End:</strong> '.$app['StudyEndDate'];

                    $widget .= '</span>';
                    $widget .= '</div>';

                    if (isset($app['InTransition'])) {
                        $widget .= '<div class="float-right"><span class="badge">Print and Sign Declaration</span> <span class="meta">|</span> <span class="badge badge-info" style="width:100px">Processing</span></div>';
                    } else {
                        $widget .= '<div class="float-right"><span class="badge">Print and Sign Declaration</span> <span class="meta">|</span> <span class="badge badge-success" style="width:100px">View Details</span></div>';
                    }

                    $widget .= '</li>';

                    $widget .= '</a>';
                }
            }
        } else {
            $widget .= '<li class="success">';
            $widget .= '<strong class="text-success">Submitted</strong>';
            $widget .= '</li>';
            $widget .= '<a href="#">';
            $widget .= '<li class="clearfix">';
            $widget .= '<div class="float-left">';
            $widget .= '(0) Submitted applications';
            $widget .= '</div>';
            $widget .= '<div class="float-right"></div>';
            $widget .= '</li>';
            $widget .= '</a>';
        }
        $widget .= '</ul>';
        $widget .= '</div>';
        $widget .= '</div>';
        $widget .= '</div>';

        return $widget;
    }

    public function fnLoadEvents($applicationDetails, $schoolOnlineStatus = 'Y')
    {
        $appNumber = $applicationDetails->applicationNumber;
        $e = $applicationDetails->applicationTimeline->EventCategories->eventCategory;
        $overallAppStatus = $applicationDetails->applicationStatus;
        $programYear = $applicationDetails->applicationProfile->programYear;

        // TODO: fix workaround for ES-196
        $programYear = str_replace('/', '', $programYear);

        // drupal_set_message($overallAppStatus);
        $events = [];
        $events['submitStatus'] = false; //indicator to tell us if the application is ready to be submitted
        $events['appSubmitted'] = false; //indicator to tell us if the application has been submitted
        $events['documentGUID'] = null;
        $events['totalAward'] = '--';

        $appendixClasses = [
            'not required' => 'btn-light',
            'required' => 'btn-warning',
            'in progress' => 'btn-info',
            'no longer required' => '',
            'declined' => 'btn-danger',
            'completed' => 'btn-success',
        ];

        foreach ($e as $eid => $event) {
            if (isset($event->eventItems->eventItem)) {
                //CHECK TO SEE IF WE HAVE MORE THAN ONE EVENT ITEMS
                if (is_array($event->eventItems->eventItem)) {
                    $events[$event->eventCategoryName] = [];
                    $events[$event->eventCategoryName]['events'] = [];
                    $events[$event->eventCategoryName]['eventCategoryCode'] = (isset($event->eventCategoryCode)) ? $event->eventCategoryCode : null;

                    //ARRAY OF ALL APPENDICES
                    $appendixTimeline = [];

                    //loop through all event items in an event
                    foreach ($event->eventItems->eventItem as $ei => $ev) {
                        $tmp = [];
                        $appendixTmp = [];

                        //verify submit status
                        if ($event->eventCategoryName == 'Start/Submit Application') {
                            $statuses = ['Application ready to Submit', 'Your Application is ready to submit'];
                            //if appendix is ready to submit enable submit appendix buttons
                            if (isset($ev->eventType) && in_array(rtrim($ev->eventType), $statuses)) {
                                $events['submitStatus'] = true;
                                $events['documentGUID'] = $ev->eventValue;
                            }

                            //if eventCategoryCode == Complete the app has already been submitted so make sure to set appSubmitted indicator
                            if (isset($event->eventCategoryCode) && $event->eventCategoryCode == 'Complete') {
                                $events['appSubmitted'] = true;
                            }

                            //if event item is waiting grab event value for appendix documentGUID
                            if (isset($ev->eventCode) && $ev->eventCode == 'Waiting' && $ev->eventType == 'Application Started') {
                                $events['documentGUID'] = $ev->eventValue;
                            }

                            if (isset($ev->eventType) && ($ev->eventType == 'Appendix 1' || $ev->eventType == 'Appendix 2' || $ev->eventType == 'Appendix 3')) {
                                // a non-integrated school will always return Appendix 3 as required UNLESS it has been loaded into SFAS.
                                // This workaround will make Appendix 3 always required until it has been loaded into SFAS.
                                // After it has been loaded into SFAS, the Appendix 3 status will be dependent on what SFAS returns

                                if ($schoolOnlineStatus == 'N' && $ev->eventType == 'Appendix 3' &&
                                    ($overallAppStatus == 'INPR' || $overallAppStatus == 'SUBMPROC' || $overallAppStatus == 'RDY')) {
                                    $ev->eventDescription = 'required';
                                }

                                $appendix_class = (isset($appendixClasses[strtolower($ev->eventDescription)])) ? $appendixClasses[strtolower($ev->eventDescription)] : '';
                                $appendixTmp['eventCode'] = (! empty($ev->eventCode)) ? $ev->eventCode : null;
                                $appendixTmp['eventType'] = '<div class="col-5"><strong>'.$ev->eventType.'</strong></div><div class="col-7"><span class="float-right label btn btn-sm '.$appendix_class.'">'.strtolower($ev->eventDescription).'</span></div>';
                                $appendixTmp['eventIncomplete'] = null;

//                                $userProfile = new userProfile();
                                $userProfile = new User();
                                $user = $userProfile->fnGetUserProfile();

                                $appx3Required = 'As you were unable to find your program or dates of study through our search menus, your school will be required to electronically complete an '.$ev->eventType.' for your application.';

                                if ($schoolOnlineStatus == 'N') {
                                    $appx3Required = 'Print and mail '.$ev->eventType.' to your school\'s financial aid office for completion.';
                                    $appx3Required .= '</p><p>';
                                    $appx3Required .= 'Your school will either return the '.$ev->eventType.' to you to mail/fax to StudentAid BC or the school can mail/fax the Appendix 3 directly to StudentAid BC.';
                                }

                                //if required append alert box to notify user that they can re-send appendix email
                                if (strtolower($ev->eventDescription) == 'required') {
                                    if ($ev->eventType == 'Appendix 1' || $ev->eventType == 'Appendix 2') {
                                        $appendixTmp['eventType'] .= '<div class="col-md-12 mt-3">
<div class="alert alert-contextual alert-info" role="alert">
<svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg>
<small> Instructions on what to do with your '.$ev->eventType.' have been sent to your email address <em>'.$user->userProfile->emailAddress.'</em>. <p>If you did not receive this email, please check your junk folder or click the "Resend '.$ev->eventType.' email" button.</p></small>
</div>
<a href="/dashboard/student-loans/resend-appendix-email/'.$events['documentGUID'].'/'.str_replace(' ', '', $ev->eventType).'/'.$applicationDetails->applicationNumber.'" class="btn btn-light btn-block">Resend '.$ev->eventType.' email</a><hr></div>';
                                    } elseif ($schoolOnlineStatus == 'N' && $ev->eventType == 'Appendix 3') {
                                        if (! isset($ev->eventValue) || empty($ev->eventValue)) {
                                            // Workaround for when the documentGUID isn't returned. The URL will show null and resolve to a blank appendix 3.
                                            $ev->eventValue = 'null';
                                        }
                                        $appendixTmp['eventType'] .= '<div class="col-md-12 mt-3">
<div class="alert alert-contextual alert-info" role="alert">
<svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg>
<p>
<small>'.$appx3Required.'</small>
</p>
</div>
<a href="/dashboard/student-loans/request-appendix-3/'.$ev->eventValue.'/'.$programYear.'" class="btn btn-light btn-block" target="_blank">Download '.$ev->eventType.'</a><hr></div>';
                                    }
                                }

                                if (strtolower($ev->eventDescription) == 'required' ||
                                    strtolower($ev->eventDescription) == 'in progress' ||
                                    strtolower($ev->eventDescription) == 'declined') {
                                    $incompleteMessages = [
                                        'required' => ['appendix 1' => 'Instructions on what to do with your '.$ev->eventType.' have been sent to your email address <em>'.$user->userProfile->emailAddress.'</em>.',
                                            'appendix 2' => 'Instructions on what to do with your '.$ev->eventType.' have been sent to your email address <em>'.$user->userProfile->emailAddress.'</em>.',
                                            'appendix 3' => $appx3Required,
                                        ],
                                        'in progress' => ['appendix 1' => 'Please ensure your parent/guardian has completed and submitted the required '.$ev->eventType,
                                            'appendix 2' => 'Please ensure your spouse/partner has completed and submitted the required '.$ev->eventType,
                                            'appendix 3' => 'Please ensure your school has completed and submitted the required '.$ev->eventType,
                                        ],
                                        'declined' => ['appendix 1' => 'Your '.$ev->eventType.'has been declined.',
                                            'appendix 2' => 'Your '.$ev->eventType.'has been declined.',
                                            'appendix 3' => 'Your '.$ev->eventType.'has been declined.',
                                        ],
                                    ];

                                    $appendixTmp['eventIncomplete'] = '<p><strong>'.$ev->eventType.'</strong><span class="badge float-right '.$appendix_class.'">'.strtolower($ev->eventDescription).'</span></p>';
                                    $appendixTmp['eventIncomplete'] .= '<p>'.$incompleteMessages[strtolower($ev->eventDescription)][strtolower($ev->eventType)].'</p>';
                                }

                                $appendixTmp['appendixType'] = (! empty($ev->eventType)) ? $ev->eventType : null;
                                $appendixTmp['eventDescription'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                                $appendixTmp['eventDate'] = (! empty($ev->eventDate)) ? date('M d, Y', strtotime($ev->eventDate)) : null;
                                $appendixTmp['eventValue'] = (! empty($ev->eventValue)) ? $ev->eventValue : null;

                                array_push($appendixTimeline, $appendixTmp);
                            } else {
                                $tmp['eventCode'] = (! empty($ev->eventCode)) ? $ev->eventCode : null;
                                $tmp['eventType'] = (! empty($ev->eventType)) ? $ev->eventType : null;
                                $tmp['eventIncomplete'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                                $tmp['eventDescription'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                                $tmp['eventDate'] = (! empty($ev->eventDate)) ? date('M d, Y', strtotime($ev->eventDate)) : null;
                                $tmp['eventValue'] = (! empty($ev->eventValue)) ? $ev->eventValue : null;
                            }

                            $events['Start/Submit Application']['events']['appendices'] = $appendixTimeline;
                        } elseif ($event->eventCategoryName == 'Submit Declaration') {
                            //$args = arg();
                            //if(isset($args[3]) && !isset($events['webDecStatus'])){
                            if (! isset($events['webDecStatus'])) {
                                $app = new application();
                                //$app = new Aeit();
                                $appID = $appNumber;
                                $reqDec = $this->requireDec($appID, 'A', $ev->eventDescription);
                                $events['webDecStatus'] = $reqDec;
                                if ($reqDec == 'f1' || $reqDec == 'f2' || $reqDec == 'f') {
                                    $tmp['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/no-signature-required/applicant/'.$appID.'">View Applicant Declaration</a><br>';
                                } elseif ($reqDec == 't') {
                                    if ($overallAppStatus != 'SUBMPROC') {
                                        $tmp['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/signature/applicant/'.$appID.'">(Re)Print and Sign Applicant Declaration</a><br>';
                                    } else {
                                        $inkReq = $app->fnIsWebDeclarationInkSignatureRequired($appID);
                                        if ($inkReq->inkSignatureRequired == 'N') {
                                            $tmp['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/no-signature-required/applicant/'.$appID.'">View Applicant Declaration</a><br>';
                                        } else {
                                            $tmp['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/signature/applicant/'.$appID.'">(Re)Print and Sign Applicant Declaration</a><br>';
                                        }
                                    }
                                } else {
                                    // WEBS-50 Removing declaration button if a fault code was returned with getWebDeclaration
                                    $tmp['webDecState'] = '<div class="alert alert-contextual alert-warning" role="alert"><svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#warning"></use></svg>';
                                    $tmp['webDecState'] .= 'Due to a technical issue, your declaration is currently unavailable. This issue has been reported, please try again on the next business day, If it persists please call us.';
                                    $tmp['webDecState'] .= '</div>';

                                    if ($reqDec != 'e') {
//                                        $headers = "From: do-not-reply-aved@gov.bc.ca";
//                                        $headers .= "\r\nReply-To: do-not-reply-aved@gov.bc.ca";
//                                        mail('AVED.systemuser@gov.bc.ca', "getWebDeclaration failed for environment: ". $_SERVER['SERVER_NAME'], "  Application # ".$appID , $headers);
                                        Log::error('getWebDeclaration failed for environment: '.$_SERVER['SERVER_NAME'].'  Application # '.$appID);
                                    } else {
                                    }
                                }
                            } else {
                                $tmp['webDecState'] = null;
                            }

                            $tmp['eventCode'] = (! empty($ev->eventCode)) ? $ev->eventCode : null;
                            $tmp['eventType'] = (! empty($ev->eventType)) ? $ev->eventType : null;
                            $tmp['eventIncomplete'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                            $tmp['eventDescription'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                            $tmp['eventDate'] = (! empty($ev->eventDate)) ? date('M d, Y', strtotime($ev->eventDate)) : null;
                            $tmp['eventValue'] = (! empty($ev->eventValue)) ? $ev->eventValue : null;
                        } elseif ($event->eventCategoryName == 'Funding Decision') {
                            if ($ev->eventType == 'Total Award') {
                                if (! empty($ev->eventValue)) {
                                    $events['totalAward'] = '$'.number_format($ev->eventValue);
                                }
                            }

                            $tmp['eventCode'] = (! empty($ev->eventCode)) ? $ev->eventCode : null;
                            $tmp['eventType'] = (! empty($ev->eventType)) ? $ev->eventType : null;
                            $tmp['eventIncomplete'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                            $tmp['eventDescription'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                            $tmp['eventDate'] = (! empty($ev->eventDate)) ? date('M d, Y', strtotime($ev->eventDate)) : null;
                            $tmp['eventValue'] = (! empty($ev->eventValue)) ? $ev->eventValue : null;
                        } elseif ($event->eventCategoryName == 'Application Review') {
                            if ($ev->eventType == 'Modified Group B') {
                                //SD-52939
                                $tmp['eventIncomplete'] = 'We require you to complete the following <a href="/sites/all/files/form-library/appeal_modifiedgroup.pdf">appeal form</a> and upload it with any other required documents to your dashboard.';

                                $tmp['eventCode'] = (! empty($ev->eventCode)) ? $ev->eventCode : null;
                                $tmp['eventType'] = (! empty($ev->eventType)) ? $ev->eventType : null;
                                $tmp['eventDescription'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                                $tmp['eventDate'] = (! empty($ev->eventDate)) ? date('M d, Y', strtotime($ev->eventDate)) : null;
                                $tmp['eventValue'] = (! empty($ev->eventValue)) ? $ev->eventValue : null;
                            }
                        } else {
                            $tmp['eventCode'] = (! empty($ev->eventCode)) ? $ev->eventCode : null;
                            $tmp['eventType'] = (! empty($ev->eventType)) ? $ev->eventType : null;
                            $tmp['eventIncomplete'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                            $tmp['eventDescription'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                            $tmp['eventDate'] = (! empty($ev->eventDate)) ? date('M d, Y', strtotime($ev->eventDate)) : null;
                            $tmp['eventValue'] = (! empty($ev->eventValue)) ? $ev->eventValue : null;
                        }

                        if (! empty($tmp)) {
                            if ($event->eventCategoryName != 'Confirmation of Enrollment and Funding Disbursement') {
                                array_push($events[$event->eventCategoryName]['events'], $tmp);
                            } else {
                                $events[$event->eventCategoryName]['events'][$ev->eventValue] = $tmp;
                            }
                        }
                    }
                } else {
                    foreach ($event->eventItems as $ei => $ev) {
                        $events[$event->eventCategoryName]['eventCategoryCode'] = (isset($event->eventCategoryCode)) ? $event->eventCategoryCode : null;

                        //verify submit status
                        if ($event->eventCategoryName == 'Start/Submit Application') {
                            $statuses = ['Application Ready to Submit', 'Your Application is ready to submit'];

                            //if appendix is ready to submit enable submit appendix buttons
                            if (isset($ev->eventType) && in_array($ev->eventType, $statuses)) {
                                $events['submitStatus'] = true;
                                $events['documentGUID'] = $ev->eventValue;
                            }

                            //if event item is waiting grab event value for appendix documentGUID
                            if ($ev->eventCode == 'Waiting') {
                                $events['documentGUID'] = $ev->eventValue;
                            }

                            if ($ev->eventType == 'Appendix 1' || $ev->eventType == 'Appendix 2' || $ev->eventType == 'Appendix 3') {
                                $appendix_class = (in_array(strtolower($ev->eventDescription), $appendixClasses)) ? $appendixClasses[strtolower($ev->eventDescription)] : '';
                                $ev->eventType = '<strong>'.$ev->eventType.'</strong> <span class="label '.$appendix_class.'">'.strtolower($ev->eventDescription).'</span>';
                            }
                        } elseif ($event->eventCategoryName == 'Submit Declaration') {
                            //$args = $this->arg();
                            //if(isset($args[3]) && !isset($events['webDecStatus'] )){
                            if (! isset($events['webDecStatus'])) {
                                $app = new application();
                                $appID = $appNumber;
                                $reqDec = $app->requireDec($appID, 'A', $ev->eventDescription);
                                $events['webDecStatus'] = $reqDec;

                                if ($reqDec == 'f1' || $reqDec == 'f2' || $reqDec == 'f') {
                                    $events[$event->eventCategoryName]['events'][0]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/no-signature-required/applicant/'.$appID.'">View Applicant Declaration</a><br>';
                                } elseif ($reqDec == 't') {
                                    if ($overallAppStatus != 'SUBMPROC') {
                                        $events[$event->eventCategoryName]['events'][0]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/signature/applicant/'.$appID.'">(Re)Print and Sign Applicant Declaration</a><br>';
                                    } else {
                                        $inkReq = $app->fnIsWebDeclarationInkSignatureRequired($appID);
                                        if ($inkReq->inkSignatureRequired == 'N') {
                                            $events[$event->eventCategoryName]['events'][0]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/no-signature-required/applicant/'.$appID.'">View Applicant Declaration</a><br>';
                                        } else {
                                            $events[$event->eventCategoryName]['events'][0]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/signature/applicant/'.$appID.'">(Re)Print and Sign Applicant Declaration</a><br>';
                                        }
                                    }
                                } else {
                                    // WEBS-50 Removing declaration button if a fault code was returned with getWebDeclaration
                                    $events[$event->eventCategoryName]['events'][0]['webDecState'] = '<div class="alert alert-contextual alert-warning" role="alert"><svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#warning"></use></svg>';
                                    $events[$event->eventCategoryName]['events'][0]['webDecState'] .= 'Due to a technical issue, your declaration is currently unavailable. This issue has been reported, please try again on the next business day, If it persists please call us.';
                                    $events[$event->eventCategoryName]['events'][0]['webDecState'] .= '</div>';
                                }
                            } else {
                                $events[$event->eventCategoryName]['events'][0]['webDecState'] = null;
                            }
                        }

                        $events[$event->eventCategoryName]['events'][0]['eventCode'] = (! empty($ev->eventCode)) ? $ev->eventCode : null;
                        $events[$event->eventCategoryName]['events'][0]['eventType'] = (! empty($ev->eventType)) ? $ev->eventType : null;
                        $events[$event->eventCategoryName]['events'][0]['eventType'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                        $events[$event->eventCategoryName]['events'][0]['eventDescription'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                        $events[$event->eventCategoryName]['events'][0]['eventDate'] = (! empty($ev->eventDate)) ? date('M d, Y', strtotime($ev->eventDate)) : null;
                        $events[$event->eventCategoryName]['events'][0]['eventValue'] = (! empty($ev->eventValue)) ? $ev->eventValue : null;
                    }
                }
            } else {
                $events[$event->eventCategoryName]['events'][0]['eventCode'] = (! empty($event->eventItems->eventItem->eventCode)) ? $event->eventItems->eventItem->eventCode : null;
                $events[$event->eventCategoryName]['events'][0]['eventType'] = (! empty($event->eventItems->eventItem->eventType)) ? $event->eventItems->eventItem->eventType : null;
                $events[$event->eventCategoryName]['events'][0]['eventType'] = (! empty($event->eventItems->eventItem->eventDescription)) ? $event->eventItems->eventItem->eventDescription : null;
                $events[$event->eventCategoryName]['events'][0]['eventDescription'] = (! empty($event->eventItems->eventItem->eventDescription)) ? $event->eventItems->eventItem->eventDescription : null;
                $events[$event->eventCategoryName]['events'][0]['eventDate'] = (! empty($event->eventItems->eventItem->eventDate)) ? date('M d, Y', strtotime($event->eventItems->eventItem->eventDate)) : null;
                $events[$event->eventCategoryName]['events'][0]['eventValue'] = (! empty($event->eventItems->eventItem->eventValue)) ? $event->eventItems->eventItem->eventValue : null;
            }
        }

        return $events;
    }

    public function fnLoadAppendixEvents($e, $docType, $appID, $overallAppStatus)
    {
        $role = ($docType == 'Appendix 1') ? 'P' : 'S';
        $roleType = ($docType == 'Appendix 1') ? 'parent' : 'spouse';
        $events = [];
        $events['submitStatus'] = false;
        $events['appendixID'] = null;

        $appendixClasses = [
            'not required' => '',
            'required' => 'btn-warning',
            'in progress' => 'btn-info',
            'no longer required' => '',
            'declined' => 'btn-danger',
            'completed' => 'btn-success',
        ];

        foreach ($e as $eid => $event) {
            if (isset($event->eventItems->eventItem)) {
                $evntItm = (array) $event->eventItems->eventItem;

                //CHECK TO SEE IF WE HAVE MORE THAN ONE EVENT ITEMS
                if (isset($evntItm[0])) {
                    //loop through all event items in an event
                    foreach ($event->eventItems->eventItem as $ei => $ev) {
                        //verify submit status
                        if ($event->eventCategoryName == 'Start/Submit Appendix') {
                            //if appendix is ready to submit enable submit appendix buttons
                            if ($ev->eventCode == 'Complete') {
                                $events['submitStatus'] = true;
                            }

                            //if event item is waiting grab event value for appendix documentGUID
                            if ($ev->eventCode == 'Waiting') {
                                $events['appendixID'] = $ev->eventValue;
                            }

                            $appendix_class = (isset($appendixClasses[strtolower($ev->eventDescription)])) ? $appendixClasses[strtolower($ev->eventDescription)] : '';
                            $events['eventType'] = $ev->eventType.' <span class="float-right btn btn-sm btn-light '.$appendix_class.'">'.strtolower($ev->eventDescription).'</span>';
                        }
                        if ($event->eventCategoryName == 'Submit Appendix Declaration') {
                            //$args = arg();
                            if (isset($appID) && ! isset($events['webDecStatus'])) {
                                //$app = new application();
                                //$appID = $args[3];
                                $reqDec = $this->requireDec($appID, $role, $ev->eventDescription);
                                $events['webDecStatus'] = $reqDec;

                                if ($reqDec == 'f1' || $reqDec == 'f2' || $reqDec == 'f') {
                                    $events[$event->eventCategoryName]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/no-signature-required/'.$roleType.'/'.$appID.'">View Declaration</a><br>';
                                } elseif ($reqDec == 't') {
                                    if ($overallAppStatus != 'SUBMPROC') {
                                        $events[$event->eventCategoryName]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/signature/'.$roleType.'/'.$appID.'">(Re)Print and Sign Appendix Declaration</a><br>';
                                    } else {
                                        $inkReq = $this->fnIsWebDeclarationInkSignatureRequired($appID, $role);
                                        if ($inkReq->inkSignatureRequired == 'N') {
                                            $events[$event->eventCategoryName]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/no-signature-required/'.$roleType.'/'.$appID.'">View Declaration</a><br>';
                                        } else {
                                            $events[$event->eventCategoryName]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/signature/'.$roleType.'/'.$appID.'">(Re)Print and Sign Appendix Declaration</a><br>';
                                        }
                                    }
                                }
                            } else {
                                $events[$event->eventCategoryName]['webDecState'] = null;
                            }
                        }
                    }
                } else {
                    foreach ($event->eventItems as $ei => $ev) {
                        //verify submit status
                        if ($event->eventCategoryName == 'Start/Submit Appendix') {
                            //if appendix is ready to submit enable submit appendix buttons
                            if ($ev->eventCode == 'Complete') {
                                $events['submitStatus'] = true;
                            }

                            //if event item is waiting grab event value for appendix documentGUID
                            if ($ev->eventCode == 'Waiting') {
                                $events['appendixID'] = $ev->eventValue;
                            }

                            $appendix_class = (isset($appendixClasses[strtolower($ev->eventDescription)])) ? $appendixClasses[strtolower($ev->eventDescription)] : '';
                            $events[$event->eventCategoryName]['eventType'] = $ev->eventType.' <span class="float-right btn btn-sm '.$appendix_class.'">'.strtolower($ev->eventDescription).'</span>';

                            if (strtolower($ev->eventDescription) == 'in progress') {
                                $incompleteMessages = [
                                    'in progress' => ['appendix 1' => $ev->eventType.' is incomplete. Please ensure all required fields are filled in. Select the <i>Edit Appendix</i> button to make changes.',
                                        'appendix 2' => $ev->eventType.' is incomplete. Please ensure all required fields are filled in. Select the <i>Edit Appendix</i> button to make changes.',
                                    ],
                                ];

                                $events[$event->eventCategoryName]['eventIncomplete'] = '<p><strong>'.$ev->eventType.'</strong><span class="float-right btn btn-sm btn-light '.$appendix_class.'">'.strtolower($ev->eventDescription).'</span></p>';
                                $events[$event->eventCategoryName]['eventIncomplete'] .= '<p>'.$incompleteMessages[strtolower($ev->eventDescription)][strtolower($ev->eventType)].'</p>';
                            }
                        } else {
                            $events[$event->eventCategoryName]['eventType'] = (! empty($ev->eventType)) ? $ev->eventType : null;
                            $events[$event->eventCategoryName]['eventIncomplete'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                        }

                        if ($event->eventCategoryName == 'Submit Appendix Declaration') {
                            //$args = arg();
                            if (isset($appID) && ! isset($events['webDecStatus'])) {
                                //$app = new application();
                                //$appID = $args[3];
                                $reqDec = (isset($ev->eventDescription)) ? $this->requireDec($appID, $role, $ev->eventDescription) : null;
                                $events['webDecStatus'] = $reqDec;

                                if ($reqDec == 'f1' || $reqDec == 'f2' || $reqDec == 'f') {
                                    $events[$event->eventCategoryName]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/no-signature-required/'.$roleType.'/'.$appID.'">View Declaration</a><br>';
                                } elseif ($reqDec == 't') {
                                    if ($overallAppStatus != 'SUBMPROC') {
                                        $events[$event->eventCategoryName]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/signature/'.$roleType.'/'.$appID.'">(Re)Print and Sign Appendix Declaration</a><br>';
                                    } else {
                                        $inkReq = $this->fnIsWebDeclarationInkSignatureRequired($appID, $role);
                                        if ($inkReq->inkSignatureRequired == 'N') {
                                            $events[$event->eventCategoryName]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/no-signature-required/'.$roleType.'/'.$appID.'">View Declaration</a><br>';
                                        } else {
                                            $events[$event->eventCategoryName]['webDecState'] = '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/signature/'.$roleType.'/'.$appID.'">(Re)Print and Sign Appendix Declaration</a><br>';
                                        }
                                    }
                                }
                            } else {
                                $events[$event->eventCategoryName]['webDecState'] = null;
                            }
                        }

                        $events[$event->eventCategoryName]['eventCode'] = (! empty($ev->eventCode)) ? $ev->eventCode : null;
                        $events[$event->eventCategoryName]['eventDescription'] = (! empty($ev->eventDescription)) ? $ev->eventDescription : null;
                        $events[$event->eventCategoryName]['eventDate'] = (! empty($ev->eventDate)) ? date('M d, Y', strtotime($ev->eventDate)) : null;
                        $events[$event->eventCategoryName]['eventValue'] = (! empty($ev->eventValue)) ? $ev->eventValue : null;
                    }
                }
            } else {
                $events[$event->eventCategoryName]['eventCode'] = (! empty($event->eventItems->eventItem->eventCode)) ? $event->eventItems->eventItem->eventCode : null;
                $events[$event->eventCategoryName]['eventType'] = (! empty($event->eventItems->eventItem->eventType)) ? $event->eventItems->eventItem->eventType : null;
                $events[$event->eventCategoryName]['eventDescription'] = (! empty($event->eventItems->eventItem->eventDescription)) ? $event->eventItems->eventItem->eventDescription : null;
                $events[$event->eventCategoryName]['eventDate'] = (! empty($event->eventItems->eventItem->eventDate)) ? date('M d, Y', strtotime($event->eventItems->eventItem->eventDate)) : null;
                $events[$event->eventCategoryName]['eventValue'] = (! empty($event->eventItems->eventItem->eventValue)) ? $event->eventItems->eventItem->eventValue : null;
            }
        }

        return $events;
    }

    public function fnLoadLetters($ol)
    {
        $letters = [];

        foreach ($ol as $letter) {
            if (is_array($letter)) {
                foreach ($letter as $l) {
                    $letterName = (isset($l->letterDescription) && ! empty($l->letterDescription)) ? $l->letterDescription : 'Document';
                    $ts = strtotime($l->sendDate);
                    $letters[$ts][] = ['LetterID' => $l->letterID, 'LetterDescript' => $letterName];
                }
            } else {
                $ts = strtotime($letter->sendDate);
                $letterName = (isset($letter->letterDescription) && ! empty($letter->letterDescription)) ? $letter->letterDescription : 'Document';
                $letters[$ts][] = ['LetterID' => $letter->letterID, 'LetterDescript' => $letter->letterDescription];
            }
        }

        ksort($letters);

        return $letters;
    }

    public function fnApplicantDetails($a)
    {
        $labels = ['institution' => 'Institution',
            'program' => 'Program',
            'studySDate' => 'Start Date',
            'studyEDate' => 'End Date'];

        $s = null;

        foreach ($a as $k => $v) {
            if (array_key_exists($k, $labels)) {
                $s .= '<dt>'.$labels[$k].'</dt>';
                $s .= '<dd>';

                //PROGRAM HAS PADDING IN IT SO WE NEED TO REMOVE PADDING TO MAKE SURE IT ISN'T ACTUALLY EMPTY
                if (is_object($v)) {
                    $tmp = trim($v->schoolName);
                } else {
                    $tmp = trim($v);
                }

                if (! empty($tmp)) {
                    if ($k == 'studySDate' || $k == 'studyEDate') {
                        $s .= date('M d, Y', strtotime($tmp));
                    } else {
                        $s .= $tmp;
                    }
                } else {
                    $s .= '<small>confirming</small>';
                }

                $s .= '</dd>';
            }
        }

        return $s;
    }

    public function loadApplicationDetails($app)
    {
        $r = new application();
        $this->uid = $this->fnDecrypt(Auth::user()->name);

        $COEExists = null;
        //STATUS FLAGS TO TELL US WHERE THE APPLICATION IS AT
        $statusFlags = ['In Progress' => ['status' => 'In Progress', 'class' => 'info'],
            'Complete' => ['status' => 'Complete', 'class' => 'success'],
            'Waiting' => ['status' => 'Waiting', 'class' => 'warning'],
            'Scheduled' => ['status' => 'Scheduled', 'class' => 'info'],
            'Missing Info' => ['status' => 'Missing Info', 'class' => 'important'],
            'Missing Information' => ['status' => 'Missing Information', 'class' => 'important'],
            'Cancelled' => ['status' => 'Cancelled', 'class' => 'danger'],
            'Not Required' => ['status' => 'Not Required Yet', 'class' => '']];

        //TIMELINE EVENTS
        $event = ['Start/Submit Application' => ['title' => 'Start/Submit Application', 'class' => 'icon-addtolist'],
            'Submit Declaration' => ['title' => 'Submit Declaration', 'class' => 'icon-uniF47C'],
            'Application Review' => ['title' => 'Application Review', 'class' => 'icon-eye2'],
            'Funding Decision' => ['title' => 'Funding Decision', 'class' => 'icon-cash'],
            'Confirmation of Enrollment and Funding Disbursement' => ['title' => 'Confirmation of Enrollment and Funding Disbursement', 'class' => 'icon-wallet']];

        $tl = (isset($app->applicationDetails->applicationTimeline)) ? (array) $app->applicationDetails->applicationTimeline : null;

        if (isset($app->applicationDetails->applicationNumber) && $app->applicationDetails->applicationNumber > 0 && ! empty($tl)) {
            $status = $statusFlags[$app->applicationDetails->applicationTimeline->applicationTimelineCode]['status'];
            $statusClass = $statusFlags[$app->applicationDetails->applicationTimeline->applicationTimelineCode]['class'];

            $disbursementStatus = null;

            $onlineStatus = $app->applicationDetails->applicationProfile->institution->onlineStatus;

            $eventItems = $this->fnLoadEvents($app->applicationDetails, $onlineStatus);

            $totalAward = $eventItems['totalAward'];

            //extract appendices from event loop
            $appendices = $eventItems['Start/Submit Application']['events']['appendices'];
            unset($eventItems['Start/Submit Application']['events']['appendices']);

            $s = '<div class="paddingL paddingR mo-padding0">';
            //application NOT submitted so show delete, edit and submit button controls

            if (empty($eventItems['appSubmitted']) && ! empty($eventItems['documentGUID'])) {
                $editApp = '/dashboard/edit/application/'.$eventItems['documentGUID'].'/'.$app->applicationDetails->applicationProfile->programYear;
                $documentGUID = $eventItems['documentGUID'];

                $submitClass = (isset($app->applicationDetails->applicationStatus) && $app->applicationDetails->applicationStatus == 'RDY') ? 'btn-success' : 'disabled';

                $s .= '<div class="arow hide-for-small">';
                $s .= '<div id="livecycle-controls" class="twelve columns btn-toolbar">';

                if (! session()->exists('read-only')) {
                    //if(!isset($_SESSION['read-only'])){ // only add delete button when not in read-only mode
                    $s .= '<a data-toggle="modal" data-remote="false" data-target="#delete-application-confirm"
								href="/dashboard/student-loans/confirm/delete-application/'.$documentGUID.'/'.$app->applicationDetails->applicationNumber.'"
								class="btn btn-danger left">Delete Application</a>';

                    $s .= '<div class="modal fade" id="delete-application-confirm" tabindex="-1"
										role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
													<h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
												</div>
												<div class="modal-body">
												<p>Are you sure you want to delete application <strong>#'.$app->applicationDetails->applicationNumber.'</strong>?</p>
												</div>
												<div class="modal-footer">
													<a href="#" class="btn btn-default" data-dismiss="modal">No, do NOT delete this application</a>
													<a href="/dashboard/student-loans/delete-application/'.$documentGUID.'/'.$app->applicationDetails->applicationNumber.'"
													class="btn btn-danger" >Yes, delete this application</a>
												</div>
											</div>
										</div>
									</div>'; //modal window for delete application confirm
                }

                if ($submitClass == 'btn-success') {
                    // apply the 'ladda' spinner to this button? just add class 'ladda-button' to the <a>
                    $s .= '<a href="/dashboard/application-submit-checklist/'.$app->applicationDetails->applicationProfile->programYear.'/'.$app->applicationDetails->applicationNumber.'/'.$documentGUID.'" class="btn '.$submitClass.' float-right" data-style="expand-left">Submit Application</a>';
                } else {
                    $eventIncomplete = [];
                    $index = 0;
                    foreach ($appendices as $k => $event) {
                        if ($event['eventCode'] == 'Waiting' || $event['eventCode'] == 'In Progress') {
                            if ($onlineStatus == 'Y' ||
                                ($onlineStatus == 'N' && $event['appendixType'] != 'Appendix 3')) {
                                $eventIncomplete[$index] = $event['eventIncomplete'];
                                $index++;
                            }
                        }
                    }

                    $incompleteItems = '';
                    foreach ($eventIncomplete as $k => $incomplete) {
                        $incompleteItems .= $incomplete;
                        if ($k < count($eventIncomplete) - 1) {
                            $incompleteItems .= '<hr>';
                        }
                    }

                    $path = current_path();
                    $path = explode('/', $path);
                    $appSubmitStatus = array_pop($path);
                    $alertType = (empty($eventIncomplete)) ? 'info' : 'block';

                    if ($appSubmitStatus == 'incomplete') {
                        $s .= '<script type="text/javascript">';
                        $s .= 'jQuery(window).load(function() {';
                        $s .= 'jQuery(\'#submit-application-incomplete\').modal(\'show\');';
                        $s .= '});';
                        $s .= '</script>';
                    }

                    $s .= '<button data-toggle="modal" data-remote="false" data-target="#submit-application-incomplete" class="btn '.$submitClass.' float-right">Submit Application</button>';

                    $s .= '<div class="modal fade" id="submit-application-incomplete" tabindex="-1"';
                    $s .= 'role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
                    $s .= '<div class="modal-dialog">';
                    $s .= '<div class="modal-content">';
                    $s .= '<div class="modal-header">';
                    $s .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
                    $s .= '<h4 class="modal-title" id="myModalLabel">';
                    if (empty($eventIncomplete)) {
                        $s .= 'Submit Application';
                    } else {
                        $s .= 'Application Incomplete';
                    }
                    $s .= '</h4>';
                    $s .= '</div>';
                    $s .= '<div class="modal-body">';

                    $s .= '<div class="alert alert-contextual alert-'.$alertType.'" role="alert"><svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#'.$alertType.'"></use></svg>';
                    $s .= '<small>';
                    if (empty($eventIncomplete)) {
                        $s .= 'Finished filling out your application and ready to submit? Press “Edit Application” and then “Submit” from inside the form.';
                    } else {
                        $s .= 'Your application cannot be submitted until the following issue'.(count($eventIncomplete) > 1 ? 's' : '').' ha'.(count($eventIncomplete) > 1 ? 've' : 's').' been resolved. ';
                        $s .= 'The status must show as "Completed" before you can submit.';
                    }
                    $s .= '</small>';
                    $s .= '</div>';
                    $s .= $incompleteItems;
                    $s .= '</div>';
                    $s .= '<div class="modal-footer">';
                    $s .= '<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>';
                    $s .= '</div>';
                    $s .= '</div>';
                    $s .= '</div>';
                    $s .= '</div>';
                }

                $s .= '<a href="'.$editApp.'" style="margin-right:6px;" class="btn btn-warning float-right ladda-button" data-style="expand-left">Edit Application</a>';
                $s .= '</div>';
                $s .= '</div>';

                $s .= '<div class="arow show-for-small">';
                $s .= '<p class="alert alert-contextual alert-info" role="alert"><svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg><p>To edit your application, or to view your web declaration, please see the <a href="/help-centre/loan-application-instructions/minimum-system-requirements">minimum system requirements</a>. Note that most mobile devices, including tablets, <strong>do not</strong> fully support the student dashboard.</p></div>';
                $s .= '</div>';
            }

            $common = new Aeit();
            $s .= '<div class="accordion appstatus clearfix" id="accordion2">';
            $s .= '<div class="accordion-group">';
            $s .= '<div class="accordion-heading large full-width">';
            $s .= '<div class="accordion-toggle clearfix">';
            $s .= '<div class="float-left"><h4>Application #'.$common->fnFormatApplicationNumber($app->applicationDetails->applicationNumber).'</h4></div>';
            $s .= '<div class="float-right"><span class="label label-'.$statusClass.'">'.$status.'</span></div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '<div id="collapseOne" class="accordion-body collapse in">';
            $s .= '<div class="accordion-inner full-width">';
            $s .= '<div class="arow">';
            $s .= '<div id="faux2">';
            $s .= '<div id="faux1">';
            $s .= '<div class="six columns" id="col1">';

            //MAKE SURE WE HAVE A TOTAL AWARD
            if (! empty($totalAward)) {
                $s .= '<div class="application-info mo-padding">';
                $s .= '<h6 class="uppercase short">Funding Details</h6>';
                $s .= '<dl class="dl-horizontal-med">';
                $s .= '<dt>Total Award</dt>';
                $s .= '<dd>';
                $s .= '<span class="supersize text-success award">';
                $s .= $totalAward;
                $s .= '</span><br>';
                $s .= '<small>';
                $s .= '<i class="icon-supportrequest muted"></i>';
                $s .= '<a href="#" class="togglepanel muted">';
                $s .= ' How was this calculated?';
                $s .= '</a>';
                $s .= '</small>';
                $s .= '</dd>';
                $s .= '</dl>';
                $s .= '</div>';
            } else {
                $s .= '<div class="application-info mo-padding">';
                $s .= '<h6 class="uppercase short">Funding Details</h6>';
                $s .= '<dl class="dl-horizontal-med">';
                $s .= '<dt>Total Award</dt>';
                $s .= '<dd>';
                $s .= '<span class="large award">';
                $s .= '--';
                $s .= '</span><br>';

                $s .= '</dd>';
                $s .= '</dl>';
                $s .= '</div>';
            }

            //PROGRAM DETAILS
            $s .= '<hr class="small">';
            $s .= '<div class="application-info mo-padding">';
            $s .= '<h6 class="uppercase short">Application Details</h6>';
            $s .= '<dl class="dl-horizontal-med">';

            //SHOULDN'T EVER HAPPEN THAT WE DON'T GET THIS RETURNED BUT JUST IN CASE
            if (isset($app->applicationDetails->applicationProfile)) {
                $s .= $this->fnApplicantDetails($app->applicationDetails->applicationProfile);
            } else {
                $s .= '<dt>N/A</dt>';
            }

            $s .= '</dl>';
            $s .= '</div>';

            //GET PDF LETTERS

            $lt = (isset($app->applicationDetails->pdfLetters)) ? (array) $app->applicationDetails->pdfLetters : null;

            if (! empty($lt)) {
                $s .= '<hr class="small">';
                $s .= '<div class="application-info paddingB mo-padding">';
                $s .= ' <h6 class="uppercase short">Document Centre</h6>';

                //LOAD LETTERS
                $lettersArray = $this->fnLoadLetters($app->applicationDetails->pdfLetters);

                foreach ($lettersArray as $sendDate => $letters) {
                    foreach ($letters as $letter) {
                        $sDate = ($sendDate > 0) ? date('M d, Y', $sendDate) : '-';

                        $s .= '<dl class="dl-horizontal-med">';
                        $s .= '<dt>';
                        $s .= '<small>';
                        $s .= $sDate;
                        $s .= '</small>';
                        $s .= '</dt>';
                        $s .= '<dd>';
                        $appStatus = ($app->applicationDetails->applicationStatus == 'SUBMPROC') ? 'T' : 'R';
                        $s .= '<a href="/dashboard/notifications/'.$letter['LetterID'].'/'.$appStatus.'" target="_blank">';
                        $s .= $letter['LetterDescript'];
                        $s .= '</a>';
                        $s .= '</dd>';
                        $s .= '</dl>';
                    }
                }

                $s .= '</div>';
            }
            $s .= '</div>';

            //LOAD APPLICATION TIMELINE
            $s .= '<div class="six columns" id="col2">';
            $s .= '<hr class="small nospace show-for-small">';
            $s .= '<h6 class="print-only">Application Timeline</h6>';
            $s .= '<ul class="timeline">';

            //START/SUBMIT APPLICATION
            $s .= '<li>';
            $s .= '<span class="icon '.$this->fnStatusClass($eventItems['Start/Submit Application'], 'icon-addtolist').'"></span>';

            //collapse details of event if it is complete
            if (isset($eventItems['Start/Submit Application']['eventCategoryCode']) && $eventItems['Start/Submit Application']['eventCategoryCode'] == 'Complete') {
                $collapse = 'collapse';
                $s .= '<a data-toggle="collapse" id="start_submit" href="#collapseStart_Submit" class="collapsed"><strong>Start/Submit Application</strong> <i id="collapseSS" class="float-right icon icon-circledown"></i></a> <br>';
            } else {
                $collapse = 'collapse in';
                $s .= '<a data-toggle="collapse" id="start_submit" href="#collapseStart_Submit" class=""><strong>Start/Submit Application</strong> <i class="float-right icon icon-circleup"></i></a> <br>';
            }

            $s .= '<div id="collapseStart_Submit" class="'.$collapse.'">';
            foreach ($eventItems['Start/Submit Application']['events'] as $k => $event) {
                $s .= '<dl class="dl-horizontal-med">';
                if (isset($event['eventDate']) && ! empty($event['eventDate'])) {
                    $s .= '<dt class="left">';
                    $s .= '<small>';
                    $s .= $event['eventDate'];
                    $s .= '</small>';
                    $s .= '</dt>';
                    $s .= '<dd>'.$event['eventDescription'].'</dd>';
                } else {
                    $s .= (! empty($event['eventDescription'])) ? '<p>- '.$event['eventDescription'].'</p>' : null;
                }

                $s .= '</dl>';
            }

            foreach ($appendices as $k => $event) {
                $s .= '<dl class="dl-horizontal-med" style="padding-top:10px;">';
                if (isset($event['eventDate']) && ! empty($event['eventDate'])) {
                    $s .= '<dt class="left">';
                    $s .= '<small>';
                    $s .= $event['eventDate'];
                    $s .= '</small>';
                    $s .= '</dt>';
                    $s .= '<dd>'.$event['eventType'].'</dd>';
                } else {
                    $s .= '<dd style="margin-left:0;">'.$event['eventType'].'</dd>';
                }
                $s .= '</dl>';
            }
            $s .= '</div>';
            $s .= '</li>';

            //SUBMIT DECLARATION
            $s .= '<li>';

            //if the application is INPR or RDY then follow e-consent process
            if ($app->applicationDetails->applicationStatus == 'INPR' || $app->applicationDetails->applicationStatus == 'RDY') {
                //show E-Consent collapse button and icon as waiting
                $s .= '<span class="icon icon-uniF47C"></span>';
                $s .= '<a data-toggle="collapse" href="#collapseSubmitEconsent" id="submit_econsent" class="collapsed"><strong>E-Consent Not Submitted</strong></a>';
                $s .= '<div id="collapseSubmitEconsent" class="collapse in"><dl class="dl-horizontal-med"></dl></div>';
            } else {
                $submit_date = 0;
                $econsent_rollout_date = 20200317; // 2020 Mar 17
                $today = date('Ymd', strtotime('today'));
                $today = (int) $today;

                $cid = 'get_application_dec'.rand().''.$app->applicationDetails->applicationNumber;
//                $common = new Aeit();
                $common = $this;
                $common->WSDL = $common->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

//                $GUID = $common->fnDecrypt($GLOBALS['user']->name);
                $GUID = $common->fnDecrypt(Auth::user()->name);
                $isWebDeclarationInkSignatureRequired = $common->fnRequest('isWebDeclarationInkSignatureRequired', ['applicationNumber' => $app->applicationDetails->applicationNumber, 'role' => 'A', 'userGUID' => $GUID], $cid, 100);

                $lettersArray = $this->fnLoadLetters($app->applicationDetails->pdfLetters);
                foreach ($lettersArray as $sendDate => $letters) {
                    foreach ($letters as $letter) {
                        if ($letter['LetterDescript'] == 'Application') {
                            $submit_date = date('Ymd', $sendDate);
                        }
                    }
                }
                /*
                                            foreach ($app->applicationDetails->pdfLetters->pdfLetter as $letter){
                                              if($letter->letterDescription == "Application"){
                                                $submit_date = $letter->sendDate;
                                              }
                                            }
                */
                //if the app submit date is > the e-consent rollout date then follow new process.
                //E-CONSENT NEW PROCESS
                if ((int) $submit_date >= $econsent_rollout_date) {
                    //show E-Consent collapse button and icon as received/active
                    $s .= '<span class="icon icon-uniF479 text-success"></span>';
                    $s .= '<a data-toggle="collapse" href="#collapseSubmitEconsent" id="submit_econsent" class="collapsed"><strong>E-Consent</strong><i class="float-right icon icon-circledown"></i></a><br>';

                    $s .= '<div id="collapseSubmitEconsent" class="collapse"><br/>';

                    $s .= '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/econsent/applicant/'.$app->applicationDetails->applicationNumber.'">View Declaration</a></dl>';
                    $s .= '</div>';

                // legacy application follow old process
                } else {
                    $s .= '<span class="icon '.$this->fnStatusClass($eventItems['Submit Declaration'], 'icon-uniF47C').'"></span>';

                    //collapse details of event if it is complete
                    if (isset($eventItems['Submit Declaration']['eventCategoryCode']) && $eventItems['Submit Declaration']['eventCategoryCode'] == 'Complete') {
                        $collapse = 'collapse';
                        $s .= '<a data-toggle="collapse" href="#collapseSubmitDec" id="submit_dec" class="collapsed"><strong>Submit Declaration</strong><i class="float-right icon icon-circledown"></i></a><br>';
                    } elseif (isset($eventItems['Submit Declaration']['eventCategoryCode'])) {
                        $collapse = 'collapse in';
                        $s .= '<a data-toggle="collapse" href="#collapseSubmitDec" id="submit_dec" class=""><strong>Submit Declaration</strong><i class="float-right icon icon-circleup"></i></a><br>';
                    } else {
                        if ($today >= $econsent_rollout_date) {
                            $s .= '<a data-toggle="collapse" href="#collapseSubmitEconsent" id="submit_econsent" class=""><strong>E-Consent</strong></a>';
                        } else {
                            $s .= '<a data-toggle="collapse" href="#collapseSubmitDec" id="submit_dec" class=""><strong>Submit Declaration</strong></a>';
                        }
                    }

                    $s .= '<div id="collapseSubmitDec" class="'.$collapse.'">';
                    foreach ($eventItems['Submit Declaration']['events'] as $k => $event) {
                        $s .= '<dl class="dl-horizontal-med">';
                        if (isset($event['eventDate']) && ! empty($event['eventDate'])) {
                            if (isset($eventItems['webDecStatus'])) {
                                // if app has not been submitted then show print declaration button
                                if ($eventItems['webDecStatus'] == 't') {
                                    $s .= '<dt class="left">';
                                    $s .= '<small>';
                                    $s .= $event['eventDate'];
                                    $s .= '</small>';
                                    $s .= '</dt>';
                                    $s .= '<dd>';
                                    $s .= $event['eventDescription'];
                                    // $s .= '<br><br>';
                                    // $s .= '<a class="btn" target="_blank" href="/dashboard/declaration/signature/applicant/'.$app->applicationDetails->applicationNumber.'">';
                                    // $s .= '(Re)Print and Sign Applicant\'s Declaration';
                                    // $s .= '</a>';
                                    $s .= '</dd>';
                                } else {
                                    if ($eventItems['webDecStatus'] == 'e') {
                                        //$s .= '<p>';
                                        $s .= '<div class="alert alert-contextual alert-info" role="alert"><svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg><p>Web declaration no longer available on-line, if you require a copy please contact StudentAidBC at 1-800-561-1818</p></div>';
                                    //$s .= '</p>';
                                    } elseif ($eventItems['webDecStatus'] == 'f' || $eventItems['webDecStatus'] == 'f1' || $eventItems['webDecStatus'] == 'f2') {
                                        $s .= '<dt class="left">';
                                        $s .= '<small>';
                                        $s .= $event['eventDate'];
                                        $s .= '</small>';
                                        $s .= '</dt>';
                                        $s .= '<dd>';
                                        $s .= $event['eventDescription'];
                                        $s .= '</dd><br>';
                                        $s .= $event['webDecState'];
                                    } else {
                                        $s .= $event['webDecState'];
                                    }
                                }
                            } else {
                                $s .= '<dt class="left">';
                                $s .= '<small>';
                                $s .= $event['eventDate'];
                                $s .= '</small>';
                                $s .= '</dt>';
                                $s .= '<dd>';
                                $s .= $event['eventDescription'];
                                $s .= '</dd>';
                            }
                        } else {
                            $s .= (! empty($event['eventDescription'])) ? '<p>- '.$event['eventDescription'].'</p>' : null;

                            if (isset($eventItems['webDecStatus'])) {
                                // if app has not been submitted then show print declaration button
                                if ($eventItems['webDecStatus'] == 't') {
                                    $s .= $event['webDecState'];
                                } else {
                                    if ($eventItems['webDecStatus'] == 'e') {
                                        //$s .= '<p>';
                                        $s .= '<div class="alert alert-contextual alert-info" role="alert"><svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#info"></use></svg><p>Web declaration no longer available on-line, if you require a copy please contact StudentAidBC at 1-800-561-1818</p></div>';
                                    //$s .= '</p>';
                                    } elseif ($eventItems['webDecStatus'] == 'f' || $eventItems['webDecStatus'] == 'f1' || $eventItems['webDecStatus'] == 'f2') {
                                        $s .= '<br>';
                                        $s .= $event['webDecState'];
                                    } else {
                                        $s .= $event['webDecState'];
                                    }
                                }
                            }
                        }
                        $s .= '</dl>';
                    }
                    $s .= '</div>';
                }
            }

            $s .= '</li>';

            //APPLICATION REVIEW
            $s .= '<li>';
            $s .= '<span class="icon '.$this->fnStatusClass($eventItems['Application Review'], 'icon-eye2').'"></span>';

            //collapse details of event if it is complete
            if (isset($eventItems['Application Review']['eventCategoryCode']) && $eventItems['Application Review']['eventCategoryCode'] == 'Complete') {
                $collapse = 'collapse';
                $s .= '<a data-toggle="collapse" href="#collapseAppReview" id="app_review" class="collapsed"><strong>Application Review</strong><i class="float-right icon icon-circledown"></i></a><br>';
            } elseif (isset($eventItems['Application Review']['eventCategoryCode'])) {
                $collapse = 'collapse in';
                $s .= '<a data-toggle="collapse" href="#collapseAppReview" id="app_review" class=""><strong>Application Review</strong><i class="float-right icon icon-circleup"></i></a><br>';
            } else {
                $s .= '<a data-toggle="collapse" href="#collapseAppReview" id="app_review" class=""><strong>Application Review</strong></a>';
            }

            $s .= '<div id="collapseAppReview" class="'.$collapse.'">';

            foreach ($eventItems['Application Review']['events'] as $k => $event) {
                $s .= '<dl class="dl-horizontal-med">';
                if (isset($event['eventIncomplete']) && ! empty($event['eventIncomplete'])) {
                    $s .= '<dl class="dl-horizontal-med alert text-white alert-danger" role="alert" style="padding: 8px 14px">';
                    $s .= $event['eventIncomplete'];
                    $s .= '</dl>';
                } elseif (isset($event['eventDate']) && ! empty($event['eventDate'])) {
                    $s .= '<dt class="left">';
                    $s .= '<small>';
                    $s .= $event['eventDate'];
                    $s .= '</small>';
                    $s .= '</dt>';

                    $s .= '<dd>'.$event['eventDescription'].'</dd>';
                } else {
                    $s .= (! empty($event['eventDescription'])) ? '<p>- '.$event['eventDescription'].'</p>' : null;
                }

                $s .= '</dl>';
            }
            $s .= '</div>';

            $s .= '</li>';

            //FUNDING DECISION
            $s .= '<li>';
            $s .= '<span class="icon '.$this->fnStatusClass($eventItems['Funding Decision'], 'icon-cash').'"></span>';
            //collapse details of event if it is complete
            if (isset($eventItems['Funding Decision']['eventCategoryCode']) && $eventItems['Funding Decision']['eventCategoryCode'] == 'Complete') {
                $collapse = 'collapse';
                $s .= '<a data-toggle="collapse" href="#collapseFundDesc" id="fund_desc" class="collapsed"><strong>Funding Decision</strong><i class="float-right icon icon-circledown"></i></a><br>';
            } elseif (isset($eventItems['Funding Decision']['eventCategoryCode'])) {
                $collapse = 'collapse in';
                $s .= '<a data-toggle="collapse" href="#collapseFundDesc" id="fund_desc" class=""><strong>Funding Decision</strong><i class="float-right icon icon-circleup"></i></a><br>';
            } else {
                $s .= '<a data-toggle="collapse" href="#collapseFundDesc" id="fund_desc" class=""><strong>Funding Decision</strong></a>';
            }

            $s .= '<div id="collapseFundDesc" class="'.$collapse.'">';

            foreach ($eventItems['Funding Decision']['events'] as $k => $event) {
                $s .= '<dl class="dl-horizontal-med">';
                if (isset($event['eventDate']) && ! empty($event['eventDate'])) {
                    $s .= '<dt class="left">';
                    $s .= '<small>';
                    $s .= $event['eventDate'];
                    $s .= '</small>';
                    $s .= '</dt>';
                    $s .= '<dd>'.$event['eventDescription'].'</dd>';
                } else {
                    $s .= (! empty($event['eventDescription'])) ? '<p>- '.$event['eventDescription'].'</p>' : null;
                }
                $s .= '</dl>';
            }
            $s .= '</div>';
            $s .= '</li>';

            //CONFIRMATION OF ENROLLMENT AND FUNDING DISBURSEMENT
            $s .= '<li>';
            $s .= '<span class="icon '.$this->fnStatusClass($eventItems['Confirmation of Enrollment and Funding Disbursement'], 'icon-wallet').'"></span>';
            $s .= '<strong>Confirmation of Enrollment and Funding Disbursement</strong><br>';

            if (isset($eventItems['Confirmation of Enrollment and Funding Disbursement']) &&
                ! empty($eventItems['Confirmation of Enrollment and Funding Disbursement']['eventCategoryCode'])) {
                $s .= '<dl class="dl-horizontal-med alert text-white alert-info">';
                $s .= '<dt class="left">';
                $s .= '<small class="text-error">Important</small>';
                $s .= '</dt>';
                $s .= '<dd>';
                $s .= 'Funding can only be released once you’ve completed your Master Student Financial Assistance Agreement (MSFAA) and your school has confirmed your enrolment.';
                $s .= ' We will initiate the Confirmation of Enrolment process shortly before the disbursement date. ';
                $s .= 'Once confirmed, you can expect to receive your funding within 7 business days of the disbursement date.';
                $s .= '</dd>';
                $s .= '</dl>';
                $s .= '<hr>';

                if (isset($app->applicationDetails->fundingDetails)) {
                    $s .= '<h6 class="uppercase">Disbursement Details</h6>';

                    //if we have an array of disbursement groups
                    if (is_array($app->applicationDetails->fundingDetails->disbursementGroup)) {
                        foreach ($app->applicationDetails->fundingDetails->disbursementGroup as $key => $value) {
                            $s .= '<table width="100%" class="table small table-condensed table-bordered">';
                            $s .= '<thead>';
                            $s .= '<tr>';
                            $s .= '<th width="55%">';
                            $s .= '<div class="text-left">Type of funding</div>';
                            $s .= '</th>';
                            $s .= '<th width="15%">';
                            $s .= '<div class="text-center">Amount</div>';
                            $s .= '</th>';
                            $s .= '<th width="15%">';
                            $s .= '<div class="text-center">To be paid to school</div>';
                            $s .= '</th>';
                            $s .= '<th width="15%">';
                            $s .= '<div class="text-center">Confirmation of Enrolment</div>';
                            $s .= '</th>';
                            $s .= '</tr>';
                            $s .= '</thead>';
                            $s .= '<tfoot>';
                            $s .= '<tr>';
                            $s .= '<td colspan="2"><strong>Amount Paid to School:</strong></td>';
                            $s .= '<td colspan="2" class="text-right">$'.$value->totalFunds->paidToSchool.'</td>';
                            $s .= '</tr>';
                            $s .= '<tr>';
                            $s .= '<td colspan="2"><strong>Est. Earliest Disbursement:</strong></td>';
                            $s .= '<td colspan="2" class="text-right">'.date('M d, Y', strtotime($value->totalFunds->earliestDate)).'</td>';
                            $s .= '</tr>';
                            $s .= '<tr>';
                            $s .= '<td colspan="2"><strong>Total Funding:</strong></td>';
                            $s .= '<td colspan="2" class="text-right">$'.$value->totalFunds->amount.'</td>';
                            $s .= '</tr>';
                            $s .= '</tfoot>';
                            $s .= '<tbody>';
                            foreach ($value->funds as $funds => $fund) {
                                if (is_array($fund)) {
                                    foreach ($fund as $key => $f) {
                                        $s .= '<tr>';
                                        $s .= '<td>'.$f->typeOfFunding.'</td>';
                                        $s .= '<td>';
                                        $s .= '<div class="text-center">$'.number_format($f->amount).'</div>';
                                        $s .= '</td>';
                                        $s .= '<td>';
                                        $s .= '<div class="text-center">$'.number_format($f->paidToSchool).'</div>';
                                        $s .= '</td>';
                                        $s .= '<td>';
                                        if ($eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$f->disbursementID]['eventCode'] == 'Complete') {
                                            $s .= '<div class="text-right">';
                                            $s .= date('M d', strtotime($eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$f->disbursementID]['eventDate']));
                                            $s .= '<i class="icon icon-uniF479 text-success" alt="Enrolment Confirmed" title="Enrolment Confirmed"></i>';
                                            $s .= '</div>';
                                        } else {
                                            $status = $eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$f->disbursementID]['eventCode'];
                                            $s .= '<div class="text-right">';
                                            $s .= '<span class="badge badge-'.$statusFlags[$status]['class'].'">';
                                            $s .= $statusFlags[$status]['status'];
                                            $s .= '</span>';
                                            $s .= '</div>';
                                        }
                                        $s .= '</td>';
                                        $s .= '</tr>';
                                    }
                                } else {
                                    $s .= '<tr>';
                                    $s .= '<td>'.$fund->typeOfFunding.'</td>';
                                    $s .= '<td>';
                                    $s .= '<div class="text-center">$'.number_format($fund->amount).'</div>';
                                    $s .= '</td>';
                                    $s .= '<td>';
                                    $s .= '<div class="text-center">$'.number_format($fund->paidToSchool).'</div>';
                                    $s .= '</td>';
                                    $s .= '<td>';
                                    if ($eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$fund->disbursementID]['eventCode'] == 'Complete') {
                                        $s .= '<div class="text-right">';
                                        $s .= date('M d', strtotime($eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$fund->disbursementID]['eventDate']));
                                        $s .= '<i class="icon icon-uniF479 text-success" alt="Enrolment Confirmed" title="Enrolment Confirmed"></i>';
                                        $s .= '</div>';
                                    } else {
                                        $status = $eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$fund->disbursementID]['eventCode'];
                                        $s .= '<div class="text-right">';
                                        $s .= '<span class="badge badge-'.$statusFlags[$status]['class'].'">';
                                        $s .= $statusFlags[$status]['status'];
                                        $s .= '</span>';
                                        $s .= '</div>';
                                    }
                                    $s .= '</td>';
                                    $s .= '</tr>';
                                }
                            }
                            $s .= '</tbody>';
                            $s .= '</table>';
                        }
                    }
                    if (is_object($app->applicationDetails->fundingDetails->disbursementGroup)) {
                        $s .= '<table width="100%" class="table small table-condensed table-bordered">';
                        $s .= '<thead>';
                        $s .= '<tr>';
                        $s .= '<th width="55%">';
                        $s .= '<div class="text-left">Type of funding</div>';
                        $s .= '</th>';
                        $s .= '<th width="15%">';
                        $s .= '<div class="text-center">Amount</div>';
                        $s .= '</th>';
                        $s .= '<th width="15%">';
                        $s .= '<div class="text-center">To be paid to school</div>';
                        $s .= '</th>';
                        $s .= '<th width="15%">';
                        $s .= '<div class="text-center">Confirmation of Enrolment</div>';
                        $s .= '</th>';
                        $s .= '</tr>';
                        $s .= '</thead>';
                        $s .= '<tfoot>';
                        $s .= '<tr>';
                        $s .= '<td colspan="2"><strong>Amount Paid to School:</strong></td>';
                        $s .= '<td colspan="2" class="text-right">$'.$app->applicationDetails->fundingDetails->disbursementGroup->totalFunds->paidToSchool.'</td>';
                        $s .= '</tr>';
                        $s .= '<tr>';
                        $s .= '<td colspan="2"><strong>Est. Earliest Disbursement:</strong></td>';
                        $s .= '<td colspan="2" class="text-right">'.date('M d, Y', strtotime($app->applicationDetails->fundingDetails->disbursementGroup->totalFunds->earliestDate)).'</td>';
                        $s .= '</tr>';
                        $s .= '<tr>';
                        $s .= '<td colspan="2"><strong>Total Funding:</strong></td>';
                        $s .= '<td colspan="2" class="text-right">$'.$app->applicationDetails->fundingDetails->disbursementGroup->totalFunds->amount.'</td>';
                        $s .= '</tr>';
                        $s .= '</tfoot>';
                        $s .= '<tbody>';
                        foreach ($app->applicationDetails->fundingDetails->disbursementGroup->funds as $funds => $fund) {
                            if (is_array($fund)) {
                                foreach ($fund as $key => $f) {
                                    $s .= '<tr>';
                                    $s .= '<td>'.$f->typeOfFunding.'</td>';
                                    $s .= '<td>';
                                    $s .= '<div class="text-center">$'.number_format($f->amount).'</div>';
                                    $s .= '</td>';
                                    $s .= '<td>';
                                    $s .= '<div class="text-center">$'.number_format($f->paidToSchool).'</div>';
                                    $s .= '</td>';
                                    $s .= '<td>';
                                    if ($eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$f->disbursementID]['eventCode'] == 'Complete') {
                                        $s .= '<div class="text-right">';
                                        $s .= date('M d', strtotime($eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$f->disbursementID]['eventDate']));
                                        $s .= '<i class="icon icon-uniF479 text-success" alt="Enrolment Confirmed" title="Enrolment Confirmed"></i>';
                                        $s .= '</div>';
                                    } else {
                                        $status = $eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$f->disbursementID]['eventCode'];
                                        $s .= '<div class="text-right">';
                                        $s .= '<span class="badge badge-'.$statusFlags[$status]['class'].'">';
                                        $s .= $statusFlags[$status]['status'];
                                        $s .= '</span>';
                                        $s .= '</div>';
                                    }
                                    $s .= '</td>';
                                    $s .= '</tr>';
                                }
                            } else {
                                $s .= '<tr>';
                                $s .= '<td>'.$fund->typeOfFunding.'</td>';
                                $s .= '<td>';
                                $s .= '<div class="text-center">$'.number_format($fund->amount).'</div>';
                                $s .= '</td>';
                                $s .= '<td>';
                                $s .= '<div class="text-center">$'.number_format($fund->paidToSchool).'</div>';
                                $s .= '</td>';
                                $s .= '<td>';
                                if ($eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$fund->disbursementID]['eventCode'] == 'Complete') {
                                    $s .= '<div class="text-right">';
                                    $s .= date('M d', strtotime($eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$fund->disbursementID]['eventDate']));
                                    $s .= '<i class="icon icon-uniF479 text-success" alt="Enrolment Confirmed" title="Enrolment Confirmed"></i>';
                                    $s .= '</div>';
                                } else {
                                    $status = $eventItems['Confirmation of Enrollment and Funding Disbursement']['events'][$fund->disbursementID]['eventCode'];
                                    $s .= '<div class="text-right">';
                                    $s .= '<span class="badge badge-'.$statusFlags[$status]['class'].'">';
                                    $s .= $statusFlags[$status]['status'];
                                    $s .= '</span>';
                                    $s .= '</div>';
                                }
                                $s .= '</td>';
                                $s .= '</tr>';
                            }
                        }
                        $s .= '</tbody>';
                        $s .= '</table>';
                    }
                }
            }
            $s .= '</li>';
            $s .= '</ul>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';

            $s .= '<div id="panel">';
            $s .= '<hr class="print-only" />';
            $s .= '<div class="content">';
            $s .= '<div class="arow taskbar">';
            $s .= '<div class="inner">';
            $s .= '<a href="#" class="badge togglepanel" aria-hidden="true">Close</a>&nbsp;&nbsp;<h3>Calculation of Assessment</h3>';
            $s .= '</div>';
            $s .= '</div>';

            //HOW WAS THIS CALCULATED PANEL
            $s .= '<div class="panelbody">';
            $s .= '<div class="padding mo-paddingT mo-paddingB">';

            if (isset($app->applicationDetails->assessedCosts)) {
                $s .= '<table class="table" width="100%">';
                $s .= '<thead>';
                $s .= '<tr>';
                $s .= '<th></th>';
                $s .= '<th><div class="text-right">Costs</div></th>';
                $s .= '<th></th>';
                $s .= '<th><div class="text-left">Resources</div></th>';
                $s .= '</tr>';
                $s .= '</thead>';
                $s .= '<tbody>';

                foreach ($app->applicationDetails->assessedCosts as $k => $calculation) {
                    if ($k == 'AssessedTotals') {
                        $s .= '<tr class="highlight">';
                        $s .= '<td>Assessed Totals</td>';
                        $s .= '<td>';
                        $s .= '<div class="text-right">';
                        $s .= '<span class="text-success">';
                        $s .= '$'.number_format($calculation->AssessedTotal[0]->Value);
                        $s .= '</span>';
                        $s .= '</div>';
                        $s .= '</td>';
                        $s .= '<td>';
                        $s .= '<div class="text-center large">-</div>';
                        $s .= '</td>';
                        $s .= '<td>';
                        $s .= '<span class="text-error">';
                        $s .= '$'.number_format($calculation->AssessedTotal[1]->Value).'';
                        $s .= '</span>';
                        $s .= '</td>';
                        $s .= '</tr>';

                        $s .= '<tr class="subheading">';
                        $s .= '<td>Assessed Need</td>';
                        $s .= '<td></td>';
                        $s .= '<td><div class="text-center large">=</div></td>';
                        $s .= '<td>';
                        $s .= '<div class="large">';
                        $s .= '$'.number_format($calculation->AssessedTotal[2]->Value);
                        $s .= '</div>';
                        $s .= '</td>';
                        $s .= '</tr>';
                    } else {
                        foreach ($calculation as $costs) {
                            foreach ($costs as $cost) {
                                $s .= '<tr>';

                                if (! empty($cost->Value)) {
                                    $desc = (isset($cost->CostType)) ? $cost->CostType : $cost->ResourceType;
                                    $s .= '<td width="50%">'.$desc.'</td>';
                                }

                                if (isset($cost->CostType) && ! empty($cost->Value)) {
                                    $s .= '<td width="24%">';
                                    $s .= '<div class="text-right">';
                                    $s .= '<strong class="text-success">';
                                    $s .= '$'.number_format($cost->Value);
                                    $s .= '</strong>';
                                    $s .= '</div>';
                                    $s .= '</td>';
                                    $s .= '<td width="2%"></td>';
                                    $s .= '<td width="24%">';
                                    $s .= '<div class="text-right">';
                                    $s .= '<strong class="text-success">';
                                    $s .= '&nbsp;';
                                    $s .= '</strong>';
                                    $s .= '</div>';
                                    $s .= '</td>';
                                }

                                if (isset($cost->ResourceType) && ! empty($cost->Value)) {
                                    $s .= '<td width="24%">';
                                    $s .= '<div class="text-right">';
                                    $s .= '<strong class="text-success">';
                                    $s .= '&nbsp;';
                                    $s .= '</strong>';
                                    $s .= '</div>';
                                    $s .= '</td>';
                                    $s .= '<td width="2%"></td>';
                                    $s .= '<td width="24%">';
                                    $s .= '<strong class="text-error">';
                                    $s .= '$'.number_format($cost->Value);
                                    $s .= '</strong>';
                                    $s .= '</td>';
                                }

                                $s .= '</tr>';
                            }
                        }
                    }
                }
                $s .= '</tbody>';
                $s .= '</table>';
                $s .= '<p>Your <strong>assessed need</strong> is compared with the <a href="/help-centre/applying-loans/your-financial-need" target="_blank">maximum weekly funding limit</a> allowed for your study period. The lesser of these two amounts is what you are eligible to receive.</p>';
            } else {
                $s .= '<p>No assessment information available</p>';
            }
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
        } else {
            $s = '';
        }

        return $s;
    }

    public function loadAppendixDetails($app)
    {
        $r = new application();

        //GET ALL OUR EVENTS IN SORT THEM IN ARRAY
        $events = fnLoadEvents($app->appendixTimeline->EventCategories->eventCategory, $app->formType, $app->applicationNumber, $app->apxStatus);
        //STATUS FLAGS TO TELL US WHERE THE APPLICATION IS AT

        //TIMELINE EVENTS
        $event = ['Start/Submit Appendix' => ['title' => 'Start/Submit Appendix', 'class' => 'icon-addtolist'],
            'Submit Appendix Declaration' => ['title' => 'Submit Appendix Declaration', 'class' => 'icon-uniF47C']];

        $tl = (isset($app->appendixTimeline)) ? (array) $app->appendixTimeline : null;

        if (isset($app->applicationNumber) && $app->applicationNumber > 0 && ! empty($tl)) {
            $s = '<div class="paddingL paddingR mo-padding0">';
            //livecycle submit/delete/edit buttons

            $submitClass = (isset($events['submitStatus']) && ! empty($events['submitStatus'])) ? 'btn-success' : (($app->apxStatus == 'RDY') ? 'btn-success' : 'disabled');
            $formType = strtoupper(str_replace(' ', '', $app->formType));

            //if($eventTimeLine['Application Review']['status'] == NULL) {
            //$submitClass =
            $s .= '<div class="arow">';
            $s .= '<div id="livecycle-controls" class="twelve columns btn-toolbar">';

            if ($app->reminderFlag == 'Y' && ! session()->exists('read-only')) {
                //if($app->reminderFlag == 'Y' && !(isset($_SESSION['read-only']))){
                $s .= '<button data-toggle="modal" data-remote="false" data-target="#delete-appendix-confirm" class="btn btn-danger left">Delete Appendix</button>';

                $s .= '<div class="modal fade" id="delete-appendix-confirm" tabindex="-1"
								role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												<h4 class="modal-title" id="myModalLabel">Confirm Delete?</h4>
											</div>
											<div class="modal-body">
												<p>Are you sure you want to delete '.$app->formType.' <strong>#'.$app->applicationNumber.'</strong>?</p>
											</div>
											<div class="modal-footer">
												<a href="#" class="btn btn-default" data-dismiss="modal">No, do NOT delete this appendix</a>
												<a href="/dashboard/student-loans/remove-appendix/'.$app->formGUID.'" class="btn btn-danger" >Yes, delete this appendix</a>
											</div>
										</div>
									</div>
								</div>'; //modal window for delete application confirm
            } else {
                if (empty($events['submitStatus'])) {
                    if ($submitClass == 'btn-success') {
                        $s .= '<a href="/dashboard/appendix-submit-checklist/'.$formType.'/'.$app->programYear.'/'.$app->applicationNumber.'/'.$app->formGUID.'" class="btn '.$submitClass.' pull-right ladda-button" data-style="expand-left">Submit Appendix</a>';
                    // /dashboard/appendix-submit-checklist/APPENDIX1/20142015/2014300249/bc79ba20066f41f2a0fe102d9ce4dfa4
                    } else {
                        $eventIncomplete = '';
                        $eventIncomplete = $events['Start/Submit Appendix']['eventIncomplete'];

                        $s .= '<button data-toggle="modal" data-remote="false" data-target="#submit-appendix-incomplete" class="btn '.$submitClass.' pull-right">Submit Appendix</button>';

                        $s .= '<div class="modal fade" id="submit-appendix-incomplete" tabindex="-1"';
                        $s .= 'role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">';
                        $s .= '<div class="modal-dialog">';
                        $s .= '<div class="modal-content">';
                        $s .= '<div class="modal-header">';
                        $s .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
                        $s .= '<h4 class="modal-title" id="myModalLabel">';
                        $s .= 'Submit Appendix';
                        $s .= '</h4>';
                        $s .= '</div>';
                        $s .= '<div class="modal-body">';
                        $s .= '<div class="alert text-white alert-info" style="padding-right: 14px; margin-top: 0px;">';
                        $s .= '<small>';
                        $s .= 'Finished filling out your appendix and ready to submit? Press “Edit Appendix" and then “Submit” from inside the form.';
                        $s .= '</small>';
                        $s .= '</div>';
                        $s .= '</div>';
                        $s .= '<div class="modal-footer">';
                        $s .= '<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>';
                        $s .= '</div>';
                        $s .= '</div>';
                        $s .= '</div>';
                        $s .= '</div>';
                    }

                    $s .= '<a href="/dashboard/apply/appendix/'.$formType.'/'.$app->formGUID.'/'.$app->programYear.'" class="btn btn-warning pull-right ladda-button" data-style="expand-left" style="margin-right:4px;">Edit Appendix</a>';
                }
            }

            $s .= '</div>';
            $s .= '</div>';
            //}

            $common = new aeit();
            $s .= '<div class="accordion appstatus clearfix" id="accordion2">';
            $s .= '<div class="accordion-group">';
            $s .= '<div class="accordion-heading large full-width">';
            $s .= '<div class="accordion-toggle clearfix">';
            $s .= '<div class="float-left"><h4>Appendix #'.$common->fnFormatApplicationNumber($app->applicationNumber).'</h4></div>';
            //$s .= '<div class="float-right"><span class="label label-'.$statusClass.'">'.$status.'</span></div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '<div id="collapseOne" class="accordion-body collapse in">';
            $s .= '<div class="accordion-inner full-width">';
            $s .= '<div class="arow">';
            $s .= '<div id="faux2">';
            $s .= '<div id="faux1">';
            $s .= '<div class="six columns" id="col1">';

            //APPLICANT DETAILS
            $s .= '<div class="application-info mo-padding">';
            $s .= '<dl class="dl-horizontal-med">';
            $s .= '<dt>For</dt>';
            $s .= '<dd>';
            $s .= $app->firstName.' '.$app->lastName;
            $s .= '</dd>';
            $s .= '</dl>';
            $s .= '</div>';

            //APPLICATION NUMBER
            $s .= '<div class="application-info mo-padding">';
            $s .= '<dl class="dl-horizontal-med">';
            $s .= '<dt>App #</dt>';
            $s .= '<dd>';
            $s .= $app->applicationNumber;
            $s .= '</dd>';
            $s .= '</dl>';
            $s .= '</div>';

            //PROGRAM START DATE
            $progStartDate = (isset($app->studyStartDate)) ?
                date('M d, Y', strtotime($app->studyStartDate)) : null;

            $s .= '<div class="application-info mo-padding">';
            $s .= '<dl class="dl-horizontal-med">';
            $s .= '<dt>Start Date</dt>';
            $s .= '<dd>';
            $s .= $progStartDate;
            $s .= '</dd>';
            $s .= '</dl>';
            $s .= '</div>';

            //PROGRAM END DATE
            $progEndDate = (isset($app->studyEndDate)) ?
                date('M d, Y', strtotime($app->studyEndDate)) : null;

            $s .= '<div class="application-info mo-padding">';
            $s .= '<dl class="dl-horizontal-med">';
            $s .= '<dt>End Date</dt>';
            $s .= '<dd>';
            $s .= $progEndDate;
            $s .= '</dd>';
            $s .= '</dl>';
            $s .= '</div>';

            //don't show unless app has been submitted (in transition or received) -- needs to be changed to match application details!!!!
            if (! empty($events['submitStatus'])) {
                $s .= '<hr class="small">';
                $s .= '<div class="application-info paddingB mo-padding">';
                $s .= '<h6 class="uppercase short">Document Centre</h6>';
                //DOCUMENT CENTER
                if (isset($app->documentID) && ! empty($app->documentID) || isset($app->formGUID) && ! empty($app->formGUID)) {
                    $appStatus = (! empty($app->documentID)) ? 'R' : 'T'; //"R" (received) : "T" (in transition)
                    $docID = (! empty($app->documentID)) ? $app->documentID : $app->formGUID;
                    $s .= '<dl class="dl-horizontal-med">';
                    // $s .= '<dt>';
                    // 	$s .= '<small>';
                    // 		$s .= $sDate;
                    // 	$s .= '</small>';
                    // $s .= '</dt>';
                    $s .= '<dt>';
                    $s .= '<a href="/dashboard/notifications/'.$docID.'/'.$appStatus.'/Appendix" target="_blank">';
                    $s .= $app->formType;
                    $s .= '</a>';
                    $s .= '</dt>';
                    $s .= '</dl>';
                }

                $s .= '</div>';
            }
            $s .= '</div>';
            //LOAD APPLICATION TIMELINE
            $s .= '<div class="six columns" id="col2">';
            $s .= '<hr class="small nospace show-for-small">';
            $s .= '<h6 class="print-only">Application Timeline</h6>';
            $s .= '<ul class="timeline">';

            $s .= '<li>';
            $s .= '<span class="icon icon-addtolist text-'.fnStatusClass($events['Start/Submit Appendix']['eventCode']).'"></span>';
            $s .= '<strong>Start/Submit Appendix</strong><br>';
            $s .= '<dl class="dl-horizontal-med">';
            if ($app->reminderFlag == 'Y') {
                $s .= '<p>';
                $s .= $events['Start/Submit Appendix']['eventDate'].'&nbsp;&nbsp;'.$events['Start/Submit Appendix']['eventType'];
                $s .= '<br><br>';
                $s .= '<button data-toggle="modal" data-remote="false" data-target="#delete-appendix-confirm" class="btn btn-danger">';
                $s .= 'Delete Appendix';
                $s .= '</button>';
                $s .= '</p>';
            }
            //$s .= '<p>'.$events['Start/Submit Appendix']['eventDescription'].' <a href="/dashboard/student-loans/remove-appendix/'.$events['appendixID'].'" class="btn btn-danger pull-right">Delete Appendix</a></p>';
            else {
                if (isset($events['Start/Submit Appendix']['eventDate']) && ! empty($events['Start/Submit Appendix']['eventDate'])) {
                    $s .= '<dt class="left">';
                    $s .= '<small>';
                    $s .= $events['Start/Submit Appendix']['eventDate'];
                    $s .= '</small>';
                    $s .= '</dt>';
                    $s .= '<dd>';
                    $s .= $events['Start/Submit Appendix']['eventType'];
                    $s .= '</dd>';
                } else {
                    $s .= '<p>';
                    $s .= $events['Start/Submit Appendix']['eventType'];
                    $s .= '</p>';
                }
            }
            $s .= '</dl>';
            $s .= '</li>';

            //# SPOUSE Submit Appendix Declaration box
            $s .= '<li>';

            $cid = 'get_application_dec'.rand().''.$app->applicationNumber;
            $common = new aeit();
            $common->WSDL = $this->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

            $GUID = $common->fnDecrypt($GLOBALS['user']->name);
            $isWebDeclarationInkSignatureRequired = $common->fnRequest('isWebDeclarationInkSignatureRequired', ['applicationNumber' => $app->applicationNumber, 'role' => 'S', 'userGUID' => $GUID], $cid, 100);

            if ($isWebDeclarationInkSignatureRequired->inkSignatureRequired == 'Y') {
                $s .= fetchSpouseLegacyCode($events);

            } else {
                // IF APPENDIX IS IN PROGRESS
                if ($events['submitStatus'] == false) {
                    //SHOW E-CONSENT if user is authenticated
                    if ($isWebDeclarationInkSignatureRequired->inkSignatureRequired == 'E') {
                        $s .= '<span class="icon icon-uniF47C text-'.fnStatusClass($events['Submit Appendix Declaration']['eventCode']).'"></span>';
                        $s .= '<strong>E-Consent Not Submitted</strong><br>';
                    } else {
                        $s .= fetchSpouseLegacyCode($events);
                    }

                //else it was submitted
                } else {
                    [$processing_consent, $show_econsent] = fetchGetWebDeclaration($common, $app, $GUID);

                    //if $processing_consent is true, attempt to wait 30s
                    if ($processing_consent == true) {
                        $wait_init = 0;
                        $wait_limit = 30;
                        while ($wait_init < $wait_limit) {
                            [$processing_consent, $show_econsent] = fetchGetWebDeclaration($common, $app, $GUID);
                            if ($processing_consent == false) {
                                break;
                            }
                            sleep(1);
                            $wait_init++;
                        }
                    }

                    if ($processing_consent == true) {
                        $s .= '<span class="icon icon-uniF47C text-warning"></span>';
                        $s .= '<p class="alert text-white alert-warning">If unable to view your declaration please contact SABC at 1-800-5611818</p>';
                    } else {
                        //if the declaration was an econsent (CSXS), show e-consent view button
                        if ($show_econsent == true) {
                            $s .= '<span class="icon icon-uniF47C text-success"></span>';
                            $s .= '<strong>E-Consent</strong><br>';
                            $s .= '<a class="btn btn-block btn-light" target="_blank" href="/dashboard/declaration/econsent/spouse/'.$app->applicationNumber.'">View Declaration</a><br>';

                        //if it was submitted before rollout then show legacy declaration button (Re)Print and sign ...
                        } else {
                            $s .= fetchSpouseLegacyCode($events);
                        }
                    }
                }
            }

            $s .= '</li>';

            $s .= '</ul>';
            $s .= '</div>';

            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
            $s .= '</div>';
        } else {
            $s = '';
        }

        return $s;
    }

    //from class.application.php

    /*
    * fnGetDeclaration:  Used to determine Declaration status.
    * @params:
    * 	$appID = application number
    *		$role = the type of declaration we are determining for
    */
    public function fnGetDeclaration($appID, $role, $noSin = false)
    {
        $this->uid = $this->fnDecrypt(Auth::user()->name);

        //CALL THE APPROPRIATE WEB SERVICE
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

        //THE NAME OF OUR CACHE FILE
        $cid = 'get_application_dec'.$this->uid.''.$appID;

        if ($noSin || $role != 'A') {
            $document = $this->fnRequest('getWebDeclaration', ['applicationNumber' => $appID, 'role' => $role], $cid, 100);
        } else {
            $document = $this->fnRequest('getWebDeclaration', ['applicationNumber' => $appID, 'userGUID' => $this->uid, 'role' => $role], $cid, 100);
        }

        //CHECK TO MAKE SURE WE GET A RESPONSE
        if (! empty($document) && is_object($document)) {
            //CHECK TO MAKE SURE WE DON'T HAVE ERRORS
            if (! isset($document->faultcode) && isset($document->WebDeclaration)) {
                $r = [];

                $appNo = str_split($appID, 4);
                $progYear = substr($appID, 0, 4);

                //OFFICE USE ONLY NUMBER
                $r['ouo'] = $document->WebDeclaration->officeUseOnly;

                //SUBMIT CODE
                $r['sc'] = $document->WebDeclaration->submitCode;

                //PROGRAM YEAR
                $r['py'] = ''.$progYear.'/'.($progYear + 1);

                //DATE SIGNED
                $r['ds'] = $document->WebDeclaration->dateSigned;

                //APPLICATION SUBMIT DATE
                $r['asd'] = $document->WebDeclaration->dateSigned;

                //APPLICATION NUMBER
                $r['appno'] = $appID;

                if (isset($document->WebDeclaration->firstName)) {
                    $r['parent2'] = true;
                    if (! $noSin) {
                        $r['parent2FName'] = $document->WebDeclaration->firstName;
                        $r['parent2LName'] = (isset($document->WebDeclaration->familyName)) ? $document->WebDeclaration->familyName : null;
                        $r['parent2Gender'] = (isset($document->WebDeclaration->gender)) ? $document->WebDeclaration->gender : null;
                        $r['parent2Address1'] = (isset($document->WebDeclaration->addressLine1)) ? $document->WebDeclaration->addressLine1 : null;
                        $r['parent2Address2'] = (isset($document->WebDeclaration->addressLine2)) ? $document->WebDeclaration->addressLine2 : null;
                        $r['parent2City'] = (isset($document->WebDeclaration->city)) ? $document->WebDeclaration->city : null;
                        $r['parent2Province'] = (isset($document->WebDeclaration->province)) ? $document->WebDeclaration->province : null;
                        $r['parent2PostalCode'] = (isset($document->WebDeclaration->postalCode)) ? $document->WebDeclaration->postalCode : null;
                        $r['parent2Country'] = (isset($document->WebDeclaration->country)) ? $document->WebDeclaration->country : null;
                        $r['parent2DOB'] = (isset($document->WebDeclaration->dateOfBirth)) ? $document->WebDeclaration->dateOfBirth : null;
                        $r['parent2Phone'] = (isset($document->WebDeclaration->phoneNumber)) ? $document->WebDeclaration->phoneNumber : null;
                    }
                }

                return $r;
            } else {
                //CHECK TO SEE IF THIS IS AN APPENDIX ERROR
                if (isset($document->detail->ApplicationFault)) {
                    return $document->detail->ApplicationFault->faultCode;
                } else {
                    //CHECK TO SEE IF THIS IS AN APPLICATION DETAILS ERROR
                    $r = [];

                    $appNo = str_split($appID, 4);
                    $progYear = substr($appID, 0, 4);

                    //OFFICE USE ONLY NUMBER
                    $r['ouo'] = null;

                    //SUBMIT CODE
                    $r['sc'] = null;

                    //PROGRAM YEAR
                    $r['py'] = ''.$progYear.'/'.($progYear + 1);

                    //DATE SIGNED
                    $r['ds'] = null;

                    //APPLICATION SUBMIT DATE
                    $r['asd'] = null;

                    //APPLICATION NUMBER
                    $r['appno'] = $appID;

                    return $r;
                }
            }
        }
    }

    public function fnGetAppendixList($formatted = true)
    {
        $this->uid = $this->fnDecrypt(Auth::user()->name);

        //CALL THE APPROPRIATE WEB SERVICE
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

        $results = [];

        $results['Appendix1'] = [];
        $results['Appendix1']['total'] = 0;
        $results['Appendix1']['Submitted'] = [];
        $results['Appendix1']['NotSubmitted'] = [];
        $results['Appendix1']['InTransition'] = [];

        $results['Appendix2'] = [];
        $results['Appendix2']['total'] = 0;
        $results['Appendix2']['Submitted'] = [];
        $results['Appendix2']['NotSubmitted'] = [];
        $results['Appendix2']['InTransition'] = [];

        $appendix = $this->fnRequest('getAppendixList', ['userGUID' => $this->uid], 'get_appendix_list'.$this->uid, 14400);

        if (! empty($formatted)) {
            //CHECK TO MAKE SURE WE GET A RESPONSE
            if (! empty($appendix) && is_object($appendix)) {
                //CHECK TO MAKE SURE WE DON'T HAVE ERRORS
                if (! isset($appendix->faultcode) && isset($appendix->AppendixLists)) {
                    foreach ($appendix->AppendixLists as $k => $app) {
                        //if we have an array then we have multiple appendix items
                        if (isset($app->appendix) && is_array($app->appendix)) {
                            //WE HAVE MULTIPLE APPENDICES RETURNED
                            if (isset($app->appendix)) {
                                foreach ($app->appendix as $ak => $av) {
                                    $role = (str_replace(' ', '', $av->formType) == 'Appendix1') ? 'P' : 'S';

                                    $decRequired = $this->fnGetDeclaration($av->applicationNumber, $role);
                                    $tmp = [];
                                    $id = $av->applicationNumber;
                                    $tmp['ApplicationNumber'] = $av->applicationNumber;
                                    $tmp['FormGUID'] = (isset($av->formGUID)) ? $av->formGUID : null;
                                    $tmp['StudyStartDate'] = (! empty($av->studyStartDate)) ? date('M d, Y', strtotime($av->studyStartDate)) : '-';
                                    $tmp['StudyEndDate'] = (! empty($av->studyEndDate)) ? date('M d, Y', strtotime($av->studyEndDate)) : '-';
                                    $tmp['InkSigRequired'] = ($decRequired == 'Y') ? 'Y' : null;
                                    $tmp['AppendixReminder'] = $av->reminderFlag;
                                    $results[str_replace(' ', '', $av->formType)][$k][$id][] = $tmp;
                                    $results[str_replace(' ', '', $av->formType)]['total'] = (int) $results[str_replace(' ', '', $av->formType)]['total'] + 1;
                                }
                            } else { //SINGLE RECORD RETURNED

                                $role = (str_replace(' ', '', $app->appendix->formType) == 'Appendix1') ? 'P' : 'S';

                                $decRequired = $this->fnGetDeclaration($app->appendix->applicationNumber, $role);

                                $tmp = [];
                                $id = $app->appendix->applicationNumber;
                                $tmp['ApplicationNumber'] = $app->appendix->applicationNumber;
                                $tmp['FormGUID'] = (isset($app->appendix->formGUID)) ? $app->appendix->formGUID : null;
                                $tmp['StudyStartDate'] = (! empty($app->appendix->studyStartDate)) ? date('M d, Y', strtotime($app->appendix->studyStartDate)) : '-';
                                $tmp['StudyEndDate'] = (! empty($app->appendix->studyEndDate)) ? date('M d, Y', strtotime($app->appendix->studyEndDate)) : '-';
                                $tmp['InkSigRequired'] = ($decRequired == 'Y') ? 'Y' : null;
                                //$tmp['InkSigRequired'] = NULL;
                                $tmp['AppendixReminder'] = $app->appendix->reminderFlag;
                                $results[str_replace(' ', '', $app->appendix->formType)][$k][$id][] = $tmp;
                                $results[str_replace(' ', '', $app->appendix->formType)]['total'] = (int) $results[str_replace(' ', '', $app->appendix->formType)]['total'] + 1;
                            }
                        } else {
                            if (isset($app->appendix) && is_object($app->appendix) && isset($app->appendix->applicationNumber, $app->appendix->appendixTimeline)) {
                                $role = (str_replace(' ', '', $app->appendix->formType) == 'Appendix1') ? 'P' : 'S';

                                $decRequired = $this->fnGetDeclaration($app->appendix->applicationNumber, $role);

                                $tmp = [];
                                $id = $app->appendix->applicationNumber;
                                $tmp['ApplicationNumber'] = $app->appendix->applicationNumber;
                                $tmp['FormGUID'] = (isset($app->appendix->formGUID)) ? $app->appendix->formGUID : null;
                                $tmp['StudyStartDate'] = (! empty($app->appendix->studyStartDate)) ? date('M d, Y', strtotime($app->appendix->studyStartDate)) : '-';
                                $tmp['StudyEndDate'] = (! empty($app->appendix->studyEndDate)) ? date('M d, Y', strtotime($app->appendix->studyEndDate)) : '-';
                                $tmp['InkSigRequired'] = ($decRequired == 'Y') ? 'Y' : null;
                                $tmp['AppendixReminder'] = $app->appendix->reminderFlag;
                                $results[str_replace(' ', '', $app->appendix->formType)][$k][$id][] = $tmp;
                                $results[str_replace(' ', '', $app->appendix->formType)]['total'] = (int) $results[str_replace(' ', '', $app->appendix->formType)]['total'] + 1;
                            }
                        }
                    }

                    $results['listType'] = 'Appendix';

                    if (isset($results['Appendix1']['InTransition']) && count($results['Appendix1']['InTransition']) > 0) {
                        foreach ($results['Appendix1']['InTransition'] as $k => $v) {
                            $results['Appendix1']['Submitted'][$k] = $v;
                        }
                    }

                    if (isset($results['Appendix2']['InTransition']) && count($results['Appendix2']['InTransition']) > 0) {
                        foreach ($results['Appendix2']['InTransition'] as $k => $v) {
                            $results['Appendix2']['Submitted'][$k] = $v;
                        }
                    }

                    return $results;
                } else {
                    //CHECK TO SEE IF THIS IS AN APPENDIX ERROR
                    if (isset($appendix->detail->SFASFault)) {
                        drupal_set_message($appendix->getMessage(), 'error');
                    } elseif (isset($appendix->detail->ApplicationFault->faultCode)) {
                        drupal_set_message($appendix->getMessage(), 'error');
                        form_set_error($errorMappings[$appendix->detail->ApplicationFault->faultCode], $appendix->getMessage());
                    } else {
                        //CHECK TO SEE IF THIS IS AN APPLICATION DETAILS ERROR
                        $this->fnError('SYSTEM ERROR :: USER_APPLICATION_DEFAULT -> getAppendixList', $appendix->getMessage(), $appendix, $triggerDefault = true);
                    }
                }
            }
        } else {
            //CHECK TO MAKE SURE WE GET A RESPONSE
            if (! empty($appendix) && is_object($appendix)) {
                //CHECK TO MAKE SURE WE DON'T HAVE ERRORS
                if (! isset($appendix->faultcode) && isset($appendix->AppendixLists)) {
                    return $appendix;
                } else {
                    //CHECK TO SEE IF THIS IS A LOGIN ERROR
                    if (isset($appendix->detail->SFASFault)) {
                        drupal_set_message($appendix->getMessage(), 'error');
                    } else {
                        //CHECK TO SEE IF THIS IS AN APPLICATION DETAILS ERROR
                        $this->fnError('SYSTEM ERROR :: USER_APPLICATION_DEFAULT -> getAppendixList', $appendix->getMessage(), $appendix, $triggerDefault = true);
                    }
                }
            }
        }
    }

    /*
        fnGetApplications: get all applications for a student
        @params:
            - $checkIsDecRequired: true by default and is used to check if a web dec is required for student
            - $appID: if we want to get a specific application. Null by default to return all applications
    */
    public function fnGetApplications($checkIsDecRequired = true, $appID = null)
    {
        if (is_null($this->uid)) {
            $this->uid = $this->fnDecrypt(Auth::user()->name);
        }

        if (! is_null(session()->get(env('ADMIN_SESSION_VAR')))) {
            $this->uid = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));
        }

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnGetApplications() this->uid: '.json_encode($this->uid));
        }
        //CALL THE APPROPRIATE WEB SERVICE
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

        $apps = $this->fnRequest('getApplicationList', ['userGUID' => $this->uid], null);

        if (! isset($apps->userApplicationList->inTransition->application)) {
            // cache application list only if there is none in transition because applications in transition will have updates when it is loaded to SFAS.
            //$this->fnSetCache($cid, 14400, $apps);
        }

        $a = [];
        $a['totalApps'] = 0;
        $a['listType'] = 'Applications';

        //WE HAVE APPLICATIONS SO LOOP THROUGH AND RETRIEVE THEM
        if (! isset($apps->Errors)) {
            //NOT SUBMITTED
            if (isset($apps->userApplicationList->draft->application)) {
                if (is_array($apps->userApplicationList->draft->application)) {
                    foreach ($apps->userApplicationList->draft->application as $application) {
                        $tmp = [];

                        //MAKE SURE APPLICATION RETURN IS NOT EMPTY
                        if (! empty($application) && isset($application->applicationNumber)) {
                            $k = $application->applicationNumber;

                            $startDate = (! empty($application->studyStartDate)) ? date('M d, Y', strtotime($application->studyStartDate)) : '-';
                            $endDate = (! empty($application->studyEndDate)) ? date('M d, Y', strtotime($application->studyEndDate)) : '-';

                            $tmp['ApplicationNumber'] = $application->applicationNumber;
                            $tmp['StudyStartDate'] = $startDate;
                            $tmp['StudyEndDate'] = $endDate;
                            $tmp['InkSigRequired'] = null;

                            if (! empty($appID) && $application->applicationNumber == $appID) {
                                unset($a); //remove all previous entries into this array
                                $a['totalApps'] = 0;
                            }

                            $a[$k] = isset($application->decStatus) ? $application->decStatus : null;
                            $a['NotSubmitted'][$k][] = $tmp;

                            $a['totalApps'] = $a['totalApps'] + 1;

                            if (! empty($appID) && $application->applicationNumber == $appID) {
                                return $a;
                            }
                        }
                    }
                } else {
                    if (! empty($apps->userApplicationList->draft->application->applicationNumber)) {
                        $tmp = [];

                        $k = $apps->userApplicationList->draft->application->applicationNumber;
                        $startDate = (! empty($apps->userApplicationList->draft->application->studyStartDate)) ? date('M d, Y', strtotime($apps->userApplicationList->draft->application->studyStartDate)) : '-';
                        $endDate = (! empty($apps->userApplicationList->draft->application->studyEndDate)) ? date('M d, Y', strtotime($apps->userApplicationList->draft->application->studyEndDate)) : '-';

                        $tmp['ApplicationNumber'] = $apps->userApplicationList->draft->application->applicationNumber;
                        $tmp['StudyStartDate'] = $startDate;
                        $tmp['StudyEndDate'] = $endDate;
                        $tmp['InkSigRequired'] = null;

                        if (! empty($appID) && $apps->userApplicationList->draft->application->applicationNumber == $appID) {
                            unset($a); //remove all previous entries into this array
                            $a['totalApps'] = 0;
                        }

                        $a[$k] = isset($apps->userApplicationList->draft->application->decStatus) ? $apps->userApplicationList->draft->application->decStatus : null;
                        $a['NotSubmitted'][$k][] = $tmp;

                        $a['totalApps'] = $a['totalApps'] + 1;

                        if (! empty($appID) && $apps->userApplicationList->draft->application->applicationNumber == $appID) {
                            return $a;
                        }
                    }
                }

                if (is_array($a['NotSubmitted'])) {
                    krsort($a['NotSubmitted']);
                }
            }

            //IN TRANSITION
            if (isset($apps->userApplicationList->inTransition->application)) {
                if (is_array($apps->userApplicationList->inTransition->application)) {
                    foreach ($apps->userApplicationList->inTransition->application as $application) {
                        $tmp = [];

                        //MAKE SURE APPLICATION RETURN IS NOT EMPTY
                        if (! empty($application) && isset($application->applicationNumber)) {
                            $k = $application->applicationNumber;

                            if (! empty($checkIsDecRequired)) {
                                $decRequired = $this->fnIsWebDeclarationInkSignatureRequired($k, 'A');
                            } else {
                                $decRequired = null;
                            }

                            $startDate = (! empty($application->studyStartDate)) ? date('M d, Y', strtotime($application->studyStartDate)) : '-';
                            $endDate = (! empty($application->studyEndDate)) ? date('M d, Y', strtotime($application->studyEndDate)) : '-';

                            $tmp['ApplicationNumber'] = $application->applicationNumber;
                            $tmp['StudyStartDate'] = $startDate;
                            $tmp['StudyEndDate'] = $endDate;
                            $tmp['InkSigRequired'] = ($decRequired == 'Y') ? 'Y' : null;

                            if (! empty($appID) && $application->applicationNumber == $appID) {
                                unset($a); //remove all previous entries into this array
                                $a['totalApps'] = 0;
                            }

                            $a[$k] = isset($application->decStatus) ? $application->decStatus : null;
                            $a['Submitted'][$k][] = $tmp;

                            $a['totalApps'] = $a['totalApps'] + 1;

                            if (! empty($appID) && $application->applicationNumber == $appID) {
                                return $a;
                            }
                        }
                    }
                } else {
                    if (! empty($apps->userApplicationList->inTransition->application->applicationNumber)) {
                        $tmp = [];

                        $k = $apps->userApplicationList->inTransition->application->applicationNumber;

                        if (! empty($checkIsDecRequired)) {
                            $decRequired = $this->fnIsWebDeclarationInkSignatureRequired($k, 'A');
                        } else {
                            $decRequired = null;
                        }

                        $startDate = (! empty($apps->userApplicationList->inTransition->application->studyStartDate)) ? date('M d, Y', strtotime($apps->userApplicationList->inTransition->application->studyStartDate)) : '-';
                        $endDate = (! empty($apps->userApplicationList->inTransition->application->studyEndDate)) ? date('M d, Y', strtotime($apps->userApplicationList->inTransition->application->studyEndDate)) : '-';

                        $tmp['ApplicationNumber'] = $apps->userApplicationList->inTransition->application->applicationNumber;
                        $tmp['StudyStartDate'] = $startDate;
                        $tmp['StudyEndDate'] = $endDate;
                        $tmp['InTransition'] = true;
                        $tmp['InkSigRequired'] = ($decRequired == 'Y') ? 'Y' : null;

                        if (! empty($appID) && $apps->userApplicationList->inTransition->application->applicationNumber == $appID) {
                            unset($a); //remove all previous entries into this array
                            $a['totalApps'] = 0;
                        }

                        $a[$k] = isset($apps->userApplicationList->inTransition->application->decStatus) ? $apps->userApplicationList->inTransition->application->decStatus : null;
                        $a['Submitted'][$k][] = $tmp;

                        $a['totalApps'] = $a['totalApps'] + 1;

                        if (! empty($appID) && $apps->userApplicationList->inTransition->application->applicationNumber == $appID) {
                            return $a;
                        }
                    }
                }

                if (is_array($a['Submitted'])) {
                    krsort($a['Submitted']);
                }
            }

            //SUBMITTED
            if (isset($apps->userApplicationList->submitted->application)) {
                if (is_array($apps->userApplicationList->submitted->application)) {
                    foreach ($apps->userApplicationList->submitted->application as $application) {
                        $tmp = [];

                        //MAKE SURE APPLICATION RETURN IS NOT EMPTY
                        if (! empty($application) && isset($application->applicationNumber)) {
                            $k = $application->applicationNumber;

                            if (! empty($checkIsDecRequired)) {
                                $decRequired = $this->requireDec($application->applicationNumber, 'A', $application->decStatus);
                            } else {
                                $decRequired = null;
                            }

                            $startDate = (! empty($application->studyStartDate)) ? date('M d, Y', strtotime($application->studyStartDate)) : '-';
                            $endDate = (! empty($application->studyEndDate)) ? date('M d, Y', strtotime($application->studyEndDate)) : '-';

                            $tmp['ApplicationNumber'] = $application->applicationNumber;
                            $tmp['StudyStartDate'] = $startDate;
                            $tmp['StudyEndDate'] = $endDate;
                            $tmp['InkSigRequired'] = ($decRequired == 't') ? 'Y' : null;

                            if (! empty($appID) && $application->applicationNumber == $appID) {
                                unset($a); //remove all previous entries into this array
                                $a['totalApps'] = 0;
                            }

                            $a[$k] = isset($application->decStatus) ? $application->decStatus : null;
                            $a['Submitted'][$k][] = $tmp;

                            $a['totalApps'] = $a['totalApps'] + 1;

                            if (! empty($appID) && $application->applicationNumber == $appID) {
                                return $a;
                            }
                        }
                    }
                } else {
                    if (! empty($apps->userApplicationList->submitted->application->applicationNumber)) {
                        $tmp = [];

                        $k = $apps->userApplicationList->submitted->application->applicationNumber;

                        if (! empty($checkIsDecRequired)) {
                            $decRequired = $this->requireDec($k, 'A', $apps->userApplicationList->submitted->application->decStatus);
                        } else {
                            $decRequired = null;
                        }

                        $startDate = (! empty($apps->userApplicationList->submitted->application->studyStartDate)) ? date('M d, Y', strtotime($apps->userApplicationList->submitted->application->studyStartDate)) : '-';
                        $endDate = (! empty($apps->userApplicationList->submitted->application->studyEndDate)) ? date('M d, Y', strtotime($apps->userApplicationList->submitted->application->studyEndDate)) : '-';

                        $tmp['ApplicationNumber'] = $apps->userApplicationList->submitted->application->applicationNumber;
                        $tmp['StudyStartDate'] = $startDate;
                        $tmp['StudyEndDate'] = $endDate;
                        $tmp['InkSigRequired'] = ($decRequired == 't') ? 'Y' : null;

                        if (! empty($appID) && $apps->userApplicationList->submitted->application->applicationNumber == $appID) {
                            unset($a); //remove all previous entries into this array
                            $a['totalApps'] = 0;
                        }

                        $a[$k] = isset($apps->userApplicationList->submitted->application->decStatus) ? $apps->userApplicationList->submitted->application->decStatus : null;
                        $a['Submitted'][$k][] = $tmp;

                        $a['totalApps'] = $a['totalApps'] + 1;

                        if (! empty($appID) && $apps->userApplicationList->submitted->application->applicationNumber == $appID) {
                            return $a;
                        }
                    }
                }

                if (is_array($a['Submitted'])) {
                    krsort($a['Submitted']);
                }
            }

            return $a;
        } else {
            //LOOP THROUGH ERRORS
        }
    }

    /*
        fnGetApplicationDetails: calls ws to get application details
        @params: $appID: application number
        @return: (object) $appDetails or NULL if there are errors
    */
    public function fnGetApplicationDetails($appID)
    {
        $this->uid = $this->fnDecrypt(Auth::user()->name);

        //GET APPROPRIATE URL FOR WEB SERVICE METHOD
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

        $appDetails = false; //disable cache

        if ($appDetails == false) {
            $appDetails = $this->fnRequest('getApplicationDetails', ['userGUID' => $this->uid, 'applicationNumber' => $appID], null);

            if ($appDetails->applicationDetails->applicationStatus != 'SUBMPROC' && $appDetails->applicationDetails->applicationStatus != 'INPR') {
                // cache application details if it's not SUBMPROC or INPR
                //$this->fnSetCache($cid, 14400, $appDetails);
            }
        }

        //CHECK TO MAKE SURE WE GET A RESPONSE
        if (! empty($appDetails) && is_object($appDetails)) {
            //CHECK TO MAKE SURE WE DON'T HAVE ERRORS
            if (! isset($appDetails->faultcode) && isset($appDetails->applicationDetails)) {
                return $appDetails;
            } else {
                //CHECK TO SEE IF THIS IS A LOGIN ERROR
                if (isset($appDetails->detail->ApplicationFault)) {
                    //drupal_set_message($appDetails->getMessage(), 'error');
                } else {
                    //CHECK TO SEE IF THIS IS AN APPLICATION DETAILS ERROR
                    $this->fnError('SYSTEM ERROR :: USER_APPLICATION_DEFAULT -> getApplicationDetails', $appDetails->getMessage(), $appDetails, $triggerDefault = true);
                }
            }
        }
    }


    /*
    * requireDec
    * @params: appID: NULL or application number, $role = (A,S or P), $a: NULL or applications events status
    * @return:
    *		- t : means we do require a web dec
    *		- f1/f2 : means we do NOT require a web dec. f1 indicates signature received f2 indicates signature not required
    *		- e : means web dec is expired and no longer available for application
    */
    public function requireDec($appID, $role, $a = null)
    {
        $this->uid = $this->fnDecrypt(Auth::user()->name);

        //DETERMINE DECLARATION STATUS
        $wd = $this->fnGetDeclaration($appID, $role);

        //missing/waiting signature statuses
        $s = [
            'waiting for applicant\'s declaration',
            'missing applicant signature',
            'waiting for spouse\'s declaration',
            'missing spouse signature',
            'waiting for parent(s)\'s declaration',
            'missing parent(s) signature',
        ];

        /*
            recieved on file signature statuses
        */
        $s2 = [
            'your signed declaration has been received.',
            'your signed declaration has been received',
            'your spouse\'s signed declaration has been received',
            'spouse\'s signed declaration has been received',
            'your parent(s)\'s signed declartion has been received',
            'parent(s)\'s signed declartion has been received',
            'parent(s) signed declaration has been received',
            'valid declaration on file for applicant',
            'valid declaration on file for spouse',
            'valid declaration on file for parent',
            'valid declaration on file for parent(s)',
        ];

        // WEBS-50 returning false if fnGetDeclaration returns a fault code
        if (! is_array($wd)) {
            return false;
        } //DON'T LOOP IN HERE AND VALIDATE IF PROGRAM YEAR IS < 2012/2013
        elseif (isset($wd['py']) && str_replace('/', '', $wd['py']) >= '20122013') {
            //CHECK REQUIREMENT FOR A LIST OF APPLICATIONS :: IN THE MAIN DASHBOARD VIEW BASED STATUS PASSED TO US
            if (! empty($a)) {
                //web declaration signature required
                if (in_array(strtolower($a), $s)) {
                    return 't';
                } else {
                    if (! empty($a)) {
                        //MAKE SURE ONE OF THOSE STATUSES ARE IN OUR ARRAY - NOW CHECKING FOR IF WE HAVE STILL SHOW DECLARATION BECAUSE WE HAVE A VALID ONE
                        if (in_array(strtolower($a), $s2)) {
                            //GET PROGRAM YEARS
                            $progYr = $this->fnGetProgramYear();

                            //IF OUR APPLICATION PROGRAM YEAR MATCHES ANY OF THE FOLLOWING CURRENT PROGRAM YEARS RETURN FALSE
                            if (str_replace('/', '', $wd['py']) == $progYr[0]['programYear'] || str_replace('/', '', $wd['py']) == $progYr[1]['programYear']) {
                                //INDICATES SIGNATURE RECEIVED
                                if (stripos($a, 'received') !== false) {
                                    return 'f1'; //FALSE INDICATES THAT DECLARATION IS NOT REQUIRED BUT CAN BE VIEWED FOR VIEWING
                                } elseif (stripos($a, 'valid')) {
                                    return 'f2'; //INDICATES SIGNATURE NO LONGER REQUIRED
                                } else {
                                    return 'f';
                                }
                            } else {
                                return 'e'; //"e" INDICATES THAT WEB DEC IS EXPIRED AND NO LONGER NEEDED
                            }
                        }
                    } else {
                        return false;
                    }
                }
            }
        } else {
            //PROGRAM YEAR WAS LESS THAN 2012/13 IMPLEMENTATION DATE SO SET DECLARATION TO EXPIRED
            if (! empty($wd) && ! empty($a) && in_array(strtolower($a), $s)) {
                return 'e';
            } else { //catastrophic error happened we weren't able to determine web dec status so have them sign one so it doesn't slow the process down
                return 't';
            }
        }
    }

    /*
    *	function fnIsWebDeclarationInkSignatureRequired
    * 	@params:
    *		$appNumber: the application number that we are verifying for
    * 	$role: A (Applicant), S (Spouse), P (Parent) -- Defaults to "A"
    */
    public function fnIsWebDeclarationInkSignatureRequired($appNumber, $role = 'A')
    {
        $this->uid = $this->fnDecrypt(Auth::user()->name);

        // no SIN accounts always require a declaration
        if ($role == 'NS' || $role == 'NP') {
            $webDecStatus = new \stdClass();
            $webDecStatus->inkSignatureRequired = 'Y';

            return $webDecStatus;
        }

        //GET APPROPRIATE URL FOR WEB SERVICE METHOD
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

        //THE NAME OF OUR CACHE FILE
        $cid = 'web_dec_status'.$this->uid.''.$appNumber;

        //REQUEST PARAMS
        $params = ['applicationNumber' => $appNumber, 'userGUID' => $this->uid, 'role' => $role];

        $webDecStatus = $this->fnRequest('isWebDeclarationInkSignatureRequired', $params, $cid, 100);

        //CHECK TO MAKE SURE WE GET A RESPONSE
        if (! empty($webDecStatus) && is_object($webDecStatus)) {
            //CHECK TO MAKE SURE WE DON'T HAVE ERRORS
            if (! isset($webDecStatus->faultcode) && isset($webDecStatus->inkSignatureRequired)) {
                return $webDecStatus;
            } else {
                //CHECK TO SEE IF THIS IS A WEB DEC ERROR ERROR
//                if (isset($webDecStatus->detail->ApplicationFault)) {
//                    drupal_set_message($webDecStatus->getMessage(), 'error');
//                } else {
//                    //CHECK TO SEE IF THIS IS AN WEB DEC ERROR
//                    $this->fnError('SYSTEM ERROR :: USER_APPLICATION_DEFAULT -> isWebDeclarationInkSignatureRequired', $webDecStatus->getMessage(), $params, $triggerDefault = true);
//                }
            }
        }

        return null;
    }

    /*
    *		GET CURRENT PROGRAM YEARS
    *		@params:void
    *		@return: (object) or void if there are errors
    */
    public function fnGetProgramYear()
    {
        $aeit = new Aeit();
        //CALL REST API FOR PROGRAM YEAR
        $url = $aeit->fnWS('LC', 'PROG_YEAR');


        $rq = $aeit->fnGetCurlRequest($url, false, 'fn_program_years', 302400);

        //MAKE SURE WE GOT A VALUE (not empty or not null)
        if (! empty($rq) && $rq != false) {
            return $this->fnParseProgramYears($rq);
        } else {
            Log::error('fnGetProgramYear call failed for URL: '.$url);

            $backup_yr = config_path().env('ACTIVE_PY_FILE');

            if (file_exists($backup_yr)) {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnGetProgramYear() fetch from backup file');
                }

                $xml = file_get_contents($backup_yr);
                if (empty($xml)) {
                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': fnGetProgramYear() backup file empty');
                    }
                    $errors['status'] = false;
                    $errors['msg'] = 'Invalid response received';

                    return $errors;
                } else {
                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': fnGetProgramYear() backup file response: '.json_encode($xml));
                    }
                    $rq = [];
                    $rq['response'] = $xml;
                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': fnGetProgramYear() backup file response: '.json_encode($this->fnParseProgramYears($rq)));
                    }

                    return $this->fnParseProgramYears($rq);
                }
            } else {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnGetProgramYear() fetch from backup failed');
                }

                // make the empty file
                $filepath = fopen($backup_yr, 'w');
                fclose($filepath);
                $errors['status'] = false;
                $errors['msg'] = 'Invalid response received';

                return $errors;
            }
        }
    }

    public function fnParseProgramYears($rq)
    {
        $xml = simplexml_load_string($rq['response']);
        $json = json_encode($xml);
        $rq = json_decode($json);

        if (isset($rq->ActiveProgramYears)) {
            $progYears = [];
            $i = 0;

            foreach ($rq->ActiveProgramYears->ActiveProgramYears->ProgramYear as $k => $v) {
                $progYears[$i]['programYear'] = ''.$v->PROGRAM_YEAR.'';
                $progYears[$i]['programYearFormatted'] = ''.substr($v->PROGRAM_YEAR, 0, 4).'/'.substr($v->PROGRAM_YEAR, 6, 2);
                $progYears[$i]['startDate'] = date('F d, Y', strtotime($v->PROGRAM_START_DTE));
                $progYears[$i]['endDate'] = date('F d, Y', strtotime($v->PROGRAM_END_DTE));
                $progYears[$i]['thisYear'] = date('Y', strtotime($v->PROGRAM_END_DTE));
                $progYears[$i]['lastYear'] = date('Y', strtotime($v->PROGRAM_START_DTE));
                $i++;
            }

            $progYears = array_reverse($progYears);

            if (! empty($progYears) && count($progYears) > 0) {
                $progYears['status'] = true;

                return $progYears;
            } else {
                $errors['status'] = false;
                $errors['msg'] = 'Invalid response received';

                return $errors;
            }
        }
    }

    public function fnGetSchools($designationType = 'All', $additionalDetails = 'false', $accreditation = 'false')
    {
        //CALL THE APPROPRIATE WEB SERVICE
        $this->WSDL = $this->fnWS('WS-HOSTS', 'GET_SCHOOLS');
        $ws = $this->fnRequest('getSchoolList', [
            'designationType' => $designationType,
            'schoolName' => '',
            'schoolIDX' => '',
            'schoolCode' => '',
            'city' => '',
            'provinceCode' => '',
            'countryCode' => '',
            'countryProvCode' => ''], 'get_schools_list_'.$designationType.'', 3600);

        //MAKE SURE WE HAVE SCHOOLS
        if (isset($ws->SchoolList)) {
            return $ws;
        } else {
            return null;
        }
    }

    public function fnGetSchoolDetails($schoolIDX)
    {
        //CALL THE APPROPRIATE WEB SERVICE
        $this->WSDL = $this->fnWS('WS-HOSTS', 'GET_SCHOOLS');

        $ws = $this->fnRequest('getSchoolDetails', ['schoolIDX' => $schoolIDX], 'get_school_details_'.$schoolIDX.'', 300);

        //MAKE SURE RESPONSE IS VALID
        if (isset($ws->schoolDetails)) {
            if (is_array($ws->schoolDetails)) {
                foreach ($ws->schoolDetails as $school) {
                    if ($school->DesignationStatus == 'Under Review') {
                        $ws->schoolDetails->DesignationStatusDescript = 'This school is currently being reviewed by the Ministry of Post-Secondary Education and Future Skills. The results of the review will determine whether this school will continue to be eligible for designation.';
                    }

                    if ($school->DesignationStatus == 'Pending') {
                        $ws->schoolDetails->DesignationStatusDescript = 'An application has been submitted by this post-secondary institution and a decision is still pending. You must wait until the school is designated before you can apply.';
                    }

                    if ($school->DesignationStatus == 'Denied') {
                        $school->DesignationStatus = 'Does Not Meet Criteria';
                        $ws->schoolDetails->DesignationStatusDescript = 'This post-secondary institution does not meet the criteria to administer the StudentAid BC program.';
                    }

                    if ($school->DesignationStatus == 'Designated') {
                        $ws->schoolDetails->DesignationStatusDescript = 'This post-secondary institution meets the criteria to administer the StudentAid BC program.';
                    }
                }
            } else {
                if ($ws->schoolDetails->DesignationStatus == 'Under Review') {
                    $ws->schoolDetails->DesignationStatusDescript = 'This school is currently being reviewed by the Ministry of Post-Secondary Education and Future Skills. The results of the review will determine whether this school will continue to be eligible for designation.';
                }

                if ($ws->schoolDetails->DesignationStatus == 'Pending') {
                    $ws->schoolDetails->DesignationStatusDescript = 'An application has been submitted by this post-secondary institution and a decision is still pending. You must wait until the school is designated before you can apply.';
                }

                if ($ws->schoolDetails->DesignationStatus == 'Denied') {
                    $ws->schoolDetails->DesignationStatus = 'Does Not Meet Criteria';
                    $ws->schoolDetails->DesignationStatusDescript = 'This post-secondary institution does not meet the criteria to administer the StudentAid BC program.';
                }

                if ($ws->schoolDetails->DesignationStatus == 'Designated') {
                    $ws->schoolDetails->DesignationStatusDescript = 'This post-secondary institution meets the criteria to administer the StudentAid BC program.';
                }
            }

            return $ws;
        } else {
            return false;
        }
    }

    /*
    *	function fnApply
    * 	@params:
    *	$programYear: year student will be attending school
    * 	$institutionID: institution IDX to lookup school code
    * 	$pdf: flag whether to return a pdf form or html5 form
    */

    public function fnApply($programYear, $institutionID, $pdf, $application_type = 'full-time')
    {
        $this->uid = session(env('GUID_SESSION_VAR'));

        $this->WSDL = $this->fnWS('WS-HOSTS', 'GET_SCHOOLS');
        $school = $this->fnRequest('getSchoolDetails', ['schoolIDX' => $institutionID], 'get_school_details_'.$institutionID.'', 300);

        $onlineStatus = ($school->schoolDetails->OnlineStatus == 'Y' ? true : false);

        $lcVersion = '1.0';
        if (strlen($programYear) == 8) {
            $lcVersion = substr_replace($programYear, '.', 4, 0);
            $lcVersion = $lcVersion;
        }

        $startYear = substr($programYear, 0, 4);

        $baseURL = $this->fnWS('LC', '');
        $submitURL = $this->fnWS('LC-SUBMIT', '');
        $dataRefURL = $this->fnWS('LC-DATAREF', '');

        //CLEAR CACHE FOR GET APPLICATION LIST

        //validate program year
        $validPY = $this->fnValidateProgramYear($programYear);
        //validate school code
        $validInst = $this->fnValidateSchoolCode($institutionID);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnApply() uid: '.json_encode($this->uid));
            session()->push('DEBUG', now().': fnApply() pdf: '.json_encode($pdf));
            session()->push('DEBUG', now().': fnApply() school: '.json_encode($school));
            session()->push('DEBUG', now().': fnApply() startYear: '.json_encode($startYear));
            session()->push('DEBUG', now().': fnApply() baseURL: '.json_encode($baseURL));
            session()->push('DEBUG', now().': fnApply() submitURL: '.json_encode($submitURL));
            session()->push('DEBUG', now().': fnApply() dataRefURL: '.json_encode($dataRefURL));
            session()->push('DEBUG', now().': fnApply() validPY: '.json_encode($validPY));
            session()->push('DEBUG', now().': fnApply() validInst: '.json_encode($validInst));
        }

        if ($validPY && $validInst) {
            //get base urls from config file
            $url = $this->fnWS('LC', 'APPLICATION_FORM_REST');
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnApply() url: '.json_encode($url));
            }

            $userDetails = ['ownerGUID' => $this->uid,
                'programYear' => str_replace(' ', '', $programYear),
                'institutionID' => $validInst];

            if ($startYear >= 2016) {
                $userDetails['partnerPortalIntegrated'] = $onlineStatus;
            }
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnApply() startYear: '.json_encode($startYear));
            }

            //Build and encode the lifecycle url based on defined parameters
            $htmlURL = $this->fnBuildLiveCycleUrl($url, [
                'type' => '.html',
                'livecycleDetails' => [
                    'contentRoot' => 'repository:///Applications/SABC_StudentLoan/'.$lcVersion.'/Forms',
                    'template' => 'StudentLoanApplication.xdp',
                    'submitUrl' => $submitURL.'/rest/services/SABC_StudentLoan/Services/Application/Intake/NewHTML:'.$lcVersion,
                    'dataRef' => $dataRefURL.'/rest/services/SABC_StudentLoan/Services/Application/Fill/New:'.$lcVersion],
                'userDetails' => $userDetails,
            ]);
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnApply() htmlURL: '.json_encode($htmlURL));
            }

            $pdfURL = $baseURL.'/rest/services/SABC_StudentLoan/Services/Application/FillAndSubmit/New:'.$lcVersion.'?'.http_build_query($userDetails);
            $arg = ['dashboard', 'apply', 'application', 'full-time'];
            //tells us if it is full-time or part-time
            $applicationType = $arg[3];
            if ($pdf) {
                $rq = '<div class="arow taskbar '.$arg[1].' dashboard-apply-taskbar">';
                $rq .= '<div class="inner inner-apply">';
                $rq .= '  <div class="paddingsm" style="margin-top: 37px;"class="uppercase short text-warning"><i class="icon-alert"></i>
									&nbsp;&nbsp;Problem viewing application?&nbsp;&nbsp;Click here for
									<a href="/help-centre/loan-application-instructions/minimum-system-requirements" target="_blank">minimum requirements</a>.</div>';
                $rq .= '<a href="/dashboard/apply/application/'.$applicationType.'/'.$institutionID.'/'.$programYear.'/html" class="right btn btn-primary">Open as HTML form</a>';
                $rq .= '</div>';
                $rq .= '</div>';

                //return embed link for PDF if ie8 or less
                $rq .= '<embed src="'.$pdfURL.'" type="application/pdf" wmode="opaque" width="100%" height="100%" ><param name="wmode" value="opaque"></embed>';
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnApply() is PDF');
                }

                return $rq;
            } else {
                $call = $this->fnGetCurlRequest($htmlURL);

                if (! empty($call['response'])) {
                    //return HTML5 form for any modern browser
                    $rq = '<div class="arow taskbar '.$arg[1].' dashboard-apply-taskbar">';
                    $rq .= '<div class="inner inner-apply">';
                    $rq .= '  <div class="paddingsm" style="margin-top: 37px;"class="uppercase short text-warning"><i class="icon-alert"></i>
										&nbsp;&nbsp;Problem viewing application?&nbsp;&nbsp;Click here for
										<a href="/help-centre/loan-application-instructions/minimum-system-requirements" target="_blank">minimum requirements</a>.</div>';
                    $rq .= '<a href="/dashboard/apply/application/'.$applicationType.'/'.$institutionID.'/'.$programYear.'/pdf"  class="right btn btn-primary">Open as PDF</a>';
                    $rq .= '</div>';
                    $rq .= '</div>';
                    $rq .= $call['response'];


                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': fnApply() rq: '.strip_tags(json_encode($rq)));
                    }

                    return $rq;
                } else {
                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': fnApply() call response is empty');
                    }
                }
            }
        }
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnApply() validPY && validInst: not valid');
        }

    }

    public function fnUpdateApplication($documentGUID, $programYear, $pdf)
    {
        $this->uid = session(env('GUID_SESSION_VAR'));

        // Check if the user is verified here.
        $userProfile = new User();
        $isUserVerified = $userProfile->fnVerifyUser();
        $verificationMethod = $userProfile->fnVerificationMethod();

        if ($isUserVerified != true && $isUserVerified != 1) {
            return redirect('/student-loans/verification/'.$verificationMethod);
        } else {

            $lcVersion = '1.0';
            if (strlen($programYear) == 8) {
                $lcVersion = substr_replace($programYear, '.', 4, 0);
                $lcVersion = $lcVersion;
            }
            $baseURL = $this->fnWS('LC', '');
            $submitURL = $this->fnWS('LC-SUBMIT', '');
            $dataRefURL = $this->fnWS('LC-DATAREF', '');

            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnUpdateApplication() uid: '.json_encode($this->uid));
                session()->push('DEBUG', now().': fnUpdateApplication() pdf: '.json_encode($pdf));
                session()->push('DEBUG', now().': fnUpdateApplication() baseURL: '.json_encode($baseURL));
                session()->push('DEBUG', now().': fnUpdateApplication() submitURL: '.json_encode($submitURL));
                session()->push('DEBUG', now().': fnUpdateApplication() dataRefURL: '.json_encode($dataRefURL));
                //session()->push('DEBUG', now() . ": fnUpdateApplication() validPY: " . json_encode($validPY));
            }

            //get base urls from config file
            $url = $this->fnWS('LC', 'APPLICATION_FORM_REST');
            $userDetails = ['ownerGUID' => $this->uid, 'documentGUID' => $documentGUID];
            $pdfURL = $baseURL.'/rest/services/SABC_StudentLoan/Services/Application/FillAndSubmit/Update:'.$lcVersion.'?'.http_build_query($userDetails);

            $lcDetails = [
                'contentRoot' => 'repository:///Applications/SABC_StudentLoan/'.$lcVersion.'/Forms',
                'template' => 'StudentLoanApplication.xdp',
                'submitUrl' => $submitURL.'/rest/services/SABC_StudentLoan/Services/Application/Intake/UpdateHTML:'.$lcVersion,
                'dataRef' => $dataRefURL.'/rest/services/SABC_StudentLoan/Services/Application/Fill/Update:'.$lcVersion,
            ];

            if (session()->exists('read-only')) {
                $lcDetails['template'] = 'StudentLoanApplication-ReadOnly.xdp';
            }

            $htmlURL = $this->fnBuildLiveCycleUrl($url, [
                'type' => '.html',
                'livecycleDetails' => $lcDetails,
                'userDetails' => $userDetails,
            ]);
            $arg = ['dashboard', 'edit', 'application', 'full-time'];

            if ($pdf) {
                $rq = '<div class="arow taskbar  '.$arg[1].' dashboard-apply-taskbar">';
                $rq .= '<div class="inner inner-apply">';
                $rq .= '  <div class="paddingsm" style="margin-top: 37px;"class="uppercase short text-warning"><i class="icon-alert"></i>
										&nbsp;&nbsp;Problem viewing application?&nbsp;&nbsp;Click here for
										<a href="/help-centre/loan-application-instructions/minimum-system-requirements" target="_blank">minimum requirements</a>.</div>';
                $rq .= '<a href="/dashboard/edit/application/'.$documentGUID.'/'.$programYear.'/html" class="right btn btn-primary">Open as HTML form</a>';
                $rq .= '</div>';
                $rq .= '</div>';

                //return embed link for PDF if ie8 or less
                $rq .= '<embed src="'.$pdfURL.'" type="application/pdf" wmode="opaque" width="100%" height="100%" ><param name="wmode" value="opaque"></embed>';

                return $rq;
            } else {
                $call = $this->fnGetCurlRequest($htmlURL);

                if (! empty($call['response'])) {
                    //return HTML5 form for any modern browser
                    $rq = '<div class="arow taskbar  '.$arg[1].' dashboard-apply-taskbar">';
                    $rq .= '<div class="inner inner-apply">';
                    $rq .= '  <div class="paddingsm" style="margin-top: 37px;"class="uppercase short text-warning"><i class="icon-alert"></i>
										&nbsp;&nbsp;Problem viewing application?&nbsp;&nbsp;Click here for
										<a href="/help-centre/loan-application-instructions/minimum-system-requirements" target="_blank">minimum requirements</a>.</div>';
                    $rq .= '<a href="/dashboard/edit/application/'.$documentGUID.'/'.$programYear.'/pdf"  class="right btn btn-primary">Open as PDF</a>';
                    $rq .= '</div>';
                    $rq .= '</div>';
                    $rq .= $call['response'];

                    return $rq;
                }
            }
        }
    }

    public function fnClaimToken($request, $uid = null)
    {
        $this->uid = session(env('GUID_SESSION_VAR'));
        $request->msg = '';
        if (isset($request->access_code, $request->appendix_type) && ! empty($request->access_code) && ! empty($request->appendix_type)) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnClaimToken(): request->access_code: '.$request->access_code);
                session()->push('DEBUG', now().': fnClaimToken(): request->appendix_type: '.$request->appendix_type);
            }

            $appendixType = ['APPENDIX1' => 'Appendix 1', 'APPENDIX2' => 'Appendix 2'];
            $correctAppendix = ($request->appendix_type == 'APPENDIX1') ? 'Appendix 2' : 'Appendix 1';
            $roleType = ['APPENDIX1' => 'parent', 'APPENDIX2' => 'spouse'];
            $applicantType = ['APPENDIX1' => 'child', 'APPENDIX2' => 'spouse'];
            $errors = ['404' => 'Please check with the student/applicant as changes to the application have made this access code no longer valid. If the Appendix is still required a new code will have been issued to the student to forward to you.',
                '407' => 'Appendix Access Code provided is for an '.$correctAppendix.' Select "Complete an '.$correctAppendix.'" from the dashboard.',
                '408' => 'This access code has already been claimed. Check your <a  href="/dashboard" >dashboard</a> under the tab My '.$appendixType[$request->appendix_type].'\'s',
                '409' => 'Sorry this access code is no longer valid. This '.$appendixType[$request->appendix_type].' document is no longer required by the applicant',
                '410' => 'This appendix has already been claimed by another user.',
                '406' => 'Sorry you cannot claim your own '.$appendixType[$request->appendix_type].'.  Please have your '.$roleType[$request->appendix_type].' login to their own dashboard account to claim.',
                '429' => 'This appendix has already been submitted.'];

            $baseURL = $this->fnWS('LC', '');

            $token = trim($request->access_code);
            $documentType = $request->appendix_type;

            $programYear = '';
            if (isset($request->program_year) && ! empty($request->program_year)) {
                $programYear = $request->program_year;
                if (! $this->fnValidateProgramYear($programYear)) {
                    $request->program_year = '';
                    $programYear = $request->program_year;
                }
            }
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnClaimToken(): strlen(token): '.strlen($token));
                session()->push('DEBUG', now().': fnClaimToken(): programYear: '.$programYear);
            }
            if (strlen($token) == 9) {
                // token has program year appended, parse it out
                if ($programYear == '') {
                    $year = '20'.substr($token, 7);
                    if (preg_match("/^\d+$/", $year)) {
                        $py = $year.$year + 1;
                    } else {
                        $request->msg = 'The access code you provided is invalid!';
                        $request->status = false;

                        return $request;
                    }

                    if ($this->fnValidateProgramYear($py)) {
                        $request->program_year = $py;
                        $programYear = $request->program_year;
                    }
                }
                $token = substr($token, 0, 7);
            }

            $lcVersion = '1.0';
            if (strlen($programYear) == 8) {
                $lcVersion = substr_replace($programYear, '.', 4, 0);
            }

            $url = $baseURL.'/rest/services/SABC_StudentLoan/Services/Tokens/ClaimTokenGetFormGUID:'.$lcVersion.'?documentToken='.$token.'&ownerGUID='.($uid == null ? $this->uid : $uid).'&documentTypeID='.$documentType;

            $rq = $this->fnRESTRequest($url, 'GET', null, null, null, 'XML');
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnClaimToken(): url: '.$url);
                session()->push('DEBUG', now().': fnClaimToken(): programYear: '.$programYear);
                session()->push('DEBUG', now().': fnClaimToken(): is rq false: '.json_encode($rq == false));
            }

            $request->statusCode = $rq->statusCode;

            //MAKE SURE STATUS CODE IS RETURNED AND THAT THE VALUE IS 200
            if (isset($rq->statusCode) && $rq->statusCode == 200) {
                $request->statusDescrition = $rq->statusDescrition;
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnClaimToken(): request->statusDescrition: '.$request->statusDescrition);
                    session()->push('DEBUG', now().': fnClaimToken(): rq->statusCode: '.$rq->statusCode);
                    session()->push('DEBUG', now().': fnClaimToken(): rq->statusDescrition: '.$rq->statusDescrition);
                }

                $request->applicationNumber = $rq->applicationNumber;
                $request->documentGUID = $rq->documentGUID;
                $request->status = true;

                return $request;
            }

            if (array_key_exists(''.$rq->statusCode.'', $errors) || is_null($rq)) {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnClaimToken(): request is NULL or error exists ');
                    session()->push('DEBUG', now().': fnClaimToken(): rq->statusCode: '.$rq->statusCode);
                    session()->push('DEBUG', now().': fnClaimToken(): error index : '.$errors[$rq->statusCode]);
                }

                if (isset($request->user_role) && ! empty($request->user_role) &&
                    ($request->user_role == 'spouse_no_sin' || $request->user_role == 'parent_no_sin')) {
                    $errors['407'] = 'Appendix Access Code provided is for an '.$correctAppendix.' Please enter an '.$appendixType[$request->appendix_type].' code.';

                    // 408 is used for no sin parent/spouse to reaccess appendix
                    if ($rq->statusCode == 408 || $rq->statusCode == 429) {
                        $request->applicationNumber = $rq->applicationNumber;
                        $request->documentGUID = $rq->documentGUID;
                        $request->status = true;

                        return $request;
                    } else {
                        $request->msg = $errors[''.$rq->statusCode.''];
                        $request->status = false;

                        return $request;
                    }
                } else {
                    //redirect user to appendix because we know it already belongs to them
                    if ($rq->statusCode == 408) {
                        if (isset($request->user_role) && ! empty($request->user_role)) {
                            $request->redirect = '/apply/appendix/'.$request->appendix_type.'/'.$rq->documentGUID.'/'.$programYear;
                            $request->status = false;

                            return $request;
                        } else {
                            $request->documentGUID = $rq->documentGUID;
                            $request->status = true;

                            return $request;
                        }
                    } elseif ($rq->statusCode == 410) {
                        $request->msg = $errors[''.$rq->statusCode.''];
                        $request->status = false;

                        return $request;
                    } else {
                        $request->msg = $errors[''.$rq->statusCode.''];
                        $request->status = false;

                        return $request;
                    }
                }
            } else {
                $request->msg = 'Invalid request made';
                $request->status = false;

                return $request;
            }
        }
        $request->msg = 'Invalid request made';
        $request->status = false;

        return $request;
    }

    public function fnValidateProgramYear($py)
    {
        $progYear = $this->fnGetProgramYear();
        if ($progYear['status'] == true) {
            foreach ($progYear as $k => $v) {
                if ($v['programYear'] == $py) {
                    return true;
                }
            }

            return false;
        }
    }

    public function fnValidateSchoolCode($sc)
    {
        $inst = $this->fnGetSchoolDetails($sc);

        if (! empty($inst)) {
            $school_code = $inst->schoolDetails->SchoolCode;

            return $school_code;
        } else {
            return false;
        }
    }

    /*
    *	function fnAppendix
    * 	@params:
    *		$documentGUID: the document id for the form we want to load
    * 	$documentType: what type of document are we trying to load - Appendix1 or Appendix2
    * 	$pdf: true or false tells us to load a PDF or an HTML5 form
    */

    public function fnAppendix($documentGUID, $documentType, $programYear, $pdf)
    {
        $this->uid = session(env('GUID_SESSION_VAR'));

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnAppendix(): documentGUID: '.$documentGUID);
            session()->push('DEBUG', now().': fnAppendix(): documentType: '.$documentType);
            session()->push('DEBUG', now().': fnAppendix(): program_year: '.$programYear);
            session()->push('DEBUG', now().': fnAppendix(): pdf: '.$pdf);
        }

        $user = User::where('id', Auth::id())->first();
        $validSin = true;
        if (in_array('parent_no_sin', $user->roles()->pluck('name')->toArray()) || in_array('spouse_no_sin', $user->roles()->pluck('name')->toArray())) {
            $validSin = false;
        }

        $baseURL = $this->fnWS('LC', '');
        $submitURL = $this->fnWS('LC-SUBMIT', '');
        $dataRefURL = $this->fnWS('LC-DATAREF', '');

        $lcVersion = '1.0';

        $startYear = substr($programYear, 0, 4);

        if (strlen($programYear) == 8) {
            $lcVersion = substr_replace($programYear, '.', 4, 0);
        }

        //make sure proper parameters were passed
        if (! empty($documentGUID) && ! empty($documentType)) {
            $dt = ucfirst(strtolower($documentType));

            //get base urls from config file
            $url = $this->fnWS('LC', 'APPLICATION_FORM_REST');
            $userDetails = ['ownerGUID' => $this->uid, 'documentGUID' => $documentGUID];
            if (! empty($startYear) && $startYear >= 2016) {
                $userDetails['ownerIDP'] = $validSin;
            }

            $lcDetails = [
                'contentRoot' => 'repository:///Applications/SABC_StudentLoan/'.$lcVersion.'/Forms',
                'template' => $dt.'.xdp',
                'submitUrl' => $submitURL.'/rest/services/SABC_StudentLoan/Services/'.$dt.'/Intake/UpdateHTML:'.$lcVersion,
                'dataRef' => $dataRefURL.'/rest/services/SABC_StudentLoan/Services/'.$dt.'/Fill/Update:'.$lcVersion,
            ];

            if (session()->exists('read-only')) {
                $lcDetails['template'] = $dt.'-ReadOnly.xdp';
            }

            //Build and encode the lifecycle url based on defined parameters
            $htmlURL = $this->fnBuildLiveCycleUrl($url, [
                'type' => '.html',
                'livecycleDetails' => $lcDetails,
                'userDetails' => $userDetails,
            ]);

            //return embed link for PDF if ie8 or less
            $pdfURL = $baseURL.'/rest/services/SABC_StudentLoan/Services/'.$dt.'/FillAndSubmit/Update:'.$lcVersion.'?'.http_build_query($userDetails);
            $arg = ['dashboard', 'apply', 'appendix'];

            if ($pdf) {
                $rq = '<div class="arow taskbar  '.$arg[1].' dashboard-apply-taskbar">';
                $rq .= '<div class="inner inner-apply">';
                $rq .= '  <div class="paddingsm" style="margin-top: 37px;"class="uppercase short text-warning"><i class="icon-alert"></i>
									&nbsp;&nbsp;Problem viewing application?&nbsp;&nbsp;Click here for
									<a href="/help-centre/loan-application-instructions/minimum-system-requirements" target="_blank">minimum requirements</a>.</div>';
                $rq .= '<a href="/dashboard/apply/appendix/'.$documentType.'/'.$documentGUID.'/'.$programYear.'/html" class="right btn btn-primary">Open as HTML form</a>';
                $rq .= '</div>';
                $rq .= '</div>';

                $rq .= '<embed src="'.$pdfURL.'" type="application/pdf" wmode="opaque" width="100%" height="100%" ><param name="wmode" value="opaque"></embed>';

                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnAppendix() rq: '.strip_tags(json_encode($rq)));
                }

                return $rq;
            } else {
                $call = $this->fnGetCurlRequest($htmlURL);

                if (! empty($call['response'])) {
                    //return HTML5 form for any modern browser
                    $rq = '<div class="arow taskbar  '.$arg[1].' dashboard-apply-taskbar">';
                    $rq .= '<div class="inner inner-apply">';
                    $rq .= '  <div class="paddingsm" style="margin-top: 37px;"class="uppercase short text-warning"><i class="icon-alert"></i>
										&nbsp;&nbsp;Problem viewing application?&nbsp;&nbsp;Click here for
										<a href="/help-centre/loan-application-instructions/minimum-system-requirements" target="_blank">minimum requirements</a>.</div>';
                    $rq .= '<a href="/dashboard/apply/appendix/'.$documentType.'/'.$documentGUID.'/'.$programYear.'/pdf"  class="right btn btn-primary">Open as PDF</a>';
                    $rq .= '</div>';
                    $rq .= '</div>';
                    $rq .= $call['response'];

                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': fnAppendix() rq: '.strip_tags(json_encode($rq)));
                    }

                    return $rq;
                }
            }
        } else {
            return false;
        }
    }

    public function fnRequestAppendixThree($documentGUID, $programYear)
    {
        $this->uid = session(env('GUID_SESSION_VAR'));

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnRequestAppendixThree(): documentGUID: '.$documentGUID);
            session()->push('DEBUG', now().': fnRequestAppendixThree(): program_year: '.$programYear);
        }

        $baseURL = $this->fnWS('LC', '');
        $ownerGUID = $this->uid;

        if ($documentGUID == 'null') {
            // Workaround for when the documentGUID isn't returned. The URL will have null, but a blank documentGUID will be passed to generate a blank appendix 3..
            $documentGUID = '';
        }

        $lcVersion = '1.0';
        if (strlen($programYear) == 8) {
            $lcVersion = substr_replace($programYear, '.', 4, 0);
            $lcVersion = $lcVersion;
        }

        $url = $baseURL.'/rest/services/SABC_StudentLoan/Services/Appendix3/PrintAndFill/PDF:'.$lcVersion.'?documentGUID='.$documentGUID.'&ownerGUID='.$ownerGUID;

        return $url;
    }

    public function fnSubmit($documentType, $documentGUID, $programYear)
    {
        $this->uid = $this->fnDecrypt(Auth::user()->name);

        if (isset($documentType, $documentGUID)) {
            $baseURL = $this->fnWS('LC', '');

            $url = $baseURL.'/rest/services/SABC_StudentLoan_APIs/Data/Submit/'.$documentType.'/Submit:1.0?ownerGUID='.$this->uid.'&documentGUID='.$documentGUID;

            $rq = $this->fnRESTRequest($url, 'GET', null, null, null, 'XML');

            //MAKE SURE STATUS CODE IS RETURNED AND THAT THE VALUE IS 200

            if (isset($rq->result) && $rq->result == 200) {
                return $rq->result;
            }
        }

        return false;
    }
}
