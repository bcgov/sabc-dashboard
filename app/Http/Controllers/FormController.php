<?php

namespace App\Http\Controllers;

use App\Application;
use App\Form;
use App\Http\Requests\AjaxRequest;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDF;
use Redirect;
use Response;

class FormController extends Aeit
{
    public function startNew(Request $request)
    {
        return view('forms-start', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    public function adminFetchForms(AjaxRequest $request)
    {
        return Response::json(['forms' => Form::orderBy('updated_at', 'desc')->with('category')->get()], 200); // Status code here
    }

    /**
     * Display the dashboard page to a logged in admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminForms()
    {
        return view('admin.forms', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * Display the dashboard page to a logged in admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminFormsNew(Request $request)
    {
        return view('admin.forms-new', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AjaxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function adminEditWeight(AjaxRequest $request, Form $form, $weight)
    {
        $weight = intval($weight);

        $form->weight = $weight;
        $form->save();

        return Response::json(['form' => $form], 200); // Status code here
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'category_id' => 'required',
            'form_body' => 'required',
            'program_year' => 'required',
            'status' => 'required|in:draft,active',
            'publish' => 'required|in:published,unpublished',
        ]);

        $form = new Form();
        $form->uuid = (string) Str::uuid();
        $form->name = $request->name;
        $form->category_id = $request->category_id;
        $form->program_year = str_replace(' ', '', $request->program_year);
        $form->form_body = $request->form_body;
        $form->publish = $request->publish == 'published' ? 1 : 0;
        $form->status = $request->status == 'active' ? 1 : 0;
        $form->save();

        return redirect('/admin/forms');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function adminShow(Form $form)
    {
        $form = Form::where('id', $form->id)->first();

        return view('admin.forms-show', ['form' => $form, 'roles' => Auth::user()->roles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function adminUpdate(Request $request, Form $form)
    {
        $this->validate($request, [
            'name' => 'required',
            'category_id' => 'required',
            'form_body' => 'required',
            'program_year' => 'required',
            'status' => 'required|in:draft,active',
            'publish' => 'required|in:published,unpublished',

        ]);

        $form->name = $request->name;
        $form->category_id = $request->category_id;

        $form->program_year = str_replace(' ', '', $request->program_year);
        $form->form_body = $request->form_body;
        $form->publish = $request->publish == 'published' ? 1 : 0;
        $form->status = $request->status == 'active' ? 1 : 0;
        $form->save();

        return redirect('/admin/forms');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function adminDelete(Request $request, Form $form)
    {
        $form->delete();

        return redirect('/admin/forms');
    }

    public function fetchPublishedFormList(AjaxRequest $request, $uuid = null)
    {
        if (! is_null($uuid)) {
            $form = Form::where('uuid', $uuid)->where('status', 1)->where('publish', 1)->first();
            $profile = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR2'));
            if (is_null($form)) {
                return Response::json(['form' => ''], 200);
            } // Status code here

            return Response::json(['profile' => $profile, 'form' => $form], 200); // Status code here
        }

        $forms = Form::where('status', 1)->where('publish', 1)->orderBy('program_year', 'desc')->get();
        $forms->makeHidden(['form_body']);

        return Response::json(['forms' => $forms], 200); // Status code here
    }

    //Display the view to load the list of appeals for a student
    public function forms(Request $request)
    {
        return view('forms', []);
    }

    //Get to show the new appeal form
    public function newForm(Request $request, $uuid)
    {
        $appeal = Form::where('uuid', $uuid)->first();
        if (is_null($appeal)) {
            return redirect('/appeal-forms');
        }

        return view('form-new', ['submit_status' => false, 'submit_msg' => '', 'uuid' => $uuid]);
    }

    //Student filling a new appeal form
    public function createForm(Request $request)
    {
        $this->uid = session(env('GUID_SESSION_VAR'));
        // Begin building file object.

        $form_name = '';
        $contents = [];
        foreach ($request->input() as $key => $val) {
            if ($key == 'form_name') {
                $form_name = $val;
            }

            if ($key == 'form_name' || $key == 'py') {
                $contents[$key] = $val;
            } elseif ($key != '_token' && $key != 'hpot') {
                $contents['data'][$key] = $val;
            }
        }
        $contents['GUID'] = $this->uid;
        $contents['uuid'] = $request->form_uuid;

        $filename = rand().'-_-'.$form_name.'-_-P001-sabc-use-only.txt';
        $saveForUpload = []; //base64_encode files that we will need for our soap request
        array_push($saveForUpload, ['fileName' => $filename, 'arrayFile64' => json_encode($contents)]);

        $params = [];
        $params['documents'] = $saveForUpload;
        $params['document_purpose'] = 'P001';
        $appNumber = str_ireplace(['--', 'Submitted', 'InTransition', 'NotSubmitted', 'Appendix1', 'Appendix2'], '', $request->application_number_v);

        $params['document_notes'] = htmlspecialchars(strip_tags($request->document_notes, '<p><b><br><strong>'));
        $params['document_notes'] = substr($params['document_notes'], 0, 1500);

        if (stripos($request->application_number_v, 'NotSubmitted')) {
            if (stripos($request->application_number_v, 'Appendix1')) {
                $params['document_notes'] .= '<br><br> *Please note that at the time of upload the following X1 had not been submitted for application #'.$appNumber.'.  Before logging please make sure that application has been submitted.<br><br>';
            } elseif (stripos($request->application_number_v, 'Appendix2')) {
                $params['document_notes'] .= '<br><br> *Please note that at the time of upload the following X2 had not been submitted for application #'.$appNumber.'.  Before logging please make sure that application has been submitted.<br><br>';
            }
        }

        $params['attach_to_application'] = ($request->application_number_v !== 'default') ? $appNumber : null;
        $notifications = $this->fnUploadDocuments($params);
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': createAppeal(): notifications: '.json_encode($notifications));
        }

        //remove temp file
        Storage::delete('files/'.$filename);

        return redirect('/appeal-forms');
    }

    public function fetchFormsListData(AjaxRequest $request)
    {
        $msg = '';
        $user = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), null);
        if (is_null($user)) {
            $user = User::select('name')->where('id', Auth::user()->id)->first();
            if (! is_null($user)) {
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR1'), $user, now()->addMinutes(env('SESSION_LIFETIME')));
            } //put it in the cache for the length of the session
        }

        if (! is_null($user)) {
            if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false)) {
                $msg = '<p>To view documents, right click and \'Save as\'</p>';
            }

            $this->uid = session(env('GUID_SESSION_VAR'));
            $notifications = $this->fnGetUploadedDocuments();

            $forms = [];
            foreach ($notifications as $note) {
                if (Str::endsWith($note['fileName'], '-P001-sabc-use-only.txt')) {
                    $forms[] = $note;
                }
            }

            return Response::json(['uploads' => $forms, 'alertMsg' => $msg], 200); // Status code here
        }

        return Response::json(['error' => 'unauthorized'], 403);
    }

    /**
     * Show a single notification page to a logged in Student/Spouse/Parent
     *
     * @return $notificationId|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fetchForm(Request $request, $notificationId, $inTransition = null, $documentType = null)
    {
        $this->uid = session(env('GUID_SESSION_VAR'));
        $data = $this->fetchFormData($notificationId, $inTransition, $documentType);

        if ($data === false) {
            return redirect()->back()->withInput()->withErrors(['errors' => 'We could not process your request. Error #239823']);
        }

        return view('form-show', ['form' => $data]);
    }

    /*
        fnGetDocument:  Call document WS or use cache response to load a particular document for a user
    */
    public function fetchFormData($letterID, $inTransition = false, $documentType = null)
    {
        $host = HOST;
        if (! empty(session()->get(env('ADMIN_SESSION_VAR')))) {
            $host = session()->get(env('ADMIN_SESSION_VAR'));
        }

        //CALL THE APPROPRIATE WEB SERVICE
        $this->WSDL = $this->fnWS('WS-HOSTS', 'GET_DOCUMENT');
        if (empty($inTransition) || (! empty($inTransition) && $inTransition == 'R')) {
            $document = $this->fnRequest('getUserDocument', ['userGUID' => $this->uid, 'documentID' => $letterID]);
            //MAKE SURE WE HAVE A VALID RESPONSE
            if (is_object($document) && ! empty($document)) {

                //MAKE SURE WE DIDN'T GET ERRORS
                if (isset($document->userDocument->url)) {
                    $s = ['http://'.$host.'/letters/', 'https://'.$host.'/letters/'];
                    $dURL = '/letters/'.str_replace($s, '', $document->userDocument->url);

                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': fnGetDocument(): document->userDocument->url: '.$document->userDocument->url);
                        session()->push('DEBUG', now().': fnGetDocument(): host: '.$host);
                        session()->push('DEBUG', now().': fnGetDocument(): dURL: '.$dURL);
                    }
                    $protocal = 'http://';
                    if ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'))) {
                        $protocal = 'https://';
                    }

                    return file_get_contents($protocal.$host.$dURL);
                } elseif (isset($document->faultstring)) {
                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': fnGetDocument(): document error for fnWS call: '.'WS-HOSTS, GET_DOCUMENT');
                        session()->push('DEBUG', now().': fnGetDocument(): document error for fnRequest call: '.'getUserDocument, '.implode(',', ['userGUID' => $this->uid, 'documentID' => $letterID]));
                        session()->push('DEBUG', now().': fnGetDocument(): document faultstring: '.json_encode($document->faultstring));
                        session()->push('DEBUG', now().': fnGetDocument(): document faultcode: '.json_encode($document->faultcode));
                    }

                    //RETURN ERRORS
                    Log::alert('There was an error trying to retrieve the requested document');
                    Log::alert('fnGetDocument(): document error for fnWS call: WS-HOSTS, GET_DOCUMENT');
                    Log::alert('fnGetDocument(): document error for fnRequest call: getUserDocument, '.implode(',', ['userGUID' => $this->uid, 'documentID' => $letterID]));
                    Log::alert('fnGetDocument(): document faultstring: '.json_encode($document->faultstring));
                    Log::alert('fnGetDocument(): document faultcode: '.json_encode($document->faultcode));
                } else {
                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': fnGetDocument(): document->userDocument->url is not set');
                        session()->push('DEBUG', now().': fnGetDocument(): document: '.json_encode($document));
                    }

                    //RETURN ERRORS
                    Log::alert('There was an error trying to retrieve the requested document');
                }
            } else {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnGetDocument(): no document found for letterID: '.$letterID);
                }

                return false;
            }
        } elseif (! empty($inTransition)) {
            $baseURL = $this->fnWS('LC', '');
            $params = ['documentGUID' => $letterID, 'ownerGUID' => $this->uid];
            $docType = (! empty($documentType)) ? 'GetSubmittedAppendix' : 'GetSubmittedApplication';
            $url = $baseURL.'/rest/services/SABC_StudentLoan_APIs/Documents/'.$docType.':1.0?'.http_build_query($params);
            $rq = $this->fnRESTRequest($url, 'GET', null, null, null, null, null);

            $xml = simplexml_load_string($rq);
            $json = json_encode($xml);
            $array = json_decode($json, true);

            if ($array['statusCode'] == 200) {
                return file_get_contents($array['pdfDocument']);
            } else {
                Log::alert('There was an error trying to retrieve the requested document');
            }
        }

        return false;
    }
}
