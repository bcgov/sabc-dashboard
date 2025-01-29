<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotBcscStepOneRequest;
use App\Http\Requests\ForgotBcscStepTwoRequest;
use App\Role;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Redirect;

//use App\Http\Requests\ForgotPasswordResetRequest;

class ForgotBcscController extends Aeit
{
    public function redirectToStepOne(Request $request)
    {
        return redirect('/temporary');
    }

    /**
     * @param  ForgotBcscStepOneRequest  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function forgotBcscStepOne(ForgotBcscStepOneRequest $request)
    {
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_AUTH');

        //RECOVER USER
        $recoverProfile = $this->fnRequest('recoverUser', ['familyName' => $request->lastname, 'SIN' => $request->socialinsnum, 'dateOfBirth' => $request->dateOfBirth]);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': forgotBcscStepOne(): lastname: '.$request->lastname);
            session()->push('DEBUG', now().': forgotBcscStepOne(): socialinsnum: '.$request->socialinsnum);
            session()->push('DEBUG', now().': forgotBcscStepOne(): dateOfBirth: '.$request->dateOfBirth);

            session()->push('DEBUG', now().': forgotBcscStepOne(): recoverProfile: '.json_encode($recoverProfile));
            if (isset($recoverProfile->detail)) {
                session()->push('DEBUG', now().': forgotBcscStepOne(): recoverProfile->detail: '.json_encode($recoverProfile->detail));
            }
            if (isset($recoverProfile->detail)) {
                session()->push('DEBUG', now().': forgotBcscStepOne(): verifyChallenge->getMessage: '.$recoverProfile->getMessage());
            }
        }
        $errorMappings = ['SPSIM15' => 'socialinsnum', 'SPSIM01.2' => 'hpot', 'SPSIM02' => 'hpot', 'SPSIM03' => 'hpot', 'SPSIM10' => 'hpot', 'SPSIM11' => 'day', 'SPSIM21' => 'hpot', 'SPSIM01.1' => 'hpot'];

        //MAKE SURE NO ERRORS WERE RETURNED
        if (! isset($recoverProfile->faultcode)) {
            //MAKE SURE WE GOT RESULTS
            if (isset($recoverProfile->recoverUserReturn)) {
                if (! isset($recoverProfile->recoverUserReturn->rescue)) {
                    Log::error('User recovery missing parameter rescue');
                    Log::error('recoverProfile->recoverUserReturn'.json_encode($recoverProfile->recoverUserReturn));

                    return redirect()->back()->withInput()->withErrors(['errors' => 'Sorry, we were unable to find a temporary access code for your account. Please call 1-800-561-1818 for assistance. Error #7599822']);
                }

                if ($recoverProfile->recoverUserReturn->rescue === 'N') {
                    return redirect()->back()->withInput()->withErrors(['errors' => 'Sorry, we were unable to find a temporary access code for your account. Please call 1-800-561-1818 for assistance.']);
                }

                //GET LIST OF CHALLENGE QUESTIONS SO THAT WE CAN DETERMINE USERS CHALLENGE QUESTIONS
                $user = new User();
                $challenge = $user->fnGetChallengeQuestions();

                //MAKE SURE WE GOT A LIST RETURNED TO US
                if (! empty($challenge)) {
                    //ENCRYPT GUID BECAUSE WE WILL BE USING THIS TO PASS ALONG FROM STEP TO STEP FOR FORGOT PASSWORD RECOVERY
                    $recoverProfile->recoverUserReturn->guid = $this->fnEncrypt($recoverProfile->recoverUserReturn->guid);

                    //view challenge question
                    return view('auth.bcsc_rescue.bcsc-forgot-step-2', ['env' => $this->env, 'data' => $recoverProfile->recoverUserReturn, 'step' => 2]);
                }
            }
        } else {
            //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
            if (isset($recoverProfile->detail->CredentialFault)) {
                $msg = $recoverProfile->getMessage();
            } else {
                $msg = $recoverProfile->getMessage();
            }

            return redirect()->back()->withInput()->withErrors(['errors' => $msg]);
        }

        return redirect()->back()->withInput()->withErrors(['errors' => 'Sorry, we were unable to find a temporary access code for your account. Please call 1-800-561-1818 for assistance. Error #239992-1']);
    }

    /**
     * @param  ForgotBcscStepTwoRequest  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function forgotBcscStepTwo(ForgotBcscStepTwoRequest $request)
    {
        $userGUID = $this->fnDecrypt($request->uid);
        $answer = intval($request->answer);

        $this->WSDL = $this->fnWS('WS-HOSTS', 'BCSC_VERIFY_CHALLENGE');

        //RECOVER USER
        $response = $this->fnRequest('verifyBcscChallengeQuestion', ['userGUID' => $userGUID, 'answer' => $answer]);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': forgotBcscStepTwo(): uid: '.$request->uid);
            session()->push('DEBUG', now().': forgotBcscStepTwo(): GUID: '.$userGUID);
            session()->push('DEBUG', now().': forgotBcscStepTwo(): answer: '.$answer);

            session()->push('DEBUG', now().': forgotBcscStepTwo(): response: '.json_encode($response));
            if (isset($response->verifyBcscChallengeQuestionReturn)) {
                session()->push('DEBUG', now().': forgotBcscStepTwo(): response->verifyBcscChallengeQuestionReturn: '.json_encode($response->verifyBcscChallengeQuestionReturn));
            }
        }

        //MAKE SURE NO ERRORS WERE RETURNED
        if (! isset($response->faultcode)) {
            //MAKE SURE WE GOT RESULTS
            if (isset($response->verifyBcscChallengeQuestionReturn)) {
                $response_access = 'N';
                $response_locked = 'Y';

                // Set form state values for furure use
                if (isset($response->verifyBcscChallengeQuestionReturn) && isset($response->verifyBcscChallengeQuestionReturn->access)) {
                    $response_access = $response->verifyBcscChallengeQuestionReturn->access;
                }

                if (isset($response->verifyBcscChallengeQuestionReturn) && isset($response->verifyBcscChallengeQuestionReturn->locked)) {
                    $response_locked = $response->verifyBcscChallengeQuestionReturn->locked;
                }

                // If account is not locked and answer is not correct
                if ($response_access == 'N' && $response_locked == 'N') {
                    return view('auth.bcsc_rescue.bcsc-forgot-step-2', ['env' => $this->env, 'step' => 2, 'data' => ['rescue' => 'Y', 'guid' => $request->uid]])->withErrors(['That challenage answer is not correct, please try again.']);
                }

                /*
                 * if too many attempts: rescue=N
                 * if valid rescue set: rescue=Y

                 * if answer correct: access=Y & locked=N <<<<< THIS IS WHAT WE NEED TO GRANT ACCESS
                 * if answer incorrect: access=N & locked=N
                 * if too many incorrect: access=N & locked=Y & rescue=N
                 */

