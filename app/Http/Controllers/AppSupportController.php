<?php

namespace App\Http\Controllers;

use App\Application;
//use App\SidePage;

use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\MessageBag;
use Response;

class AppSupportController extends Aeit
{
    /**
     * Display the dashboard page to a logged in admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function appSupportDashboard()
    {
        return view('app_support.dashboard', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * Login an SABC student/spouse/parent to the dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function findUser(Request $request)
    {
        $this->validate($request, [
            'environment' => 'required|in:DEV,UAT,STUDENTAIDBC',
            'user_guid' => 'required',
        ]);
        session()->forget(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        session()->forget(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));
        session()->forget(env('ADMIN_SESSION_VAR'));
        session()->put(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'), $request->environment);
        session()->put(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'), $request->user_guid);

        $env = Str::upper($request->environment);
        switch ($request->environment) {
            case 'DEV':
            case 'UAT':
                session()->put(env('ADMIN_SESSION_VAR'), Str::lower($request->environment).'.'.env('APP_DOMAIN'));
                break;
            default:
                $env = 'PROD';
                session()->put(env('ADMIN_SESSION_VAR'), env('APP_DOMAIN'));
        }

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': findUser(): ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR: '.json_encode(session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'))));
            session()->push('DEBUG', now().': findUser(): ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2: '.json_encode(session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'))));
            session()->push('DEBUG', now().': findUser(): ADMIN_SESSION_VAR: '.json_encode(session()->get(env('ADMIN_SESSION_VAR'))));
            session()->push('DEBUG', now().': findUser(): env: '.$env);
        }

        $profile = [];
        $errors = new MessageBag;

        //verify the user
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_VERIFY');
        $verifyAccount = $this->fnRequest('getUserProfile', ['userGUID' => $request->user_guid], 'get_user_profile'.$request->user_guid, 14400);

        //OVERRIDE if we know already that the account is a BCSC account
        if (isset($verifyAccount->userProfile->assuranceLevel)) {
            $this->WSDL = $this->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');
        } else {
            $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_ACCOUNT');
        }

        $usrProfile = $this->fnRequest('getUserProfile', ['userGUID' => $request->user_guid], 'get_user_profile'.$request->user_guid, 14400);

        //MAKE SURE IT IS NOT AN ERROR
        if (! is_null($usrProfile) && ! isset($usrProfile->faultcode)) {
            $usrProfile->status = true;
            $usrProfile->userProfile->SIN = $this->fnEncrypt($usrProfile->userProfile->SIN);
            $usrProfile->userProfile->userGUID = $this->fnEncrypt($usrProfile->userProfile->userGUID);
            $usrProfile->userProfile->userConsent = (! isset($usrProfile->userProfile->userConsent) || $usrProfile->userProfile->userConsent == 'N') ? false : true;
            $usrProfile->hasBcscAccount = isset($verifyAccount->userProfile->assuranceLevel);
            $usrProfile->verified = (! isset($verifyAccount->userProfile->verified) || $usrProfile->userProfile->userConsent == 'N') ? false : true;

            $profile = $usrProfile;
        } elseif (is_null($usrProfile)) {
            // Add new messages to the message bag.
            $errors->add('email', 'Could not find the user!');
        }

        if (isset($verifyAccount->faultcode)) {
            $errors->add('email', $verifyAccount->faultstring);
        }

        return view('app_support.profile', ['errors' => $errors, 'env' => $env, 'user_guid' => $request->user_guid, 'profile' => json_encode($profile), 'verify' => json_encode($verifyAccount), 'roles' => Auth::user()->roles]);
    }

    /**
     * View an already fetched user by GUID profile info.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userProfile(Request $request)
    {
        $profile = [];
        $errors = new MessageBag;
        $env = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        $user_guid = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));
        if (is_null($user_guid)) {
            return redirect('/app_support');
        }

        //verify the user
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_VERIFY');
        $verifyAccount = $this->fnRequest('getUserProfile', ['userGUID' => $user_guid], 'get_user_profile'.$user_guid, 14400);

        //OVERRIDE if we know already that the account is a BCSC account
        if (isset($verifyAccount->userProfile->assuranceLevel)) {
            $this->WSDL = $this->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');
        } else {
            $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_ACCOUNT');
        }

        $usrProfile = $this->fnRequest('getUserProfile', ['userGUID' => $user_guid], 'get_user_profile'.$user_guid, 14400);

        //MAKE SURE IT IS NOT AN ERROR
        if (! is_null($usrProfile) && ! isset($usrProfile->faultcode)) {
            $usrProfile->status = true;
            $usrProfile->userProfile->SIN = $this->fnEncrypt($usrProfile->userProfile->SIN);
            $usrProfile->userProfile->userGUID = $this->fnEncrypt($usrProfile->userProfile->userGUID);
            $usrProfile->userProfile->userConsent = (! isset($usrProfile->userProfile->userConsent) || $usrProfile->userProfile->userConsent == 'N') ? false : true;
            $usrProfile->hasBcscAccount = isset($verifyAccount->userProfile->assuranceLevel);
            $usrProfile->verified = (! isset($verifyAccount->userProfile->verified) || $usrProfile->userProfile->userConsent == 'N') ? false : true;

            $profile = $usrProfile;
        } elseif (is_null($usrProfile)) {
            // Add new messages to the message bag.
            $errors->add('email', 'Could not find the user!');
        }

        if (isset($verifyAccount->faultcode)) {
            $errors->add('email', $verifyAccount->faultstring);
        }

        return view('app_support.profile', ['errors' => $errors, 'env' => $env, 'user_guid' => $user_guid, 'profile' => json_encode($profile), 'verify' => json_encode($verifyAccount), 'roles' => Auth::user()->roles]);
    }

    /**
     * Show view page for applications or application details.
     *
     * @param  Request  $request
     * @param  null  $application_number
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showApplications(Request $request, $application_number = null)
    {
        $errors = new MessageBag;
        $env = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        $user_guid = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));

        if (! is_null($application_number)) {
            return view('app_support.application-detail', ['errors' => $errors, 'app_number' => $application_number, 'user_guid' => $user_guid, 'env' => $env, 'roles' => Auth::user()->roles]);
        }

        return view('app_support.applications', ['errors' => $errors, 'user_guid' => $user_guid, 'env' => $env, 'roles' => Auth::user()->roles]);
    }

    /**
     * Fetch list of applications.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function fetchApplications(Request $request)
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fetchApplications(): start');
        }
        $errors = new MessageBag;
        $env = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        $user_guid = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));

        $user = new User();
        $user->aeit->uid = $user_guid;
        $apps = $user->fnGetApplications();
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fetchApplications(): env: '.$env);
            session()->push('DEBUG', now().': fetchApplications(): user_guid: '.$user_guid);
            session()->push('DEBUG', now().': fetchApplications(): this->uid: '.$this->uid);
        }
        return Response::json(['applications' => $apps], 200); // Status code here
    }

    /**
     * Fetch single application detail.
     *
     * @param  Request  $request
     * @param $application_number
     * @return mixed
     */
    public function fetchApplicationDetail(Request $request, $application_number)
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fetchApplicationDetail(): start');
        }
        $errors = new MessageBag;
        $env = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        $user_guid = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));

        $user = new User();
        $user->aeit->uid = $user_guid;
        $app = $user->fnGetApplicationDetails($application_number);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fetchApplicationDetail(): env: '.$env);
            session()->push('DEBUG', now().': fetchApplicationDetail(): user_guid: '.$user_guid);
            session()->push('DEBUG', now().': fetchApplicationDetail(): this->uid: '.$this->uid);
        }
        return Response::json(['application' => $app], 200); // Status code here
    }

    /**
     * Show view page for appendix list or single appendix details.
     *
     * @param  Request  $request
     * @param  null  $application_number
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showAppendixList(Request $request, $application_number = null)
    {
        $errors = new MessageBag;
        $env = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        $user_guid = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));

        if (! is_null($application_number)) {
            return view('app_support.appendix-detail', ['errors' => $errors, 'app_number' => $application_number, 'user_guid' => $user_guid, 'env' => $env, 'roles' => Auth::user()->roles]);
        }

        return view('app_support.appendix_list', ['errors' => $errors, 'user_guid' => $user_guid, 'env' => $env, 'roles' => Auth::user()->roles]);
    }

    /**
     * Fetch list of appendix list.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function fetchAppendixList(Request $request)
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fetchAppendixList(): start');
        }
        $errors = new MessageBag;
        $env = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        $user_guid = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));

        $user = new User();
        $user->aeit->uid = $user_guid;
        $apps = $user->fnGetAppendixList();
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fetchAppendixList(): env: '.$env);
            session()->push('DEBUG', now().': fetchAppendixList(): user_guid: '.$user_guid);
            session()->push('DEBUG', now().': fetchAppendixList(): this->uid: '.$this->uid);
        }
        return Response::json(['appendix_list' => $apps], 200); // Status code here
    }

    /**
     * Fetch single application detail.
     *
     * @param  Request  $request
     * @param $application_number
     * @return mixed
     */
    public function fetchAppendixDetail(Request $request, $application_number)
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fetchAppendixDetail(): start');
        }
        $errors = new MessageBag;
        $env = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        $user_guid = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));

        $user = new User();
        $user->aeit->uid = $user_guid;
        $app = $user->fnGetAppendixDetails('', $application_number);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fetchAppendixDetail(): env: '.$env);
            session()->push('DEBUG', now().': fetchAppendixDetail(): user_guid: '.$user_guid);
            session()->push('DEBUG', now().': fetchAppendixDetail(): this->uid: '.$this->uid);
        }

        if (! is_null($app)) {
            $app_details = new Application();

            //TIMELINE EVENTS
            $event = ['Start/Submit Appendix' => ['title' => 'Start/Submit Appendix', 'class' => 'icon-addtolist'],
                'Submit Appendix Declaration' => ['title' => 'Submit Appendix Declaration', 'class' => 'icon-uniF47C']];

            $tl = (isset($app->appendixTimeline)) ? (array) $app->appendixTimeline : null;
            $eventItems = null;
            $isWebDeclarationInkSignatureRequired = null;
            $processing_consent = null;
            $show_econsent = null;
            if (isset($app->applicationNumber) && $app->applicationNumber > 0 && ! empty($tl)) {
                //GET ALL OUR EVENTS IN SORT THEM IN ARRAY
                $eventItems = $app_details->fnLoadAppendixEvents($app->appendixTimeline->EventCategories->eventCategory, $app->formType, $app->applicationNumber, $app->apxStatus);
                //STATUS FLAGS TO TELL US WHERE THE APPLICATION IS AT

                $user->aeit->WSDL = $user->aeit->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

                $isWebDeclarationInkSignatureRequired = $user->aeit->fnRequest('isWebDeclarationInkSignatureRequired', ['applicationNumber' => $app->applicationNumber, 'role' => 'S', 'userGUID' => $user->aeit->uid], null, 100);
                [$processing_consent, $show_econsent] = $user->aeit->fetchGetWebDeclaration('S', $app->applicationNumber, $user->aeit->uid);
            }

            return Response::json(['application' => $app, 'details' => $eventItems,
                'isWebDeclarationInkSignatureRequired' => $isWebDeclarationInkSignatureRequired,
                'processing_consent' => $processing_consent, 'show_econsent' => $show_econsent], 200); // Status code here
        }
        return Response::json(['appendix' => $app], 200); // Status code here
    }

    /**
     * Access an SABC student/spouse/parent dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function access(Request $request)
    {
        $request->environment = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        $request->user_guid = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));
        $user = $this->fnSystemLogin($request);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': access(): user: '.json_encode($user));
        }

        if (! empty($user)) {
            $login = $this->fnDashboardLogin($user['uid'], $user['login_name'], 'student', $request);
            if ($login == 'dashboard') {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': access(): 2 user: '.json_encode($user));
                }
                session()->put('read-only', true);
                Session::save();

                return redirect('/');
            }
            redirect('/app_support');
        }

        return redirect()->back()->withInput()->withErrors(['guid' => 'We recently made some changes to enhance the security of our online services.  These changes require you to update your user profile.  Please fill in the required fields.']);
    }

    /*
    *		USED TO VALIDATE USER/ RETRIEVE USERS GUID FROM THE IDENTITY MANAGEMENT STORE
    *		@params: $u (username) $p (password)
    *		@return: $user which is an array that contains users GUID and full name
    */

    private function fnSystemLogin(Request $request)
    {
        $env = $request->environment;
        $up = null;
        $config = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), null);
        if (is_null($config)) {
            $config = parse_ini_file(config_path().env('APP_CONFIG_FILE'), true);
            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), $config, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
        }
        define('visitor_environment', $env);
        $this->uid = $request->user_guid;
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_ACCOUNT');
        $usrProfile = $this->fnRequest('getUserProfile', ['userGUID' => $this->uid], 'get_user_profile'.$this->uid, 14400);
        if (! isset($usrProfile->faultcode)) {
            $usrProfile->status = true;
            $usrProfile->userProfile->SIN = $this->fnEncrypt($usrProfile->userProfile->SIN);
            $usrProfile->userProfile->userGUID = $this->fnEncrypt($usrProfile->userProfile->userGUID);
            $usrProfile->userProfile->userConsent = (! isset($usrProfile->userProfile->userConsent) || $usrProfile->userProfile->userConsent == 'N') ? false : true;

            $up = $usrProfile;
        } else {
            $this->WSDL = $this->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');
            $usrProfile = $this->fnRequest('getUserProfile', ['userGUID' => $this->uid], 'get_user_profile'.$this->uid, 14400);
            if (! isset($usrProfile->faultcode)) {
                $usrProfile->status = true;
                $usrProfile->userProfile->SIN = $this->fnEncrypt($usrProfile->userProfile->SIN);
                $usrProfile->userProfile->userGUID = $this->fnEncrypt($usrProfile->userProfile->userGUID);
                $usrProfile->userProfile->userConsent = (! isset($usrProfile->userProfile->userConsent) || $usrProfile->userProfile->userConsent == 'N') ? false : true;

                $up = $usrProfile;
            } else {
                //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
                if (isset($usrProfile->detail->ProfileFault)) {
                    $errors = [];
                    $errors['status'] = false;
                    $errors['msg'] = $usrProfile->getMessage();
                    $errors['username'] = $usrProfile->getMessage();

                    $up = $errors;
                } //NOT A VALID PROFILE ERROR SO TRIGGER SYSTEM ERROR
                else {
                    $this->fnError('SYSTEM ERROR :: USER_ACCOUNT ->getUserProfile', $usrProfile->getMessage(), $usrProfile, $triggerDefault = true);
                }
            }
        }
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnSystemLogin(): up: '.json_encode($up));
        }

        if (! empty($up->status)) {
            $user['status'] = true;
            $user['uid'] = $this->fnEncrypt($this->uid);

            $name = isset($up->userProfile->firstName) ? $up->userProfile->firstName : '';
            if ($name != '') {
                $name .= ' ';
            }
            $name .= isset($up->userProfile->familyName) ? $up->userProfile->familyName : '';
            $user['login_name'] = $name;
            $_SESSION['admin_login'] = $this->uid;
            $_SESSION[env('ADMIN_SESSION_VAR')] = $config['ENVIRONMENTS'][$env];

            return $user;
        }

        return $up;
    }

    public function loginSabcUser(Request $request)
    {
        $this->validate($request, [
            'environment' => 'required|in:DEV,UAT,STUDENTAIDBC',
            'user_id' => 'required',
            'password' => 'required',
        ]);
        $config = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), null);
        if (is_null($config)) {
            $config = parse_ini_file(config_path().env('APP_CONFIG_FILE'), true);
            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), $config, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
        }
        session()->forget(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        session()->forget(env('ADMIN_SESSION_VAR'));
        session()->put(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'), $request->environment);

        session()->put(env('ADMIN_SESSION_VAR'), $config['ENVIRONMENTS'][$request->environment]);

        $backup_admin_user = User::find(Auth::user()->id);

        $user = Auth::user();
        Auth::logout($user);

        $values = $this->fnLogin($request);
        session()->push('DEBUG', now().': loginSabcUser(): values: '.json_encode($values));

        //we could not login the user
        if (Auth::check() == false) {
            Auth::login($backup_admin_user);

            return redirect()->back()->withInput()->withErrors(['user_id' => $values['username']]);
        } else {
            //we know we can login using provided credentials

            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'));
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'));
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR3'));
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR4'));
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR5'));
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR6'));
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR7'));
            Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR8'));

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Session::forget(env('GUID_SESSION_VAR'));
            Session::forget(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
            Session::forget(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));
            Session::forget(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR3'));
            Session::forget('DEBUG');
            Session::forget('read-only');

            $values = $this->fnLogin($request);
            session()->push('DEBUG', now().': loginSabcUser(): 2nd login: '.json_encode($values));

            if (Auth::check()) {
                session()->push('DEBUG', now().': loginSabcUser(): Auth check');
                $user = User::find(Auth::user()->id);

                if ($user->isStudentSpouseParent() == true) {
                    session()->push('DEBUG', now().': loginSabcUser(): isStudentSpouseParent');

                    if ($values === false) {
                        session()->push('DEBUG', now().': loginSabcUser(): values === FALSE. profile updates is required');

                        return redirect('/profile');
                    }
                    session()->push('DEBUG', now().': loginSabcUser(): isStudentSpouseParent and redirect to dashboard');

                    return redirect('/');
                }
            } else {
                session()->push('DEBUG', now().': loginSabcUser(): Auth check failed');
                if (is_array($values) && isset($values['status']) && $values['status'] === false) {
                    session()->push('DEBUG', now().': login(): Auth check failed values: '.json_encode($values));

                    return redirect()->back()->withInput()->withErrors(['username' => $values['username']]);
                }
            }

            return redirect('/login');
        }
    }

    private function fnDashboardLogin($uid, $userName, $userRole = 'student', $request = null)
    {
        $guid = $this->fnDecrypt($uid);

        $this->uid = $uid;
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnDashboardLogin(): encrypted guid: '.$this->uid);
        }

        if (isset($userName)) {
            $env = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
            $bcscusrProfile = $this->fnGetBCSCUserProfile();
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnDashboardLogin(): bcscusrProfile: '.json_encode($bcscusrProfile));
                session()->push('DEBUG', now().': fnDashboardLogin(): guid: '.$guid);
                session()->push('DEBUG', now().': fnDashboardLogin(): env: '.$env);
            }
            switch ($env) {
                case 'DEV': $on = 'pgsql_dev';
                break;
                case 'UAT': $on = 'pgsql_uat';
                break;
                default: $on = 'pgsql_prod';
            }

            $this->user = User::on($on)->where('name', $this->uid)->with('roles')->first();
            if (is_null($this->user)) {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnDashboardLogin(): we could not find the user');
                    session()->push('DEBUG', now().': fnDashboardLogin(): on: '.$on);
                }

                return false;
            }

            if (isset($bcscusrProfile->userProfile)) {
                $assuranceLevel1 = $bcscusrProfile->userProfile->assuranceLevel;
                $userRole = 'bcsc_student';

                $userProfile = $this->fnBcscLogin();
            } else {
                $assuranceLevel1 = null;
                $userProfile = $this->fnGetUserProfile($guid);
            }

            $logout = new \App\Http\Controllers\UserController();
            $logout->logoutUser($request);

            Auth::login($this->user);
            session([env('GUID_SESSION_VAR') => $guid, env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR3') => Auth::user()->email]);
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnDashboardLogin(): userProfile: '.json_encode($userProfile));
                session()->push('DEBUG', now().': fnDashboardLogin(): session GUID_SESSION_VAR: '.session(env('GUID_SESSION_VAR')));
                session()->push('DEBUG', now().': fnDashboardLogin(): session ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR3: '.session(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR3')));
            }

            return 'dashboard';
        } else {
            return false;
        }
    }

    /**
     * Fetch list of applications.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fetchNotification(Request $request, $notificationId, $inTransition = null, $documentType = null)
    {
        $aeit = new Aeit();
        $errors = new MessageBag;
        $env = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        $aeit->uid = session()->get(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));

        return $aeit->fnGetDocument($notificationId, $inTransition, $documentType);
    }
}
