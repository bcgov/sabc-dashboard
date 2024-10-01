<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

class BcscCreateRequest extends FormRequest
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

        $msgs = [
            'userGUID.required' => 'We are having a technical issue determining your BC Service Card. Error #84529.',
            'assuranceLevel.required' => 'We are having a technical issue determining your BC Service Card type. Error #84521',
            'email.required' => 'E-mail address is invalid.',
            //            'user_id.required' => 'User ID is required.',
            //            'user_id.min' => 'User ID must be at least 8 characters long.',
            'social_insurance_number.required' => 'Social Insurance Number is required.',
            'Prov.in' => 'Invalid Province code for Canada.',
            'State.in' => 'Invalid state code for United States of America.',

            //            'password.required' => 'Password must be between 8 and 20 characters.',
            //            'password.between' => 'Password must be between 8 and 20 characters.',
            //            'password.regex' => 'Password must include one or more characters from the 4 categories below:<br><ul class="disc" style="color:#bd362f;"><li>English lower case (a-z)</li><li>English upper case characters (A-Z)</li><li>Base 10 digits (0-9)</li><li>Special characters/symbols (~!@#$%^&*_-+=`|\(){}[]:;"\'<>,.?/)</li></ul>',
            //            'confirm_password.required' => 'Password and Confirm Password must be between 8 and 20 characters.',
            //            'confirm_password.same' => 'Password and Confirm Password must be the same.',

            'gender.required' => 'Gender field is required.',
            'Street1.required' => 'Address Line 1 field is required.',
            'City.required' => 'City field is required.',
            'Phone.required' => 'Phone Number field is required.',

            //            'question1.required' => 'Challenge Question 1 field is required.',
            //            'answer1.required' => 'Answer for question #1 field is required.',
            //            'question2.required' => 'Challenge Question 2 field is required.',
            //            'answer2.required' => 'Answer for question #2 field is required.',
            //            'question3.required' => 'Challenge Question 3 field is required.',
            //            'answer3.required' => 'Answer for question #3 field is required.',

            'day.required' => 'Birth Day field is required.',
            'month.required' => 'Birth Month field is required.',
            'year.required' => 'Birth Year field is required.',

            'day.regex' => 'Invalid date for Birth Day field.',
            'month.regex' => 'Invalid date for Birth Month field.',

            'day.between' => 'Birth Day must be between 1 - 31.',
            'month.between' => 'Birth Month is invalid.',

            'dateOfBirth.required' => 'Date of Birth field is required.',
            'year.date_format' => 'Invalid date for Birth Year field.',
            'year.before' => 'Provided Birth Year can not be after today.',

            'year.after' => 'Please ensure your birth year is after '.date('Y', strtotime('120 years ago')),
        ];

        if (isset($this->Country) && ($this->Country == 'CAN' || $this->Country == 'USA')) {
            $msgs['PostZip.required'] = $postZipReq;
            $msgs['PostZip.size'] = $postZipLen;
        }

        return $msgs;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //UPDATE PROFILE REQUIRED FIELDS

        $provCodes = ['AB', 'BC', 'MB', 'NB', 'NL', 'NT', 'NS', 'NU', 'ON', 'PE', 'QC', 'SK', 'YT'];
        $stateCodes = ['AL', 'AK', 'AS', 'AZ', 'AR', 'AA', 'AP', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'];

        $postZipLength = 0;
        if (isset($this->Country) && $this->Country == 'CAN') {
            $postZipLength = 6;
        }

        if (isset($this->Country) && $this->Country == 'USA') {
            $postZipLength = 5;
        }

        $rules = [
            'first_name' => 'nullable|string',
            'last_name' => 'required',
            'email' => 'required|email',
            'confirm_email' => 'required|same:email',
            'gender' => 'required',
            'month' => 'required|numeric|between:1,12',
            'day' => 'required|numeric|between:1,31',
            'year' => 'required|date_format:Y|before:today|after:-120 years',
            'dateOfBirth' => 'required|date_format:Ymd|before:today|after:-120 years',

            'social_insurance_number' => 'required|digits:9',

            'Street1' => 'required',
            'City' => 'required',
            'Country' => 'required',
            'Phone' => 'required|digits:10',

            'assuranceLevel' => 'required',
            'userGUID' => 'required',
        ];

        if (isset($this->Country) && ($this->Country == 'CAN' || $this->Country == 'USA')) {
            $rules['PostZip'] = 'required:size:'.$postZipLength;

            if ($this->Country == 'CAN') {
                $rules['Prov'] = 'required|in:'.implode(',', $provCodes).'';
            }
            if ($this->Country == 'USA') {
                $rules['State'] = 'required|in:'.implode(',', $stateCodes).'';
            }
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
        Session::forget('DEBUG');
        //convert all inputs to upper case
        $inputs = $this->input();
        foreach ($inputs as $key => $value) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': BcscCreateRequest() key&value before : '.$key.': '.$value.' ('.$this->input($key).')');
            }
            if ($key != 'password' && $key != 'confirm_password' && $key != '_token' && $key != 'userGUID') {
                $value = mb_strtoupper(str_replace(['"'], '', $value));
                $this->merge([$key => $value]);
            }
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': BcscCreateRequest() key&value after : '.$key.': '.$value.' ('.$this->input($key).')');
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

        if (isset($this->gender_select)) {
            $this->merge(['gender' => $this->gender_select]);
        }
        if ($this->gender == 'WOMAN') {
            $this->merge(['gender' => 'F']);
        }
        if ($this->gender == 'MAN') {
            $this->merge(['gender' => 'M']);
        }
        if ($this->gender == 'NON-BINARY') {
            $this->merge(['gender' => 'X']);
        }
        if ($this->gender == 'PREFER NOT TO ANSWER') {
            $this->merge(['gender' => 'U']);
        }

        $this->merge([
            'email' => filter_var($this->email, FILTER_SANITIZE_EMAIL),
            'Phone' => str_replace(['(', ')', '+', '-', ' '], '', $this->Phone),
            'PostZip' => str_replace(' ', '', $this->PostZip), //STRIP OUT ANY SPACES
            'ProvState' => $provState,
        ]);

        if (isset($this->year)) {
            $this->year = (int) $this->year;
        }
        if (isset($this->day)) {
            $this->day = (int) $this->day;
        }
        if (isset($this->month)) {
            $this->month = (int) $this->month;
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
                    } elseif ($this->year < 1900) {
                        $status = false;
                    } else {
                        $this->merge([
                            'birthdate' => $this->year.''.sprintf('%02s', $this->month).''.sprintf('%02s', $this->day),
                        ]);
                    }
                } else {
                    $status = false;
                }
            } else {
                $status = false;
            }
        }

        if (isset($this->birthdate)) {
            $this->merge([
                'dateOfBirth' => $this->birthdate,
            ]);
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': BcscCreateRequest() dateOfBirth: '.$this->dateOfBirth);
            }
        }
        if (isset($this->first_name) && strlen($this->first_name) >= 16) {
            $this->merge([
                'first_name' => substr($this->first_name, 0, 15),
            ]);
        }
        if (isset($this->last_name) && strlen($this->last_name) > 25) {
            $this->merge([
                'last_name' => substr($this->last_name, 0, 25),
            ]);
        }
        if (isset($this->middle_name) && strlen($this->middle_name) > 15) {
            $this->merge([
                'middle_name' => substr($this->middle_name, 0, 15),
            ]);
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
                } else {
                    $this->merge([
                        'social_insurance_number' => $sin,
                    ]);
                }
            } else {
                $status = false;
            }
        }

        if (isset($this->userGUID)) {
            $aeit = new \App\Http\Controllers\Aeit();
            $this->merge([
                'userGUID' => $aeit->fnDecrypt($this->userGUID),
            ]);
        }
    }
}
