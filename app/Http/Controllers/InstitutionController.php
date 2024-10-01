<?php

namespace App\Http\Controllers;

use App\Application;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Response;

//use Symfony\Component\Console\Input\Input;

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

class InstitutionController extends Aeit
{
    public function instDataLoadPublic(Request $request)
    {
        return Response::json($this->sabc_institution_data_load(true), 200);
    }

    public function instDetailsPublic(Request $request, $id)
    {
        return Response::json(['output' => $this->sabc_institution_details_page_ajax($id, true)], 200);
    }

    public function instDataLoad(Request $request)
    {
        return Response::json($this->sabc_institution_data_load(), 200);
    }

    public function instDetails(Request $request, $id)
    {
        return Response::json(['output' => $this->sabc_institution_details_page_ajax($id)], 200);
    }

    public function sabc_institution_data_load($public = false)
    {

        //get institutions
        $inst = new application(false);
        $schools = $inst->fnGetSchools();

        if (is_null($schools)) {
            return null;
        }

        //required datatables when passing back
        $data['recordsTotal'] = count($schools->SchoolList->School);
        $data['recordsFiltered'] = count($schools->SchoolList->School);
        $data['draw'] = 1;

        //start building json object
        $data['data'] = $schools->SchoolList->School;

        /* Assign the datatable row id for each school.
        * DT_RowID - Datatable element defined for tr ids.
        */
        foreach ($data['data'] as $school) {
            $school->DT_RowId = $school->SchoolIDX;
            unset($school->FullTimeEligible);
            unset($school->SchoolCode);
            unset($school->SchoolIDX);
            unset($school->PartTimeEligible);
        }
        //output text in json format
        return $data;
    }

    public function sabc_institution_details_page_ajax($institution_code, $public = false)
    {
        //Create a new SOAP Call
        $inst = new application();
        $output = '';

        //Query results based on the url/institution code
        $institution_result = $inst->fnGetSchoolDetails($institution_code);
        $institution_data = $institution_result->schoolDetails;
        $output = $this->build_institution_output($institution_data, $public);
        $output = mb_convert_encoding($output, 'ISO-8859-1', 'UTF-8');
        if ($output == false) {
            $output = 'Warning illegal character exists in the institution details, please return back to the results';
        }

        return $output;
    }

    /**************************************************************************
     * Institution Details Callback
     **************************************************************************/
    public function sabc_institution_details_page($institution_code)
    {
        $output = '';

        drupal_add_css(drupal_get_path('module', 'sabc_institution').'/css/sabc_institution.css', ['type' => 'file']);

        //Create a new SOAP Call
        $inst = new application(false);

        //Query results based on the url/institution code
        $institution_result = $inst->fnGetSchoolDetails($institution_code);
        $institution_data = $institution_result->schoolDetails;

        //sets html title and page title defaults to html title
        drupal_set_title($institution_data->SchoolName);

        //Set breadcrumb for the page
        $breadcrumb = [];
        $breadcrumb[] = $institution_data->SchoolName;
        drupal_set_breadcrumb($breadcrumb);

        $output = build_institution_output($institution_data);

        return $output;
    }