                if ($response_locked === 'N' && $response_access === 'Y') {
                    $user = $this->fnSystemLogin($userGUID);
                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': forgotBcscStepTwo(): user: '.json_encode($user));
                    }

                    if (! empty($user)) {
                        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                            session()->push('DEBUG', now().': forgotBcscStepTwo(): isset(user->status): '.isset($user->status));
                            session()->push('DEBUG', now().': forgotBcscStepTwo(): !empty(user->status): '.! empty($user->status));
                        }
                        if (is_array($user)) {
                            if (isset($user['status']) && ! empty($user['status'])) {
                                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                                    session()->push('DEBUG', now().': forgotBcscStepTwo(): user is array');
                                }

                                $login = $this->fnDashboardLogin($user['uid'], $user['login_name'], 'student');
                                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                                    session()->push('DEBUG', now().': forgotBcscStepTwo(): login: '.$login);
                                }

                                if ($login == 'dashboard') {
                                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                                        session()->push('DEBUG', now().': access(): 2 user: '.json_encode($user));
                                    }
                                    Session::save();

                                    return redirect('/');
                                }
                            }
                        } elseif (is_object($user)) {
                            if (isset($user->status) && ! empty($user->status)) {
                                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                                    session()->push('DEBUG', now().': forgotBcscStepTwo(): user is object');
                                }

                                $login = $this->fnDashboardLogin($user['uid'], $user['login_name'], 'student');
                                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                                    session()->push('DEBUG', now().': forgotBcscStepTwo(): login: '.$login);
                                }

                                if ($login == 'dashboard') {
                                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                                        session()->push('DEBUG', now().': access(): 2 user: '.json_encode($user));
                                    }
                                    Session::save();

                                    return redirect('/');
                                }
                            }
                        }

                        return redirect()->back()->withInput()->withErrors(['errors' => 'Sorry, we were unable to find your account. Please call 1-800-561-1818 for assistance. Error #239994-2']);
                    }
                }
            }
        }

        return redirect()->back()->withInput()->withErrors(['errors' => 'Sorry, we were unable to verify your account. Please call 1-800-561-1818 for assistance. Error #239993-2']);
    }

    private function fnSystemLogin($userGUID)
    {
        $up = null;

        $this->uid = $userGUID;
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
                    //$up = null;
                }
            }
        }
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnSystemLogin(): up: '.json_encode($up));
        }

        if (isset($up->status) && ! empty($up->status)) {
            $user['status'] = true;
            $user['uid'] = $this->fnEncrypt($this->uid);

            $name = isset($up->userProfile->firstName) ? $up->userProfile->firstName : '';
            if ($name != '') {
                $name .= ' ';
            }
            $name .= isset($up->userProfile->familyName) ? $up->userProfile->familyName : '';
            $user['login_name'] = $name;

            return $user;
        }

        return $up;
    }

    private function fnDashboardLogin($uid, $userName, $userRole = 'student', $request = null)
    {
        $guid = $this->fnDecrypt($uid);

        $this->uid = $uid;
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnDashboardLogin(): encrypted guid: '.$this->uid);
        }

        if (isset($userName)) {
            $bcscusrProfile = $this->fnGetBCSCUserProfile();
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnDashboardLogin(): bcscusrProfile: '.json_encode($bcscusrProfile));
                session()->push('DEBUG', now().': fnDashboardLogin(): guid: '.$guid);
                session()->push('DEBUG', now().': fnDashboardLogin(): this->uid: '.$this->uid);
            }

            $this->user = User::where('name', $this->uid)->with('roles')->first();
            if (is_null($this->user)) {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnDashboardLogin(): we could not find the user');
                }
                if (! is_null($uid) && is_null($this->user)) {
                    $next_uid = User::select('uid')->orderBy('uid', 'desc')->first();
                    $old_uid = User::select('uid')->where('name', 'like', $bcscusrProfile->userProfile->userGUID)->first();

                    $user = new User();
                    $user->uid = $next_uid->uid + 1;

                    //if the user has an account recreate it with the same UID
                    if (! is_null($old_uid)) {
                        $user->uid = $old_uid->uid;
                    }

                    $user->name = $bcscusrProfile->userProfile->userGUID;
                    $user->password = $user->user_hash_password(Str::random(Aeit::DRUPAL_HASH_COUNT));
                    $user->created = strtotime('now');
                    $user->status = 1;
                    $user->save();

                    $user_role = Role::where('name', 'bcsc_student')->first();
                    $user->roles()->attach($user_role);

                    $this->user = User::where('name', $bcscusrProfile->userProfile->userGUID)->with('roles')->first();
                }
            }

            if (isset($bcscusrProfile->userProfile)) {
                $assuranceLevel1 = $bcscusrProfile->userProfile->assuranceLevel;
                $userRole = 'bcsc_student';

                $userProfile = $this->fnBcscLogin();
            } else {
                $assuranceLevel1 = null;
                $userProfile = $this->fnGetUserProfile($guid);
            }

            Auth::login($this->user);
            session([env('GUID_SESSION_VAR') => $guid, env('ADMIN_APP_SUPPORT_STUDENT_SESSION_VAR3') => Auth::user()->email]);
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnDashboardLogin(): userProfile: '.json_encode($userProfile));
                session()->push('DEBUG', now().': fnDashboardLogin(): session guid: '.session(env('GUID_SESSION_VAR')));
            }

            return 'dashboard';
        } else {
            return false;
        }
    }
}
