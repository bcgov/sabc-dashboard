<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
//        return [
//            'files.*.*' => 'Sorry 0the uploaded file must be of types.',
//            ];

        return [
            'files.*.required' => 'Sorry we had problems processing your request.',
            'files.*.mimes' => 'Sorry the uploaded file must be of types: pdf, png, jpg, jpeg, gif, doc, docx, xls',
            'files.*.max' => 'Sorry the size of the uploaded file must not exceed 2MB.',
        ];
    }

//        document_purpose: P000
//attach_to_application: 22222222222
//document_notes: aaaaaa
//nid:
//_triggering_element_name: sabc_file_manager_block_form
//_triggering_element_value: op
//ajax_html_ids[]: Upload
//filesize: 126967
//files[0]: (binary)
//        $files = [];
//        for ($i=0; $i<sizeof($this->file('files')); $i++){
//            $files[] = ['files.' . $i => $this->file('files')[$i]->getClientOriginalName()];
//        }
//        return $files;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'files.*' => 'required|mimes:pdf,png,jpg,jpeg,gif,doc,docx,xls|max:2480', //max 2MB
            //'attach_to_application' => 'present|numeric|between:0,12|nullable',
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
//        $params['documents'] = $saveForUpload;
//        $params['document_purpose'] = $form_state['values']['document_purpose'];
//        $appNumber = str_ireplace(array('--', 'Submitted', 'InTransition', 'NotSubmitted','Appendix1', 'Appendix2'), '', $form_state['values']['attach_to_application']);
//
//        $params['document_notes'] = htmlspecialchars(strip_tags($form_state['values']['document_notes'], '<p><b><br><strong>'));
//
//        if(stripos($form_state['values']['attach_to_application'], 'NotSubmitted')){
//            if(stripos($form_state['values']['attach_to_application'], 'Appendix1')){
//                $params['document_notes'] .= '<br><br> *Please note that at the time of upload the following X1 had not been submitted for application #'.$appNumber.'.  Before logging please make sure that application has been submitted.<br><br>';
//            }
//            else if(stripos($form_state['values']['attach_to_application'], 'Appendix2')){
//                $params['document_notes'] .= '<br><br> *Please note that at the time of upload the following X2 had not been submitted for application #'.$appNumber.'.  Before logging please make sure that application has been submitted.<br><br>';
//            }
//        }
//
//        $params['attach_to_application'] = ($form_state['values']['attach_to_application'] !== 'default') ? $appNumber : NULL;

        $appNumber = str_ireplace(['--', 'Submitted', 'InTransition', 'NotSubmitted', 'Appendix1', 'Appendix2'], '', $this->attach_to_application);

        $this->document_notes = htmlspecialchars(strip_tags($this->document_notes, '<p><b><br><strong>'));

        if (stripos($this->attach_to_application, 'NotSubmitted')) {
            if (stripos($this->attach_to_application, 'Appendix1')) {
                $this->document_notes .= '<br><br> *Please note that at the time of upload the following X1 had not been submitted for application #'.$appNumber.'.  Before logging please make sure that application has been submitted.<br><br>';
            } elseif (stripos($this->attach_to_application, 'Appendix2')) {
                $this->document_notes .= '<br><br> *Please note that at the time of upload the following X2 had not been submitted for application #'.$appNumber.'.  Before logging please make sure that application has been submitted.<br><br>';
            }
        }
        $this->attach_to_application = ($this->attach_to_application !== 'default') ? $appNumber : null;

        $this->merge([
            'appNumber' => $appNumber,
            'document_notes' => $this->document_notes,
            'attach_to_application' => $this->attach_to_application,
        ]);
    }
}
