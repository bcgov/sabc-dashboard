<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordStepTwoRequest extends FormRequest
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
            'step.required' => 'We could not process your request. Please contact StudentAidBC and provide error #2021-43210.',
            'questionPool.required' => 'We could not process your request. Please contact StudentAidBC and provide error #2021-43211',
            'questionNumber.required' => 'We could not process your request. Please contact StudentAidBC and provide error #2021-43212',
            'userGUID.required' => 'We could not process your request. Please contact StudentAidBC and provide error #2021-43213',
            'emailAddress.required' => 'We could not process your request. Please contact StudentAidBC and provide error #2021-43214',

            'answer.required' => 'Answer to the challenge question is required.',
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
            'step' => 'required',
            'questionPool' => 'required',
            'questionNumber' => 'required',
            'userGUID' => 'required',
            'emailAddress' => 'required',
            'answer' => 'required',

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
    }
}
