<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordResetRequest extends FormRequest
{
    /**
     * Redirect route when errors occur.
     *
     * @var string
     */
    protected $redirect = '/forgot/password';

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
            'step.required' => 'We could not process your request. Please contact StudentAidBC and provide error #2021-53210.',
            'userID.required' => 'We could not process your request. Please contact StudentAidBC and provide error #2021-53212',
            'username.required' => 'We could not process your request. Please contact StudentAidBC and provide error #2021-53213',

            'userID.size' => 'Recovery GUID length can only be a minimum of 32 characters long.',

            'password.required' => 'Password must be between 8 and 20 characters.',
            'password.between' => 'Password must be between 8 and 20 characters.',
            'password.regex' => 'Password must include one or more characters from the 4 categories below:<br><ul class="disc" style="color:#bd362f;"><li>English lower case (a-z)</li><li>English upper case characters (A-Z)</li><li>Base 10 digits (0-9)</li><li>Special characters/symbols (~!@#$%^&*_-+=`|\(){}[]:;"\'<>,.?/)</li></ul>',
            'confirm_password.required' => 'Password and Confirm Password must be between 8 and 20 characters.',
            'confirm_password.same' => 'Password and Confirm Password must be the same.',

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
            'userID' => 'required|size:32',
            'username' => 'required',

            'password' => 'required|string|between:8,20|regex:/^.*(?=.*[a-zA-Z])(?=.*[^a-zA-Z\d\s])(?=.*[0-9])(?=.*[\d\x]).*$/',
            'confirm_password' => 'required|string|same:password',

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
