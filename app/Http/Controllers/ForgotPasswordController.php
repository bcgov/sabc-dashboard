<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordResetRequest;
use App\Http\Requests\ForgotPasswordStepOneRequest;
use App\Http\Requests\ForgotPasswordStepTwoRequest;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Redirect;

class ForgotPasswordController extends Aeit
{
    public function redirectToStepOne(Request $request)
    {
        return redirect('/forgot/password');
    }

    /**
     * @param  ForgotPasswordStepOneRequest  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function forgotPasswordStepOne(ForgotPasswordStepOneRequest $request)
    {
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_AUTH');

        //RECOVER USER
        $recoverProfile = $this->fnRequest('recoverUser', ['familyName' => $request->lastname, 'SIN' => $request->socialinsnum, 'dateOfBirth' => $request->dateOfBirth]);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': forgotPasswordStepOne(): lastname: '.$request->lastname);
            session()->push('DEBUG', now().': forgotPasswordStepOne(): socialinsnum: '.$request->socialinsnum);
            session()->push('DEBUG', now().': forgotPasswordStepOne(): dateOfBirth: '.$request->dateOfBirth);

            session()->push('DEBUG', now().': forgotPasswordStepOne(): recoverProfile: '.json_encode($recoverProfile));
            if (isset($recoverProfile->detail)) {
                session()->push('DEBUG', now().': forgotPasswordStepOne(): recoverProfile->detail: '.json_encode($recoverProfile->detail));
            }
            if (isset($recoverProfile->detail)) {
                session()->push('DEBUG', now().': forgotPasswordStepOne(): verifyChallenge->getMessage: '.$recoverProfile->getMessage());
            }
        }

        //MAKE SURE NO ERRORS WERE RETURNED
        if (! isset($recoverProfile->faultcode)) {
            //MAKE SURE WE GOT RESULTS
            if (isset($recoverProfile->recoverUserReturn)) {
                //GET LIST OF CHALLENGE QUESTIONS SO THAT WE CAN DETERMINE USERS CHALLENGE QUESTIONS
                $user = new User();
                $challenge = $user->fnGetChallengeQuestions();

                //MAKE SURE WE GOT A LIST RETURNED TO US
                if (! empty($challenge)) {
                    //ENCRYPT GUID BECAUSE WE WILL BE USING THIS TO PASS ALONG FROM STEP TO STEP FOR FORGOT PASSWORD RECOVERY
                    $recoverProfile->recoverUserReturn->guid = $this->fnEncrypt($recoverProfile->recoverUserReturn->guid);

                    if (! isset($recoverProfile->recoverUserReturn->questionPoolNumber)) {
                        $msg = 'Sorry we could not process your request please try again later. If you have a BC Services Card please try to log in using it. Error #2021-58494';

                        return redirect()->back()->withInput()->withErrors(['errors' => $msg]);
                    }

                    //GET CHALLENGE QUESTION BASED ON WHAT QUESTION POOL AND QUESTION NUMBER WAS RETURNED TO US
                    $recoverProfile->recoverUserReturn->challengeQuestion = $challenge['pool'.$recoverProfile->recoverUserReturn->questionPoolNumber.''][$recoverProfile->recoverUserReturn->questionNumber];

                    //view challenge question
                    return view('auth.logins.forgot-password-step-2', ['env' => $this->env, 'data' => $recoverProfile->recoverUserReturn, 'step' => 2]);
                }
            }
        } else {
            $errorMappings = ['SPSIM15' => 'socialinsnum', 'SPSIM01.2' => 'hpot', 'SPSIM02' => 'hpot', 'SPSIM03' => 'hpot', 'SPSIM10' => 'hpot', 'SPSIM11' => 'day', 'SPSIM21' => 'hpot', 'SPSIM01.1' => 'hpot'];

            //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
            if (isset($recoverProfile->detail->CredentialFault)) {
                $msg = $recoverProfile->getMessage();
            } else {
                $msg = $recoverProfile->getMessage();
            }

            return redirect()->back()->withInput()->withErrors(['errors' => $msg]);
        }
    }

    /**
     * Challenge question is submitted
     * On Success redirect to step 7 to reset password
     * On Fail redirect to step 2 (attempt challenge question again) or step 3 (3 attempts fail)
     *
     * @param  ForgotPasswordStepTwoRequest  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function forgotPasswordStepTwo(ForgotPasswordStepTwoRequest $request)
    {
        $faultCode = '';
        $msg = [];
        $uGUID = $this->fnDecrypt($request->userGUID);
        $qp = $request->questionPool;
        $qn = $request->questionNumber;
        $answer = $request->answer;

        $this->WSDL = $this->fnWS('WS-HOSTS', 'CHALLENGE_QUESTIONS');
        $verifyChallenge = $this->fnRequest('verifyChallengeQuestion', ['userGUID' => $uGUID, 'questionPool' => $qp, 'questionNumber' => $qn, 'answer' => $answer]);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': forgotPasswordStepTwo(): verifyChallenge: '.json_encode($verifyChallenge));
            session()->push('DEBUG', now().': forgotPasswordStepTwo(): uGUID: '.$uGUID);
            if (isset($verifyChallenge->detail)) {
                session()->push('DEBUG', now().': forgotPasswordStepTwo(): verifyChallenge->detail: '.json_encode($verifyChallenge->detail));
            }
            if (isset($verifyChallenge->detail)) {
                session()->push('DEBUG', now().': forgotPasswordStepTwo(): verifyChallenge->getMessage: '.$verifyChallenge->getMessage());
            }
        }

        if (isset($verifyChallenge->userID)) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': forgotPasswordStepTwo(): verifyChallenge->userID is set: return view 7');
            }

            $verifyChallenge->userGUID = $uGUID;
            //Show view to reset password
            return view('auth.logins.forgot-password-step-7', ['env' => $this->env, 'data' => $verifyChallenge, 'step' => 7, 'errors' => $msg]);
        } else {
            $msg = $verifyChallenge->getMessage();
            $faultCode = $verifyChallenge->detail->ChanllengeQuestionFault->faultCode;
        }

        //faild 3 times
        if ($faultCode == 'SPSIM26.1') {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': forgotPasswordStepTwo(): SPSIM26.1 fault code: return view 3');
            }

            return view('auth.logins.forgot-password-step-3', ['env' => $this->env, 'data' => ['guid' => $request->userGUID], 'step' => 3]);
        }

        //there was an error. Show Challenge question again
        $old_data = ['faultCode' => $faultCode, 'challengeQuestion' => $request->challengeQuestion, 'guid' => $request->userGUID, 'emailAddress' => $request->emailAddress, 'questionPoolNumber' => $request->questionPool, 'questionNumber' => $request->questionNumber, 'answer' => $request->answer];
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': forgotPasswordStepTwo(): end of function: return view 2');
        }

        return view('auth.logins.forgot-password-step-2', ['env' => $this->env, 'data' => $old_data, 'step' => 2])->withErrors(['errors' => $msg]);
    }

    /**
     * Three attempts failed and now user is selecting to view either to reset password or delete account
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function forgotPasswordStepThree(Request $request)
    {
        if ($request->action == 'reset') {
            return $this->forgotPasswordStepFour($request);
        }
        if ($request->action == 'del') {
            return view('auth.logins.forgot-password-step-5', ['env' => $this->env, 'step' => 5, 'data' => ['guid' => $request->userGUID]]);
        }

        return view('auth.logins.forgot-password-step-3', ['env' => $this->env, 'data' => '', 'step' => 3]);
    }

    /**
     * User selected clicked on link to reset password
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function forgotPasswordStepFour(Request $request)
    {
        //todo process to send reset link
        $recover = null;
        $faultCode = '';
        $msg = 'Sorry we could not process your request. Please try again later. Error #20218493';

        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_AUTH');
        if (! empty($request->userGUID)) {
            $userGUID = $this->fnDecrypt($request->userGUID);
            $recover = $this->fnRequest('requestEmailRecovery', ['userGUID' => $userGUID]);

            if (! isset($recover->faultcode) && ! is_null($recover)) {
                return view('auth.logins.forgot-password-step-4', ['env' => $this->env, 'data' => '', 'step' => 4]);
            } else {
                $msg = $recover->getMessage();
                $faultCode = $recover->detail->CredentialFault->faultCode;
            }
        }

        $old_data = ['faultCode' => $faultCode, 'userGUID' => $request->userGUID];

        return view('auth.logins.forgot-password-step-3', ['env' => $this->env, 'data' => $old_data, 'step' => 3])->withErrors(['errors' => $msg]);
    }

    /**
     * User submitted confirm to delete account
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function forgotPasswordDelete(Request $request)
    {
        //todo process delete SABC Account
        $faultCode = '';
        $msg = [];
        $uGUID = $this->fnDecrypt($request->userGUID);

        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_AUTH');
        $deactivate = $this->fnRequest('deactivateProfile', ['userGUID' => $uGUID]);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': forgotPasswordDelete(): deactivate: '.json_encode($deactivate));
            session()->push('DEBUG', now().': forgotPasswordDelete(): uGUID: '.$uGUID);
            if (isset($deactivate->detail)) {
                session()->push('DEBUG', now().': forgotPasswordDelete(): deactivate->detail: '.json_encode($deactivate->detail));
            }
            if (isset($deactivate->detail)) {
                session()->push('DEBUG', now().': forgotPasswordDelete(): deactivate->getMessage: '.$deactivate->getMessage());
            }
        }

        if (! isset($deactivate->faultcode)) {
            //Show view to reset password
            return view('auth.logins.forgot-password-step-5-1', ['env' => $this->env, 'data' => $deactivate, 'step' => 8, 'errors' => $msg]);
        } else {
            $msg = $deactivate->getMessage();
            $faultCode = $deactivate->detail->CredentialFault->faultCode;
        }

        //there was an error. Show Challenge question again
        $old_data = ['faultCode' => $faultCode, 'guid' => $request->userGUID];

        return view('auth.logins.forgot-password-step-5', ['env' => $this->env, 'data' => $old_data, 'step' => 5])->withErrors(['errors' => $msg]);
    }

    public function forgotPasswordStepSeven(Request $request)
    {
        return view('auth.logins.forgot-password-step-7', ['env' => $this->env, 'data' => '', 'step' => 7]);
    }

    public function confirmReset(Request $request, $guid)
    {
        $recover = null;
        $faultCode = '';
        $msg = 'Sorry we could not process your request. Please try again later. Error #20218494';
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_AUTH');
        $recover = $this->fnRequest('resolveRecoveryGuid', ['recoveryGuid' => $guid]);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': confirmReset(): recover: '.json_encode($recover));
            session()->push('DEBUG', now().': confirmReset(): guid: '.$guid);
        }

        if (! isset($recover->faultcode) && ! is_null($recover)) {
            $userGUID = $recover->resolveRecoveryGuidReturn->userGUID;
            $userID = $recover->resolveRecoveryGuidReturn->userID;

            return view('auth.logins.forgot-password-step-7', ['env' => $this->env, 'data' => ['userID' => $userID, 'userGUID' => $userGUID], 'step' => 7]);
        } else {
            $msg = $recover->getMessage();
            $faultCode = $recover->detail->CredentialFault->faultCode;
        }
        $userGUID = $this->fnEncrypt($guid);
        $old_data = ['faultCode' => $faultCode, 'userGUID' => $userGUID];

        return view('auth.logins.forgot-password-step-3', ['env' => $this->env, 'data' => $old_data, 'step' => 3])->withErrors(['errors' => $msg]);
    }

    public function forgotPasswordReset(ForgotPasswordResetRequest $request)
    {
        $faultCode = '';
        $msg = 'Sorry we could not process your request. Please try again later. Error #20219494';

        //todo handle submit of password reset

        $userGUID = $request->userID;
        $pswd = $request->password;

        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_AUTH');
        $changePassword = $this->fnRequest('changePassword', ['userGUID' => $userGUID, 'password' => $pswd, 'confirmationPassword' => $pswd]);

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': forgotPasswordReset(): userID: '.$request->userID);
            session()->push('DEBUG', now().': forgotPasswordReset(): username: '.$request->username);
            session()->push('DEBUG', now().': forgotPasswordReset(): password: '.$request->password);
            session()->push('DEBUG', now().': forgotPasswordReset(): changePassword: '.json_encode($changePassword));
        }

        if (! isset($changePassword->faultcode)) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': forgotPasswordReset(): GOOD TO GO ');
            }

            Session::flash('password-reset', 'Password reset was successful!');

            return redirect('/login');
        } else {
            $msg = $changePassword->getMessage();
            $faultCode = $changePassword->detail->CredentialFault->faultCode;
        }
        $old_data = ['faultCode' => $faultCode, 'userGUID' => $userGUID, 'userID' => $request->username];

        return view('auth.logins.forgot-password-step-7', ['env' => $this->env, 'data' => $old_data, 'step' => 7])->withErrors(['errors' => $msg]);
    }
}