    public function build_institution_output($institution_details, $public)
    {
        $html = '';

        if (is_object($institution_details)) {
            //remove any opening whitespace on strings
            foreach ($institution_details as &$trimmed_details) {
                if (is_string($trimmed_details)) {
                    $trimmed_details = trim($trimmed_details);
                }
            }

            //break up and get the accredation code and url
            $accredations = (isset($institution_details->Accreditations->Accreditation)) ?
                $institution_details->Accreditations->Accreditation : null;

            $accredation_url = (isset($institution_details->Accreditations->Accreditation->AccreditationURL)) ?
                $institution_details->Accreditations->Accreditation->AccreditationURL : null;
            //designation status description
            $designation_status_descript = (isset($institution_details->DesignationStatusDescript)) ? $institution_details->DesignationStatusDescript : null;

            $accredation_output = [];

            //check to see if there is single or multiple results
            if (is_array($accredations)) {
                if ($accredations) {
                    //loop through and build accreditation output for multiple accreditations
                    foreach ($accredations as $accredation) {
                        if ($accredation->AccreditationURL) {
                            $accredation_output[] .= ($accredation->AccreditationCode) ? '<a href="'.$accredation->AccreditationURL.'">'.trim($accredation->AccreditationCode).'</a>' : null;
                        } else {
                            $accredation_output[] .= ($accredation->AccreditationCode) ? trim($accredation->AccreditationCode) : null;
                        }
                    }
                }
            } else {
                if ($accredations) {
                    //accreditation out for a single result
                    if ($accredations->AccreditationURL) {
                        $accredation_output[] .= ($accredations->AccreditationCode) ? '<a href="'.$accredations->AccreditationURL.'">'.trim($accredations->AccreditationCode).'</a>' : null;
                    } else {
                        $accredation_output[] .= ($accredations->AccreditationCode) ? trim($accredations->AccreditationCode) : null;
                    }
                }
            }

            $institution_details->SchoolUrl = $institution_details->SchoolUrl ? trim($institution_details->SchoolUrl) : null;

            //make sure the user is allowed to apply
            //drupal_goto('/dashboard/student-loans/apply');

            //build html output
            $html = '<div class="institution-details box card">';
            $html .= '<div class="card-header text-muted">'.$institution_details->SchoolName.'<span class="icon icon-importcontacts float-right"></span></div>'; //Header/title
            $html .= '<div class="card-body"><div class="row"><div class="col-sm-6">'; //Left Container
            $html .= '<div id="institution-address">'; //full address
            $html .= (! (empty($institution_details->Address1))) ? $institution_details->Address1 : null;
            $html .= (! (empty($institution_details->Address2))) ? '<br/>'.$institution_details->Address2 : null;
            $html .= (! (empty($institution_details->Address3))) ? '<br/>'.$institution_details->Address3 : null;
            $html .= (! (empty($institution_details->City))) ? '<br/>'.$institution_details->City : null;
            $html .= (! (empty($institution_details->ProvinceCode))) ? ' '.$institution_details->ProvinceCode : null;
            $html .= (! (empty($institution_details->PostalCode))) ? ' '.$institution_details->PostalCode : null;
            $html .= (! (empty($institution_details->CountryDesc))) ? '<br/>'.$institution_details->CountryDesc : null;
            $html .= '</div>'; //end address
            $html .= (isset($institution_details->FAOPhone) && $institution_details->FAOPhone) ? '<div id="institution-phone"><strong>Phone: </strong>'.$this->formatPhoneNumber($institution_details->FAOPhone).'</div>' : null; //phone
            $html .= (isset($institution_details->FAOFax) && $institution_details->FAOFax) ? '<div id="institution-fax"><strong>Fax: </strong>'.$this->formatPhoneNumber($institution_details->FAOFax).'</div>' : null; //fax
            $html .= (isset($institution_details->FAOEmail) && $institution_details->FAOEmail) ? '<div id="institution-email"><strong>Email: </strong> <a href="mailto:'.
                $institution_details->FAOEmail.'">'.$institution_details->FAOEmail.'</a></div>' : null; //email
            $html .= (($institution_details->SchoolUrl) && ! (empty($institution_details->SchoolUrl))) ? '<div id="institution-url"><a href="'.$institution_details->SchoolUrl.'" target="_blank">Website</a></div>' : null; //URL
            $html .= '</div>'; //close left container
            $html .= '<div class="col-sm-6">'; //right container
            $html .= (isset($institution_details->SchoolCode) && $institution_details->SchoolCode) ? '<div id="institution-code"><strong>Code: </strong>'.$institution_details->SchoolCode.'</div>' : null; //Institution Code
            $html .= ($accredation_output) ? '<div id="institution-accredited"><strong>Authority/References: </strong>'.implode(', ', $accredation_output).'</div>' : null;
            $html .= (isset($institution_details->WebStatusDesc) && $institution_details->WebStatusDesc) ? '<div id="institution-status"><strong>Type:</strong> '.$institution_details->WebStatusDesc.'</div>' : null;
            $html .= '</div></div>'; //close right container and row

            $html .= '<div class="row"><div class="pt-3 col-12">';
            $style = '';
            if (isset($institution_details->DesignationStatus) && $institution_details->DesignationStatus == 'Designated') {
                $style = 'success';
            }
            if (isset($institution_details->DesignationStatus) && $institution_details->DesignationStatus == 'Pending') {
                $style = 'warning';
            }
            if (isset($institution_details->DesignationStatus) && $institution_details->DesignationStatus == 'Denied') {
                $style = 'danger';
            }
            if (isset($institution_details->DesignationStatus) && $institution_details->DesignationStatus == 'Under Review') {
                $style = 'info';
            }
            if (isset($institution_details->DesignationStatus) && $institution_details->DesignationStatus == 'Does Not Meet Criteria') {
                $style = 'danger';
            }
            $html .= (isset($institution_details->DesignationStatus) && $institution_details->DesignationStatus) ? '<div id="institution-designation"><span class="badge badge-'.$style.'">'.$institution_details->DesignationStatus.'</span>' : null;
            $html .= (isset($institution_details->SchoolCode) && ($institution_details->SchoolCode == 'AJAA' || $institution_details->SchoolCode == 'AJBH')) ? '<p class="instSpecificMsg"><strong>IMPORTANT</strong> - Use Camosun College - All Campuses (for classes beginning August 1, 2019 or later).</p>' : null;
            $html .= ($designation_status_descript) ? '<p>'.$designation_status_descript.'</p>' : null;

            if ($public === true) {
                $isUserVerified = false;
            } else {
                $userProfile = new User();
                $isUserVerified = $userProfile->fnVerifyUser();
            }

            if ($isUserVerified === true && isset($institution_details->DesignationStatus) && ($institution_details->DesignationStatus == 'Designated' || $institution_details->DesignationStatus == 'Under Review')) {
                $html .= '<ul class="institution-assistance anchor list-group list-group-flush">';

                //check if institution is eligible for online applications
                if (isset($institution_details->FullTimeEligible) && $institution_details->FullTimeEligible == 'true') {
                    $html .= '<li class="list-group-item"><span>Start your <strong>full-time</strong> application now</span>
												<a href="/dashboard/apply/confirm/full-time/'.$institution_details->SchoolIDX.'" id="full-time"
												class="apply-for-assistance btn btn-primary float-right four mobile-four columns">Start full-time application</a></li>';
                }

                //check if institution is eligible for online applications
                if (isset($institution_details->PartTimeEligible) && $institution_details->PartTimeEligible == 'true') {
                    //if part time is available show apply for part-time financial assistance
                    $html .= '<li class="list-group-item"><span>Start your <strong>part-time</strong> application now</span>
												<a href="/help-centre/applying-loans/how-apply-part-time-student" id="part-time"
												class="apply-for-assistance btn btn-primary float-right four mobile-four columns">Download part-time application</a></li>';
                }

                $html .= '</ul>';
            }

            $html .= '</div></div></div>'; //close row, content, and box
        }

        return $html;
    }

    private function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (strlen($phoneNumber) > 10) {
            $countryCode = substr($phoneNumber, 0, strlen($phoneNumber) - 10);
            $areaCode = substr($phoneNumber, -10, 3);
            $nextThree = substr($phoneNumber, -7, 3);
            $lastFour = substr($phoneNumber, -4, 4);

            $phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
        } elseif (strlen($phoneNumber) == 10) {
            $areaCode = substr($phoneNumber, 0, 3);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour = substr($phoneNumber, 6, 4);

            $phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
        } elseif (strlen($phoneNumber) == 7) {
            $nextThree = substr($phoneNumber, 0, 3);
            $lastFour = substr($phoneNumber, 3, 4);

            $phoneNumber = $nextThree.'-'.$lastFour;
        }

        return $phoneNumber;
    }

}
