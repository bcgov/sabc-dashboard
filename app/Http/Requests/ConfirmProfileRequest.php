<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $postZipReq = '';
        $postZipLen = '';
        if (isset($this->Country) && $this->Country == 'CAN') {
            $postZipReq = 'Postal Code is required';
            $postZipLen = 'Invalid Postal Code';
        }

        if (isset($this->Country) && $this->Country == 'USA') {
            $postZipReq = 'Zip Code is required';
            $postZipLen = 'Invalid Zip Code';
        }

        return [
            'email.required' => 'E-mail address is invalid',
            'Prov.in' => 'Invalid Province code for Canada.',
            'State.in' => 'Invalid state code for United States of America.',
            'PostZip.required' => $postZipReq,
            'PostZip.size' => $postZipLen,
            'new_password.between' => 'Password must be at least 8 characters and must include one or more characters from at least 3 of the 4 categories below:<br><ul class="disc" style="color:#bd362f;"><li>English lower case (a-z)</li><li>English upper case characters (A-Z)</li><li>Base 10 digits (0-9)</li><li>Special characters/symbols (~!@#$%^&*_-+=`|\(){}[]:;"\'<>,.?/)</li></ul>',
            'gender.required' => 'Gender field is required.',
            'Street1.required' => 'Address Line 1 field is required.',
            'City.required' => 'City field is required.',
            'Phone.required' => 'Phone Number field is required.',
            'question1.required' => 'Challenge Question 1 field is required.',
            'answer1.required' => 'Answer for question #1 field is required.',
            'question2.required' => 'Challenge Question 2 field is required.',
            'answer2.required' => 'Answer for question #2 field is required.',
            'question3.required' => 'Challenge Question 3 field is required.',
            'answer3.required' => 'Answer for question #3 field is required.',
            'dateOfBirth.required' => 'Date of Birth field is required.',
            'dateOfBirth.date_format' => 'Invalid date for Date of Birth field.',
            'dateOfBirth.before' => 'Provided Date of Birth can not be after today.',
            'dateOfBirth.after' => 'Please ensure your birth year is after '.date('Y', strtotime('120 years ago')),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //UPDATE PROFILE REQUIRED FIELDS
//        $updateProfRF = array('gender'      									=> array('Req' => 't', 'Field' => 'Gender'),
//            'email'      									=> array('Req' => 't', 'Field' => 'E-mail Address'),
//            'Street1'      								=> array('Req' => 't', 'Field' => 'Address Line 1'),
//            'City'      										=> array('Req' => 't', 'Field' => 'City'),
//            'Country'      								=> array('Req' => 't', 'Field' => 'Country'),
//            'Phone'      									=> array('Req' => 't', 'Field' => 'Phone Number'),
//            'question1'     								=> array('Req' => 't', 'Field' => 'Challenge Question 1'),
//            'answer1'      								=> array('Req' => 't', 'Field' => 'Answer for question #1'),
//            'question2'     								=> array('Req' => 't', 'Field' => 'Challenge Question 2'),
//            'answer2'      								=> array('Req' => 't', 'Field' => 'Answer for question #2'),
//            'question3'     								=> array('Req' => 't', 'Field' => 'Challenge Question 3'),
//            'answer3'      								=> array('Req' => 't', 'Field' => 'Answer for question #3'),
//            'Prov'      										=> array('Req' => 'f', 'Field' => 'Province'),
//            'State'      									=> array('Req' => 'f', 'Field' => 'State'),
//            'PostZip'      								=> array('Req' => 'f', 'Field' => 'Postal / ZIP Code'),
//            'Phone'      									=> array('Req' => 't', 'Field' => 'Phone Number'),
//            'Consent'											=> array('Req' => 't', 'Field' => 'Subscribe'));

        $provCodes = ['AB', 'BC', 'MB', 'NB', 'NL', 'NT', 'NS', 'NU', 'ON', 'PE', 'QC', 'SK', 'YT'];
        $stateCodes = ['AL', 'AK', 'AS', 'AZ', 'AR', 'AA', 'AP', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'];

        $rules = [
            'gender' => 'required',
            'email' => 'required|email',
            'confirm_email' => 'required|same:email',
            'Street1' => 'required',
            'City' => 'required',
            'Country' => 'required',
            'Phone' => 'required|digits:10',
            //            'dateOfBirth' => 'required|date_format:Ymd|before:today|after:-120 years',
            'question1' => 'required',
            'answer1' => 'required',
            'question2' => 'required',
            'answer2' => 'required',
            'question3' => 'required',
            'answer3' => 'required',
            'Prov' => 'in:'.implode(',', $provCodes).'|nullable',
            'State' => 'in:'.implode(',', $stateCodes).'|nullable',
            'Consent' => 'required',
            //            'new_password' => 'present|string|between:8,20|nullable',
            //            'confirm_new_password' => 'present|string|same:new_password|nullable',
            //            'DID' => 'nullable|string|',
            //            'assuranceLevel' => 'nullable|string|'
        ];

        if (isset($this->Country) && $this->Country == 'CAN') {
            $postZipLength = 6;
            $rules['PostZip'] = 'size:'.$postZipLength;
        }

        if (isset($this->Country) && $this->Country == 'USA') {
            $postZipLength = 5;
            $rules['PostZip'] = 'size:'.$postZipLength;
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        //convert all inputs to upper case
//        var_dump();
        $inputs = $this->input();
        foreach ($inputs as $key => $value) {
            //var_dump($this[$key]);
//            var_dump($key);
//            var_dump($value);
//            $input = mb_strtoupper($value);
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if ($k != 'new_password' && $k != 'confirm_new_password' && $k != '_token') {
                        $v = mb_strtoupper($v);
                        $this->merge([$k => $v]);
                    }
                }
            } else {
                if ($key != 'new_password' && $key != 'confirm_new_password' && $key != '_token') {
                    $value = mb_strtoupper($value);
                    $this->merge([$key => $value]);
                }
            }
        }

        $provState = '';
        if (isset($this->Country) && $this->Country == 'CAN') {
            //REMOVE SINGLE QUOTES AROUND RETURN
            $provState = str_replace("'", '', $this->Prov);
            $this->merge([
                'Prov' => str_replace("'", '', $this->Prov),
            ]);
        }

        if (isset($this->Country) && $this->Country == 'USA') {
            $provState = $this->State;
        }

        if ($this->gender == 'WOMAN') {
            $this->gender = 'F';
        }
        if ($this->gender == 'MAN') {
            $this->gender = 'M';
        }
        if ($this->gender == 'NON-BINARY') {
            $this->gender = 'X';
        }
        if ($this->gender == 'PREFER NOT TO ANSWER') {
            $this->gender = 'U';
        }

        $this->merge([
            'email' => filter_var($this->email, FILTER_SANITIZE_EMAIL),
            'confirm_email' => filter_var($this->confirm_email, FILTER_SANITIZE_EMAIL),
            'Phone' => str_replace(['(', ')', '+', '-', ' '], '', $this->Phone),
            'PostZip' => str_replace(' ', '', $this->PostZip), //STRIP OUT ANY SPACES
            'ProvState' => $provState,
            'gender' => $this->gender,

        ]);

        if (isset($this->dateOfBirth)) {
            $this->merge([
                'dateOfBirth' => date('Ymd', strtotime($this->dateOfBirth)),
            ]);
        }
        //TRUNCATE NAMES IF FNAME IS LONGER THAN 15 AND LNAME IS LONGER THAN 25 CHARACTERS IF BC SERVICES CARD
        if (isset($this->assuranceLevel)) {
            if (isset($this->first_name) && strlen($this->first_name) >= 16) {
//                $this->first_name = substr($this->first_name, 0, 15);
                $this->merge([
                    'first_name' => substr($this->first_name, 0, 15),
                ]);
            }

            if (isset($this->last_name) && strlen($this->last_name) > 25) {
//                $this->last_name = substr($this->last_name, 0, 25);
                $this->merge([
                    'last_name' => substr($this->last_name, 0, 25),
                ]);
            }
        }

        //MAKE SURE BIRTHDAY FIELDS HAS BEEN PASSED TO US BEFORE VALIDATING.  IF NEW ACCOUNT THEN IT SHOULD ALWAYS BE THERE BUT ON EDIT IT WON'T.
        if (isset($this->month, $this->day, $this->year)) {
            //MAKE SURE YEAR IS 4 DIGITS LONG
            if (strlen($this->year) == 4) {
                $birthdate = $this->year.'-'.$this->month.'-'.$this->day;

                //MAKE SURE DATE IS VALID
                if (checkdate((int) $this->month, (int) $this->day, (int) $this->year)) {
                    //MAKE SURE BIRTHDATE IS NOT GREATER THAN OR EQUAL TO TODAY
                    if (strtotime($birthdate) >= strtotime(date('Y-m-d'))) {
                        $status = false;
                    //form_set_error('month', 'Birth date is invalid');
                    } elseif ($this->year < 1900) {
                        $status = false;
//                        form_set_error('year', 'Please ensure birth year is after 1900. If you have any questions, please contact us.');
                    } else {
//                        $this->birthdate = $this->year.''.sprintf("%02s", $this->month).''.sprintf("%02s", $this->day); //DATE IS GOOD SO FORMAT TO PROPER FORMAT FOR E-SERVICE YYYYMMDD
                        $this->merge([
                            'birthdate' => $this->year.''.sprintf('%02s', $this->month).''.sprintf('%02s', $this->day),
                        ]);
                    }
                } else {
                    $status = false;
//                    form_set_error('month', 'Invalid date for birthday.'); //DATE IS INVALID
                }
            } else {
                $status = false;
//                form_set_error('year', 'Birth date year is invalid.  Please ensure year is formatted YYYY');
            }
        }

        //MAKE SURE SIN HAS BEEN PASSED TO US BEFORE VALIDATING.  IF NEW ACCOUNT THEN IT SHOULD ALWAYS BE THERE BUT ON EDIT IT WON'T.
        if (isset($this->social_insurance_number)) {
            //VALIDATE SIN
            $sin = str_replace(['-', ' '], '', $this->social_insurance_number); //FORMAT SIN TAKE OUT "-" AND SPACES

            //MAKE SURE SIN IS 9 DIGITS LONG
            if (strlen($sin) == 9) {
                //ENSURE SIN IS A NUMBER
                if (! filter_var($sin, FILTER_VALIDATE_INT)) {
                    $status = false;
//                    form_set_error('social_insurance_number', 'Invalid SIN.  SIN can only contain numbers.');
                } else {
//                    $this->social_insurance_number = $sin; //SIN IS GOOD SO FORMAT TO PROPER FORMAT FOR E-SERVICE
                    $this->merge([
                        'social_insurance_number' => $sin,
                    ]);
                }
            } else {
                $status = false;
//                form_set_error('social_insurance_number', 'Invalid SIN.  SIN must contain 9 numbers and can be separated by spaces and dashes.');
            }
        }

        if (! isset($this->userGUID)) {
            //FORMAT CONSENT TO PROPER VALUE - IF WE DIDN'T GET CONSENT PASSED BACK TO US OR CONSENT WAS EMPTY SET TO "N" OTHERWISE SET TO "Y"
            $this->Consent = (! isset($this->Consent) || empty($this->Consent)) ? 'N' : 'Y';
            $this->userConsent = $this->Consent;
            $this->merge([
                'Consent' => $this->Consent,
            ]);
            $this->merge([
                'userConsent' => $this->Consent,
            ]);
        }
    }
}
