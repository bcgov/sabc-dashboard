<?php

namespace App\Http\Requests;

class AjaxRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->isXmlHttpRequest()) {
            return true;
        }

        abort(404);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    public function ajax()
    {
        if ($this->isXmlHttpRequest()) {
            return true;
        }

        abort(404);
    }
}
