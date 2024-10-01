<?php

namespace App;

use App\BcscXmlDecrypt\XmlSecEnc;
use App\Http\Controllers\Aeit;
use Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * The standard log2 number of iterations for password stretching. This should
 * increase by 1 every Drupal version in order to counteract increases in the
 * speed and power of computers available to crack the hashes.
 */
const DRUPAL_HASH_COUNT = 15;

/**
 * The minimum allowed log2 number of iterations for password stretching.
 */
const DRUPAL_MIN_HASH_COUNT = 7;

/**
 * The maximum allowed log2 number of iterations for password stretching.
 */
const DRUPAL_MAX_HASH_COUNT = 30;

/**
 * The expected (and maximum) number of characters in a hashed password.
 */
const DRUPAL_HASH_LENGTH = 55;
class User extends Authenticatable
{
    use Notifiable;

    public $aeit;

    protected $appends = ['guid'];

    //protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'name', 'email', 'created', 'login', 'access', 'login', 'status', 'timezone', 'data', 'saml',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $timestamps = true;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->aeit = new Aeit();
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_user', 'uid', 'rid', 'uid', 'rid');
    }

    public function isActive()
    {
        return $this->status == '1' ? true : false;
    }

    public function isAdmin()
    {
        $roles = [];
        if (! is_array($this->roles)) {
            $roles = $this->roles->pluck('name')->toArray();
        }

        return in_array('administrator', $roles);
    }

    public function isAppSupport()
    {
        $roles = [];
        if (! is_array($this->roles)) {
            $roles = $this->roles->pluck('name')->toArray();
        }

        return in_array('app_support', $roles);
    }

    public function isStudent()
    {
        $roles = [];
        if (! is_array($this->roles)) {
            $roles = $this->roles->pluck('name')->toArray();
        }

        return in_array('student', $roles) || in_array('bcsc_student', $roles);
    }

    public function isStudentSpouseParent()
    {
        $roles = [];
        if (! is_array($this->roles)) {
            $roles = $this->roles->pluck('name')->toArray();
        }

        return ! in_array('app_support', $roles) && ! in_array('administrator', $roles) ? true : false;
    }

    public function isBcscSpouse()
    {
        $roles = [];
        if (! is_array($this->roles)) {
            $roles = $this->roles->pluck('name')->toArray();
        }

        return in_array('bcsc_spouse', $roles);
    }

    public function isBcscParent()
    {
        $roles = [];
        if (! is_array($this->roles)) {
            $roles = $this->roles->pluck('name')->toArray();
        }

        return in_array('bcsc_parent', $roles);
    }

    public function gotSin()
    {
        $roles = [];
        if (! is_array($this->roles)) {
            $roles = $this->roles->pluck('name')->toArray();
        }
        if (
            in_array('student', $roles) ||
            in_array('parent', $roles) ||
            in_array('spouse', $roles) ||
            in_array('bcsc_student', $roles) ||
            in_array('bcsc_parent', $roles) ||
            in_array('bcsc_spouse', $roles)
        ) {
            return true;
        }

        if (in_array('app_support', $roles) || in_array('administrator', $roles)) {
            return true;
        }

        return false;
    }

    //return user GUID if the user is a Student/Spouse/Parent
    //else return null;
    public function getGuidAttribute()
    {
        if ($this->isStudentSpouseParent() == true && Auth::check() == true) {
            return session(env('GUID_SESSION_VAR'));
        }

        return null;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function appendix($application_number, $formGUID)
    {
        $this->aeit->uid = Auth::user()->guid;

        $app = $this->fnGetAppendixDetails($formGUID, $application_number);

        return $app;
    }

    public function application($application_number)
    {
        $this->aeit->uid = Auth::user()->guid;

        $app = $this->fnGetApplicationDetails($application_number);

        return $app;
    }

    public function getProfileAttribute()
    {
        if (is_null(session()->get(env('ADMIN_SESSION_VAR')))) {
            $this->aeit->uid = Auth::user()->guid;

            $profile = $this->fnGetUserProfile();

            return $profile;
        }
    }

    public function getApplicationsAttribute()
    {
        if (is_null(session()->get(env('ADMIN_SESSION_VAR')))) {
            $this->aeit->uid = Auth::user()->guid;

            $apps = $this->fnGetApplications();

            return $apps;
        }
    }

    public function getAppendixListAttribute()
    {
        if (is_null(session()->get(env('ADMIN_SESSION_VAR')))) {
            $this->aeit->uid = Auth::user()->guid;

            $appendix = $this->fnGetAppendixList();

            return $appendix;
        }
    }

    public function getAttestationVerifiedAttribute()
    {
        return $this->fnVerifyUser();
    }


    /*
        fnVerifyUser
        Method to perform a verification check.  The method calls an e-service to obtain the following:

            $userProfile = new userProfile();
            $isUserVerified = $userProfile->fnVerify();

            @params: $uid
            @return: (boolean)
    */
    public function fnVerifyUser($uid = null)
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnVerifyUser(): uid: '.$uid);
            session()->push('DEBUG', now().': fnVerifyUser(): auth id: '.Auth::user()->id);
            Session::save();
        }

        $isUserVerified = false;
        $user = User::select('id')->where('id', Auth::user()->id)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['student', 'bcsc_student', 'bcsc_spouse', 'bcsc_parent']);
            })->first();
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnVerifyUser(): user->id: '.$user->id);
            Session::save();
        }

        // CALL GET USER VERIFY SOAP WEB SERVICE if user is a student or bcsc_student
        if (Auth::check() == true && ! is_null($user)) {
            $this->aeit->uid = Auth::user()->guid;

            // Build and make web service call
            $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'USER_VERIFY');
            $action = 'getUserProfile';
            $params = ['userGUID' => $this->aeit->uid];
            $cid = null;
            $cacheExpire = 0;
            $resposnse = $this->aeit->fnRequest($action, $params, $cid, $cacheExpire);

            // Retreive verified field from web service response
            // Set TRUE or FALSE return value
            if (! isset($resposnse->faultcode)) {
                if (! empty($resposnse->userProfile->verified)) {
                    $verified = $resposnse->userProfile->verified;
                    if ($verified === 'Y') {
                        $isUserVerified = true;
                    }
                } else {
                    // $this->fnError('SYSTEM ERROR :: USER_VERIFY ->getUserProfile', $resposnse->userProfile->verified, $resposnse, $triggerDefault = true);
                    //error_log('10 isUserVerified: ' . $isUserVerified, 0);
                }
            //error_log('E-Service verified: ' . $resposnse->userProfile->verified, 0);
            //error_log('PHP isUserVerified: ' . $isUserVerified, 0);
            } else {
                // $this->fnError('SYSTEM ERROR :: USER_VERIFY FAULT CODE.', $resposnse->faultcode);
            }
        }

        //error_log('PHP fnVerifyUser isUserVerified: ' . $isUserVerified, 0);
        return $isUserVerified;
    }

    /*
        fnVerificationMethod: check if the user is a student/bcsc_student
          Convenience method to add session data
          @params: (string) $key
          @params: (string) $value
     */
    public function fnVerificationMethod()
    {

        $verificationMethod = '';
        $user = User::select('id')->where('id', Auth::user()->id)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['student', 'bcsc_student', 'bcsc_spouse', 'bcsc_parent']);
            })->first();

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnVerificationMethod(): user->id: '.$user->id);
            session()->push('DEBUG', now().': fnVerificationMethod(): is auth: '.Auth::check());
        }

        // CALL GET USER VERIFY SOAP WEB SERVICE if user is a student or bcsc_student
        if (Auth::check() == true && ! is_null($user)) {
            $this->aeit->uid = session(env('GUID_SESSION_VAR'));
            $verificationMethod = 'bcsc';
        }

        return $verificationMethod;
    }

    /*
    *		USED TO GET USER PROFILE
    *		@params: $request
    *		@return (object) or void if system errors
    */
    public function fnGetUserProfile()
    {
        $this->aeit->uid = Auth::user()->guid;
        $roles = [];
        if (! is_array($this->roles)) {
            $roles = Auth::user()->roles->pluck('name')->toArray();
        }

        //CALL GET USER PROFILE SOAP WEB SERVICE
        if (in_array('bcsc_student', $roles) || in_array('bcsc_parent', $roles) || in_array('bcsc_spouse', $roles)) {
            $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');
        } else {
            $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'USER_ACCOUNT');
        }

        //verify the user
        $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'USER_VERIFY');
        $verifyAccount = $this->aeit->fnRequest('getUserProfile', ['userGUID' => $this->aeit->uid], 'get_user_profile'.$this->aeit->uid, 14400);

        //OVERRIDE if we know already that the account is a BCSC account
        if (isset($verifyAccount->userProfile->assuranceLevel)) {
            $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');
        } else {
            $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'USER_ACCOUNT');
        }

        $usrProfile = $this->aeit->fnRequest('getUserProfile', ['userGUID' => $this->aeit->uid], 'get_user_profile'.$this->aeit->uid, 14400);

        //MAKE SURE IT IS NOT AN ERROR
        if (! is_null($usrProfile) && ! isset($usrProfile->faultcode)) {
            $usrProfile->status = true;
            $usrProfile->userProfile->SIN = $this->aeit->fnEncrypt($usrProfile->userProfile->SIN);
            $usrProfile->userProfile->userGUID = $this->aeit->fnEncrypt($usrProfile->userProfile->userGUID);
            $usrProfile->userProfile->userConsent = (! isset($usrProfile->userProfile->userConsent) || $usrProfile->userProfile->userConsent == 'N') ? false : true;

            //this is being added to tell the dashboard that the student is BCSC. If NOT then show modal id="require_bcsc" with msg to convert.
            if (in_array('bcsc_student', $roles) || in_array('bcsc_parent', $roles) || in_array('bcsc_spouse', $roles)) {
                $usrProfile->userProfile->require_bcsc_account = false;
            } else {
                $usrProfile->userProfile->require_bcsc_account = true;
            }

            //disable it if not on PRODUCTION and UAT/test
            if (env('APP_ENV') != 'production' && env('APP_ENV') != 'test') {
                $usrProfile->userProfile->require_bcsc_account = false;
            }

            return $usrProfile;
        } elseif (is_null($usrProfile)) {
            //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
            Log::alert('SYSTEM ERROR :: USER_ACCOUNT ->getUserProfile failed for GUID:'.Auth::user()->guid);
            $errors = [];
            $errors['status'] = false;
            $errors['msg'] = 'We are experiencing some technical issues. Please check back again. ';
            $errors['username'] = 'We are experiencing some technical issues. Please check back again. ';

            return $errors;
        } else {
            //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
            if (isset($usrProfile->detail->ProfileFault)) {
                $errors = [];
                $errors['status'] = false;
                $errors['msg'] = $usrProfile->getMessage();
                $errors['username'] = $usrProfile->getMessage();

                return $errors;
            }
            //NOT A VALID PROFILE ERROR SO TRIGGER SYSTEM ERROR
            else {
                Log::alert('SYSTEM ERROR :: USER_ACCOUNT ->getUserProfile'.$usrProfile->getMessage());
            }
        }
    }

    /*
    * fnGetDeclaration:  Used to determine Declaration status.
    * @params:
    * 	$appID = application number
    *		$role = the type of declaration we are determining for
    */
    private function fnGetDeclaration($appID, $role, $noSin = false)
    {
        $this->aeit->uid = Auth::user()->guid;

        //CALL THE APPROPRIATE WEB SERVICE
        $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

        //THE NAME OF OUR CACHE FILE
        $cid = 'get_application_dec'.$this->aeit->uid.''.$appID;

        if ($noSin || $role != 'A') {
            $document = $this->aeit->fnRequest('getWebDeclaration', ['applicationNumber' => $appID, 'role' => $role], $cid, 100);
        } else {
            $document = $this->aeit->fnRequest('getWebDeclaration', ['applicationNumber' => $appID, 'userGUID' => $this->aeit->uid, 'role' => $role], $cid, 100);
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
        //CALL THE APPROPRIATE WEB SERVICE
        $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

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

        $appendix = $this->aeit->fnRequest('getAppendixList', ['userGUID' => $this->aeit->uid], 'get_appendix_list'.$this->aeit->uid, 14400);

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

                                    //$decRequired = $this->fnIsWebDeclarationInkSignatureRequired($av->applicationNumber, $role);
                                    $decRequired = $this->fnGetDeclaration($av->applicationNumber, $role);
                                    $tmp = [];
                                    $id = $av->applicationNumber;
                                    $tmp['ApplicationNumber'] = $av->applicationNumber;
                                    $tmp['FormGUID'] = (isset($av->formGUID)) ? $av->formGUID : null;
                                    $tmp['StudyStartDate'] = (! empty($av->studyStartDate)) ? date('M d, Y', strtotime($av->studyStartDate)) : '-';
                                    $tmp['StudyEndDate'] = (! empty($av->studyEndDate)) ? date('M d, Y', strtotime($av->studyEndDate)) : '-';
                                    $tmp['InkSigRequired'] = ($decRequired == 'Y') ? 'Y' : null;
                                    //$tmp['InkSigRequired'] = NULL;
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
                        $results['error'] = $appendix->detail->SFASFault;
                    } elseif (isset($appendix->detail->ApplicationFault->faultCode)) {
                        $results['error'] = $appendix->detail->ApplicationFault->faultCode;
                    } else {
                        $results['error'] = 'Get Appendix List Error #89832';
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
                        $results['error'] = $appendix->detail->SFASFault;
                    } else {
                        $results['error'] = 'Get Appendix List Error #89833';
                    }

                    return $results;
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
        //CALL THE APPROPRIATE WEB SERVICE
        $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');
        $apps = $this->aeit->fnRequest('getApplicationList', ['userGUID' => $this->aeit->uid], null);

        if (! isset($apps->userApplicationList->inTransition->application)) {
            // cache application list only if there is none in transition because applications in transition will have updates when it is loaded to SFAS.
        }

        $a = [];
        $a['totalApps'] = 0;
        $a['listType'] = 'Applications';
        if (is_soap_fault($apps)) {
            $a['error'] = $apps->faultstring;

            return $a;
        }

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
                            $tmp['DecStatus'] = empty($application->decStatus) ? null : $application->decStatus;

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
                        $tmp['DecStatus'] = empty($application->decStatus) ? null : $application->decStatus;

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
                            $tmp['DecStatus'] = empty($application->decStatus) ? null : $application->decStatus;

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
                        $tmp['DecStatus'] = empty($application->decStatus) ? null : $application->decStatus;

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

                if (isset($a) && is_array($a['Submitted'])) {
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
                            $tmp['DecStatus'] = empty($application->decStatus) ? null : $application->decStatus;

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
                        $tmp['DecStatus'] = empty($application->decStatus) ? null : $application->decStatus;

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

                if (isset($a) && is_array($a['Submitted'])) {
                    krsort($a['Submitted']);
                }
            }

            if (isset($a['Submitted']) && is_array($a['Submitted'])) {
                sort($a['Submitted'], SORT_ASC);
                $a['Submitted'] = array_reverse($a['Submitted']);
            }

            if (isset($a['NotSubmitted']) && is_array($a['NotSubmitted'])) {
                sort($a['NotSubmitted'], SORT_ASC);
                $a['NotSubmitted'] = array_reverse($a['NotSubmitted']);
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
        //GET APPROPRIATE URL FOR WEB SERVICE METHOD
        $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

        $appDetails = $this->aeit->fnRequest('getApplicationDetails', ['userGUID' => $this->aeit->uid, 'applicationNumber' => $appID], null);
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
                    //$this->fnError('SYSTEM ERROR :: USER_APPLICATION_DEFAULT -> getApplicationDetails', $appDetails->getMessage(), $appDetails, $triggerDefault = true);
                }
            }
        }

        return null;
    }

    /*
        fnGetAppendixDetails: calls ws to get application details
        @params: $formGUID: forms GUID used to uniquely identify appendix as appID is not unique indictaor
                         $appID: application number (optional)
        @return: (object) $appDetails or NULL if there are errors
    */
    public function fnGetAppendixDetails($formGUID, $appID = '')
    {
        $appendixDetails = false; //disable cache

        if ($appendixDetails == false) {
            $appendix = $this->fnGetAppendixList(false);

            if (isset($appendix->AppendixLists) && ! empty($appendix->AppendixLists)) {
                foreach ($appendix->AppendixLists as $k => $app) {
                    if (isset($app->appendix)) {
                        //WE HAVE MULTIPLE APPENDICES RETURNED
                        if (is_array($app->appendix)) {
                            foreach ($app->appendix as $ak => $av) {
                                if (! empty($formGUID)) {
                                    if (! empty($appID) && $av->applicationNumber == $appID && $av->formGUID == $formGUID) {
                                        $appendixDetails = $av;
                                        break;
                                    } elseif (! empty($av->formGUID) && $av->formGUID == $formGUID) {
                                        $appendixDetails = $av;
                                        break;
                                    }
                                } else {
                                    if ($av->applicationNumber == $appID) {
                                        $appendixDetails = $av;
                                        break;
                                    }
                                }
                            }
                        } else { //SINGLE RECORD RETURNED
                            if (! empty($formGUID)) {
                                if (! empty($appID) && $app->appendix->applicationNumber == $appID && $app->appendix->formGUID == $formGUID) {
                                    $appendixDetails = $app->appendix;
                                    break;
                                } elseif (! empty($app->appendix->formGUID) && $app->appendix->formGUID == $formGUID) {
                                    $appendixDetails = $app->appendix;
                                    break;
                                }
                            } else {
                                if (! empty($app->appendix->applicationNumber) && $app->appendix->applicationNumber == $appID) {
                                    $appendixDetails = $app->appendix;
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($appendixDetails) {
                    if ($appendixDetails->apxStatus != 'SUBMPROC' && $appendixDetails->apxStatus != 'RDY') {
                        // cache appendix details if it's not SUBMPROC or RDY
                    }

                    return $appendixDetails;
                }
            }
        } elseif (! empty($appendixDetails)) {
            return $appendixDetails;
        }

        //drupal_set_message('Sorry failed to load appendix details.', 'error');
        return false;
    }

    /*
    * requireDec
    * @params: appID: NULL or application number, $role = (A,S or P), $a: NULL or applications events status
    * @return:
    *		- t : means we do require a web dec
    *		- f1/f2 : means we do NOT require a web dec. f1 indicates signature received f2 indicates signature not required
    *		- e : means web dec is expired and no longer available for application
    */
    private function requireDec($appID, $role, $a = null)
    {
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
        }
        //DON'T LOOP IN HERE AND VALIDATE IF PROGRAM YEAR IS < 2012/2013
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
    private function fnIsWebDeclarationInkSignatureRequired($appNumber, $role = 'A')
    {
        // no SIN accounts always require a declaration
        if ($role == 'NS' || $role == 'NP') {
            $webDecStatus = new stdClass();
            $webDecStatus->inkSignatureRequired = 'Y';

            return $webDecStatus;
        }

        //GET APPROPRIATE URL FOR WEB SERVICE METHOD
        $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

        //THE NAME OF OUR CACHE FILE
        $cid = 'web_dec_status'.$this->aeit->uid.''.$appNumber;

        //REQUEST PARAMS
        $params = ['applicationNumber' => $appNumber, 'userGUID' => $this->aeit->uid, 'role' => $role];

        $webDecStatus = $this->aeit->fnRequest('isWebDeclarationInkSignatureRequired', $params, $cid, 100);

        //CHECK TO MAKE SURE WE GET A RESPONSE
        if (! empty($webDecStatus) && is_object($webDecStatus)) {
            //CHECK TO MAKE SURE WE DON'T HAVE ERRORS
            if (! isset($webDecStatus->faultcode) && isset($webDecStatus->inkSignatureRequired)) {
                return $webDecStatus;
            } else {
                //CHECK TO SEE IF THIS IS A WEB DEC ERROR ERROR
                if (isset($webDecStatus->detail->ApplicationFault)) {
                    //drupal_set_message($webDecStatus->getMessage(), 'error');
                } else {
                    //CHECK TO SEE IF THIS IS AN WEB DEC ERROR
                    //$this->fnError('SYSTEM ERROR :: USER_APPLICATION_DEFAULT -> isWebDeclarationInkSignatureRequired', $webDecStatus->getMessage(), $params, $triggerDefault = true);
                }
            }
        }
    }

    /*
    *		GET CURRENT PROGRAM YEARS
    *		@params:void
    *		@return: (object) or void if there are errors
    */
    private function fnGetProgramYear()
    {
        //CALL REST API FOR PROGRAM YEAR
        $url = $this->aeit->fnWS('LC', 'PROG_YEAR');


        $rq = $this->aeit->fnGetCurlRequest($url, false, 'fn_program_years', 302400);

        //MAKE SURE WE GOT A VALUE (not empty or not null)
        if (! empty($rq)) {
            if (isset($cached_value)) {
                $backup_yr = config_path().env('ACTIVE_PY_FILE');

                $xmlDoc = new \DOMDocument('1.0');
                $xmlDoc->loadXML($rq['response']);
                $xmlDoc->save($backup_yr);
            }

            return $this->fnParseProgramYears($rq);
        } else {
            $xmlDoc = new \DOMDocument('1.0');
            $backup_yr = config_path().env('ACTIVE_PY_FILE');

            if (file_exists($backup_yr)) {
                $xml = file_get_contents($backup_yr);
                if (empty($xml)) {
                    $errors['status'] = false;
                    $errors['msg'] = 'Invalid response received';

                    return $errors;
                } else {
                    $rq = [];
                    $rq['response'] = $xml;

                    return $this->fnParseProgramYears($rq);
                }
            } else {
                // make the empty file
                $filepath = fopen($backup_yr, 'w');
                fclose($filepath);
                $errors['status'] = false;
                $errors['msg'] = 'Invalid response received';

                return $errors;
            }
        }
    }

    private function fnParseProgramYears($rq)
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
                $progYears[$i]['thisYear'] = date('Y', strtotime($v->PROGRAM_START_DTE));
                $progYears[$i]['lastYear'] = date('Y', strtotime($v->PROGRAM_END_DTE));
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

    /**
     * METHOD TO CREATE A USER ACCOUNT/PROFILE FOR SABC
     *
     * @param $request
     * @return array
     */
    public function fnCreateUserProfile($request)
    {
        $status = false;
        $msg = '';
        $guid = '';

        $aeit = new Aeit();

        $aeit->uid = null;
        $aeit->valid = true;

        //FORM FIELD MAPPINGS TO OUR WS
        $mappings = [
            'first_name' => 'firstName',
            'middle_name' => 'middleName',
            'last_name' => 'familyName',
            'birthdate' => 'dateOfBirth',
            'social_insurance_number' => 'SIN',
            'gender' => 'gender',
            'email' => 'emailAddress',
            'Street1' => 'addressLine1',
            'Street2' => 'addressLine2',
            'City' => 'city',
            'ProvState' => 'province',
            'Country' => 'country',
            'PostZip' => 'postalCode',
            'Phone' => 'phoneNumber',
            'user_id' => 'userID',
            'password' => 'userPassword',
            'question1' => 'question1Number',
            'answer1' => 'answer1',
            'question2' => 'question2Number',
            'answer2' => 'answer2',
            'question3' => 'question3Number',
            'answer3' => 'answer3',
            'Consent' => 'userConsent',
        ];

        //ERROR CODE MAPPINGS TO OUR FORM FIELDS
        $errorMappings = ['SPSIM11' => 'birthdate',
            'SPSIM15' => 'social_insurance_number',
            'SPSIM21' => 'social_insurance_number',
            'SPSIM22' => 'social_insurance_number',
            'SPSIM16' => 'gender_select',
            'SPSIM14' => 'email',
            'SPSIM25' => 'ProvState',
            'SPSIM24' => 'Country',
            'SPSIM20' => 'social_insurance_number',
            'SPSIM17' => 'user_id',
            'SPSIM26' => 'user_id',
            'SPSIM09' => 'password',
            'SPSIM19' => 'question1',
            'SPSIM20.1' => 'social_insurance_number',
            'SPSIM23' => 'Consent'];

        //CALL THE APPROPRIATE WEB SERVICE
        $aeit->WSDL = $aeit->fnWS('WS-HOSTS', 'USER_ACCOUNT');

        //PRE-POPULATE POOL QUESTIONS INTO OUR ARRAY THAT WE WILL BE PASSING ALONG WITH OUR WS REQUEST
        $ws = ['question1Pool' => 1, 'question2Pool' => 2, 'question3Pool' => 3];

        //LOOP THROUGH ALL THE POSTED FORM VALUES AND FIND THE WS MAPPINGS THAT CORRESPOND TO OUR FIELD
        $inputs = $request->all();
        foreach ($inputs as $k => $v) {
            //loop in here if SABC ID

            //create ws data and make sure to ignore any unnecessary values that are not required for SABC ID
            if (isset($mappings[$k]) && ! empty($v)) {
                if ($k == 'password') {
                    $ws['userPassword'] = $v;
                    $ws['confirmationPassword'] = $v;
                } else {
                    if ($k == 'email') {
                        $v = $this->fnSanitizeData($v, 'email');
                    }
                    if ($k == 'first_name' || $k == 'middle_name' || $k == 'last_name' || $k == 'Street1' || $k == 'Street2') {
                        $v = $this->fnSanitizeData($v);
                    }

                    if ($k == 'Phone') {
                        $v = str_replace(['(', ')', '-', ' '], '', $v);
                    }
                    if ($k != 'password') {
                        $ws[$mappings[$k]] = strtoupper($v);
                    } else {
                        $ws[$mappings[$k]] = $v;
                    }
                }
            }
        }
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnCreateUserProfile() ws: '.json_encode($ws));
            Log::debug('fnCreateUserProfile() ws: ', $ws);
        }

        //CALL CREATEUSERPROFILE WEBSERVICE
        $createProfile = $aeit->fnRequest('createUserProfile', $ws);
        //MAKE SURE WE DON'T HAVE ANY ERRORS
        if (! isset($createProfile->faultcode)) {
            if (isset($createProfile->userProfile)) {
                //SET OUR UID
                $guid = $createProfile->userProfile->userGUID;
                $status = true;

            } else {
                $msg = 'SYSTEM ERROR :: failed to create account #100430.';
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnCreateUserProfile() SYSTEM ERROR :: USER_ACCOUNT -> createUserProfile (userProfile not set). '.$createProfile->getMessage());
                    Log::error('SYSTEM ERROR :: USER_ACCOUNT -> createUserProfile (userProfile not set). '.$createProfile->getMessage());
                }
            }
        } else {
            //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
            if (isset($createProfile->detail->ProfileFault)) {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnCreateUserProfile() SYSTEM ERROR :: USER_ACCOUNT -> createUserProfile (ProfileFault):  '.$createProfile->detail->ProfileFault->faultCode.'-'.$errorMappings[$createProfile->detail->ProfileFault->faultCode].' ('.$createProfile->getMessage().').');
                    Log::debug('fnCreateUserProfile() ws: ', $ws);
                }
                Log::error('SYSTEM ERROR :: USER_ACCOUNT -> createUserProfile (ProfileFault): '.$createProfile->detail->ProfileFault->faultCode.'-'.$errorMappings[$createProfile->detail->ProfileFault->faultCode].' ('.$createProfile->getMessage().').');

                if ($createProfile->detail->ProfileFault->faultCode == 'SPSIM09') {
                    Log::error(' <br>Must include at least one character from 3 of the 4 categories below: <ul><li>- English lower case characters (a-z)</li><li>- English upper case characters (A-Z)</li><li>- Base 10 digits (0-9)</li><li>- Special characters/symbols</li></ul>');
                }

                $msg = $createProfile->getMessage();
                Log::error('SYSTEM ERROR :: failed to create account. Profile Fault error #100431. ');
                if ($createProfile->detail->ProfileFault->faultCode == 'SPSIM09') {
                    $msg = $msg.' <br>Must include at least one character from 3 of the 4 categories below: <ul><li>- English lower case characters (a-z)</li><li>- English upper case characters (A-Z)</li><li>- Base 10 digits (0-9)</li><li>- Special characters/symbols</li></ul>';
                }

            } else {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnCreateUserProfile() SYSTEM ERROR :: failed to create account #100432.');
                    Log::debug('fnCreateUserProfile() ws: ', $ws);
                }
                $msg = 'Sorry, the information you provided failed to create an account. Error #100432.';
                Log::error('SYSTEM ERROR :: USER_ACCOUNT -> createUserProfile Error #100432: '.$createProfile->getMessage());
            }
        }

        return [$status, $msg, $guid];
    }

    /**
     * METHOD TO CREATE A USER ACCOUNT/PROFILE FOR SABC
     *
     * @param $request
     * @return array
     */
    public function fnCreateBcscUserProfile($request)
    {
        $status = false;
        $msg = '';
        $guid = '';

        $aeit = new Aeit();

        $aeit->uid = null;
        $aeit->valid = true;

        //FORM FIELD MAPPINGS TO OUR WS
        $mappings = [
            'first_name' => 'firstName',
            'middle_name' => 'middleName',
            'last_name' => 'familyName',
            'birthdate' => 'dateOfBirth',
            'social_insurance_number' => 'SIN',
            'gender_select' => 'gender_select',
            'gender' => 'gender',
            'email' => 'emailAddress',
            'Street1' => 'addressLine1',
            'Street2' => 'addressLine2',
            'City' => 'city',
            'ProvState' => 'province',
            'Country' => 'country',
            'PostZip' => 'postalCode',
            'Phone' => 'phoneNumber',
            'user_id' => 'userID',
            'assuranceLevel' => 'assuranceLevel',
            'userGUID' => 'userGUID',
        ];

        //ERROR CODE MAPPINGS TO OUR FORM FIELDS
        $errorMappings = [
            'SPSIM11' => 'birthdate',
            'SPSIM15' => 'social_insurance_number',
            'SPSIM21' => 'social_insurance_number',
            'SPSIM22' => 'social_insurance_number',
            'SPSIM16' => 'gender_select',
            'SPSIM14' => 'email',
            'SPSIM25' => 'ProvState',
            'SPSIM24' => 'Country',
            'SPSIM20' => 'social_insurance_number',
            'SPSIM17' => 'user_id',
            'SPSIM26' => 'user_id',
            'SPSIM09' => 'password',
            'SPSIM19' => 'question1',
            'SPSIM20.1' => 'social_insurance_number',
            'SPSIM23' => 'Consent'];

        $aeit->WSDL = $aeit->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');

        //PRE-POPULATE POOL QUESTIONS INTO OUR ARRAY THAT WE WILL BE PASSING ALONG WITH OUR WS REQUEST
        $ws = ['question1Pool' => 1, 'question2Pool' => 2, 'question3Pool' => 3];

        //LOOP THROUGH ALL THE POSTED FORM VALUES AND FIND THE WS MAPPINGS THAT CORRESPOND TO OUR FIELD
        $inputs = $request->input();
        foreach ($inputs as $k => $v) {
            //create ws data and make sure to ignore any unnecessary values that are not required for SABC ID
            if (isset($mappings[$k]) && ! empty($v)) {
                $ws[$mappings[$k]] = $v;
            }
        }
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnCreateBcscUserProfile() ws: '.json_encode($ws));
            Log::debug('fnCreateBcscUserProfile() ws: ', $ws);
        }

        //flush the session output
        Session::save();

        //CALL CREATEUSERPROFILE WEBSERVICE
        $createProfile = $aeit->fnRequest('createUserProfile', $ws);

        //MAKE SURE WE DON'T HAVE ANY ERRORS
        if (! isset($createProfile->faultcode)) {
            if (isset($createProfile->userProfile)) {
                //SET OUR UID
                $guid = $createProfile->userProfile->userGUID;
                $status = true;
            } else {
                $msg = 'SYSTEM ERROR :: failed to create account #100430.';
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnCreateUserProfile() SYSTEM ERROR :: USER_ACCOUNT -> createUserProfile (userProfile not set). '.$createProfile->getMessage());
                    Log::error('SYSTEM ERROR :: USER_ACCOUNT -> createUserProfile (userProfile not set). '.$createProfile->getMessage());
                }
            }
        } else {
            //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
            if (isset($createProfile->detail->ProfileFault)) {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnCreateBcscUserProfile() SYSTEM ERROR :: USER_ACCOUNT -> createUserProfile (ProfileFault):  '.$createProfile->detail->ProfileFault->faultCode.' - '.$errorMappings[$createProfile->detail->ProfileFault->faultCode].' ('.$createProfile->getMessage().').');
                }
                if (env('LOG_ESERVICES') == true) {
                    Log::error('SYSTEM ERROR :: USER_ACCOUNT -> createBcscUserProfile (ProfileFault): '.$createProfile->detail->ProfileFault->faultCode.' - '.$errorMappings[$createProfile->detail->ProfileFault->faultCode].' ('.$createProfile->getMessage().').');
                    Log::error('SYSTEM ERROR :: failed to create account. Profile Fault error #100431-2.');
                }

                $msg = $createProfile->getMessage();
                if ($createProfile->detail->ProfileFault->faultCode == 'SPSIM09') {
                    if (env('LOG_ESERVICES') == true) {
                        Log::error(' <br>Must include at least one character from 3 of the 4 categories below: <ul><li>- English lower case characters (a-z)</li><li>- English upper case characters (A-Z)</li><li>- Base 10 digits (0-9)</li><li>- Special characters/symbols</li></ul>');
                    }
                    $msg = $msg.' <br>Must include at least one character from 3 of the 4 categories below: <ul><li>- English lower case characters (a-z)</li><li>- English upper case characters (A-Z)</li><li>- Base 10 digits (0-9)</li><li>- Special characters/symbols</li></ul>';
                }

                if ($createProfile->detail->ProfileFault->faultCode == 'SPSIM21') {
                    $msg = 'Sorry, creating your profile failed. Please contact StudentAid BC to update your records.';
                }
            } else {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnCreateBcscUserProfile() SYSTEM ERROR :: failed to create account #100432-1.');
                    session()->push('DEBUG', now().': fnCreateBcscUserProfile() SYSTEM ERROR :: USER_ACCOUNT -> createBcscUserProfile: '.$createProfile->getMessage());
                    session()->push('DEBUG', now().': fnCreateBcscUserProfile() SYSTEM ERROR :: USER_ACCOUNT -> createBcscUserProfile: '.json_encode($createProfile));
                }
                $msg = 'SYSTEM ERROR :: failed to create account #100432-1.';
                Log::error('SYSTEM ERROR :: USER_ACCOUNT -> createBcscUserProfile '.$createProfile->getMessage());
            }
        }

        return [$status, $msg, $guid];
    }

    /**
     * @param  bool  $raw
     * @return array|null
     */
    public function fnGetChallengeQuestions()
    {
        //GET APPROPRIATE URL FOR WEB SERVICE METHOD
        $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'CHALLENGE_QUESTIONS');
        $cq = $this->aeit->fnRequest('getChallengeQuestions');
        if (isset($cq->challengeQuestionPool)) {
            $questions = [];
            foreach ($cq->challengeQuestionPool as $k => $v) {
                $questions['pool'.$v->poolNumber.''] = [];
                foreach ($v->challengeQuestion as $id => $val) {
                    $questions['pool'.$v->poolNumber.''][$val->questionNumber] = $val->questionText;
                }
            }

            return $questions;
        }

        return false;
    }

    public function fnSanitizeData($text, $type = 'text')
    {
        if ($type == 'email') {
            $text = filter_var($text, FILTER_SANITIZE_EMAIL);

            return trim($text);
        }

        return str_replace(['"'], '', $text);
    }

    /**
     * POST to upload profile data
     *
     * @param $request
     * @param  bool  $returnView
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|array
     */
    public function fnUpdateProfile($request, $returnView)
    {
        $mappings = [
            'gender' => 'gender',
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

        //values to ignore for SABC ID
        $sabcIgnore = ['assuranceLevel', 'userGUID', 'first_name', 'last_name', 'assuranceLevel'];

        $aeit = new Aeit();
        $aeit->uid = session(env('GUID_SESSION_VAR'));
        //PRE-POPULATE POOL QUESTIONS INTO OUR ARRAY THAT WE WILL BE PASSING ALONG WITH OUR WS REQUEST for sabc accounts
        if (! isset($request->assuranceLevel)) {
            $ws = ['question1Pool' => 1, 'question2Pool' => 2, 'question3Pool' => 3, 'userGUID' => $aeit->uid];
        } else {
            if (isset($request->autoUpdate) && $request->autoUpdate == true) {
                $ws = ['userGUID' => $aeit->fnDecrypt($aeit->uid)];
            } else {
                $ws = ['userGUID' => $aeit->uid];
            }
        }

        $inputs = $request->all();
        foreach ($inputs as $k => $v) {
            if (isset($mappings[$k]) && ! empty($v)) {
                if ($k == 'new_password') {
                    $ws['password'] = $v;
                    $ws['confirmationPassword'] = $v;
                } else {
                    if (isset($request->assuranceLevel) && ! in_array($k, $bcscIgnore)) {
                        $ws[$mappings[$k]] = strtoupper($v);
                    } elseif (! in_array($k, $sabcIgnore)) {
                        if ($k == 'first_name' || $k == 'last_name') {
                            $v = str_replace(['"'], '', $v);
                        }
                        $ws[$mappings[$k]] = strtoupper($v);
                    }
                }
            }
        }

        if (empty($request->assuranceLevel)) {
            $aeit->WSDL = $aeit->fnWS('WS-HOSTS', 'USER_ACCOUNT');
        } else {
            $aeit->WSDL = $aeit->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');
        }

        $submit_status = false;
        $submit_msg = '';
        $updateProfile = $aeit->fnRequest('updateUserProfile', $ws, 'update_user_profile'.$aeit->uid, 0);

        if (isset($updateProfile->faultcode)) {
            //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
            if (isset($updateProfile->detail->ProfileFault)) {
                $submit_msg = $errorMappings[$updateProfile->detail->ProfileFault->faultCode].'. '.$updateProfile->getMessage();
            } else {
                Log::error('SYSTEM ERROR :: USER_ACCOUNT -> updateUserProfile: '.$updateProfile->getMessage());
            }

        // return FALSE;
        } else {
            if (isset($_SESSION['bcsc_profile'])) {
                unset($_SESSION['bcsc_profile']);
            }

            //Update profile was updated return TRUE
            $submit_status = true;
        }

        Cache::forget(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'));
        if ($returnView == true) {
            return view('profile', ['submit_status' => $submit_status, 'submit_msg' => $submit_msg]);
        }

        return [$submit_status, $submit_msg];
    }

    /**
     * @param $did
     * @return array
     */
    public function fnBcscDashboardLogin($did)
    {
        $this->aeit = new Aeit();
        $uid = $this->aeit->fnDecrypt($did);

        $this->aeit->uid = $uid;
        if (isset($_SESSION['bcsc_profile'])) {
            $passphrase = config('services.aeit.salt');
            $cipher = config('services.aeit.cipher');
            $iv = config('services.aeit.iv');
            $saml = openssl_encrypt(json_encode($_SESSION['bcsc_profile']), $cipher, $passphrase, 0, $iv);
        }
        [$bcscusrProfile, $bcscusrProfileStatus] = $this->fnGetBCSCUserProfile();

        $create_user = false;
        $user = null;
        //user has an account
        if ($bcscusrProfileStatus == true) {
            $assuranceLevel1 = $bcscusrProfile->userProfile->assuranceLevel;
            $encrypted_guid = $did;
            $user = User::where('name', $encrypted_guid)->with('roles')->first();

        //redirect to create account provided the SAML_RESPONSE
        } else {
            $create_user = true;
            $assuranceLevel1 = null;
        }

        return [$user, $bcscusrProfile, $create_user, $uid];
    }

    /**
     * Generate a random alphanumeric password.
     */
    public function user_password($length = 10)
    {
        // This variable contains the list of allowable characters for the
        // password. Note that the number 0 and the letter 'O' have been
        // removed to avoid confusion between the two. The same is true
        // of 'I', 1, and 'l'.
        $allowable_characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        // Zero-based count of characters in the allowable list:
        $len = strlen($allowable_characters) - 1;

        // Declare the password as a blank string.
        $pass = '';

        // Loop the number of times specified by $length.
        for ($i = 0; $i < $length; $i++) {
            do {
                // Find a secure random number within the range needed.
                $index = rand(5, 15);
            } while ($index > $len);

            // Each iteration, pick a random character from the
            // allowable string and append it to the password:
            $pass .= $allowable_characters[$index];
        }

        return $pass;
    }

    public function fnGetBCSCUserProfile()
    {
        $status = false;
        $profile = null;
        //CALL GET USER PROFILE SOAP WEB SERVICE
        $this->aeit->WSDL = $this->aeit->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');
        $bcscusrProfile = $this->aeit->fnRequest('getUserProfile', ['userGUID' => $this->aeit->uid], 'get_user_profile'.$this->aeit->uid, 14400);

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnGetBCSCUserProfile(): this->aeit->uid: '.$this->aeit->uid);
            session()->push('DEBUG', now().': fnGetBCSCUserProfile(): this->aeit->WSDL: '.$this->aeit->WSDL);
            session()->push('DEBUG', now().': fnGetBCSCUserProfile(): bcscusrProfile: '.json_encode($bcscusrProfile));
        }

        if (is_null($bcscusrProfile)) {
        }
        //MAKE SURE IT IS NOT AN ERROR
        elseif (! isset($bcscusrProfile->faultcode)) {
            $bcscusrProfile->status = true;
            $bcscusrProfile->userProfile->SIN = $this->aeit->fnEncrypt($bcscusrProfile->userProfile->SIN);
            $bcscusrProfile->userProfile->userGUID = $this->aeit->fnEncrypt($bcscusrProfile->userProfile->userGUID);
            $status = true;

            $profile = $bcscusrProfile;
        } else {
            //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
            if (isset($bcscusrProfile->detail->ProfileFault)) {
                $profile = $bcscusrProfile->getMessage();
            } //NOT A VALID PROFILE ERROR SO TRIGGER SYSTEM ERROR
            else {
                $profile = 'An error occurred attempting to fetch the BCSC user profile.';
            }
        }

        return [$profile, $status];
    }

    public function fnCert()
    {
        $env = env('APP_ENV');
        $config = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), null);
        if (is_null($config)) {
            $config = parse_ini_file(config_path().env('APP_CONFIG_FILE'), true);
            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), $config, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
        }

        if (isset($config['CERTS'])) {
            return $config['CERTS'][$env];
        } else {
            return null;
        }
    }

    public function fnBCSCSAMLParser($data)
    {
        $decoded = base64_decode($data);

        $dom = new \DOMDocument('1.0');
        $dom->loadXML($decoded);

        //data returned from BC Services for DEV does not use certificates to encrypt data
        //only UAT and PROD use certificates which will include the element tag name EncryptedAssertion
        //make sure we have EncryptedAssertion
        $encryptedAssertionNodes = $dom->getElementsByTagName('EncryptedAssertion');
        if ($encryptedAssertionNodes->length !== 0) {
            $cert = $this->fnCert();

            $filename = config_path().'/certs/'.$cert.'.pem';
            $objenc = new XmlSecEnc();
            $encData = $objenc->locateEncryptedData($dom);

            if (! $encData) {
                throw new \Exception('Cannot process SAML response', 1);
            }

            $objenc->setNode($encData);
            $objenc->type = $encData->getAttribute('Type');

            if (! $objKey = $objenc->locateKey()) {
                throw new \Exception('Error Processing Request', 1);
            }

            $key = null;
            if ($objKeyInfo = $objenc->locateKeyInfo($objKey)) {
                if ($objKeyInfo->isEncrypted) {

                    $objencKey = $objKeyInfo->encryptedCtx;
                    $objKeyInfo->loadKey($filename, true, false);
                    $key = $objencKey->decryptKey($objKeyInfo);
                }

            }

            if (empty($objKey->key)) {
                $objKey->loadKey($key);
            }
            $decrypted = $objenc->decryptNode($objKey, true);

            if ($decrypted instanceof \DOMDocument) {
                return $decrypted;
            } else {
                $encryptedAssertion = $decrypted->parentNode;
                $container = $encryptedAssertion->parentNode;
                // Fix possible issue with saml namespace
                if (! $decrypted->hasAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:saml') &&
                    ! $decrypted->hasAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:saml2') &&
                    ! $decrypted->hasAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns') &&
                    ! $container->hasAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:saml') &&
                    ! $container->hasAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:saml2')
                ) {
                    if (strpos($encryptedAssertion->tagName, 'saml2:') !== false) {
                        $ns = 'xmlns:saml2';
                    } elseif (strpos($encryptedAssertion->tagName, 'saml:') !== false) {
                        $ns = 'xmlns:saml';
                    } else {
                        $ns = 'xmlns';
                    }
                    $decrypted->setAttributeNS('http://www.w3.org/2000/xmlns/', $ns, 'urn:oasis:names:tc:SAML:2.0:assertion');
                }
                $container->replaceChild($decrypted, $encryptedAssertion);
                $dom = $decrypted->ownerDocument;
                $decoded = $dom->saveXML();
            }
        }

        $xml = simplexml_load_string($decoded);
        $xml->registerXPathNamespace('ns1', 'urn:oasis:names:tc:SAML:2.0:assertion');

        $responsedata = [];

        $result = $xml->xpath('//ns1:Attribute');
        foreach ($result as $value) {
            $child = $xml->xpath('//ns1:Attribute[@Name="'.$value['Name'].'"]/ns1:AttributeValue');
            $v = $child[0];
            if ($value['Name'] == 'useridentifier') {
                $DID = preg_split('/[|:]/', (string) $v[0]);
                $responsedata['DID'] = $this->aeit->fnEncrypt($DID[2]);
            }
            $responsedata[''.$value['Name'].''] = (string) $v[0];

        }
        $split_DOB = preg_split('/-/', $responsedata['birthdate']);

        $name = [];

        if (isset($responsedata['givennames'])) {
            $fullname = $responsedata['givennames'];

            if (isset($responsedata['givenname'])) {
                $fname = $responsedata['givenname'];
                $mname = str_replace($responsedata['givenname'], '', $responsedata['givennames']);
            } else {
                $fname = strtok($fullname, ' ');
            }
            array_push($name, $fname);
        }
        if (isset($responsedata['surname'])) {
            $lname = $responsedata['surname'];
            array_push($name, $lname);
        }

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            Log::warning(": fnBCSCSAMLParser(): SAML Response data 'responsedata' next line");
            Log::warning(': '.json_encode($responsedata));
        }

        if (! isset($responsedata['streetaddress'])) {
        } else {
            $address_line = explode('|', $responsedata['streetaddress']);
            $street1 = substr($address_line[0], 0, 35);
            if (count($address_line) > 1) {
                $street2 = substr($address_line[1], 0, 35);
            }
        }

        $responsedata['DID'] = $responsedata['DID'];
        $responsedata['firstName'] = (isset($fname)) ? $fname : null;
        $responsedata['middleName'] = (isset($mname)) ? $mname : (isset($fname) ? ltrim(str_replace($fname, ' ', $responsedata['givennames'])) : null);
        $responsedata['lastName'] = (isset($lname)) ? $lname : null;
        $responsedata['DOB'] = $responsedata['birthdate'];
        $responsedata['Year'] = $split_DOB[0];
        $responsedata['Month'] = $split_DOB[1];
        $responsedata['Day'] = $split_DOB[2];
        $responsedata['gender'] = $responsedata['sex'][0];
        $responsedata['assuranceLevel'] = $responsedata['identityassurancelevel'];

        $responsedata['Street1'] = isset($street1) ? $street1 : null;
        $responsedata['Street2'] = isset($street2) ? $street2 : null;

        $responsedata['City'] = isset($responsedata['locality']) ? $responsedata['locality'] : null;
        $responsedata['Province'] = 'BC';
        $responsedata['Country'] = 'CAN';
        $responsedata['PostZip'] = isset($responsedata['postalcode']) ? $responsedata['postalcode'] : null;

        $_SESSION['bcsc_profile'] = $responsedata; //used to compare profile data after login

        if (! isset($responsedata['streetaddress'])) {
            Log::warning(': fnBCSCSAMLParser(): SAML Response missing streetaddress');
            Log::warning('firstName: '.$responsedata['firstName']);
            Log::warning('middleName: '.$responsedata['middleName']);
            Log::warning('lastName: '.$responsedata['lastName']);
        }

        return $responsedata;
    }

    //#################################################
    //#################################################
    //#################################################
    //#### PASSWORD HANDLERS MIGRATED FROM DRUPAL #####
    //#################################################
    //#################################################
    //#################################################

    /**
     * Check whether a plain text password matches a stored hashed password.
     *
     * Alternative implementations of this function may use other data in the
     * $account object, for example the uid to look up the hash in a custom table
     * or remote database.
     *
     * @param $password
     *   A plain-text password
     * @param $account
     *   A user object with at least the fields from the {users} table.
     * @return
     *   TRUE or FALSE.
     */
    public function user_check_password($password, $account)
    {
        if (substr($account->password, 0, 2) == 'U$') {
            // This may be an updated password from user_update_7000(). Such hashes
            // have 'U' added as the first character and need an extra md5().
            $stored_hash = substr($account->password, 1);
            $password = md5($password);
        } else {
            $stored_hash = $account->password;
        }

        $type = substr($stored_hash, 0, 3);
        switch ($type) {
            case '$S$':
                // A normal Drupal 7 password using sha512.
                $hash = $this->_password_crypt('sha512', $password, $stored_hash);
                break;
            case '$H$':
                // phpBB3 uses "$H$" for the same thing as "$P$".
            case '$P$':
                // A phpass password generated using md5.  This is an
                // imported password or from an earlier Drupal version.
                $hash = $this->_password_crypt('md5', $password, $stored_hash);
                break;
            default:
                return false;
        }

        return $hash && $stored_hash == $hash;
    }

    /**
     * Hash a password using a secure stretched hash.
     *
     * By using a salt and repeated hashing the password is "stretched". Its
     * security is increased because it becomes much more computationally costly
     * for an attacker to try to break the hash by brute-force computation of the
     * hashes of a large number of plain-text words or strings to find a match.
     *
     * @param $algo
     *   The string name of a hashing algorithm usable by hash(), like 'sha256'.
     * @param $password
     *   Plain-text password up to 512 bytes (128 to 512 UTF-8 characters) to hash.
     * @param $setting
     *   An existing hash or the output of _password_generate_salt().  Must be
     *   at least 12 characters (the settings and salt).
     * @return
     *   A string containing the hashed password (and salt) or FALSE on failure.
     *   The return string will be truncated at DRUPAL_HASH_LENGTH characters max.
     */
    private function _password_crypt($algo, $password, $setting)
    {
        // Prevent DoS attacks by refusing to hash large passwords.
        if (strlen($password) > 512) {
            return false;
        }
        // The first 12 characters of an existing hash are its setting string.
        $setting = substr($setting, 0, 12);

        if ($setting[0] != '$' || $setting[2] != '$') {
            return false;
        }
        $count_log2 = $this->_password_get_count_log2($setting);
        // Hashes may be imported from elsewhere, so we allow != DRUPAL_HASH_COUNT
        if ($count_log2 < DRUPAL_MIN_HASH_COUNT || $count_log2 > DRUPAL_MAX_HASH_COUNT) {
            return false;
        }
        $salt = substr($setting, 4, 8);
        // Hashes must have an 8 character salt.
        if (strlen($salt) != 8) {
            return false;
        }

        // Convert the base 2 logarithm into an integer.
        $count = 1 << $count_log2;

        // We rely on the hash() function being available in PHP 5.2+.
        $hash = hash($algo, $salt.$password, true);
        do {
            $hash = hash($algo, $hash.$password, true);
        } while (--$count);

        $len = strlen($hash);
        $output = $setting.$this->_password_base64_encode($hash, $len);

        // _password_base64_encode() of a 16 byte MD5 will always be 22 characters.
        // _password_base64_encode() of a 64 byte sha512 will always be 86 characters.
        $expected = 12 + ceil((8 * $len) / 6);

        return (strlen($output) == $expected) ? substr($output, 0, DRUPAL_HASH_LENGTH) : false;
    }

    /**
     * Parse the log2 iteration count from a stored hash or setting string.
     */
    private function _password_get_count_log2($setting)
    {
        $itoa64 = $this->_password_itoa64();

        return strpos($itoa64, $setting[3]);
    }

    /**
     * Encodes bytes into printable base 64 using the *nix standard from crypt().
     *
     * @param $input
     *   The string containing bytes to encode.
     * @param $count
     *   The number of characters (bytes) to encode.
     * @return
     *   Encoded string
     */
    private function _password_base64_encode($input, $count)
    {
        $output = '';
        $i = 0;
        $itoa64 = $this->_password_itoa64();
        do {
            $value = ord($input[$i++]);
            $output .= $itoa64[$value & 0x3F];
            if ($i < $count) {
                $value |= ord($input[$i]) << 8;
            }
            $output .= $itoa64[($value >> 6) & 0x3F];
            if ($i++ >= $count) {
                break;
            }
            if ($i < $count) {
                $value |= ord($input[$i]) << 16;
            }
            $output .= $itoa64[($value >> 12) & 0x3F];
            if ($i++ >= $count) {
                break;
            }
            $output .= $itoa64[($value >> 18) & 0x3F];
        } while ($i < $count);

        return $output;
    }

    /**
     * Returns a string for mapping an int to the corresponding base 64 character.
     */
    private function _password_itoa64()
    {
        return './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    }

//# FOR CREATING ACCOUNTS OR UPDATES

    /**
     * Hash a password using a secure hash.
     *
     * @param $password
     *   A plain-text password.
     * @param $count_log2
     *   Optional integer to specify the iteration count. Generally used only during
     *   mass operations where a value less than the default is needed for speed.
     * @return
     *   A string containing the hashed password (and a salt), or FALSE on failure.
     */
    public function user_hash_password($password, $count_log2 = 0)
    {
        if (empty($count_log2)) {
            // Use the standard iteration count.
            $count_log2 = DRUPAL_HASH_COUNT;
        }

        return $this->_password_crypt('sha512', $password, $this->_password_generate_salt($count_log2));
    }

    /**
     * Generates a random base 64-encoded salt prefixed with settings for the hash.
     *
     * Proper use of salts may defeat a number of attacks, including:
     *  - The ability to try candidate passwords against multiple hashes at once.
     *  - The ability to use pre-hashed lists of candidate passwords.
     *  - The ability to determine whether two users have the same (or different)
     *    password without actually having to guess one of the passwords.
     *
     * @param $count_log2
     *   Integer that determines the number of iterations used in the hashing
     *   process. A larger value is more secure, but takes more time to complete.
     * @return
     *   A 12 character string containing the iteration count and a random salt.
     */
    private function _password_generate_salt($count_log2)
    {
        $output = '$S$';
        // Ensure that $count_log2 is within set bounds.
        $count_log2 = $this->_password_enforce_log2_boundaries($count_log2);
        // We encode the final log2 iteration count in base 64.
        $itoa64 = $this->_password_itoa64();
        $output .= $itoa64[$count_log2];
        // 6 bytes is the standard salt for a portable phpass hash.
        $output .= $this->_password_base64_encode($this->drupal_random_bytes(6), 6);

        return $output;
    }

    /**
     * Ensures that $count_log2 is within set bounds.
     *
     * @param $count_log2
     *   Integer that determines the number of iterations used in the hashing
     *   process. A larger value is more secure, but takes more time to complete.
     * @return
     *   Integer within set bounds that is closest to $count_log2.
     */
    private function _password_enforce_log2_boundaries($count_log2)
    {
        if ($count_log2 < DRUPAL_MIN_HASH_COUNT) {
            return DRUPAL_MIN_HASH_COUNT;
        } elseif ($count_log2 > DRUPAL_MAX_HASH_COUNT) {
            return DRUPAL_MAX_HASH_COUNT;
        }

        return (int) $count_log2;
    }

    /**
     * Returns a string of highly randomized bytes (over the full 8-bit range).
     *
     * This function is better than simply calling mt_rand() or any other built-in
     * PHP function because it can return a long string of bytes (compared to < 4
     * bytes normally from mt_rand()) and uses the best available pseudo-random
     * source.
     *
     * @param $count
     *   The number of characters (bytes) to return in the string.
     */
    private function drupal_random_bytes($count)
    {
        // $random_state does not use drupal_static as it stores random bytes.
        static $random_state, $bytes, $has_openssl;

        $missing_bytes = $count - strlen($bytes);

        if ($missing_bytes > 0) {
            // PHP versions prior 5.3.4 experienced openssl_random_pseudo_bytes()
            // locking on Windows and rendered it unusable.
            if (! isset($has_openssl)) {
                $has_openssl = version_compare(PHP_VERSION, '5.3.4', '>=') && function_exists('openssl_random_pseudo_bytes');
            }

            // openssl_random_pseudo_bytes() will find entropy in a system-dependent
            // way.
            if ($has_openssl) {
                $bytes .= openssl_random_pseudo_bytes($missing_bytes);
            }

            // Else, read directly from /dev/urandom, which is available on many *nix
            // systems and is considered cryptographically secure.
            elseif ($fh = @fopen('/dev/urandom', 'rb')) {
                // PHP only performs buffered reads, so in reality it will always read
                // at least 4096 bytes. Thus, it costs nothing extra to read and store
                // that much so as to speed any additional invocations.
                $bytes .= fread($fh, max(4096, $missing_bytes));
                fclose($fh);
            }

            // If we couldn't get enough entropy, this simple hash-based PRNG will
            // generate a good set of pseudo-random bytes on any system.
            // Note that it may be important that our $random_state is passed
            // through hash() prior to being rolled into $output, that the two hash()
            // invocations are different, and that the extra input into the first one -
            // the microtime() - is prepended rather than appended. This is to avoid
            // directly leaking $random_state via the $output stream, which could
            // allow for trivial prediction of further "random" numbers.
            if (strlen($bytes) < $count) {
                // Initialize on the first call. The contents of $_SERVER includes a mix of
                // user-specific and system information that varies a little with each page.
                if (! isset($random_state)) {
                    $random_state = print_r($_SERVER, true);
                    if (function_exists('getmypid')) {
                        // Further initialize with the somewhat random PHP process ID.
                        $random_state .= getmypid();
                    }
                    $bytes = '';
                }

                do {
                    $random_state = hash('sha256', microtime().mt_rand().$random_state);
                    $bytes .= hash('sha256', mt_rand().$random_state, true);
                } while (strlen($bytes) < $count);
            }
        }
        $output = substr($bytes, 0, $count);
        $bytes = substr($bytes, $count);

        return $output;
    }
}
