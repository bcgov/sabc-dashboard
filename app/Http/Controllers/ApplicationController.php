<?php

namespace App\Http\Controllers;

use App\Application;
use App\Declaration;
use App\Http\Requests\ApplicationSubmitRequest;
use App\Http\Requests\ConfirmBcscProfileRequest;
use App\Http\Requests\ConfirmProfileRequest;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class ApplicationController extends Aeit
{
    public function applyConfirmProfile(Request $request, $school_code)
    {
        $r = new Application();
        $programYears = $r->fnGetProgramYear();

        $common = new Aeit();
        $common->WSDL = $common->fnWS('WS-HOSTS', 'GET_SCHOOLS');
        $school = $common->fnRequest('getSchoolDetails', ['schoolIDX' => $school_code], 'get_school_details_'.$school_code.'', 300);

        return view('application-apply', ['program_years' => json_encode($programYears), 'school' => json_encode($school), 'submit_status' => null, 'submit_msg' => null]);
    }

    /**
     * POST to confirm user information before starting a new application
     *
     * @param  ConfirmProfileRequest  $request
     * @return rediret
     */
    public function applyUpdateProfile(ConfirmProfileRequest $request)
    {
        if ($request->form_update == 'TRUE' || $request->form_update == null) {
            $user = new User();
            [$submit_status, $submit_msg] = $user->fnUpdateProfile($request, false);
        }

        $program_year = $request->Year;
        $school_code = $request->school_code;
        $school_classification = $request->school_classification;
        //pass values to lifecycle through url

        return redirect('/apply/application/full-time/'.$school_code.'/'.$program_year);
    }

    /**
     * POST to confirm user BCSC information before starting a new application
     *
     * @param  ConfirmBcscProfileRequest  $request
     * @return rediret
     */
    public function applyUpdateBcscProfile(ConfirmBcscProfileRequest $request)
    {
        if ($request->form_update == 'TRUE' || $request->form_update == null) {
            $user = new User();
            [$submit_status, $submit_msg] = $user->fnUpdateProfile($request, false);
        }

        $program_year = $request->Year;
        $school_code = $request->school_code;
        $school_classification = $request->school_classification;
        //pass values to lifecycle through url

        return redirect('/apply/application/full-time/'.$school_code.'/'.$program_year);
    }

    /**
     * POST to confirm user information before starting a new application
     *
     * @param  Request  $request
     * @param $school_code
     * @param $program_year
     * @param  null  $document_type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function applyFulltimeApplication(Request $request, $school_code, $program_year, $document_type = null)
    {
        $load_status = false;
        $load_msg = '';

        $application = new Application();

        //get institution code
        $institution_code = $school_code;

        //get program year from url
        //$program_year = $args[5];
        $documentType = (! is_null($document_type) && $document_type == 'pdf') ? true : false;
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': applyFulltimeApplication() documentType: '.json_encode($documentType));
        }

        if (is_null($document_type)) {
            if (preg_match('/(?i)msie/', $_SERVER['HTTP_USER_AGENT'])) {
                //get pdf version of livecycle form
                $livecycle_form = $application->fnApply($program_year, $institution_code, true);
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': applyFulltimeApplication() msie program_year: '.$program_year);
                    session()->push('DEBUG', now().': applyFulltimeApplication() msie institution_code: '.$institution_code);
                }

                if (empty($livecycle_form)) {
                    $load_msg = 'Sorry we are having difficulties loading this form.';
                }
            } else {
                //get html5 version of livecycle form
                $livecycle_form = $application->fnApply($program_year, $institution_code, false);
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': applyFulltimeApplication() program_year: '.$program_year);
                    session()->push('DEBUG', now().': applyFulltimeApplication() institution_code: '.$institution_code);
                }

                if (empty($livecycle_form)) {
                    $load_msg = 'Sorry we are having difficulties loading this form. Please refresh your browser to try again or click on the "Open as PDF" button.';
                }
                $livecycle_form = '<div class="lc-no-conflict">'.$livecycle_form.'</div>';
            }
        } else {
            if (! empty($documentType)) {
                //get pdf version of livecycle form
                $livecycle_form = $application->fnApply($program_year, $institution_code, true);
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': applyFulltimeApplication() documentType NOT empty: program_year: '.$program_year);
                    session()->push('DEBUG', now().': applyFulltimeApplication() documentType NOT empty: institution_code: '.$institution_code);
                    //session()->push('DEBUG', now() . ": applyFulltimeApplication() documentType NOT empty: livecycle_form: " . strip_tags(json_encode($livecycle_form)));
                }

                if (empty($livecycle_form)) {
                    //SD-17853
                    //drupal_set_message('Sorry we are having difficulties loading this form.');
                    $load_msg = '<div class="alert alert-contextual alert-warning" role="alert">
<svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#warning"></use></svg>
<p>PDF form is currently unavailable, please use the <a href="./">HTML</a> version.<p>
</div>';
                }
            } else {
                //get html5 version of livecycle form
                $livecycle_form = $application->fnApply($program_year, $institution_code, false);
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': applyFulltimeApplication() documentType is empty: program_year: '.$program_year);
                    session()->push('DEBUG', now().': applyFulltimeApplication() documentType is empty: institution_code: '.$institution_code);
                    //session()->push('DEBUG', now() . ": applyFulltimeApplication() documentType is empty: livecycle_form: " . strip_tags(json_encode($livecycle_form)));
                }

                if (empty($livecycle_form)) {
                    $load_msg = 'Sorry we are having difficulties loading this form. Please refresh your browser to try again or click on the "Open as PDF" button.';
                }
                $livecycle_form = '<div class="lc-no-conflict">'.$livecycle_form.'</div>';
            }
        }

        $user_name = '';
        $user_profile = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'), null);
        if (! is_null($user_profile)) {
            $user_name = isset($user_profile->userProfile->firstName) ? $user_profile->userProfile->firstName.' '.$user_profile->userProfile->familyName : $user_profile->userProfile->familyName;
        }
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': applyFulltimeApplication() user_name: '.$user_name);
            //session()->push('DEBUG', now() . ": applyFulltimeApplication() documentType is empty: livecycle_form: " . strip_tags(json_encode($livecycle_form)));
        }

        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR7'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR8'));

        return view('lc-application', ['user_name' => $user_name, 'livecycle_form' => json_encode($livecycle_form), 'load_status' => $load_status, 'load_msg' => $load_msg]);
    }

    /**
     * POST checklist to submit an application
     *
     * @param  ApplicationSubmitRequest  $request
     * @param $program_year
     * @param $app_id
     * @param  null  $document_guid
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function confirmSubmitChecklist(ApplicationSubmitRequest $request, $program_year, $app_id, $document_guid = null)
    {
        $this->uid = session(env('GUID_SESSION_VAR'));

        //does the application belong to the user?
        $this->user = User::where('id', Auth::user()->id)->with('roles')->first();
        $application = $this->user->application($app_id);
        if (is_null($application)) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': applicationSubmitChecklist() application does not exist!');
            }

            return redirect('/');
        }

        $documentType = 'Application';
        $documentGUID = $document_guid;
        $programYear = $program_year;

        $app = new Application();
        $this->uid = session(env('GUID_SESSION_VAR'));

        $submit = $app->fnSubmit($documentType, $documentGUID, $programYear);

        if (! empty($submit)) {
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR7'));
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR8'));

            return redirect('/student-loans/check-application-status/'.$app_id);
        }

        return redirect('/application-submit-checklist/'.$program_year.'/'.$app_id.'/'.($document_guid == null ? '' : $document_guid));
    }

    /**
     * GET checklist to submit an application
     *
     * @param  Request  $request
     * @param $program_year
     * @param $app_id
     * @param  null  $document_guid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function applicationSubmitChecklist(Request $request, $program_year, $app_id, $document_guid = null)
    {
        $load_status = false;
        $load_msg = '';
        $checklist = null;

        $this->uid = session(env('GUID_SESSION_VAR'));

        //does the application belong to the user?
        $this->user = User::where('id', Auth::user()->id)->with('roles')->first();
        $application = $this->user->application($app_id);
        if (is_null($application)) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': applicationSubmitChecklist() application does not exist!');
            }

            return redirect('/');
        }

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': applicationSubmitChecklist() program_year: '.json_encode($program_year));
            session()->push('DEBUG', now().': applicationSubmitChecklist() app_id: '.json_encode($app_id));
            session()->push('DEBUG', now().': applicationSubmitChecklist() document_guid: '.json_encode($document_guid));
            session()->push('DEBUG', now().': applicationSubmitChecklist() application: '.json_encode($application));
        }

        $programYear = $program_year;
        $startYear = (int) substr($programYear, 0, 4);
        $endYear = (int) substr($programYear, -4, 4);
        $docType = 'application';
        $appID = $app_id;
        $role = 'A';
        $noSinUser = false;

        $r = new Application();
        $econsent_rollout_date = 20200123; // 2020 Jan 23
        $today = date('Ymd', strtotime('today'));
        $today = (int) $today;

        $decRequired = $r->fnIsWebDeclarationInkSignatureRequired($appID, $role);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': applicationSubmitChecklist() decRequired: '.json_encode($decRequired));
            session()->push('DEBUG', now().': applicationSubmitChecklist() startYear: '.json_encode($startYear));
            session()->push('DEBUG', now().': applicationSubmitChecklist() endYear: '.json_encode($endYear));
        }

        if (is_null($decRequired) || ! is_object($decRequired) || ! isset($decRequired->inkSignatureRequired)) {
            $load_msg = 'Sorry! An error occurred. Please try again later. Error #20239823';
        } else {
            $programYear = $r->fnValidateProgramYear($programYear);
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': applicationSubmitChecklist() programYear: '.json_encode($programYear));
            }

            //PROGRAM YEAR NOW IS A BOOLEAN
            if ($programYear) {
                $sigReq = $decRequired->inkSignatureRequired;
                if ($sigReq == 'Y') {
                    $dectype = 'Applicant - Signature Required';
                } elseif ($sigReq == 'E') {
                    $dectype = 'Applicant - E-Consent';
                } else {
                    $dectype = 'Applicant - No Signature Required';
                }

                //remove the following from returned content
                $s = [
                    '<strong class="font11">STUDENT NAME</strong>',
                    'SIGNATURE OF STUDENT (in ink)',
                    '<strong class="font11">DATE SIGNED</strong>',
                    '"; ?>',
                ];

                $checklist = Declaration::where('program_year', $startYear.'/'.$endYear)->where('type', $dectype)->where('status', 1)->with('fields')->first();
                $s = [
                    '<strong class="font11">STUDENT NAME</strong>',
                    'SIGNATURE OF STUDENT (in ink)',
                    '<strong class="font11">DATE SIGNED</strong>',
                    '"; ?>',
                ];
                $r = ['', '', '', ''];
                $stack = ['field_consent_req_agreement_text', 'field_consent2_req_agreement', 'field_new_dec_req_agreement_text', 'field_new_dec2_req_agreement', 'field_sig_req_agreement_text'];
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': applicationSubmitChecklist() 1 is checklist null: '.is_null($checklist));
                }
                if (! is_null($checklist)) {
                    foreach ($checklist->fields as $f) {
                        if (in_array($f->field_id, $stack)) {
                            $f->field_value = str_replace($s, $r, $f->field_value);
                        }
                    }
                    $load_status = true;
                }
            } else {
                $load_msg = 'Program Year Invalid - Unable to load form. Refresh the page or return the previous page and resubmit. Error #20239824';
            }
        }

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': applicationSubmitChecklist() 2 is checklist null: '.is_null($checklist));
        }

        return view('application-submit-checklist',
            ['program_year' => $program_year, 'app_id' => $app_id, 'document_guid' => $document_guid, 'declaration' => json_encode($decRequired),
                'load_status' => $load_status, 'load_msg' => $load_msg, 'checklist' => json_encode($checklist), 'submit_msg' => '', 'submit_status' => false]);
    }

    /**
     * GET to edit an application
     *
     * @param  Request  $request
     * @param $document_guid
     * @param $program_year
     * @param  null  $document_type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function editFulltimeApplication(Request $request, $document_guid, $program_year, $document_type = null)
    {
        $load_status = false;
        $load_msg = '';

        $application = new Application();
        $documentType = (! is_null($document_type) && $document_type == 'pdf') ? true : false;
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': editFulltimeApplication() documentType: '.json_encode($documentType));
        }

        if (is_null($document_type)) {
            if (preg_match('/(?i)msie/', $_SERVER['HTTP_USER_AGENT'])) {
                //get pdf version of livecycle form
                $livecycle_form = $application->fnUpdateApplication($document_guid, $program_year, true);
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': editFulltimeApplication() msie program_year: '.$program_year);
                    session()->push('DEBUG', now().': editFulltimeApplication() msie document_guid: '.$document_guid);
                }

                if (empty($livecycle_form)) {
                    $load_msg = 'Sorry we are having difficulties loading this form.';
                }
            } else {
                //get html5 version of livecycle form
                $livecycle_form = $application->fnUpdateApplication($document_guid, $program_year, false);
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': editFulltimeApplication() program_year: '.$program_year);
                    session()->push('DEBUG', now().': editFulltimeApplication() document_guid: '.$document_guid);
                }

                if (empty($livecycle_form)) {
                    $load_msg = 'Sorry we are having difficulties loading this form. Please refresh your browser to try again or click on the "Open as PDF" button.';
                }
                $livecycle_form = '<div class="lc-no-conflict">'.$livecycle_form.'</div>';
            }
        } else {
            if (! empty($documentType)) {
                //get pdf version of livecycle form
                $livecycle_form = $application->fnUpdateApplication($document_guid, $program_year, true);
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': editFulltimeApplication() documentType NOT empty: program_year: '.$program_year);
                    session()->push('DEBUG', now().': editFulltimeApplication() documentType NOT empty: document_guid: '.$document_guid);
                }

                if (empty($livecycle_form)) {
                    //SD-17853
                    //drupal_set_message('Sorry we are having difficulties loading this form.');
                    $load_msg = '<div class="alert alert-contextual alert-warning" role="alert">
<svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#warning"></use></svg>
<p>PDF form is currently unavailable, please use the <a href="./">HTML</a> version.<p>
</div>';
                }
            } else {
                //get html5 version of livecycle form
                $livecycle_form = $application->fnUpdateApplication($document_guid, $program_year, false);
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': editFulltimeApplication() documentType is empty: program_year: '.$program_year);
                    session()->push('DEBUG', now().': editFulltimeApplication() documentType is empty: document_guid: '.$document_guid);
                }

                if (empty($livecycle_form)) {
                    $load_msg = 'Sorry we are having difficulties loading this form. Please refresh your browser to try again or click on the "Open as PDF" button.';
                }
                $livecycle_form = '<div class="lc-no-conflict">'.$livecycle_form.'</div>';
            }
        }

        $user_name = '';
        $user_profile = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'), null);
        if (! is_null($user_profile)) {
            $user_name = isset($user_profile->userProfile->firstName) ? $user_profile->userProfile->firstName.' '.$user_profile->userProfile->familyName : $user_profile->userProfile->familyName;
        }
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': editFulltimeApplication() user_name: '.$user_name);
            //session()->push('DEBUG', now() . ": applyFulltimeApplication() documentType is empty: livecycle_form: " . strip_tags(json_encode($livecycle_form)));
        }

        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR7'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR8'));

        if (session()->exists('read-only')) {
            return view('lc-application-read-only', ['user_name' => $user_name, 'livecycle_form' => json_encode($livecycle_form), 'load_status' => $load_status, 'load_msg' => $load_msg]);
        }

        return view('lc-application', ['user_name' => $user_name, 'livecycle_form' => json_encode($livecycle_form), 'load_status' => $load_status, 'load_msg' => $load_msg]);
    }

    /**
     * GET to delete application
     *
     * @param  Request  $request
     * @return rediret
     */
    public function deleteApplication(Request $request, $documentGUID, $app_id)
    {
        $this->uid = session(env('GUID_SESSION_VAR'));
        $load_status = false;
        $load_msg = '';
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': deleteApplication() documentGUID: '.$documentGUID);
            session()->push('DEBUG', now().': deleteApplication() app_id: '.$app_id);
        }

        //check ownership
        $user = User::where('id', Auth::id())->first();
        $app = $user->application($app_id);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': deleteApplication() app: '.json_encode($app));
        }

        if (! is_null($app) && isset($documentGUID) && ! session()->exists(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR3'))) {

            $baseURL = $this->fnWS('LC', '');

            $url = $baseURL.'/rest/services/SABC_StudentLoan_APIs/Data/Delete/Application:1.0?documentGUID='.$documentGUID.'&ownerGUID='.$this->uid;
            $rq = $this->fnRESTRequest($url, 'GET', null, null, null, 'XML');
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': deleteApplication() rq: '.json_encode($rq));
                session()->push('DEBUG', now().': deleteApplication() rq->statusCode: '.$rq->statusCode);
            }

            //MAKE SURE STATUS CODE IS RETURNED AND THAT THE VALUE IS 200
            if (isset($rq->statusCode) && $rq->statusCode == 200) {
                //$this->fnClearApplicationsCache();
                Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR8'));
                $load_status = true;
            }
        }

        if ($load_status == true) {
            return redirect('/');
        } else {
            return redirect('/student-loans/check-application-status/'.$app_id);
        }
        return redirect('/student-loans/check-application-status/'.$app_id);
    }
}
