<?php

namespace App\Http\Controllers;

use App\Application;
use App\Declaration;
use App\Http\Requests\AppendixClaimRequest;
use App\Http\Requests\ApplicationSubmitRequest;
use App\Role;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Response;

class AppendixController extends Aeit
{
    /**
     * When the student is required to provide Appendix 1 or 2 and provided that the spouse/parent does NOT have a valid SIN
     * The spouse/parent is required to fill in an appendix but can NOT create an SABC/BCSC account
     * So we allow them to claim and access the appendix without authentication
     *
     * @param  Request  $request
     * @param $appendix_type
     * @param  null  $access_code
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function appendixNoSinClaim(Request $request, $program_year, $appendix_type)
    {
        $r = new Application();
        $programYears = $r->fnGetProgramYear();

        if ($appendix_type == 'APPENDIX1') {
            return view('appendix1-no-sin-claim', ['program_years' => json_encode($programYears), 'submit_status' => false, 'submit_msg' => '', 'program_year' => $program_year]);
        }
        if ($appendix_type == 'APPENDIX2') {
            return view('appendix2-no-sin-claim', ['program_years' => json_encode($programYears), 'submit_status' => false, 'submit_msg' => '', 'program_year' => $program_year]);
        }

        return redirect('/');
    }

    /**
     * @param  Request  $request
     * @param $appendix_type
     * @param  null  $access_code
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function appendixClaim(Request $request, $appendix_type, $access_code = null)
    {
        $r = new Application();
        $programYears = $r->fnGetProgramYear();

        if ($appendix_type == 'APPENDIX1') {
            return view('appendix1-claim', ['program_years' => json_encode($programYears), 'submit_status' => false, 'submit_msg' => '', 'access_code' => $access_code]);
        }
        if ($appendix_type == 'APPENDIX2') {
            return view('appendix2-claim', ['program_years' => json_encode($programYears), 'submit_status' => false, 'submit_msg' => '', 'access_code' => $access_code]);
        }

        return redirect('/');
    }

    /**
     * Provide data to build the Appendix Claim form
     *
     * @param  Request  $request
     * @return Response
     */
    public function appendixClaimData(Request $request)
    {
        $this->uid = session(env('GUID_SESSION_VAR'));
        $this->user = User::where('id', Auth::user()->id)->with('roles')->first();
        $config = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), null);
        if (is_null($config)) {
            $config = parse_ini_file(config_path().env('APP_CONFIG_FILE'), true);
            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), $config, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
        }
        $countries = $config['COUNTRIES'];
        $provinces = $config['PROVINCES'];
        $states = $config['STATES'];

        return Response::json(['profile' => $this->fnGetUserProfile($request), 'countries' => $countries, 'provinces' => $provinces, 'states' => $states], 200); // Status code here
    }

    /**
     * @param  AppendixClaimRequest  $request
     * @param $appendix_type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function appendixClaimSubmit(AppendixClaimRequest $request, $appendix_type)
    {
        $submit_status = false;
        $submit_msg = '';

        if ($request->form_update == 'TRUE' || $request->form_update == null) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': appendixClaimSubmit(): profile update required');
            }
            $user = new User();
            [$submit_status, $submit_msg] = $user->fnUpdateProfile($request, false);
        }

        $request->appendix_type = $appendix_type;
        $application = new Application();
        $programYears = $application->fnGetProgramYear();

        $claim_token_response = $application->fnClaimToken($request);

        $appendix_type = $claim_token_response->appendix_type;
        $document_guid = $claim_token_response->documentGUID;
        $program_year = $claim_token_response->program_year;

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': appendixClaimSubmit(): appendix_type: '.$appendix_type);
            session()->push('DEBUG', now().': appendixClaimSubmit(): document_guid: '.$document_guid);
            session()->push('DEBUG', now().': appendixClaimSubmit(): program_year: '.$program_year);
            session()->push('DEBUG', now().': appendixClaimSubmit(): claim_token_response: '.json_encode($claim_token_response));
        }

        if (isset($claim_token_response->redirect)) {
            //redirect user to appendix because we know it already belongs to them
            return redirect($claim_token_response->redirect);
        } elseif ($claim_token_response->msg != '' || $claim_token_response->status == false) {
            $submit_status = $claim_token_response->status;
            $submit_msg = $claim_token_response->msg;

            if ($request->appendix_type == 'APPENDIX1') {
                return view('appendix1-claim', ['program_years' => json_encode($programYears), 'submit_status' => $submit_status, 'submit_msg' => $submit_msg, 'access_code' => $request->access_code]);
            }
            if ($request->appendix_type == 'APPENDIX2') {
                return view('appendix2-claim', ['program_years' => json_encode($programYears), 'submit_status' => $submit_status, 'submit_msg' => $submit_msg, 'access_code' => $request->access_code]);
            }
        } else {
            //claim was successful
            if ($request->appendix_type == 'APPENDIX1') {
                $role = Role::where('name', 'bcsc_parent')->first();
            }
            if ($request->appendix_type == 'APPENDIX2') {
                $role = Role::where('name', 'bcsc_spouse')->first();
            }

            $u = User::where('id', Auth::user()->id)->with('roles')->first();
            //if the user claiming the appendix does not have the role associated with the appendix (appx1->parent, appx2->spouse
            if (! is_null($u) && ($u->isBcscSpouse() == false || $u->isBcscParent() == false)) {
                $u->roles()->attach($role);
            }

            return redirect('/apply/appendix/'.$appendix_type.'/'.$document_guid.'/'.$program_year);
        }


        if ($appendix_type == 'APPENDIX1') {
            return view('appendix1-claim', ['program_years' => json_encode($programYears), 'submit_status' => $submit_status, 'submit_msg' => $submit_msg]);
        }
        if ($appendix_type == 'APPENDIX2') {
            return view('appendix2-claim', ['program_years' => json_encode($programYears), 'submit_status' => $submit_status, 'submit_msg' => $submit_msg]);
        }
    }

    /**
     * @param  AppendixClaimRequest  $request
     * @param $appendix_type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function appendixNoSinClaimSubmit(Request $request, $program_year, $appendix_type)
    {
        $request->merge([
            'appendix_type' => $appendix_type,
            'program_year' => $program_year,
        ]);
        $this->validate($request, [
            'appendix_type' => 'required|in:APPENDIX1,APPENDIX2',
            'program_year' => 'required|string|min:8|max:9',
            'access_code' => 'required|string|max:9',
        ]);

        $userRole = '';
        if ($appendix_type == 'APPENDIX1') {
            $userRole = 'parent_no_sin';
        }
        if ($appendix_type == 'APPENDIX2') {
            $userRole = 'spouse_no_sin';
        }
        $request->merge([
            'user_role' => $userRole,
        ]);
        $uid = md5($request->access_code);
        $claimYear = '';
        $no_sin_user = [];
        $no_sin_user['status'] = false;
        $code_data = $this->encryptAccessCode($request->access_code);
        if ($code_data['status'] == true && strlen($request->access_code) == 9) {
            $claimYear = '20'.substr($request->access_code, 7);
            $claimYear .= intval($claimYear) + 1;

            if ($claimYear == $program_year) {
                $no_sin_user['uid'] = $code_data['uid'];
                $no_sin_user['login_name'] = $request->access_code;
                $no_sin_user['status'] = true;
            }
        }
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): strlen request->access_code: '.strlen($request->access_code));
            session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): claimYear: '.$claimYear);
            session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): program_year: '.$program_year);
            session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): no_sin_user: '.json_encode($no_sin_user));
            session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): userRole: '.json_encode($userRole));
            session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): code_data: '.json_encode($code_data));
        }

        $ct = new Application();
        $programYears = $ct->fnGetProgramYear();

        //if we have no issues with access code
        if ($code_data['status'] == true && $no_sin_user['status'] == true && $userRole != '') {
            $user = User::where('name', $no_sin_user['uid'])->first();
            if (is_null($user)) {
                //$next_id = User::select('id')->orderBy('id', 'desc')->first();
                $next_uid = User::select('uid')->orderBy('uid', 'desc')->first();
                $old_uid = User::select('uid')->where('name', 'like', $no_sin_user['uid'])->first();

                $user = new User();
                $user->uid = $next_uid->uid + 1;

                //if the user has an account recreate it with the same UID
                if (! is_null($old_uid)) {
                    $user->uid = $old_uid->uid;
                }

                $user->name = $no_sin_user['uid'];
                $user->password = $user->user_password();
                $user->created = strtotime('now');
                $user->status = 1;
                $user->save();

                $role = Role::where('name', $userRole)->first();
                $user->roles()->attach($role);
            }

            // attempt to claim appendix
            $autoClaim = $ct->fnClaimToken($request, $uid);
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): uid: '.$uid);
                session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): autoClaim: '.json_encode($autoClaim));
                session()->push('DEBUG', now().": appendixNoSinClaimSubmit(): autoClaim['status']: ".$autoClaim->status);
                session()->push('DEBUG', now().": appendixNoSinClaimSubmit(): autoClaim['statusCode']: ".$autoClaim->statusCode);
            }

            if (! empty($autoClaim) && $autoClaim->status == true) {

                $this->uid = $uid;
                Auth::login($user);
                session([env('GUID_SESSION_VAR') => $uid]);

                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): autoClaim->documentGUID: '.$autoClaim->documentGUID);
                    session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): autoClaim->applicationNumber: '.$autoClaim->applicationNumber);
                    session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): access_code: '.$no_sin_user['uid']);
                    session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): appendix_type: '.$appendix_type);
                    session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): program_year: '.$program_year);
                    session()->push('DEBUG', now().': appendixNoSinClaimSubmit(): this->uid: '.$this->uid);
                }

                if ($autoClaim->statusCode == 429) {
                    return redirect('/declaration/download/'.$appendix_type.'/'.$autoClaim->documentGUID.'/'.$autoClaim->applicationNumber);
                } else {
                    return redirect('/apply/appendix/'.$appendix_type.'/'.$autoClaim->documentGUID.'/'.$program_year);
                }

            }
        }
        if ($appendix_type == 'APPENDIX1') {
            return view('appendix1-no-sin-claim', ['program_years' => json_encode($programYears), 'submit_status' => false, 'submit_msg' => 'Please check with the student/applicant as changes to the application have made this access code no longer valid. If the Appendix is still required a new code will have been issued to the student to forward to you.', 'program_year' => $program_year]);
        }
        if ($appendix_type == 'APPENDIX2') {
            return view('appendix2-no-sin-claim', ['program_years' => json_encode($programYears), 'submit_status' => false, 'submit_msg' => 'Please check with the student/applicant as changes to the application have made this access code no longer valid. If the Appendix is still required a new code will have been issued to the student to forward to you.', 'program_year' => $program_year]);
        }

        return redirect('/');
    }

    /*
    *		Create a UID for the user using the Access Code they received
    *		@params: $access_code
    *		@return: $code_data
    */
    private function encryptAccessCode($access_code)
    {
        $u = trim($access_code);
        $code_data = [];
        // bypass login
        if (isset($u) && $u != '') {
            // TODO: sanitize user input. Can be done here, but better to be done on LC side
            $code_data['status'] = true;

            $this->uid = md5($u);
            $code_data['uid'] = $this->fnEncrypt(md5($u));

            return $code_data;
        }

        $code_data['status'] = false;

        return $code_data;
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function downloadAppendix3(Request $request, $document_guid, $program_year)
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': downloadAppendix3(): documentGUID: '.$document_guid);
            session()->push('DEBUG', now().': downloadAppendix3(): program_year: '.$program_year);
        }

        $application = new Application();
        $url = $application->fnRequestAppendixThree($document_guid, $program_year);

        return redirect($url);

    }

    /**
     * Get to confirm user information before starting a new application
     *
     * @param  Request  $request
     * @param $appendix_type
     * @param $document_guid
     * @param $program_year
     * @param  null  $format_type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function applyAppendix(Request $request, $appendix_type, $document_guid, $program_year, $format_type = null)
    {
        $application = new Application();

        $load_status = false;
        $load_msg = '';
        if (is_null($format_type)) {
            if (preg_match('/(?i)msie/', $_SERVER['HTTP_USER_AGENT'])) {
                //get pdf version of livecycle form
                $livecycle_form = $application->fnAppendix($document_guid, $appendix_type, $program_year, true);
                if (empty($livecycle_form)) {
                    $load_msg = 'Sorry we are having difficulties loading this form. Error #234985';
                }
            } else {
                $livecycle_form = $application->fnAppendix($document_guid, $appendix_type, $program_year, false);
                if (empty($livecycle_form)) {
                    $load_msg = 'Sorry we are having difficulties loading this form. Please refresh your browser to try again or click on the "Open as PDF" button. Error #234986';
                }
            }
        } else {
            if (! empty($appendix_type)) {
                $livecycle_form = $application->fnAppendix($document_guid, $appendix_type, $program_year, true);
                if (empty($livecycle_form)) {
                    $load_msg = 'Sorry we are having difficulties loading this form. Error #234987';
                }
            } else {
                $livecycle_form = $application->fnAppendix($document_guid, $appendix_type, $program_year, false);

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
            session()->push('DEBUG', now().': applyAppendix() user_name: '.$user_name);
            session()->push('DEBUG', now().': applyAppendix() documentType: '.$appendix_type);
        }

        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR7'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR8'));

        $view = 'lc-appendix';
        if ($appendix_type == 'APPENDIX2') {
            $view = 'lc-appendix2';
        }

        return view($view, ['user_name' => $user_name, 'livecycle_form' => json_encode($livecycle_form), 'load_status' => $load_status, 'load_msg' => $load_msg]);
    }

    /**
     * POST checklist to submit an appendix
     *
     * @param  ApplicationSubmitRequest  $request
     * @param $program_year
     * @param $app_id
     * @param  null  $document_guid
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function confirmSubmitChecklist(ApplicationSubmitRequest $request, $appendix_type, $program_year, $app_id, $document_guid = null)
    {
        $this->uid = session(env('GUID_SESSION_VAR'));

        //does the application belong to the user?
        $this->user = User::where('id', Auth::user()->id)->with('roles')->first();
        $appendix = $this->user->appendix($app_id, $document_guid);
        if (is_null($appendix)) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': confirmSubmitChecklist() appendix does not exist!');
            }

            return redirect('/')->withErrors(['errors' => 'Appendix does not exist!']);
        }

        $documentType = ($appendix_type == 'APPENDIX1') ? 'Appendix 1' : 'Appendix 2';

        $app = new Application();
        $this->uid = session(env('GUID_SESSION_VAR'));

        $submit = $app->fnSubmit(str_replace(' ', '', $documentType), $document_guid, $program_year);

        if (! empty($submit)) {
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR7'));
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR8'));

            return redirect('/appendix-submit-success/'.$app_id.'/'.$appendix_type.'/'.$document_guid);

            if ($this->user->gotSin() === false) {
                return redirect('/declaration/download/'.$appendix_type.'/'.$document_guid.'/'.$app_id);
            }

            return redirect('/student-loans/check-appendix-status/'.$app_id);
        }

        return redirect('/appendix-submit-checklist/'.$appendix_type.'/'.$program_year.'/'.$app_id.'/'.($document_guid == null ? '' : $document_guid));
    }

    /**
     * POST checklist to submit an appendix
     *
     * @param  Request  $request
     * @param $app_id
     * @param $appendix_type
     * @param  null  $document_guid
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function waitAfterAppxSubmit(Request $request, $app_id, $appendix_type, $document_guid = null)
    {
        return view('appendix-submit-delay',
            ['appendix_type' => $appendix_type, 'app_id' => $app_id, 'document_guid' => $document_guid,
                'load_status' => '', 'load_msg' => '', 'submit_msg' => '', 'submit_status' => false]);
    }

    /**
     * POST checklist to submit an appendix
     *
     * @param  Request  $request
     * @param $app_id
     * @param $appendix_type
     * @param  null  $document_guid
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectAfterWait(Request $request, $app_id, $appendix_type, $document_guid = null)
    {
        $this->user = User::where('id', Auth::user()->id)->with('roles')->first();

        if ($this->user->gotSin() === false) {
            return redirect('/declaration/download/'.$appendix_type.'/'.$document_guid.'/'.$app_id);
        }

        return redirect('/student-loans/check-appendix-status/'.$app_id);
    }

    /**
     * GET checklist to submit an application
     *
     * @param  Request  $request
     * @param $appendix_type
     * @param $program_year
     * @param $app_id
     * @param  null  $document_guid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function appendixSubmitChecklist(Request $request, $appendix_type, $program_year, $app_id, $document_guid = null, $prog_year = null)
    {
        $load_status = false;
        $load_msg = '';
        $checklist = null;

        $this->uid = session(env('GUID_SESSION_VAR'));

        //does the application belong to the user?
        $this->user = User::where('id', Auth::user()->id)->with('roles')->first();
        //$application = $this->user->application($app_id);
        $appendix = $this->user->appendix($app_id, $document_guid);
        if (is_null($appendix)) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': appendixSubmitChecklist() appendix does not exist!');
            }

            return redirect('/')->withErrors(['errors' => 'Appendix does not exist!']);
        }

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': appendixSubmitChecklist() program_year: '.json_encode($program_year));
            session()->push('DEBUG', now().': appendixSubmitChecklist() app_id: '.json_encode($app_id));
            session()->push('DEBUG', now().': appendixSubmitChecklist() document_guid: '.json_encode($document_guid));
            session()->push('DEBUG', now().': appendixSubmitChecklist() appendix: '.json_encode($appendix));
        }

        $programYear = $program_year;
        $startYear = (int) substr($programYear, 0, 4);
        $endYear = (int) substr($programYear, -4, 4);
        $docType = $appendix_type == 'APPENDIX1' ? 'appendix 1' : 'appendix 2';
        $appID = $app_id;
        $role = $appendix_type == 'APPENDIX1' ? 'P' : 'S';
        $user_roles = $this->user->roles()->pluck('name')->toArray();
        $noSinUser = false;
        if (in_array('parent_no_sin', $user_roles)) {
            $role = 'NP';
            $noSinUser = true;
        } elseif (in_array('spouse_no_sin', $user_roles)) {
            $role = 'NS';
            $noSinUser = true;
        }

        $r = new Application();
        $econsent_rollout_date = 20200123; // 2020 Jan 23
        $today = date('Ymd', strtotime('today'));
        $today = (int) $today;

        $decRequired = $r->fnIsWebDeclarationInkSignatureRequired($appID, $role);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': appendixSubmitChecklist() noSinUser: '.json_encode($noSinUser));
            session()->push('DEBUG', now().': appendixSubmitChecklist() role: '.json_encode($role));
            session()->push('DEBUG', now().': appendixSubmitChecklist() decRequired: '.json_encode($decRequired));
            session()->push('DEBUG', now().': appendixSubmitChecklist() startYear: '.json_encode($startYear));
            session()->push('DEBUG', now().': appendixSubmitChecklist() endYear: '.json_encode($endYear));
        }

        if (is_null($decRequired) || ! is_object($decRequired) || ! isset($decRequired->inkSignatureRequired)) {
            $load_msg = 'Sorry! An error occurred. Please try again later. Error #10239823';
        } else {
            $programYear = $r->fnValidateProgramYear($programYear);
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': appendixSubmitChecklist() programYear: '.json_encode($programYear));
            }

            //PROGRAM YEAR NOW IS A BOOLEAN
            if ($programYear) {
                $sigReq = $decRequired->inkSignatureRequired;
                if ($role == 'P' || $role == 'NP') {
                    if ($sigReq == 'Y') {
                        $dectype = 'Parent - Signature Required';
                    } else {
                        $dectype = 'Parent - No Signature Required';
                    }
                } elseif ($role == 'S' || $role == 'NS') {
                    if ($sigReq == 'Y') {
                        $dectype = 'Spouse - Signature Required';
                    } else {
                        $dectype = 'Spouse - No Signature Required';
                    }
                }

                if ($role == 'S' && $decRequired->inkSignatureRequired == 'E') {
                    $dectype = 'Spouse - E-Consent';
                }

                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': appendixSubmitChecklist() sigReq: '.$sigReq);
                    session()->push('DEBUG', now().': appendixSubmitChecklist() dectype: '.$dectype);
                }

                //remove the following from returned content
                $s = [
                    '<strong class="font11">STUDENT NAME</strong>',
                    'SIGNATURE OF STUDENT (in ink)',
                    '<strong class="font11">DATE SIGNED</strong>',
                    '"; ?>',
                ];

                $checklist = Declaration::where('program_year', $startYear.'/'.$endYear)->where('type_id', '!=', 'sabc_new_dec')->where('type', $dectype)->where('status', 1)->with('fields')->first();
                if(is_null($checklist)){
                    Log::warning('appendixSubmitChecklist(): startYear: '.$startYear);
                    Log::warning('appendixSubmitChecklist(): endYear: '.$endYear);
                    Log::warning('appendixSubmitChecklist(): dectype: '.$dectype);
                    Log::warning('appendixSubmitChecklist(): role: '.json_encode($role));
                    Log::warning('appendixSubmitChecklist(): noSinUser: '.json_encode($noSinUser));
                    Log::warning('appendixSubmitChecklist(): decRequired: '.json_encode($decRequired));
                    Log::warning('appendixSubmitChecklist(): app_id: '.json_encode($app_id));
                    Log::warning('appendixSubmitChecklist(): document_guid: '.json_encode($document_guid));
                    Log::warning('appendixSubmitChecklist(): appendix: '.json_encode($appendix));

                }

                //spouse new declaration has different structure. applicable only to declarations of startYear >= 2020
                if ($role == 'S' && $decRequired->inkSignatureRequired != 'E' && $startYear >= 2020) {
                    $checklist = Declaration::where('program_year', $startYear.'/'.$endYear)
                        ->where('type_id', 'sabc_new_dec')
                        ->where('type', $dectype)->where('status', 1)->with('fields')->first();
                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': appendixSubmitChecklist() it is a Spouse and inkSign not E and start year >= 2020: ');
                    }
                }
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': appendixSubmitChecklist() 1 is checklist null: '.is_null($checklist));
                }

                $s = [
                    '<strong class="font11">STUDENT NAME</strong>',
                    'SIGNATURE OF STUDENT (in ink)',
                    '<strong class="font11">DATE SIGNED</strong>',
                    '"; ?>',
                ];
                $r = ['', '', '', ''];
                $stack = ['field_consent_req_agreement_text', 'field_consent2_req_agreement', 'field_new_dec_req_agreement_text', 'field_new_dec2_req_agreement', 'field_sig_req_agreement_text'];
                if (! is_null($checklist)) {
                    foreach ($checklist->fields as $f) {
                        if (in_array($f->field_id, $stack)) {
                            $f->field_value = str_replace($s, $r, $f->field_value);
                        }
                    }
                    $load_status = true;
                }
            } else {
                $load_msg = 'Program Year Invalid - Unable to load form. Refresh the page or return the previous page and resubmit. Error #10239824';
            }
        }

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': appendixSubmitChecklist() 2 is checklist null: '.is_null($checklist));
        }

        return view($appendix_type == 'APPENDIX1' ? 'appendix1-submit-checklist' : 'appendix2-submit-checklist',
            ['program_year' => $program_year, 'app_id' => $app_id, 'document_guid' => $document_guid, 'declaration' => json_encode($decRequired),
                'load_status' => $load_status, 'load_msg' => $load_msg, 'checklist' => json_encode($checklist), 'submit_msg' => '', 'submit_status' => false]);
    }

    public function deleteAppendix(Request $request, $app_id, $formGUID)
    {
        $deleteStatus = $this->fnRemoveAppendixReminder($formGUID);

        if ($deleteStatus === true) {
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR7'));

            return redirect('/');
        }

        return redirect('/student-loans/check-appendix-status/'.$app_id)
            ->withErrors(['errors' => 'Appendix failed to delete, please try to delete
			the appendix later or contact a system administration if the problem persists. Error #837422']);
    }

    /*
    *	function fnRemoveReminder - used to remove an appendix reminder that appendix is no longer required
    * 	@params:
    *		$documentGUID: the form id
    */
    private function fnRemoveAppendixReminder($documentGUID)
    {
        $baseURL = $this->fnWS('LC', '');
        $this->uid = session(env('GUID_SESSION_VAR'));

        $url = $baseURL.'/rest/services/SABC_StudentLoan_APIs/Data/Reminders/Remove:1.0?documentGUID='.$documentGUID.'&ownerGUID='.$this->uid;
        $rq = $this->fnRESTRequest($url, 'GET', null, null, null, 'XML');

        //MAKE SURE STATUS CODE IS RETURNED AND THAT THE VALUE IS 200
        if (isset($rq->statusCode) && $rq->statusCode == 200) {
            //CLEAR CACHE FOR GET APPLICATION LIST
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR7'));

            return true;
        }

        return $rq->statusDescription;
    }
}
