<?php

namespace App\Http\Controllers;

use App\Application;
use App\Declaration;
use App\DeclarationField;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDF;
use Response;

class DeclarationController extends Aeit
{
    public function fetchDeclarations(Request $request)
    {
        return Response::json(['declarations' => Declaration::orderBy('updated_at', 'desc')->get()], 200); // Status code here
    }

    /**
     * Display the dashboard page to a logged in admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function declarations()
    {
        return view('admin.declarations', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * Display the dashboard page to a logged in admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function declarationsNew(Request $request)
    {
        return view('admin.declarations-new', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*
         * TYPES:
        Applicant - Signature Required
        Applicant - No Signature Required
        Parent - Signature Required
        Parent - No Signature Required
        Spouse - Signature Required
        Spouse - No Signature Required
        Applicant - E-Consent
        Spouse - E-Consent

        Applicant Declaration 2013 - Signature Required
        Applicant Declaration 2013 - No Signature Required
        Applicant Declaration 2012 - Signature Required
        Applicant Declaration 2012 - No Signature Required

         */
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'type_id' => 'required',
            'program_year' => 'required',
            'status' => 'required|in:draft,active',
        ]);

        $decl = new Declaration();
        $decl->name = $request->name;
        $decl->type = $request->type;
        $decl->type_id = $request->type_id;
        $decl->program_year = str_replace(' ', '', $request->program_year);
        $decl->status = $request->status == 'active' ? 1 : 0;
        $decl->save();

        for ($i = 0; $i < count($request->field_labels); $i++) {
            $field = new DeclarationField();
            $field->declaration_id = $decl->id;
            $field->field_id = str_replace(' ', '', $request->field_ids[$i]);
            $field->field_label = $request->field_labels[$i];
            $field->field_value = $request->field_data[$i];
            $field->field_weight = intval($request->field_weights[$i]);
            $field->save();
        }

        return redirect('/admin/declarations');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Declaration  $declaration
     * @return \Illuminate\Http\Response
     */
    public function show(Declaration $declaration)
    {
        $declaration = Declaration::where('id', $declaration->id)->with('fields')->first();

        return view('admin.declarations-show', ['declaration' => $declaration, 'roles' => Auth::user()->roles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Declaration  $declaration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Declaration $declaration)
    {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'type_id' => 'required',
            'program_year' => 'required',
            'status' => 'required|in:draft,active',
        ]);

        $declaration->name = $request->name;
        $declaration->type = $request->type;
        $declaration->type_id = $request->type_id;
        $declaration->program_year = str_replace(' ', '', $request->program_year);
        $declaration->status = $request->status == 'active' ? 1 : 0;
        $declaration->save();

        DeclarationField::where('declaration_id', $declaration->id)->delete();
        for ($i = 0; $i < count($request->field_labels); $i++) {
            $field = new DeclarationField();
            $field->declaration_id = $declaration->id;
            $field->field_id = str_replace(' ', '', $request->field_ids[$i]);
            $field->field_label = $request->field_labels[$i];
            $field->field_value = $request->field_data[$i];
            $field->field_weight = intval($request->field_weights[$i]);
            $field->save();
        }

        return redirect('/admin/declarations');
    }

    public function signatureRequired(Request $request, $role_type, $application_number)
    {
        $this->user = User::where('id', Auth::id())->with('roles')->first();
        $dectype = 'Applicant - Signature Required';
        switch ($role_type) {
            case 'applicant':
                $dectype = 'Applicant - Signature Required';
                break;
            case 'parent':
                $dectype = 'Parent - Signature Required';
                break;
            case 'spouse':
                $dectype = 'Spouse - Signature Required';
                break;
        }

        return $this->loadDec($request, $dectype, $role_type, $application_number);
    }

    public function noSignature(Request $request, $role_type, $application_number)
    {
    }

    /**
     * @param  Request  $request
     * @param $role_type
     * @param $application_number
     */
    public function noSignatureRequired(Request $request, $role_type, $application_number)
    {
        $this->user = User::where('id', Auth::id())->with('roles')->first();
        $dectype = 'Applicant - No Signature Required';
        switch ($role_type) {
            case 'applicant':
                $dectype = 'Applicant - No Signature Required';
                break;
            case 'parent':
                $dectype = 'Parent - No Signature Required';
                break;
            case 'spouse':
                $dectype = 'Spouse - No Signature Required';
                break;
        }

        return $this->loadDec($request, $dectype, $role_type, $application_number);
    }

    public function noSignatureReceived(Request $request, $role_type, $application_number)
    {
        $this->user = User::where('id', Auth::id())->with('roles')->first();
        $dectype = 'Applicant - No Signature Required';
        switch ($role_type) {
            case 'applicant':
                $dectype = 'Applicant - No Signature Required';
                break;
            case 'parent':
                $dectype = 'Parent - No Signature Required';
                break;
            case 'spouse':
                $dectype = 'Spouse - No Signature Required';
                break;
        }

        return $this->loadDec($request, $dectype, $role_type, $application_number);
    }

    public function downloadDeclaration(Request $request, $appendix_type, $document_guid, $application_number)
    {
        return view('no-sin-declaration',
            ['appendix_type' => $appendix_type, 'application_number' => $application_number, 'document_guid' => $document_guid]
        );
    }

    public function loadEconsent(Request $request, $role_type, $application_number)
    {
        $this->user = User::where('id', Auth::id())->with('roles')->first();
        $dectype = 'Applicant - E-Consent';
        switch ($role_type) {
            case 'applicant':
                $dectype = 'Applicant - E-Consent';
                break;
            case 'parent':
                $dectype = 'Parent - E-Consent';
                break;
            case 'spouse':
                $dectype = 'Spouse - E-Consent';
                break;
        }

        return $this->loadDec($request, $dectype, $role_type, $application_number, 'econsent');
    }

    private function loadDec($request, $dectype, $role_type, $application_number, $dec_vs_consent = 'declaration')
    {
        $this->uid = session(env('GUID_SESSION_VAR'));

        $role = ['applicant' => 'A', 'parent' => 'P', 'spouse' => 'S'];
        $implementationDate = '2014-03-05'; //DATE WE IMPLEMENTED NEW DEC FORMS - DON'T CHANGE!!

        $user_roles = $this->user->roles->pluck('name')->toArray();
        $noSinUser = false;
        $address = '';
        $noSinUser = false;
        if (in_array('parent_no_sin', $user_roles) || in_array('spouse_no_sin', $user_roles)) {
            $noSinUser = true;
        }

        $r = new Application();

        //$dec has to be an array otherwise it's an error
        $dec = $r->fnGetDeclaration($application_number, $role[$role_type], $noSinUser);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': loadDec() application_number: '.$application_number);
            session()->push('DEBUG', now().': loadDec() role[role_type]: '.$role[$role_type]);
            session()->push('DEBUG', now().': loadDec() user_roles: '.json_encode($user_roles));
            session()->push('DEBUG', now().': loadDec() noSinUser: '.$noSinUser);
            session()->push('DEBUG', now().': loadDec() dec: '.json_encode($dec));
        }

        $profile = \stdClass::class;
        if (! $noSinUser) {
            $profile = $this->fnGetUserProfile($request);
        }
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': loadDec() profile: '.json_encode($profile));
        }

        if (! is_array($dec)) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': loadDec() dec is array: '.is_array($dec));
            }
            $pdf = PDF::loadView('pdfs.document', ['html' => '<h4>Access denied!</h4>', 'style' => '']);

            return $pdf->stream($application_number.'.pdf');
        }

        //determine type of declaration required to load

        if (isset($profile->userProfile->addressLine1)) {
            $address = $profile->userProfile->addressLine1;
        } elseif (isset($profile->userProfile->addressLine2)) {
            $address .= $profile->userProfile->addressLine2;
        } else {
            $address = null;
        }

        $user_name = '';
        if (! $noSinUser) {
            if (env('LOG_ESERVICES') == true) {
                Log::warning('loadDec(): application_number: '.$application_number);
                Log::warning('loadDec(): role[role_type]: '.$role[$role_type]);
                Log::warning('loadDec(): user_roles: '.json_encode($user_roles));
                Log::warning('loadDec(): noSinUser: '.$noSinUser);
                Log::warning('loadDec(): this->uid: '.$this->uid);
                Log::warning('loadDec(): dectype: '.$dectype);
                Log::warning('loadDec(): dec: '.json_encode($dec));
                Log::warning('loadDec(): profile->userProfile: '.json_encode($profile->userProfile));
            }

            if (isset($profile->userProfile->firstName)) {
                $user_name = $profile->userProfile->firstName.' ';
            }
            if (isset($profile->userProfile->middleName)) {
                $user_name .= $profile->userProfile->middleName.' ';
            }
            $user_name .= $profile->userProfile->familyName;
        }

        $phone = (isset($profile->userProfile->phoneNumber)) ? stripslashes($profile->userProfile->phoneNumber).' < Phone' : null;
        $email = (isset($profile->userProfile->emailAddress)) ? stripslashes($profile->userProfile->emailAddress).' < Email' : null;
        $city = (isset($profile->userProfile->city)) ? stripslashes($profile->userProfile->city) : null;
        $country = (isset($profile->userProfile->country)) ? stripslashes($profile->userProfile->country) : null;
        $prov = (isset($profile->userProfile->province)) ? stripslashes($profile->userProfile->province) : null;
        $postzip = (isset($profile->userProfile->postalCode)) ? stripslashes($profile->userProfile->postalCode) : null;

        $startYear = (int) substr($dec['py'], 0, 4);
        $endYear = (int) substr($dec['py'], -4, 4);
        $page1_field_name = 'field_sig_req_intro_text';
        $page2_field_name = 'field_sig_req_agreement_text';
        if ($dec_vs_consent == 'econsent') {
            //$dec_vs_consent = "e_consent";
            $page1_field_name = 'field_consent_req_intro_text';
            $page2_field_name = 'field_consent_req_agreement_text';
        }
        if ($dec_vs_consent == 'declaration' && $role_type == 'spouse' && $startYear >= 2020) {
            $dec_vs_consent = 'new_dec';
            $page1_field_name = 'field_new_dec_req_intro_text';
            $page2_field_name = 'field_new_dec_req_agreement_text';
        }

        $get_decl = Declaration::where('type', $dectype)->where('program_year', $startYear.'/'.$endYear)->where('status', 1)->with('fields')->first();
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': loadDec() dectype: '.$dectype);
            session()->push('DEBUG', now().': loadDec() get_decl: '.is_null($get_decl));
            session()->push('DEBUG', now().': loadDec() startYear: '.$startYear);
            session()->push('DEBUG', now().': loadDec() endYear: '.$endYear);
            session()->push('DEBUG', now().': loadDec() dec_vs_consent: '.$dec_vs_consent);
            session()->push('DEBUG', now().': loadDec() role_type: '.$role_type);
            session()->push('DEBUG', now().': loadDec() profile: '.json_encode($profile));
        }

        if (is_null($get_decl)) {
            $html = mb_convert_encoding(mb_convert_encoding('<h4>Sorry we are having an issue generating this declaration! Please check back again. Error #239823</h4>', 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32');
            $pdf = PDF::loadView('pdfs.document', ['html' => $html, 'style' => '']);

            return $pdf->stream($application_number.'.pdf');
        } else {
            $node = [];
            foreach ($get_decl->fields as $field) {
                $node[$field->field_id] = $field->field_value;
            }
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': loadDec() node: '.strip_tags(json_encode($node)));
            }

            $srch = ['{APPLICATION_NUMBER}', '#:APPLICATION_NUMBER'];

            $blankField = $noSinUser ? '_____________________________________' : '';

            $tableClass = $noSinUser ? 'no-sin-appendix' : 'appendix';

            $html = '';

            $style = '<style type="text/css">';

            $style .= '@font-face {font-family: \'arial\'; font-style: normal; font-weight: normal; src: url("'.SITE_URL.'/fonts/ArialUnicodeMS.ttf");}';
            $style .= 'body {font-family: \'arial\' !important; }';
            $style .= '.font8 {font-size:8px; line-height:9px;}';
            $style .= '.font9 {font-size:9px;}';
            $style .= '.font10 {font-size:10px;}';
            $style .= '.font14 {font-size:14px;}';
            $style .= '.font12 {font-size:12px;}';
            $style .= '.font11 {font-size:11px; line-height:12px;}';
            $style .= '.font16 {font-size:16px;}';
            $style .= 'table.no-sin-appendix {table-layout:fixed}';
            $style .= 'table.no-sin-appendix td {width: 50%; white-space: nowrap; padding-right: 15px; overflow:hidden; }';
            $style .= 'table.appendix {font-size:11px;}';
            $style .= '</style>';

            $html .= '<h2>'.$dec['py'].' '.strip_tags($node['field_'.$dec_vs_consent.'_title'], '<br>').'</h2>';
            $html .= $node[$page1_field_name].'<pagebreak />';
            $html .= '<table width="100%" border="0">';
            $html .= '<tr>';
            $html .= '<td rowspan="2">';
            $html .= '<img src="/dashboard/img/bc-logo-grey.gif" alt="StudentAidBC Logo" width="75px" height="75px" style="float:left; margin-right:20px;">';
            $html .= '</td>';
            $html .= '<td style="vertical-align: top;">';
            $html .= '</td>';
            $html .= '<td style="vertical-align: top; text-align:right;">';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="2" style="vertical-align: top;">';
            $html .= '<h2>'.$dec['py'].' '.strip_tags($node['field_'.$dec_vs_consent.'_title_pg_2_'], '<br>').'</h2>';
            $html .= '<br><span class="font12">Submit Code: <strong>'.$dec['sc'].'</strong></span>';
            $html .= '<br><span class="font12">Office Use Only: <strong>'.$dec['ouo'].'</strong></span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<div style="clear::both;"></div>';
            $html .= '<div class="font8">'.str_replace($srch, '#'.$dec['appno'], $node[$page2_field_name]).'</div>';
            if ($dec_vs_consent == 'econsent') {
                $html .= '<div class="font8">'.str_replace($srch, '#'.$dec['appno'], $node['field_consent2_req_agreement']).'</div>';
                $html .= '<div class="font8">'.str_replace($srch, '#'.$dec['appno'], $node['field_e_consent_page_footer']).'</div>';
            }
            if ($dec_vs_consent == 'declaration' && $role_type == 'spouse' && $startYear >= 2020) {
                $html .= '<div class="font8">'.str_replace($srch, '#'.$dec['appno'], $node['field_new_dec_req2_agreement']).'</div>';
                $html .= '<div class="font8">'.str_replace($srch, '#'.$dec['appno'], $node['field_new_dec_page_footer']).'</div>';
            }

            if ($role_type == 'applicant') {
                //implementation of e-consent
                if ($dec_vs_consent == 'econsent') {
                    $html .= '<table width="100%" border=0>';
                    $html .= '<tr>';
                    $html .= '<td style="width:170px;"><strong class="font11">Student Name</strong></td>';
                    $html .= '<td style="background-color:#d6d6d6; width:290px; text-align:center;"><div style="font-weight:bold; font-size:30px; padding:4px;">E-CONSENT</div></td>';
                    $html .= '<td style="width:100px;" class="font12"></td>';
                    $html .= '<td><strong class="font11">Date: '.date('F d, Y', strtotime($dec['ds'])).'</strong></td>';
                    $html .= '</tr>';
                    $html .= '</table>';
                }

                //POST IMPLEMENTATION DATE SIGNATURE REQUIRED
                if ($dectype == 'Applicant - Signature Required') {
                    $html .= '<table width="100%" border=0>';
                    $html .= '<tr>';
                    $html .= '<td style="width:170px;"><strong class="font11">'.base64_decode($user_name).'</strong></td>';
                    $html .= '<td style="background-color:#d6d6d6; width:290px;"><div style="font-weight:bold; font-size:30px; padding:4px; color:#ff0000;"> X</div></td>';
                    $html .= '<td style="color:#ff0000; width:100px;" class="font12"><strong><== Sign here</strong></td>';
                    $html .= '<td><strong class="font11">'.date('F d, Y', strtotime($dec['ds'])).'</strong></td>';
                    $html .= '</tr>';
                    $html .= '</table>';
                }

                //POST IMPLEMENTATION DATE NO SIGNATURE REQUIRED
                if ($dectype == 'Applicant - No Signature Required') {
                    $status = 'Not Applicable';
                    $html .= '<table width="100%" border=0>';
                    $html .= '<tr>';
                    $html .= '<td style="width:170px;"><strong class="font11">'.base64_decode($user_name).'</strong></td>';
                    $html .= '<td style="background-color:#d6d6d6; width:290px; text-align:center;"><div style="font-weight:bold; font-size:26px; padding:4px; color:#ff0000;">'.$status.'</div></td>';
                    $html .= '<td style="width:100px;" class="font12"></td>';
                    $html .= '<td><strong class="font11">'.date('F d, Y', strtotime($dec['ds'])).'</strong></td>';
                    $html .= '</tr>';
                    $html .= '</table>';
                }

                //IF APPLICATIONS ARE 2012 OR 2013 AND PRE IMPLEMENTATION DATE  LOOP IN HERE
                if ($dectype == 'Applicant Declaration 2012 - No Signature Required' || $dectype == 'Applicant Declaration 2013 - No Signature Required' ||
                    $dectype == 'Applicant Declaration 2012 - Signature Required' || $dectype == 'Applicant Declaration 2013 - Signature Required') {
                    $html .= '<table width="100%" border=0>
                                <tr>
                                    <td><strong class="font11">STUDENT NAME</strong></td>
                                    <td style="width:290px;" align="center"><strong class="font11" >SIGNATURE OF STUDENT (in ink)</strong></td>
                                    <td style="width:100px;"></td>
                                    <td><strong class="font11">DATE SIGNED</strong></td>
                                </tr>
                            </table>';

                    //SIGNATURE BLOCKS REQUIRED :: FOR 2012 AND 2013 PRE IMPLEMENTATION DATES
                    if ($dectype == 'Applicant Declaration 2012 - Signature Required' || $dectype == 'Applicant Declaration 2013 - Signature Required') {
                        $html .= '<table width="100%" border=0>';
                        $html .= '<tr>';
                        $html .= '<td style="width:170px;"><strong class="font11">'.base64_decode($user_name).'</strong></td>';
                        $html .= '<td style="background-color:#d6d6d6; width:290px;"><div style="font-weight:bold; font-size:30px; padding:4px; color:#ff0000;"> X</div></td>';
                        $html .= '<td style="color:#ff0000; width:100px;" class="font12"><strong><== Sign here</strong></td>';
                        $html .= '<td><strong class="font11">'.date('F d, Y', strtotime($dec['ds'])).'</strong></td>';
                        $html .= '</tr>';
                        $html .= '</table>';
                    } //SIGNATURE BLOCKS NOT REQUIRED :: FOR 2012 AND 2013 PRE IMPLEMENTATION DATES
                    elseif ($dectype == 'Applicant Declaration 2012 - No Signature Required' || $dectype == 'Applicant Declaration 2013 - No Signature Required') {
                        $status = 'Not Applicable';
                        $html .= '<table width="100%" border=0>';
                        $html .= '<tr>';
                        $html .= '<td style="width:170px;"><strong class="font11">'.base64_decode($user_name).'</strong></td>';
                        $html .= '<td style="background-color:#d6d6d6; width:290px; text-align:center;"><div style="font-weight:bold; font-size:26px; padding:4px; color:#ff0000;">'.$status.'</div></td>';
                        $html .= '<td style="width:100px;" class="font12"></td>';
                        $html .= '<td><strong class="font11">'.date('F d, Y', strtotime($dec['ds'])).'</strong></td>';
                        $html .= '</tr>';
                        $html .= '</table>';
                    }
                    $html .= '<p class="font11"><strong>Canada Revenue Agency Consent Form</strong></p>';
                    $html .= '<p class="font10">';
                    $html .= 'For the purpose of verifying the data provided in this application for student assistance, I hereby consent to the release, by the Canada Revenue Agency, to the Ministry of Post-Secondary ';
                    $html .= 'Education and Future Skills (or a person delegated by the Ministry), of taxpayer information from any portion of my income tax records that pertains to information given by me on any StudentAid BC ';
                    $html .= 'application. The information will be used solely for the purpose of verifying information on my StudentAid BC application forms and for the general administration and enforcement of';
                    $html .= 'StudentAid BC and the Canada Student Financial Assistance Act. This authorization is valid for the two taxation years prior to the year of signature of this consent, the year of signature of ';
                    $html .= 'this consent and for any other subsequent consecutive taxation year for which assistance is requested.';
                    $html .= '</p>';

                    $html .= '<table width="100%" border=0>';
                    $html .= '<tr>';
                    $html .= '<td><strong class="font11">STUDENT NAME</strong></td>';
                    $html .= '<td style="width:290px;" align="center"><strong class="font11" >SIGNATURE OF STUDENT (in ink)</strong></td>';
                    $html .= '<td style="width:100px;"></td>';
                    $html .= '<td><strong class="font11">DATE SIGNED</strong></td>';
                    $html .= '</tr>';
                    $html .= '</table>';

                    //SIGNATURE REQUIRED :: FOR 2012 AND 2013 PRE IMPLEMENTATION DATES
                    if ($dectype == 'Applicant Declaration 2012 - Signature Required' || $dectype == 'Applicant Declaration 2013 - Signature Required') {
                        $html .= '<table width="100%" border=0>';
                        $html .= '<tr>';
                        $html .= '<td style="width:170px;"><strong class="font11">'.base64_decode($user_name).'</strong></td>';
                        $html .= '<td style="background-color:#d6d6d6; width:290px;"><div style="font-weight:bold; font-size:30px; padding:4px; color:#ff0000;"> X</div></td>';
                        $html .= '<td style="color:#ff0000; width:100px;" class="font12"><strong><== Sign here</strong></td>';
                        $html .= '<td><strong class="font11">'.date('F d, Y', strtotime($dec['ds'])).'</strong></td>';
                        $html .= '</tr>';
                        $html .= '</table>';
                    } //SIGNATURE BLOCKS NOT REQUIRED :: FOR 2012 AND 2013 PRE IMPLEMENTATION DATES
                    elseif ($dectype == 'Applicant Declaration 2012 - No Signature Required' || $dectype == 'Applicant Declaration 2013 - No Signature Required') {
                        $status = 'Not Applicable';
                        $html .= '<table width="100%" border=0>';
                        $html .= '<tr>';
                        $html .= '<td style="width:170px;"><strong class="font11">'.base64_decode($user_name).'</strong></td>';
                        $html .= '<td style="background-color:#d6d6d6; width:290px; text-align:center;"><div style="font-weight:bold; font-size:26px; padding:4px; color:#ff0000;">'.$status.'</div></td>';
                        $html .= '<td style="width:100px;" class="font12"></td>';
                        $html .= '<td><strong class="font11">'.date('F d, Y', strtotime($dec['ds'])).'</strong></td>';
                        $html .= '</tr>';
                        $html .= '</table>';
                    }
                }

                $html .= '<table width="100%" border=0>';
                $html .= '<tr>';
                $html .= '<td>'.$user_name.'</td>';
                $html .= '<td style="width:290px;"></td>';
                $html .= '<td style="width:10px;" class="font12"></td>';
                $html .= '<td align="right">'.$phone.'</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td>'.$address.'</td>';
                $html .= '<td style="width:290px;"></td>';
                $html .= '<td style="width:10px;" class="font12"></td>';
                $html .= '<td align="right">'.$email.'</td>';
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td>'.$city.' '.$country.' '.$prov.' '.$postzip.'</td>';
                $html .= '<td style="width:290px;"></td>';
                $html .= '<td style="width:10px;" class="font12"></td>';
                $html .= '<td align="right"></td>';
                $html .= '</tr>';
                $html .= '<tr></tr><tr><td><span class="font12">Submit Code: <strong>'.$dec['sc'].'</strong></span></td>';
                $html .= '<td><span class="font12">Office Use Only: <strong>'.$dec['ouo'].'</strong></span></td></tr>';
                $html .= '</table>';
            } else {
                $html .= '<table class="'.$tableClass.'" width="100%" border=0>';
                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'Last Name: '.(isset($profile->userProfile->familyName) ? $profile->userProfile->familyName : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'Last Name: '.(isset($dec['parent2LName']) && ! empty($dec['parent2LName']) ? $dec['parent2LName'] : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'First Name: '.(isset($profile->userProfile->firstName) ? $profile->userProfile->firstName : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'First Name: '.(isset($dec['parent2FName']) && ! empty($dec['parent2FName']) ? $dec['parent2FName'] : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'Middle Name: '.(isset($profile->userProfile->middleName) ? $profile->userProfile->middleName : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'Middle Name: '.(isset($dec['parent2MName']) && ! empty($dec['parent2MName']) ? $dec['parent2MName'] : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                if (isset($profile->userProfile->gender)) {
                    switch ($profile->userProfile->gender){
                        case 'M': $html .= 'Gender: Man'; break;
                        case 'F': $html .= 'Gender: Woman'; break;
                        case 'X': $html .= 'Gender: Non-Binary'; break;
                        case 'U': $html .= 'Gender: Prefer Not to Answer'; break;
                        default: $html .= 'Gender: '.$blankField;
                    }
//                    $html .= 'Gender: '.(($profile->userProfile->gender == 'M') ? 'Man' : (($profile->userProfile->gender == 'F') ? 'Woman' : $blankField));
                } else {
                    $html .= 'Gender: '.$blankField;
                }
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    if (isset($dec['parent2Gender']) && ! empty($dec['parent2Gender'])) {
                        switch ($dec['parent2Gender']){
                            case 'M': $html .= 'Gender: Male'; break;
                            case 'F': $html .= 'Gender: Woman'; break;
                            case 'X': $html .= 'Gender: Non-Binary'; break;
                            case 'U': $html .= 'Gender: Prefer Not to Answer'; break;
                            default: $html .= 'Gender: '.$blankField;
                        }

//                        $html .= 'Gender: '.(($dec['parent2Gender'] == 'M') ? 'Male' : (($dec['parent2Gender'] == 'F') ? 'Female' : $blankField));
                    } else {
                        $html .= 'Gender: '.$blankField;
                    }
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'Date of Birth: '.(isset($profile->userProfile->dateOfBirth) ? date('M d, Y', strtotime($profile->userProfile->dateOfBirth)) : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'Date of Birth: '.(isset($dec['parent2DOB']) && ! empty($dec['parent2DOB']) ? date('M d, Y', strtotime($dec['parent2DOB'])) : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'Address (Line 1): '.(isset($profile->userProfile->addressLine1) ? $profile->userProfile->addressLine1 : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'Address (Line 1): '.(isset($dec['parent2Address1']) && ! empty($dec['parent2Address1']) ? $dec['parent2Address1'] : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'Address (Line 2): '.(isset($profile->userProfile->addressLine2) ? $profile->userProfile->addressLine2 : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'Address (Line 2): '.(isset($dec['parent2Address2']) && ! empty($dec['parent2Address2']) ? $dec['parent2Address2'] : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'City / Town: '.(isset($profile->userProfile->city) ? $profile->userProfile->city : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'City / Town: '.(isset($dec['parent2City']) && ! empty($dec['parent2City']) ? $dec['parent2City'] : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'Province / State: '.(isset($profile->userProfile->province) ? $profile->userProfile->province : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'Province / State: '.(isset($dec['parent2Province']) && ! empty($dec['parent2Province']) ? $dec['parent2Province'] : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'Postal / ZIP Code: '.(isset($profile->userProfile->postalCode) ? $profile->userProfile->postalCode : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'Postal / ZIP Code: '.(isset($dec['parent2PostalCode']) && ! empty($dec['parent2PostalCode']) ? $dec['parent2PostalCode'] : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'Country: '.(isset($profile->userProfile->country) ? $profile->userProfile->country : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'Country: '.(isset($dec['parent2Country']) && ! empty($dec['parent2Country']) ? $dec['parent2Country'] : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'Telephone Number: '.(isset($profile->userProfile->phoneNumber) ? $profile->userProfile->phoneNumber : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'Telephone Number: '.(isset($dec['parent2Phone']) && ! empty($dec['parent2Phone']) ? $dec['parent2Phone'] : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= 'E-Mail Address: '.(isset($profile->userProfile->emailAddress) ? $profile->userProfile->emailAddress : $blankField);
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= 'E-Mail Address: '.(isset($dec['parent2Email']) && ! empty($dec['parent2Email']) ? $dec['parent2Email'] : $blankField);
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $status = 'Not Applicable';

                $html .= '<tr>';
                $html .= '<td>';
                if ($role_type == 'parent') {
                    $html .= '<strong class="font11">PARENT SIGNATURE</strong>';
                    if ($dectype == 'Parent - Signature Required') {
                        $html .= '<span style="color:#ff0000;" class="font12"> Sign IN INK in the box below:</span>';
                    }
                } elseif ($role_type == 'spouse') {
                    if ($dec_vs_consent == 'econsent') {
                        $html .= '<div style="background-color:#d6d6d6; font-size:28px; padding:10px;"><span>E-CONSENT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>';
                    } else {
                        $html .= '<strong class="font11">SIGNATURE OF SPOUSE/PARTNER (in Ink)</strong>';
                    }
                }
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= '<strong class="font11">PARENT SIGNATURE</strong>';
                    if ($dectype == 'Parent - Signature Required') {
                        $html .= '<span style="color:#ff0000;" class="font12"> Sign IN INK in the box below:</span>';
                    }
                    $html .= '</td>';
                }
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td>';
                if ($dectype == 'Student - No Signature Required' || $dectype == 'Parent - No Signature Required' || $dectype == 'Spouse - No Signature Required') {
                    $html .= '<table width="100%" border=0>';
                    $html .= '<tr>';
                    $html .= '<td style="background-color:#d6d6d6; width:290px; text-align:center;"><div style="font-weight:bold; font-size:26px; padding:4px; color:#ff0000;">'.$status.'</div></td>';
                    $html .= '<td style="width:100px;" class="font12"></td>';
                    $html .= '</tr>';
                    $html .= '</table>';
                } elseif ($dectype == 'Student - Signature Required' || $dectype == 'Parent - Signature Required' || $dectype == 'Spouse - Signature Required') {
                    $html .= '<table width="100%" border=0>';
                    $html .= '<tr>';
                    $html .= '<td style="background-color:#d6d6d6; width:290px;"><div style="font-weight:bold; font-size:30px; padding:4px; color:#ff0000;"> X</div></td>';
                    if ($role_type == 'spouse') {
                        $html .= '<td style="color:#ff0000; width:80px;" class="font12"><strong><== Sign here</strong></td>';
                    }
                    $html .= '</tr>';
                    $html .= '</table>';
                }
                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    if ($dectype == 'Parent - No Signature Required') {
                        $html .= '<table width="100%" border=0>';
                        $html .= '<tr>';
                        $html .= '<td style="background-color:#d6d6d6; width:290px; text-align:center;"><div style="font-weight:bold; font-size:26px; padding:4px; color:#ff0000;">'.$status.'</div></td>';
                        $html .= '<td style="width:100px;" class="font12"></td>';
                        $html .= '</tr>';
                        $html .= '</table>';
                    } elseif ($dectype == 'Parent - Signature Required') {
                        $html .= '<table width="100%" border=0>';
                        $html .= '<tr>';
                        $html .= '<td style="background-color:#d6d6d6; width:290px;"><div style="font-weight:bold; font-size:30px; padding:4px; color:#ff0000;"> X</div></td>';
                        $html .= '</tr>';
                        $html .= '</table>';
                    }
                    $html .= '</td>';
                }
                $html .= '</tr>';
                $html .= '<tr>';
                $html .= '<td>';

                if (mb_stripos($dectype, 'e-consent')) {
                    $html .= '<strong class="font11">Date: '.(isset($dec['ds']) && ! empty($dec['ds']) ? date('F d, Y', strtotime($dec['ds'])) : '').'</strong>';
                } else {
                    $html .= '<strong class="font11">Date Signed: '.(isset($dec['ds']) && ! empty($dec['ds']) ? date('F d, Y', strtotime($dec['ds'])) : '').'</strong>';
                }
                $html .= '<br><br><span class="font12">Submit Code: <strong>'.$dec['sc'].'</strong></span>';
                $html .= '<br><span class="font12">Office Use Only: <strong>'.$dec['ouo'].'</strong></span>';

                $html .= '</td>';
                if (isset($dec['parent2'])) {
                    $html .= '<td>';
                    $html .= '<strong class="font11">Date: '.(isset($dec['ds']) && ! empty($dec['ds']) ? date('F d, Y', strtotime($dec['ds'])) : '').'</strong>';
                    $html .= '</td>';
                }
                $html .= '</tr>';
                $html .= '</table>';
            }


            //this line is a must. The code fails without ending in an infinite loop at Mpdf.php: line #25972
            //laravel Mpdf purify_utf8 fails
            $html = mb_convert_encoding(mb_convert_encoding($html, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32');
            $pdf = PDF::loadView('pdfs.document', ['html' => $html, 'style' => $style]);

            return $pdf->stream($application_number.'.pdf');
        }
    }
}
