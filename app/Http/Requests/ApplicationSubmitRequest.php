<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationSubmitRequest extends FormRequest
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
        $econsent_rollout_date = 20200211; // 2020 Feb 11
        $today = date('Ymd', strtotime('today'));
        $today = (int) $today;

        $messages = [
            'verification_confirmation.*' => 'You must accept our agreement to submit an application.',
        ];
        if ($today >= $econsent_rollout_date) {
//            if(isset($this->declaration_confirmation)){
            $messages['declaration_confirmation.*'] = 'Please confirm you agree to the terms of the SABC declaration.';
//            }
//            if(isset($this->declaration_cra_confirmation)){
            $messages['declaration_cra_confirmation.*'] = 'Please confirm you agree to the terms of the CRA consent form.';
//            }
        } else {
//            if(isset($this->declaration_confirmation)){
            $messages['declaration_confirmation.*'] = 'Please confirm you agree to the terms of the SABC declaration and to the terms of the CRA consent form.';
//            }
        }

        if (isset($this->decRequired) && $this->decRequired == 'Y') {
            $messages['consent_confirmation.*'] = 'Please confirm you will print, sign and mail your declaration.';
        }

        return $messages;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
//
//        if(isset($this->Country) && $this->Country == 'CAN'){
//            $postZipLength = 6;
//        }

        $rules = [
            'verification_confirmation' => 'accepted',
            'declaration_confirmation' => 'accepted',
        ];
        if (isset($this->decRequired) && $this->decRequired == 'Y') {
            $rules['consent_confirmation'] = 'accepted';
        }
        if (isset($this->decRequired) && $this->decRequired == 'E') {
            $rules['declaration_cra_confirmation'] = 'accepted';
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
//
//        $this->merge([
//            'userConsent' => $this->Consent
//        ]);
    }
}
