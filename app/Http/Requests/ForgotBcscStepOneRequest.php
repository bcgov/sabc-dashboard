<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotBcscStepOneRequest extends FormRequest
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
        return [
            'lastname.required' => 'Last Name is required.',
            'socialinsnum.digits' => 'Invalid SIN.  SIN can only contain 9 numbers along with separating spaces or dashes.',
            'socialinsnum.required' => 'Social Insurance Number is required.',

            'day.required' => 'Birth Day field is required.',
            'month.required' => 'Birth Month field is required.',
            'year.required' => 'Birth Year field is required.',

            'day.regex' => 'Invalid date for Birth Day field.',
            'month.regex' => 'Invalid date for Birth Month field.',

            'day.between' => 'Birth Day must be between 1 - 31.',
            'month.between' => 'Birth Month is invalid.',

            //            'dateOfBirth.required' => 'Date of Birth field is required.',
            'year.date_format' => 'Invalid date for Birth Year field.',
            'year.before' => 'Provided Birth Year can not be after today.',
            'year.after' => 'Please ensure your birth year is after '.date('Y', strtotime('120 years ago')),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'lastname' => 'required',

            'month' => 'required|numeric|between:1,12',
            'day' => 'required|numeric|between:1,31',
            'year' => 'required|date_format:Y|before:today|after:-120 years',
            //            'dateOfBirth' => 'required|date_format:Ymd|before:today|after:-120 years',

            'socialinsnum' => 'required|digits:9',
        ];

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
            if ($key != '_token') {
                $value = mb_strtoupper($value);
            }
        }

        //TRUNCATE NAMES IF FNAME IS LONGER THAN 15 AND LNAME IS LONGER THAN 25 CHARACTERS IF BC SERVICES CARD
        if (isset($this->lastname) && strlen($this->lastname) > 25) {
//                $this->last_name = substr($this->last_name, 0, 25);
            $this->merge([
                'lastname' => substr($this->lastname, 0, 25),
            ]);
        }

        if (isset($this->year)) {
            $this->year = (int) $this->year;
        }
        if (isset($this->day)) {
            $this->day = (int) $this->day;
        }
        if (isset($this->month)) {
            $this->month = (int) $this->month;
        }

        $this->merge([
            'dateOfBirth' => $this->year.''.sprintf('%02s', $this->month).''.sprintf('%02s', $this->day),
        ]);

        //MAKE SURE SIN HAS BEEN PASSED TO US BEFORE VALIDATING.  IF NEW ACCOUNT THEN IT SHOULD ALWAYS BE THERE BUT ON EDIT IT WON'T.
        if (isset($this->socialinsnum)) {
            //VALIDATE SIN
            $sin = str_replace(['-', ' '], '', $this->socialinsnum); //FORMAT SIN TAKE OUT "-" AND SPACES

            //MAKE SURE SIN IS 9 DIGITS LONG
            if (strlen($sin) == 9) {
                //ENSURE SIN IS A NUMBER
                if (! filter_var($sin, FILTER_VALIDATE_INT)) {
                    $status = false;
//                    form_set_error('social_insurance_number', 'Invalid SIN.  SIN can only contain numbers.');
                } else {
//                    $this->social_insurance_number = $sin; //SIN IS GOOD SO FORMAT TO PROPER FORMAT FOR E-SERVICE
                    $this->merge([
                        'socialinsnum' => $sin,
                    ]);
                }
            } else {
                $status = false;
//                form_set_error('social_insurance_number', 'Invalid SIN.  SIN must contain 9 numbers and can be separated by spaces and dashes.');
            }
        }
    }
}
