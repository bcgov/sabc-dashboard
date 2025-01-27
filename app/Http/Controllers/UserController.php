<?php

namespace App\Http\Controllers;

use App\Application;
use App\Http\Requests\AjaxRequest;
use App\Http\Requests\BcscCreateRequest;
use App\Http\Requests\FileUploadRequest;
use App\Http\Requests\SabcCreateRequest;
use App\Http\Requests\UpdateBcscProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Role;
use App\User;
use Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Redirect;
use Response;

class UserController extends Aeit
{
    use ThrottlesLogins;

    /**
     * The maximum number of attempts to allow.
     *
     * @return int
     */
    protected $maxAttempts = 5;

    /**
     * The number of minutes to throttle for.
     *
     * @return int
     */
    protected $decayMinutes = 5;

    public function username()
    {
        return 'name';
    }

    /**
     * Logout user, remove all cache data and invalidate the session
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logoutUser(Request $request)
    {
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR3'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR4'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR5'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR6'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR7'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR8'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR9'));
        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'));

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Session::forget(env('GUID_SESSION_VAR'));
        Session::forget('bcsc_profile');
        Session::forget(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR'));
        Session::forget(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR2'));
        Session::forget(env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR3'));
        Session::forget('DEBUG');
        Session::forget('read-only');
        //unset($_SESSION['read-only']);

        return redirect('/login');
    }

    public function clearDebugger(Request $request)
    {
        Session::forget('DEBUG');

        return redirect()->back();
    }

    public function getChallengeQuestions(Request $request)
    {
        $user = new User();
        return Response::json(['questions' => $user->fnGetChallengeQuestions()], 200); // Status code here
    }

    /*
    * @param  \Illuminate\Http\Request  $request
    */
    public function registerSabcUser(SabcCreateRequest $request)
    {

        $user = new User();
        [$status, $msg, $guid] = $user->fnCreateUserProfile($request);

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': registerSabcUser(): status: '.$status);
            session()->push('DEBUG', now().': registerSabcUser(): msg: '.$msg);
            session()->push('DEBUG', now().': registerSabcUser(): guid: '.$guid);
        }

        if ($status == true) {
            $next_uid = User::select('uid')->orderBy('uid', 'desc')->first();
            $old_uid = User::select('uid')->where('name', 'like', $this->fnEncrypt($guid))->first();
            $user->uid = $next_uid->uid + 1;

            //if the user has an account recreate it with the same UID
            if (! is_null($old_uid)) {
                $user->uid = $old_uid->uid;
            }

            $user->name = $this->fnEncrypt($guid);
            $user->email = $request->email;
            $user->password = $user->user_hash_password($request->password, DRUPAL_HASH_COUNT);
            $user->created = strtotime('now');
            $user->status = 1;
            $user->save();

            if (! isset($request->role_type)) {
                $request->merge([
                    'role_type' => 'student',
                ]);
            }
            $role = Role::where('name', $request->role_type)->first();
            $user->roles()->attach($role);

            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': registerSabcUser(): user: '.$request->user_id);
                session()->push('DEBUG', now().': registerSabcUser(): pass: '.$request->password);
            }

            return $this->login($request);
        }

        return redirect()->back()->withInput()->withErrors(['errors' => $msg]);
    }

    /**
     * @param $user = User::find(x)
     * @param $user_profile: first element in the array returned from fnGetBCSCUserProfile()
     *
     * @uid: fnDecrypt(BCSC DID)
     * @role: bcsc_student|bcsc_spouse|bcsc_parent
     */
    public function bcscLogin(Request $request, $user, $user_profile, $uid, $role)
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': bcscLogin(): user: '.json_encode($user));
            session()->push('DEBUG', now().': bcscLogin(): role: '.$role);
            session()->push('DEBUG', now().': bcscLogin(): user_profile: '.json_encode($user_profile));
            session()->push('DEBUG', now().': bcscLogin(): uid: '.$uid);

            Session::save();

            Log::warning('bcscLogin(): user: '.json_encode($user));
            Log::warning(': bcscLogin(): role: '.$role);
            Log::warning(': bcscLogin(): user_profile: '.json_encode($user_profile));
            Log::warning(': bcscLogin(): uid: '.$uid);
            Log::warning(': bcscLogin(): is_null(user_profile): '.is_null($user_profile));
        }

        $values = [];
        //GET USER ACCOUNT DETAILS - LOOKING FOR FIRSTNAME AND FAMILY NAME TO DISPLAY ON THE DASHBOARD

        if (is_null($user_profile) || ! isset($user_profile->status) || $user_profile->status === false) {
            //failed to fetch user profile data (most likely this is an admin user
            $values = ['status' => false, 'username' => 'Failed to login using BCSC!'];
        } else {
            $this->user = User::where('name', $user_profile->userProfile->userGUID)->with('roles')->first();

            //if the e-service returned a user but we don't have a user in our immediate DB
            //then create the user
            if (! is_null($uid) && is_null($this->user)) {
                $next_uid = User::select('uid')->orderBy('uid', 'desc')->first();
                $old_uid = User::select('uid')->where('name', 'like', $user_profile->userProfile->userGUID)->first();

                $user = new User();
                $user->uid = $next_uid->uid + 1;

                //if the user has an account recreate it with the same UID
                if (! is_null($old_uid)) {
                    $user->uid = $old_uid->uid;
                }

                $user->name = $user_profile->userProfile->userGUID;
                $user->password = $user->user_hash_password(Str::random(DRUPAL_HASH_COUNT));
                $user->created = strtotime('now');
                $user->status = 1;
                $user->save();

                $user_role = Role::where('name', $role)->first();
                $user->roles()->attach($user_role);

                $this->user = User::where('name', $user_profile->userProfile->userGUID)->with('roles')->first();
            }
            if (! is_null($this->user)) {
                Auth::login($this->user);
                session([env('GUID_SESSION_VAR') => $uid]);

                $name = '';
                [$returned_name, $returned_status] = $this->checkBcscUpdates($user_profile);
                if ($returned_status == true) {
                    $name = $returned_name;
                    $values = ['uid' => $uid, 'name' => $name, 'status' => true];
                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': bcscLogin(): this->user->isStudentSpouseParent(): '.$this->user->isStudentSpouseParent());
                        session()->push('DEBUG', now().': bcscLogin(): is auth: '.Auth::check());
                    }
                } else {
                    $values = ['status' => false, 'username' => $returned_name];
                }
            } else {
                $values = ['status' => false, 'username' => 'LV1: User does not exist'];
            }

            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': bcscLogin(): values: '.json_encode($values));
            }
        }

        if (is_null($this->user) && env('LOG_ESERVICES') == true) {
            Log::warning('bcscLogin(): user: '.json_encode($user));
            Log::warning('bcscLogin(): this->user: '.json_encode($this->user));
            Log::warning(': bcscLogin(): role: '.$role);
            Log::warning(': bcscLogin(): user_profile: '.json_encode($user_profile));
            Log::warning(': bcscLogin(): uid: '.$uid);
            Log::warning(': bcscLogin(): is_null(user_profile): '.is_null($user_profile));
        }

        //if DEBUG is ON authentication will fail because there should not be any output before
        if (Auth::check() && ! is_null(Auth::user()) && Auth::user()->id == $this->user->id) {
            if ($this->user->isStudentSpouseParent() == true) {
                if (empty($values)) {
                    return redirect('/profile');
                } elseif (is_array($values) && $values['status'] === false) {
                    return redirect('/profile');
                }

                return redirect('/');
            }
        } else {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().'bcscLogin(): User account does not exist LV1');
            }

            if (isset($values['status']) && $values['status'] === false) {
                return redirect('/login')->withErrors(['username' => $values['username']]);
            }
        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form.
        return redirect('/login?msg=invalid');
    }

    public function createBcscUser(BcscCreateRequest $request)
    {
        if (! session()->exists('BCSCSAML')) {
            return redirect('/login');
        }

        $bcsc_saml = json_decode(session()->pull('BCSCSAML'));
        $bcsc_saml = json_decode(json_encode($bcsc_saml->data), 1);
        $uid = $bcsc_saml['DID'];
        //global $user;
        $uid2 = $this->fnDecrypt($uid);
        $userName = $bcsc_saml['userdisplayname'];

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().' createBcscUser(): encrypted guid uid: '.$uid);
            session()->push('DEBUG', now().' createBcscUser(): decrypted guid uid2: '.$uid2);

            Session::save();
        }

        $next_uid = User::select('uid')->orderBy('uid', 'desc')->first();
        $old_uid = User::select('uid')->where('name', 'like', $uid)->first();

        $user = new User();
        $user->uid = $next_uid->uid + 1;

        //if the user has an account recreate it with the same UID
        if (! is_null($old_uid)) {
            $user->uid = $old_uid->uid;
        }

        $user->name = $uid;
        $user->email = null;
        $user->password = $user->user_hash_password(rand(), DRUPAL_HASH_COUNT);
        $user->created = strtotime('now');
        $user->status = 1;

        $user->save();

        //flush the session output
        Session::save();

        if (! isset($request->role_type)) {
            $request->merge([
                'role_type' => 'bcsc_student',
            ]);
        }
        $role = Role::where('name', $request->role_type)->first();
        $user->roles()->attach($role);
        [$status, $msg, $guid] = $user->fnCreateBcscUserProfile($request);

        if ($status == false) {
            return redirect('/login')->withErrors(['username' => 'SYSTEM ERROR :: failed to create account. Profile Fault error #100431-3.']);
        }

        Auth::login($user);

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': createBcscUser(): is auth: '.Auth::check());
        }

        [$user, $bcsc_profile, $create_account, $uid3] = $user->fnBcscDashboardLogin($uid);
        if (strlen($uid) > 40) {
            session([env('GUID_SESSION_VAR') => $uid3]);
        } else {
            session([env('GUID_SESSION_VAR') => $uid]);
        }
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': createBcscUser(): role: '.$role);
            session()->push('DEBUG', now().': createBcscUser(): user: '.json_encode($user));
            session()->push('DEBUG', now().': createBcscUser(): bcsc_profile: '.json_encode($bcsc_profile));
            session()->push('DEBUG', now().': createBcscUser(): create_account: '.$create_account);
            session()->push('DEBUG', now().': createBcscUser(): uid3: '.$uid3);

            Log::warning('createBcscUser(): role: '.$role);
            Log::warning(': createBcscUser(): user: '.json_encode($user));
            Log::warning(': createBcscUser(): bcsc_profile: '.json_encode($bcsc_profile));
            Log::warning(': createBcscUser(): create_account: '.$create_account);
            Log::warning(': createBcscUser(): uid3: '.$uid3);
        }
        //flush the session output
        Session::save();

        return $this->bcscLogin($request, $user, $bcsc_profile, $uid3, $role);
    }

    public function fetchCountries(Request $request)
    {
        $config = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), null);
        if (is_null($config)) {
            $config = parse_ini_file(config_path().env('APP_CONFIG_FILE'), true);
            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), $config, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
        }
        $countries = $config['COUNTRIES'];
        $provinces = $config['PROVINCES'];
        $states = $config['STATES'];

        return Response::json(['countries' => $countries, 'provinces' => $provinces, 'states' => $states], 200); // Status code here
    }

    /**
     * Receive a response from BCSC to a user attempt to login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bcscHook(Request $request)
    {
        if (isset($_REQUEST['SAMLResponse'])) {
            //ATTEMPT LOG USER INTO DASHBOARD
            $u = new User();
            $resp = $u->fnBCSCSAMLParser($_REQUEST['SAMLResponse']);
            $role = 'bscs_student';

            $did = $resp['DID'];

            if (isset($_SESSION['destination'])) {
                define('APPENDIXPATTERN', "/^dashboard\/appendix\/claim/");

                //get the url redirect/destination of the user logging in
                $destination_args = explode('/', $_SESSION['destination']);

                //match for appendix url
                if (preg_match(APPENDIXPATTERN, $_SESSION['destination'], $matches, PREG_OFFSET_CAPTURE)) {
                    //get appendix type
                    $appendixType = $destination_args[4];
                    //need to auto validate appendix claim code for user if it is already provided
                    if (isset($destination_args[5]) && ! empty($destination_args[5])) {
                        //only try to claim if we know it is an APPENDIX1 or APPENDIX2
                        if (isset($destination_args[3]) && $destination_args[4] == 'APPENDIX1') {
                            $role = 'bcsc_parent';
                        } elseif (isset($destination_args[3]) && $destination_args[4] == 'APPENDIX2') {
                            $role = 'bcsc_spouse';
                        }
                    }
                } else {
                    $role = 'bcsc_student';
                }
            } else {
                $role = 'bcsc_student';
            }

            $_SESSION['SAMLResponse'] = $_REQUEST['SAMLResponse'];

            //this will return either bcsc create account view or info to login the user
            [$user, $bcsc_profile, $create_account, $uid] = $u->fnBcscDashboardLogin($did);
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': bcscHook(): role: '.$role);
                session()->push('DEBUG', now().': bcscHook(): user: '.json_encode($user));
                session()->push('DEBUG', now().': bcscHook(): bcsc_profile: '.json_encode($bcsc_profile));
                session()->push('DEBUG', now().': bcscHook(): create_account: '.$create_account);
                session()->push('DEBUG', now().': bcscHook(): uid: '.$uid);

                Log::warning(': bcscHook(): role: '.$role);
                Log::warning(': bcscHook(): user: '.json_encode($user));
                Log::warning(': bcscHook(): bcsc_profile: '.json_encode($bcsc_profile));
                Log::warning(': bcscHook(): create_account: '.$create_account);
                Log::warning(': bcscHook(): uid: '.$uid);
            }

            if ($create_account == true) {
                session()->push('DEBUG', now().': bcscHook(): create_account: is true');
                session()->put('BCSCSAML', json_encode(['data' => $resp, 'role' => $role]));

                return view('auth.logins.create-bcsc-account', ['saml' => json_encode(['data' => $resp]), 'role' => $role, 'submit_status' => null, 'submit_msg' => null]);

            } else {
                return $this->bcscLogin($request, $user, $bcsc_profile, $uid, $role);
            }
        }

        return redirect('/login');
    }

    public function showCreateBcscUser(Request $request)
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': showCreateBcscUser(): begins');
        }

        if (session()->exists('BCSCSAML')) {
            session()->push('DEBUG', now().': showCreateBcscUser(): BCSCSAML exists');
            $bcsc_saml = json_decode(session()->pull('BCSCSAML'));
            session()->push('DEBUG', now().': showCreateBcscUser(): bcsc_saml->data: '.json_encode($bcsc_saml->data));
            session()->push('DEBUG', now().': showCreateBcscUser(): bcsc_saml->role: '.$bcsc_saml->role);
            session()->put('BCSCSAML', json_encode(['data' => $bcsc_saml->data, 'role' => $bcsc_saml->role]));

            return view('auth.logins.create-bcsc-account', ['saml' => json_encode(['data' => $bcsc_saml->data]), 'role' => $bcsc_saml->role, 'submit_status' => null, 'submit_msg' => null]);
        }

        return redirect('/login');
    }

    /**
     * Login an SABC student/spouse/parent to the dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sabcLogin(Request $request)
    {
        return $this->login($request);
    }

    private function login($request)
    {
        $values = $this->fnLogin($request);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': login(): values: '.json_encode($values));
        }

        /** This line should be in the start of method */
        if ($this->hasTooManyLoginAttempts($request)) {
            session()->push('DEBUG', now().': login(): hasTooManyLoginAttempts');
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if (Auth::check()) {
            session()->push('DEBUG', now().': login(): Auth check');
            $user = User::find(Auth::user()->id);
            if ($user->isStudentSpouseParent() == true) {
                session()->push('DEBUG', now().': login(): isStudentSpouseParent');
                //return redirect()->intended('dashboard');
                //if $values === false then profile update is required
                $this->clearLoginAttempts($request);
                if ($values === false) {
                    session()->push('DEBUG', now().': login(): values === FALSE. profile updates is required');

                    return redirect('/profile');
                }
                session()->push('DEBUG', now().': login(): isStudentSpouseParent and redirect to dashboard');

                return redirect('/');
            }
        } else {
            session()->push('DEBUG', now().': login(): Auth check failed');
            if (is_array($values) && isset($values['status']) && $values['status'] === false) {
                session()->push('DEBUG', now().': login(): Auth check failed values: '.json_encode($values));
                // If the login attempt was unsuccessful we will increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
                $this->incrementLoginAttempts($request);

                return redirect()->back()->withInput()->withErrors(['username' => $values['username']]);
            }
        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return redirect('/login');
    }

    /*
     * Admin/App_Support type of users login
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function sabcAdminLogin(Request $request)
    {
        $user = User::where('name', 'like', $request->user_id)->first();

        /** This line should be in the start of method */
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if (! is_null($user)) {
            $check = $user->user_check_password($request->password, $user);
            if ($check == true) {

                if ($user->isAdmin() == true) {
                    Auth::login($user);
                    $this->clearLoginAttempts($request);

                    return redirect('/admin');
                }
                if ($user->isAdmin() == false && $user->isAppSupport() == true) {
                    Auth::login($user);
                    $this->clearLoginAttempts($request);

                    return redirect('/app_support');
                }

                // If the login attempt was unsuccessful we will increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
                $this->incrementLoginAttempts($request);

                return redirect()->back()->withInput()->withErrors(['user_id' => 'User provided is not Admin']);

            } else {
                // If the login attempt was unsuccessful we will increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
                $this->incrementLoginAttempts($request);

                return redirect()->back()->withInput()->withErrors(['user_id' => 'User info provided does not match our records', 'password' => 'Invalid credentials provided.']);
            }
        }
        $this->incrementLoginAttempts($request);

        return redirect()->back()->withInput()->withErrors(['user_id' => 'User not found!']);
    }

    /**
     * @param  Request  $request
     * @param $document_guid
     * @param $document_type
     * @param $application_id
     */
    public function resendAppendixEmail(Request $request, $document_guid, $appendix_type, $application_id)
    {
        $docType = ucwords(strtolower($appendix_type));

        $baseURL = $this->fnWS('LC', '');

        $url = $baseURL.'/rest/services/SABC_StudentLoan_APIs/Messaging/Resend:1.0?documentGUID='.$document_guid.'&documentTypeID='.strtoupper($docType);
        $rq = $this->fnRESTRequest($url, 'GET', null, null, null, 'XML');

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': resendAppendixEmail(): docType: '.$docType);
            session()->push('DEBUG', now().': resendAppendixEmail(): document_guid: '.$document_guid);
            session()->push('DEBUG', now().': resendAppendixEmail(): url: '.$url);
            session()->push('DEBUG', now().': resendAppendixEmail(): rq->result: '.$rq->result);
        }
        //MAKE SURE STATUS CODE IS RETURNED AND THAT THE VALUE IS 200
        if (isset($rq->result) && $rq->result == 200) {
            return redirect('/student-loans/check-application-status/'.$application_id)->with('resend_success', $docType.' reminder successfully resent!');
        }

        return redirect('/student-loans/check-application-status/'.$application_id)->with('resend_error', 'Failed to re-send '.$docType.' reminder.');
    }

    /**
     * @param  Request  $request
     * @param $appendix_type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function appendixClaimViaUrl(Request $request, $program_year, $appendix_type, $access_code)
    {
        $return = $this->logoutUser($request);

        //add an intend to redirect the user after login
        session()->put('url.intended', url('/appendix/claim/'.$appendix_type.'/'.$access_code));
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': appendixClaimViaUrl(): url.intended: '.session()->get('url.intended'));
        }

        return redirect('/appendix/claim/'.$appendix_type.'/'.$access_code);
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bcscRequired(Request $request)
    {
        return view('bcsc-required');
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function interestFree(Request $request)
    {
        return view('interest-free');
    }

    /**
     * Display the dashboard page to a logged in Student/Spouse/Parent
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function dashboard()
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': dashboard(): url.intended: '.session()->get('url.intended'));
        }

        if (Str::contains(session()->get('url.intended'), 'dashboard/appendix/claim')) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': dashboard(): redirect to claim');
            }
            $url = session()->get('url.intended');
            session()->forget('url.intended');

            return redirect($url);
        }
        session()->push('DEBUG', now().': dashboard(): return dashboard view');

        return view('dashboard');
    }

    /**
     * Display the profile page to a logged in Student/Spouse/Parent
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile()
    {
        return view('profile', ['submit_status' => null, 'submit_msg' => null]);
    }

    /**
     * Display the notifications page to a logged in Student/Spouse/Parent
     *
     * @return $notificationId|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function notifications(Request $request)
    {
        return view('notifications');
    }

    /**
     * Show a single notification page to a logged in Student/Spouse/Parent
     *
     * @return $notificationId|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fetchNotification(Request $request, $notificationId, $inTransition = null, $documentType = null)
    {
        $this->uid = session(env('GUID_SESSION_VAR'));

        return $this->fnGetDocument($notificationId, $inTransition, $documentType);
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function studentApply(Request $request)
    {
        ini_set('memory_limit', '-1');

        $verificationMethod = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR5'), null);
        $isUserVerified = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR6'), null);
        if (is_null($verificationMethod)) {
            [$isUserVerified, $verificationMethod] = $this->verifyUser();
            //$user_profile = $user->profile;
            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR5'), $verificationMethod, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR6'), $isUserVerified, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
        }

        if ($isUserVerified != true && $isUserVerified != 1) {
            return redirect('/student-loans/verification/'.$verificationMethod);
        }
        return view('student-apply');
    }

    public function verifyUser()
    {
        $this->uid = session(env('GUID_SESSION_VAR'));
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': verifyUser(): uid: '.$this->uid);
            Session::save();
        }

        $userProfile = new User();
        $isUserVerified = $userProfile->fnVerifyUser();
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': verifyUser(): isUserVerified: '.$isUserVerified);
            Session::save();
        }
        $verificationMethod = $userProfile->fnVerificationMethod();
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': verifyUser(): verificationMethod: '.$verificationMethod);
            Session::save();
        }

        return [$isUserVerified, $verificationMethod];
    }

    /**
     * Display the file uploads page to a logged in Student/Spouse/Parent
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function uploads()
    {
        return view('file-uploads');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Requests\AjaxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function fetchDashboardData(AjaxRequest $request)
    {
        $user = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), null);
        if (is_null($user)) {
            $user = User::select('name')->where('id', Auth::user()->id)->first();
            if (! is_null($user)) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), $user, now()->addMinutes(env('SESSION_LIFETIME')));
            } //put it in the cache for the length of the session
        }

        $user_profile = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'), null);
        if (is_null($user_profile)) {
            $user_profile = $user->profile;
            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'), $user_profile, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
        }

        $user_applications = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR8'), null);
        if (is_null($user_applications)) {
            $user_applications = $user->applications;

            //cache data only if there is no soap error
            if (! isset($user_applications['error'])) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR8'), $user_applications, now()->addMinutes(env('SESSION_LIFETIME')));
            } //put it in the cache for the length of the session
        }

        $user_appendix_list = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR7'), null);
        if (is_null($user_appendix_list)) {
            $user_appendix_list = $user->appendix_list;

            //cache data only if there is no soap error
            if (! isset($user_appendix_list['error'])) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR7'), $user_appendix_list, now()->addMinutes(env('SESSION_LIFETIME')));
            } //put it in the cache for the length of the session
        }

        $user_total_unread_messages = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR4'), null);
        if (is_null($user_total_unread_messages)) {
            $notifications = $this->fnGetDocuments('U');
            $user_total_unread_messages = 0;
            //cache data only if there is no soap error
            if (! isset($user_total_unread_messages['error'])) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR4'), $notifications['totalUnread'], now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
                $user_total_unread_messages = $notifications['totalUnread'];
            }
        }

        if (! is_null($user)) {
            return Response::json(['profile' => $user_profile, 'apps' => $user_applications, 'appx' => $user_appendix_list, 'new_messages' => $user_total_unread_messages], 200);
        } // Status code here

        return Response::json(['error' => 'unauthorized'], 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Requests\AjaxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function fetchProfileData(AjaxRequest $request)
    {
        $user = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), null);
        if (is_null($user)) {
            $user = User::select('name')->where('id', Auth::user()->id)->first();
            if (! is_null($user)) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), $user, now()->addMinutes(env('SESSION_LIFETIME')));
            } //put it in the cache for the length of the session
        }

        $user_profile = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'), null);
        if (is_null($user_profile)) {
            $user_profile = $user->profile;
            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'), $user_profile, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
        }

        if (! is_null($user)) {
            $config = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), null);
            if (is_null($config)) {
                $config = parse_ini_file(config_path().env('APP_CONFIG_FILE'), true);
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), $config, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
            }
            $countries = $config['COUNTRIES'];
            $provinces = $config['PROVINCES'];
            $states = $config['STATES'];

            return Response::json(['profile' => $user_profile, 'countries' => $countries, 'provinces' => $provinces, 'states' => $states], 200); // Status code here
        }

        return Response::json(['error' => 'unauthorized'], 403);
    }

    /**
     * @param  AjaxRequest  $request
     * @return mixed
     */
    public function fetchProfileQuestionPool(AjaxRequest $request)
    {
        $user = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), null);
        if (is_null($user)) {
            $user = User::select('name')->where('id', Auth::user()->id)->first();
            if (! is_null($user)) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), $user, now()->addMinutes(env('SESSION_LIFETIME')));
            } //put it in the cache for the length of the session
        }

        if (! is_null($user)) {
            $questions = $user->fnGetChallengeQuestions();

            return Response::json(['questions' => $questions], 200); // Status code here
        }

        return Response::json(['error' => 'unauthorized'], 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Requests\AjaxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function fetchUploadsData(AjaxRequest $request)
    {
        $user = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), null);
        if (is_null($user)) {
            $user = User::select('name')->where('id', Auth::user()->id)->first();
            if (! is_null($user)) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), $user, now()->addMinutes(env('SESSION_LIFETIME')));
            } //put it in the cache for the length of the session
        }

        $user_profile = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'), null);
        if (is_null($user_profile)) {
            $user_profile = $user->profile;
            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'), $user_profile, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
        }

        if (! is_null($user)) {
            $config = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), null);
            if (is_null($config)) {
                $config = parse_ini_file(config_path().env('APP_CONFIG_FILE'), true);
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), $config, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
            }
            $countries = $config['COUNTRIES'];
            $provinces = $config['PROVINCES'];
            $states = $config['STATES'];

            return Response::json(['profile' => $user_profile, 'countries' => $countries, 'provinces' => $provinces, 'states' => $states], 200); // Status code here
        }

        return Response::json(['error' => 'unauthorized'], 403);
    }

    /**
     * POST to upload profile data
     *
     * @param  UpdateProfileRequest  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|array
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = User::where('id', Auth::user()->id)->with('roles')->first();

        return $user->fnUpdateProfile($request, true);
    }

    /**
     * POST to update bcsc profile data
     *
     * @param  UpdateBcscProfileRequest  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|array
     */
    public function updateBcscProfile(UpdateBcscProfileRequest $request)
    {
        $user = User::where('id', Auth::user()->id)->with('roles')->first();

        return $user->fnUpdateProfile($request, true);
    }

    public function fetchUploadsListData(AjaxRequest $request)
    {
        $msg = '';
        $user = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), null);
        if (is_null($user)) {
            $user = User::select('name')->where('id', Auth::user()->id)->first();
            if (! is_null($user)) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), $user, now()->addMinutes(env('SESSION_LIFETIME')));
            } //put it in the cache for the length of the session
        }

        if (! is_null($user)) {
            if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false)) {
                $msg = '<p>To view documents, right click and \'Save as\'</p>';
            }

            $this->uid = session(env('GUID_SESSION_VAR'));
            $notifications = $this->fnGetUploadedDocuments();
            $files = [];
            if ($notifications != false) {
                foreach ($notifications as $note) {
                    $x = explode('-_-', $note['fileName']);
                    if (count($x) != 3 && Str::endsWith($note['fileName'], 'sabc-use-only.txt') == false) {
                        $files[] = $note;
                    }
                }
            }

            return Response::json(['uploads' => $files, 'alertMsg' => $msg], 200); // Status code here
        }

        return Response::json(['error' => 'unauthorized'], 403);
    }

    public function fetchNotificationsData(AjaxRequest $request)
    {
        $user = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), null);
        if (is_null($user)) {
            $user = User::select('name')->where('id', Auth::user()->id)->first();
            if (! is_null($user)) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), $user, now()->addMinutes(env('SESSION_LIFETIME')));
            } //put it in the cache for the length of the session
        }

        if (! is_null($user)) {
            $this->uid = session(env('GUID_SESSION_VAR'));
            $notifications = $this->fnGetDocuments(['U', 'A', 'G'], true);

            return Response::json(['notifications' => $notifications], 200); // Status code here
        }

        return Response::json(['error' => 'unauthorized'], 403);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function applicationPage($application)
    {
        return view('application', ['application' => $application]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function appendixPage($application, $formGuid = null, $program_year = null)
    {
        return view('appendix', ['application' => $application, 'formGuid' => $formGuid]);
    }

    /**
     * Display the specified resource.
     * Drupal's function was loadApplicationDetails
     *
     * @param  \App\Http\Requests\AjaxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function fetchApplicationData(AjaxRequest $request)
    {
        $user = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), null);
        if (is_null($user)) {
            $user = User::select('name')->where('id', Auth::user()->id)->first();
            if (! is_null($user)) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), $user, now()->addMinutes(env('SESSION_LIFETIME')));
            } //put it in the cache for the length of the session
        }

        $app = $user->application($request->application_number);
        if (! is_null($app)) {
            $app_details = new Application();

            $tl = (isset($app->applicationDetails->applicationTimeline)) ? (array) $app->applicationDetails->applicationTimeline : null;
            $eventItems = null;
            if (isset($app->applicationDetails->applicationNumber) && $app->applicationDetails->applicationNumber > 0 && ! empty($tl)) {

                $disbursementStatus = null;

                $onlineStatus = $app->applicationDetails->applicationProfile->institution->onlineStatus;

                $eventItems = $app_details->fnLoadEvents($app->applicationDetails, $onlineStatus);
            }

            return Response::json(['application' => $app, 'details' => $eventItems], 200); // Status code here
        }

        return Response::json(['error' => 'unauthorized'], 403);
    }

    /**
     * Display the specified resource.
     * Drupal's function was loadAppendixDetails
     *
     * @param  \App\Http\Requests\AjaxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function fetchAppendixData(AjaxRequest $request)
    {
        $user = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), null);
        if (is_null($user)) {
            $user = User::select('name')->where('id', Auth::user()->id)->first();
            if (! is_null($user)) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), $user, now()->addMinutes(env('SESSION_LIFETIME')));
            } //put it in the cache for the length of the session
        }

        $app = $user->appendix($request->application_number, $request->form_guid);
        if (! is_null($app)) {
            $app_details = new Application();

            // $r = new application();

//            //GET ALL OUR EVENTS IN SORT THEM IN ARRAY
//            //STATUS FLAGS TO TELL US WHERE THE APPLICATION IS AT

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

                $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');
                $this->uid = $this->fnDecrypt(Auth::user()->name);

                $isWebDeclarationInkSignatureRequired = $this->fnRequest('isWebDeclarationInkSignatureRequired', ['applicationNumber' => $app->applicationNumber, 'role' => 'S', 'userGUID' => $this->uid], null, 100);
                [$processing_consent, $show_econsent] = $this->fetchGetWebDeclaration('S', $app->applicationNumber, $this->uid);
            }

            return Response::json(['application' => $app, 'details' => $eventItems,
                'isWebDeclarationInkSignatureRequired' => $isWebDeclarationInkSignatureRequired,
                'processing_consent' => $processing_consent, 'show_econsent' => $show_econsent], 200); // Status code here
        }

        return Response::json(['error' => 'unauthorized'], 403);
    }

    /**
     * Display the dashboard page to a logged in Student/Spouse/Parent
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminDashboard()
    {
        return view('admin.dashboard', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * Display the dashboard page to a logged in Student/Spouse/Parent
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function appSupportDashboard()
    {
        return view('app-support');
    }

    /*
    * @param  \Illuminate\Http\Request  $request
    */
    public function createUser(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|unique:users,name',
            'password' => 'required|min:8',
            'email' => 'email',
        ]);

        $next_uid = User::select('uid')->orderBy('uid', 'desc')->first();
        $old_uid = User::select('uid')->where('name', 'like', $request->user_id)->first();

        $user = new User();
        $user->uid = $next_uid->uid + 1;

        //if the user has an account recreate it with the same UID
        if (! is_null($old_uid)) {
            $user->uid = $old_uid->uid;
        }

        $user->name = $request->user_id;
        $user->email = $request->email;
        $user->password = $user->user_hash_password($request->password, DRUPAL_HASH_COUNT);
        $user->created = strtotime('now');
        $user->status = 1;
        $user->save();

        $role = Role::where('name', $request->role_type)->first();
        $user->roles()->attach($role);

        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Requests\AjaxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function fetchNavbarData(AjaxRequest $request)
    {
        $cacheData_name = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR3'), null);
        $cacheData_msgs = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR4'), null);
        if ($cacheData_name && $cacheData_msgs) {
            return Response::json(['name' => $cacheData_name, 'new_messages' => $cacheData_msgs], 200); // Status code here
        }

        $user = User::where('id', Auth::user()->id)->with('roles')->first();
        if (! is_null($user)) {
            $this->uid = session(env('GUID_SESSION_VAR'));

            if (! is_object($user->profile)) {
                return Response::json(['name' => '', 'new_messages' => 0], 204); // Status code No Content
            }

            $name = '';
            $user_profile = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'), null);
            if (is_null($user_profile)) {
                $user_profile = $user->profile;
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'), $user_profile, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
            }

            if (! is_null($user_profile)) {
                $name = isset($user_profile->userProfile->firstName) ? $user_profile->userProfile->firstName.' '.$user_profile->userProfile->familyName : $user_profile->userProfile->familyName;
            }

            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR3'), $name, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session

            $user_total_unread_messages = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR4'), null);
            if (is_null($user_total_unread_messages)) {
                $notifications = $this->fnGetDocuments('U');
                $user_total_unread_messages = 0;
                //cache data only if there is no soap error
                if (! isset($user_total_unread_messages['error'])) {
                    Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR4'), $notifications['totalUnread'], now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
                    $user_total_unread_messages = $notifications['totalUnread'];
                }
            }

            return Response::json(['name' => $name, 'new_messages' => $user_total_unread_messages], 200); // Status code here
        }

        return Response::json(['error' => 'unauthorized'], 403);
    }

    /**
     * Ajax callback function.
     */
    public function sabcFileManagerFormSubmit(FileUploadRequest $request)
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': sabcFileManagerFormSubmit() upload begins');
            Session::save();
        }

        $failedUploads = [];
        if (isset($_FILES['files']['name'])) {
            $totalUploadedFiles = count($_FILES['files']['name']);
            $saveForUpload = []; //base64_encode files that we will need for our soap request
            $files = [];
            $this->uid = session(env('GUID_SESSION_VAR'));
            for ($i = 0; $i < $totalUploadedFiles; $i++) {

                [$msg, $valid, $file, $path] = $this->file_save_upload(
                    $request,
                    ''.$i.'',
                    ['file_validate_extensions' => ['png gif jpg jpeg pdf doc docx xls']],
                    'private://'
                );
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': sabcFileManagerFormSubmit() processing file i = '.$i);
                    session()->push('DEBUG', now().': sabcFileManagerFormSubmit() msg: '.json_encode($msg));
                    session()->push('DEBUG', now().': sabcFileManagerFormSubmit() valid: '.json_encode($valid));
                    session()->push('DEBUG', now().': sabcFileManagerFormSubmit() file: '.json_encode($file));
                    session()->push('DEBUG', now().': sabcFileManagerFormSubmit() path: '.json_encode($path));
                }

                //check to make sure it is a valid file extension that we accept
                if ($file && ($file->filesize / 1000) < 2000) {
                    $contents = Storage::get($file->destination);
                    array_push($saveForUpload, ['fileName' => $file->filename, 'arrayFile64' => $contents]);
                    array_push($files, $file);
                } else {
                    array_push($failedUploads, $_FILES['files']['name'][$i]);
                }
            }
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': sabcFileManagerFormSubmit() failedUploads: '.json_encode($failedUploads));
            }

            if (count($failedUploads) > 0) {
                Session::save();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'failed', 'totalFilesFailed' => count($failedUploads), 'failedFiles' => $failedUploads, 'msg' => 'Sorry we had problems processing your request.  The following files failed to upload:<br>- '.implode('<br> - ', $failedUploads)]);
                exit;
            } else {
                $params['documents'] = $saveForUpload;
                $params['document_purpose'] = $request->document_purpose;
                $appNumber = str_ireplace(['--', 'Submitted', 'InTransition', 'NotSubmitted', 'Appendix1', 'Appendix2'], '', $request->attach_to_application);

                $params['document_notes'] = htmlspecialchars(strip_tags($request->document_notes, '<p><b><br><strong>'));

                if (stripos($request->attach_to_application, 'NotSubmitted')) {
                    if (stripos($request->attach_to_application, 'Appendix1')) {
                        $params['document_notes'] .= '<br><br> *Please note that at the time of upload the following X1 had not been submitted for application #'.$appNumber.'.  Before logging please make sure that application has been submitted.<br><br>';
                    } elseif (stripos($request->attach_to_application, 'Appendix2')) {
                        $params['document_notes'] .= '<br><br> *Please note that at the time of upload the following X2 had not been submitted for application #'.$appNumber.'.  Before logging please make sure that application has been submitted.<br><br>';
                    }
                }

                $params['attach_to_application'] = ($request->attach_to_application !== 'default') ? $appNumber : null;


                $notifications = $this->fnUploadDocuments($params);
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': sabcFileManagerFormSubmit() params: '.json_encode($params));
                }


                foreach ($files as $file) {
                    Storage::delete($file->destination);
                }
                Session::save();

                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'msg' => 'Thank you, your files have been received. All files will be reviewed by date order received.']);
                exit;
            }
        } else {
            Session::save();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'failed', 'totalFilesFailed' => 0, 'failedFiles' => $failedUploads, 'msg' => 'Sorry we had problems processing your request.']);
            exit;
        }
    }

    /**
     * compare bcsc profile data and update if different than get profile
     *
     * @param $bcscusrProfile
     * @return string
     */
    private function checkBcscUpdates($bcscusrProfile)
    {
        if (isset($_SESSION['bcsc_profile'])) {
            $updateProfile = [];

            $fname = (isset($bcscusrProfile->userProfile->firstName)) ? strtolower($bcscusrProfile->userProfile->firstName) : null;
            $trimmedFirstName = substr(trim($_SESSION['bcsc_profile']['givenname']), 0, 15);
            if (strtolower($trimmedFirstName) != $fname) {
                $updateProfile['first_name'] = $trimmedFirstName;
            }

            $lname = (isset($bcscusrProfile->userProfile->familyName)) ? strtolower($bcscusrProfile->userProfile->familyName) : null;
            $trimmedLastName = substr(trim($_SESSION['bcsc_profile']['surname']), 0, 25);
            if (strtolower($trimmedLastName) != $lname) {
                $updateProfile['last_name'] = $trimmedLastName;
            }

            $middleName = str_replace($_SESSION['bcsc_profile']['givenname'], '', $_SESSION['bcsc_profile']['givennames']);
            $currentMiddleName = (isset($bcscusrProfile->userProfile->middleName)) ? $bcscusrProfile->userProfile->middleName : null;
            $trimmedMiddleName = substr(trim($middleName), 0, 15);
            if (strtolower(trim($trimmedMiddleName)) != strtolower(trim($currentMiddleName))) {
                $updateProfile['middle_name'] = $trimmedMiddleName;
            }

            if ($_SESSION['bcsc_profile']['identityassurancelevel'] != $bcscusrProfile->userProfile->assuranceLevel) {
                $updateProfile['assuranceLevel'] = $_SESSION['bcsc_profile']['identityassurancelevel'];
            }

            if (isset($_SESSION['bcsc_profile']['sex']) && in_array(strtoupper($_SESSION['bcsc_profile']['sex'][0]), ['M', 'F']) && $_SESSION['bcsc_profile']['sex'][0] != strtoupper($bcscusrProfile->userProfile->gender)) {
                $updateProfile['gender'] = $_SESSION['bcsc_profile']['sex'][0];
            }

            if (str_replace('-', '', $_SESSION['bcsc_profile']['birthdate']) != $bcscusrProfile->userProfile->dateOfBirth) {
                $updateProfile['dateOfBirth'] = str_replace('-', '', $_SESSION['bcsc_profile']['birthdate']);
            }

            //check to make sure updateProfile is not empty.  If not empty then we need to update profile
            if (! empty($updateProfile)) {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': checkBcscUpdates(): BCSC update profile starting');
                    Session::save();
                    Log::warning('checkBcscUpdates(): BCSC update profile starting');
                }
                $origValues = [
                    'assuranceLevel' => $bcscusrProfile->userProfile->assuranceLevel,
                    'first_name' => (isset($bcscusrProfile->userProfile->firstName)) ? $bcscusrProfile->userProfile->firstName : null,
                    'last_name' => (isset($bcscusrProfile->userProfile->familyName)) ? $bcscusrProfile->userProfile->familyName : null,
                    'middle_name' => (isset($bcscusrProfile->userProfile->middleName)) ? $bcscusrProfile->userProfile->middleName : null,
                    'dateOfBirth' => $bcscusrProfile->userProfile->dateOfBirth,
                    'gender' => $bcscusrProfile->userProfile->gender,
                    'email' => $bcscusrProfile->userProfile->emailAddress,
                    'Street1' => $bcscusrProfile->userProfile->addressLine1,
                    'City' => $bcscusrProfile->userProfile->city,
                    'Country' => $bcscusrProfile->userProfile->country,
                    'Phone' => $bcscusrProfile->userProfile->phoneNumber,
                ];

                if (isset($bcscusrProfile->userProfile->postalCode) && ! empty($bcscusrProfile->userProfile->postalCode)) {
                    $origValues['PostZip'] = $bcscusrProfile->userProfile->postalCode;
                }
                if (isset($bcscusrProfile->userProfile->province) && ! empty($bcscusrProfile->userProfile->province)) {
                    $origValues['ProvState'] = $bcscusrProfile->userProfile->province;
                }
                if (isset($bcscusrProfile->userProfile->addressLine2) && ! empty($bcscusrProfile->userProfile->addressLine2)) {
                    $origValues['Street2'] = $bcscusrProfile->userProfile->addressLine2;
                }
                //loop through updated values and overwrite the old value
                foreach ($updateProfile as $k => $v) {
                    $origValues[$k] = $v;
                }

                //create payload data to be updated
                $p = ['autoUpdate' => true, 'values' => $origValues];

                [$submit_status, $submit_msg] = $this->reSyncUserProfile($p, $bcscusrProfile->userProfile->userGUID);
                //if we did update the profile successfully
                if ($submit_status == true) {
                    $this->uid = $bcscusrProfile->userProfile->userGUID;
                    $bcscusrProfile = $this->fnGetBCSCUserProfile();
                }
            }
        }

        if (is_array($bcscusrProfile)) {
            //we got an ERROR with fnGetBCSCUserProfile
            return [$bcscusrProfile['msg'], false];
        }

        return [isset($bcscusrProfile->userProfile) &&
            isset($bcscusrProfile->userProfile->firstName) ?
            $bcscusrProfile->userProfile->firstName.' '.$bcscusrProfile->userProfile->familyName :
            $bcscusrProfile->userProfile->familyName, true];
    }

    /**
     * @param $p
     * @param $GUID
     * @return array
     */
    private function reSyncUserProfile($p, $GUID)
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': reSyncUserProfile(): BCSC update profile starting');
            Session::save();
            Log::warning('reSyncUserProfile(): BCSC update profile starting');
        }

        $mappings = ['gender' => 'gender',
            'email' => 'emailAddress',
            'Street1' => 'addressLine1',
            'Street2' => 'addressLine2',
            'City' => 'city',
            'ProvState' => 'province',
            'Country' => 'country',
            'PostZip' => 'postalCode',
            'Phone' => 'phoneNumber',
            'question1' => 'question1Number',
            'answer1' => 'answer1',
            'question2' => 'question2Number',
            'answer2' => 'answer2',
            'question3' => 'question3Number',
            'answer3' => 'answer3',
            'user_id' => 'userID',
            'new_password' => 'userPassword',
            'Consent' => 'userConsent',
            'assuranceLevel' => 'assuranceLevel',
            'first_name' => 'firstName',
            'last_name' => 'familyName',
            'middle_name' => 'middleName',
            'dateOfBirth' => 'dateOfBirth'];

        //ERROR CODE MAPPINGS TO OUR FORM FIELDS
        $errorMappings = ['SPSIM02' => 'user_id',
            'SPSIM03' => 'user_id',
            'SPSIM06' => 'hpot',
            'SPSIM07' => 'user_id',
            'SPSIM08' => 'new_password',
            'SPSIM09' => 'new_password',
            'SPSIM14' => 'Email',
            'SPSIM18' => 'hpot',
            'SPSIM19' => 'PostZip',
            'SPSIM20' => 'PostZip',
            'SPSIM22' => 'Consent',
            'SPSIM23' => 'Country',
            'SPSIM24' => 'ProvState'];

        //values to ignore for BCSC ID
        $bcscIgnore = ['Consent', 'question1', 'question2', 'question3', 'answer1', 'answer2', 'answer3'];

        $ws = ['userGUID' => $this->fnDecrypt($GUID)];
        foreach ($p['values'] as $k => $v) {
            if ($k == 'email') {
                $v = $this->fnSanitizeData($v, 'email');
            }
            if ($k == 'middle_name' || $k == 'Street1' || $k == 'Street2') {
                $v = $this->fnSanitizeData($v);
            }

            if ($k == 'Phone') {
                $v = str_replace(['(', ')', '-', ' '], '', $v);
            }
//
//            if ($k == 'gender') {
//                if ($v == 'WOMAN') {
//                    $v = 'F';
//                }
//                if ($v == 'MAN') {
//                    $v = 'M';
//                }
//                if ($v == 'NON-BINARY') {
//                    $v = 'X';
//                }
//                if ($v == 'PREFER NOT TO ANSWER') {
//                    $v = ' ';
//                }
//            }

            if (isset($mappings[$k]) && ! empty($v)) {
                if (isset($p['values']['assuranceLevel']) && ! in_array($k, $bcscIgnore)) {
                    $ws[$mappings[$k]] = strtoupper($v);
                }
            }
        }

        $this->WSDL = $this->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');
        $submit_status = false;
        $submit_msg = '';
        $updateProfile = $this->fnRequest('updateUserProfile', $ws, 'update_user_profile'.$this->fnDecrypt($GUID), 0);
        if (isset($updateProfile->faultcode)) {
            //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
            if (isset($updateProfile->detail->ProfileFault)) {
                $submit_msg = $errorMappings[$updateProfile->detail->ProfileFault->faultCode].'. '.$updateProfile->getMessage();
            } else {
                Log::error('SYSTEM ERROR :: USER_ACCOUNT -> reSyncUserProfile: '.$updateProfile->getMessage());
            }
        } else {
            //if all is good remove our check to auto update profile
            $submit_status = true;
        }

        return [$submit_status, $submit_msg];
    }

    /**
     * Saves a file upload to a new location.
     *
     * The file will be added to the {file_managed} table as a temporary file.
     * Temporary files are periodically cleaned. To make the file a permanent file,
     * assign the status and use file_save() to save the changes.
     *
     * @param $form_field_name
     *   A string that is the associative array key of the upload form element in
     *   the form array.
     * @param $validators
     *   An optional, associative array of callback functions used to validate the
     *   file. See file_validate() for a full discussion of the array format.
     *   If no extension validator is provided it will default to a limited safe
     *   list of extensions which is as follows: "jpg jpeg gif png txt
     *   doc xls pdf ppt pps odt ods odp". To allow all extensions you must
     *   explicitly set the 'file_validate_extensions' validator to an empty array
     *   (Beware: this is not safe and should only be allowed for trusted users, if
     *   at all).
     * @param $destination
     *   A string containing the URI that the file should be copied to. This must
     *   be a stream wrapper URI. If this value is omitted, Drupal's temporary
     *   files scheme will be used ("temporary://").
     * @param $replace
     *   Replace behavior when the destination file already exists:
     *   - FILE_EXISTS_REPLACE: Replace the existing file.
     *   - FILE_EXISTS_RENAME: Append _{incrementing number} until the filename is
     *     unique.
     *   - FILE_EXISTS_ERROR: Do nothing and return FALSE.
     * @return
     *   An object containing the file information if the upload succeeded, FALSE
     *   in the event of an error, or NULL if no file was uploaded. The
     *   documentation for the "File interface" group, which you can find under
     *   Related topics, or the header at the top of this file, documents the
     *   components of a file object. In addition to the standard components,
     *   this function adds:
     *   - source: Path to the file before it is moved.
     *   - destination: Path to the file after it is moved (same as 'uri').
     */
    private function file_save_upload($request, $form_field_name, $validators = [], $destination = false, $replace = 0)
    {
        $file = null;
        $msg = '';
        $path = '';
        $valid = true;
        // Make sure there's an upload to process.
        if (empty($_FILES['files']['name'][$form_field_name])) {
            $valid = null;
        } else {
            // Check for file upload errors and return FALSE if a lower level system
            // error occurred. For a complete list of errors:
            // See http://php.net/manual/features.file-upload.errors.php.
            switch ($_FILES['files']['error'][$form_field_name]) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $valid = false;
                    $msg = 'The file '.$_FILES['files']['name'][$form_field_name].' could not be saved, because it exceeds the maximum allowed size for uploads.';
                    break;

                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_NO_FILE:
                    $valid = false;
                    $msg = 'The file '.$_FILES['files']['name'][$form_field_name].' could not be saved, because the upload did not complete.';
                    break;

                case UPLOAD_ERR_OK:
                    // Final check that this is a valid upload, if it isn't, use the
                    // default error handler.
                    if (is_uploaded_file($_FILES['files']['tmp_name'][$form_field_name])) {
                        break;
                    }

                    // Unknown error
                default:
                    $valid = false;
                    $msg = 'The file '.$_FILES['files']['name'][$form_field_name].' could not be saved. An unknown error has occurred.';
            }
        }

        if ($valid == false || $valid == null) {
            return [$msg, $valid, $file, $path];
        } else {
            // Begin building file object.
            $file = new \stdClass();
            $file->uid = $this->uid;
            $file->status = 0;
            $file->filename = trim(basename($_FILES['files']['name'][$form_field_name]), '.');
            $file->uri = $_FILES['files']['tmp_name'][$form_field_name];
            $file->filemime = $request->file('files')[$form_field_name]->getMimeType();

            $file->filesize = $_FILES['files']['size'][$form_field_name];

            $extensions = '';
            if (isset($validators['file_validate_extensions'])) {
                if (isset($validators['file_validate_extensions'][0])) {
                    // Build the list of non-munged extensions if the caller provided them.
                    $extensions = $validators['file_validate_extensions'][0];
                } else {
                    // If 'file_validate_extensions' is set and the list is empty then the
                    // caller wants to allow any extension. In this case we have to remove the
                    // validator or else it will reject all extensions.
                    unset($validators['file_validate_extensions']);
                }
            } else {
                // No validator was provided, so add one using the default list.
                // Build a default non-munged safe list for file_munge_filename().
                $extensions = 'jpg jpeg gif png txt doc xls pdf ppt pps odt ods odp';
                $validators['file_validate_extensions'] = [];
                $validators['file_validate_extensions'][0] = $extensions;
            }

            if (! empty($extensions)) {
                // Munge the filename to protect against possible malicious extension hiding
                // within an unknown file type (ie: filename.html.foo).
                $tmp_file = explode('.', $file->filename);
                $file->filename = $tmp_file[0].'.'.$tmp_file[count($tmp_file) - 1];
            }

            // If the destination is not provided, use the temporary directory.
            if (empty($destination)) {
                $destination = 'temporary://';
            }

            $file->source = $form_field_name;
            // A URI may already have a trailing slash or look like "public://".
            if (substr($destination, -1) != '/') {
                $destination .= '/';
            }

            // Move uploaded files from PHP's upload_tmp_dir to Drupal's temporary
            // directory. This overcomes open_basedir restrictions for future file
            // operations.

            $path = Storage::putFileAs('files', $request->file('files')[$form_field_name], $file->filename);
            $file->destination = $path;

            // Set the permissions on the new file.
            return [$msg, $valid, $file, $path];
        }
    }
}
