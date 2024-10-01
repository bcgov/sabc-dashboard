<?php
/**
 * CACHE KEYS USED EVERYWHERE
 * session(env('GUID_SESSION_VAR')) . env('GUID_SESSION_VAR1')
 * session(env('GUID_SESSION_VAR')) . env('GUID_SESSION_VAR2')
 * session(env('GUID_SESSION_VAR')) . env('GUID_SESSION_VAR3')
 * session(env('GUID_SESSION_VAR')) . '_fetchNavBarData_msgs'
 * session(env('GUID_SESSION_VAR')) . env('GUID_SESSION_VAR5')
 * session(env('GUID_SESSION_VAR')) . env('GUID_SESSION_VAR6')
 */

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

// Determine DRUPAL_ROOT.
define('DRUPAL_ROOT', '');


$server_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] == 8003 ? ':8003' : '' : '';

$env = env('APP_ENV_SHORT');
$server_name = env('APP_URL_CLEAN');

if (! defined('ENV')) {
    define('ENV', $env);
}

ini_set('default_socket_timeout', 40);
$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? explode('/', $_SERVER['SERVER_PROTOCOL']) : ['https'];

if (! defined('HOST')) {
    define('HOST', ((isset($_SERVER['HTTP_X_FORWARDED_HOST'])) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $server_name));
}
if (! defined('BASE')) {
    define('BASE', 'studentaidbc.ca');
}

if (! defined('PROTOCOL')) {
    define('PROTOCOL', strtolower($protocol[0]));
}

if (! defined('SITE_PORT')) {
    define('SITE_PORT', $server_port);
}

if (! defined('SITE_URL')) {
    define('SITE_URL', strtolower($protocol[0]).'://'.HOST.SITE_PORT);
}


class Aeit extends Controller
{
    public $WSDL; //WSDL LOCATION FOR SERVICE

    public $user; //DRUPAL USER OBJECT

    public $q; //QUERY STRING ARRAY

    public $uid; //ACTUAL UsersID

    public $valid = false;

    public $env = ENV;

    public function fnFormatApplicationNumber($appNumber)
    {
        if (! empty($appNumber)) {
            $progYear = substr($appNumber, 0, 4);
            $appNo = substr($appNumber, 4, strlen($appNumber));

            return $progYear.'-'.$appNo;
        }
    }

    /*
        fnEncrypt: STRING HASHING METHOD
        params: $t = text to be hashed
    */
    public function fnEncrypt($t)
    {
        [$key, $cipher, $iv] = $this->opensslParamsExtracted();
        return openssl_encrypt($t, $cipher, $key, 0, $iv);
    }

    /*
        fnDecrypt: STRING DECRYPT METHOD
        params: $t = hashed texted
    */
    public function fnDecrypt($t)
    {
        [$key, $cipher, $iv] = $this->opensslParamsExtracted();
        return openssl_decrypt($t, $cipher, $key , 0, $iv);
    }

    public function fnUploadDocuments($params)
    {
        $this->WSDL = $this->fnWS('WS-HOSTS', 'GET_DOCUMENT');

        if (! empty($params) && isset($params['documents'])) {
            $request = [
                'userGUID' => $this->uid,
                'document' => $params['documents'],
            ];

            if (isset($params['document_purpose']) && ! empty($params['document_purpose'])) {
                $request['purposeCode'] = $params['document_purpose'];
            }

            if (isset($params['document_notes']) && ! empty($params['document_notes'])) {
                $request['groupComment'] = $params['document_notes'];
            }

            if (isset($params['attach_to_application']) && ! empty($params['attach_to_application'])) {
                $request['applicationNumber'] = $params['attach_to_application'];
            }

            $uploadDocs = $this->fnRequest('uploadDocument', $request, 'upload_users_documents'.$this->uid, 0);

            return $uploadDocs;
        }
    }

    /*
        fnGetUploadedDocuments returns all the uploaded documents for a user
        This
    */
    public function fnGetUploadedDocuments()
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnGetUploadedDocuments(): userGUID: ' . $this->uid);
        }
        $this->WSDL = $this->fnWS('WS-HOSTS', 'GET_DOCUMENT');
        $documentList = $this->fnRequest('getUploadedDocumentList', ['userGUID' => $this->uid], 'get_uploaded_documents_lists'.$this->uid, 0);
        $uploadedDoc = [];

        if (! isset($documentList->faultcode) && isset($documentList->document)) {
            if (is_array($documentList->document)) {
                foreach ($documentList->document as $documents) {
                    array_push($uploadedDoc, ['fileName' => $documents->fileName, 'dateUploaded' => $documents->dateUploaded, 'documentID' => $documents->documentID]);
                }
            } else {
                array_push($uploadedDoc, ['fileName' => $documentList->document->fileName, 'dateUploaded' => $documentList->document->dateUploaded, 'documentID' => $documentList->document->documentID]);
            }
        } else {
            if (isset($documentList->detail->DocumentFault)) {
                return false;
            }
        }

        return $uploadedDoc;
    }

    /*
        fnGetDocuments returns all documents for a user
        params: $docType = 'U' (unread), 'A' (Application Letters), 'G' (General Documents)
                $loadDocuments is there so that it doesn't get called twice due to the fact that
                fnGetDocuments could get called twice depending if you are on notifications page.
    */
    public function fnGetDocuments($docType, $loadDocuments = false)
    {
        $docTypes = ['U', 'A', 'G'];

        //LIST OF ID'S THAT ARE UNREAD - WE USE THIS SO WE AREN'T LOADING DUPLICATES
        $unreadDocs = [];

        //MAKE SURE WE HAVE A VALID LETTERID

        $notifications = [];
        $notifications['totalUnread'] = 0;
        $notifications['totalApplicationDocuments'] = 0;
        $notifications['totalGeneralDocuments'] = 0;
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnGetDocuments(): docType: ' . json_encode($docType));
            session()->push('DEBUG', now().': fnGetDocuments(): this->uid: ' . $this->uid);

            Session::save();
        }

        if ($this->uid) {
            //MAKE SURE DOCTYPE IS VALID
            if (is_array($docType)) {
                //REMOVE INVALID DOCTYPE(S)
                foreach ($docType as $k => $v) {
                    if (! in_array($v, $docTypes)) {
                        unset($docType[$k]);
                    }
                }

                //COUNT TO MAKE SURE WE STILL HAVE VALUES
                if (count($docType) > 0) {
                    $valid = true;
                } else {
                    $valid = false;
                }
            } else {
                //PASSED AS A STRING INSTEAD OF ARRAY SO JUST MAKE SURE THAT VALUE IS IN ARRAY
                $valid = in_array($docType, $docTypes);
            }

            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnGetDocuments(): valid: ' . $valid);
                Session::save();
            }

            //WE HAVE VALID DOCTYPE(S)
            if ($valid == true) {
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnGetDocuments(): valid is true');
                    Session::save();
                }

                //CALL THE APPROPRIATE WEB SERVICE
                $this->WSDL = $this->fnWS('WS-HOSTS', 'GET_DOCUMENT');

                //ARRAY OF DOCUMENT TYPE TO PASS TO OUR WEB SERVICE
                $return_array = new \stdClass();
                $return_array->documentTypes = [];

                $docCID = null;

                //IF PASS DOCTYPE WAS AN ARRAY LOOP THROUGH TO BUILD STRUCTURE FOR XML
                if (is_array($docType)) {
                    $i = 0;
                    foreach ($docType as $type) {
                        //Create our Type element
                        $documentTypeParams = new \stdClass();
                        $documentTypeParams->type = $type;

                        //Append all our DocumentType to an Array so we can use it in the final construction of our element
                        $return_array->documentTypes[] = $documentTypeParams;

                        $docCID .= $type;
                        $i++;
                    }

                } else {
                    //Create our Type element
                    $documentTypeParams = new \stdClass();
                    $documentTypeParams->type = $docType;

                    //Append the DocumentType to an Array so we can use it in the final construction of our element
                    $return_array->documentTypes[] = $documentTypeParams;
                    $docCID .= $docType;

                }

                //REQUEST FOR OUR WEB SERVICE TO GET DOCUMENTS
                $request = [
                    'request' => [
                        'guid' => $this->uid,
                        'DocumentTypes' => $return_array,
                    ],
                ];
                if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                    session()->push('DEBUG', now().': fnGetDocuments(): request: ' . json_encode($request));
                    Session::save();

                    Log::debug(': fnGetDocuments(): request: '.json_encode($request));
                }
                //GETDOCUMENTS REQUEST
                $documents = $this->fnRequest('getUserDocumentList', $request, 'get_documents'.$this->uid.''.$docCID, 0);

                //MAKE SURE WE HAVE A VALID RETURN
                if (is_object($documents) && ! empty($documents)) {
                    $tmp = [];

                    //GET ID'S OF ALL UNREAD IF WE HAVE UNREAD DOCS
                    if (isset($documents->Documents->Unread)) {
                        foreach ($documents->Documents->Unread as $unread) {
                            if (is_array($unread)) {
                                foreach ($unread as $doc) {
                                    array_push($unreadDocs, $doc->DocumentID);
                                }
                            } else {
                                array_push($unreadDocs, $unread->DocumentID);
                            }
                        }
                    }

                    //MAKE SURE WE HAVE DOCUMENTS
                    if (isset($documents->Documents)) {
                        //LOOP MULTI DIMENSIONAL ARRAY OF DOCUMENTS
                        foreach ($documents->Documents as $k => $v) {
                            foreach ($v as $id => $document) {
                                if (is_array($document)) {
                                    foreach ($document as $doc) {
                                        //COUNT TOTAL DOCUMENTS PER RETURNED DOCUMENT TYPE REQUESTED
                                        $notifications['total'.$k] = $notifications['total'.$k] + 1;

                                        if ($k == 'Unread') {
                                            $status = 'unread';
                                        } else {
                                            $status = 'read';
                                        }

                                        /*
                                            TO PREVENT DUPLICATE DOCUMENTS BEING DISPLAYED DUE TO UNREAD STATE
                                              BEING RETURNED AS A SEPARATE NODE
                                        */
                                        if ($k != 'Unread' && ! in_array($doc->DocumentID, $unreadDocs)) {
                                            //STORE TO TEMP ARRAY WITH KEY BEING UNIX TS OF DOCUMENT CREATED
                                            $tmp[strtotime($doc->Created)][] =
                                            ['DocumentID' => $doc->DocumentID,
                                                'Title' => $doc->Title,
                                                'Created' => date('M d, Y', strtotime($doc->Created)),
                                                'Marked' => $status];
                                        } elseif ($k == 'Unread') {
                                            //STORE TO TEMP ARRAY WITH KEY BEING UNIX TS OF DOCUMENT CREATED
                                            $tmp[strtotime($doc->Created)][] =
                                            ['DocumentID' => $doc->DocumentID,
                                                'Title' => $doc->Title,
                                                'Created' => date('M d, Y', strtotime($doc->Created)),
                                                'Marked' => $status];
                                        }
                                    }
                                } else {
                                    //COUNT TOTAL DOCUMENTS PER RETURNED DOCUMENT TYPE REQUESTED
                                    $notifications['total'.$k] = $notifications['total'.$k] + 1;

                                    if ($k == 'Unread') {
                                        $status = 'unread';
                                    } else {
                                        $status = 'read';
                                    }

                                    /*
                                        TO PREVENT DUPLICATE DOCUMENTS BEING DISPLAYED DUE TO UNREAD STATE
                                        BEING RETURNED AS A SEPARATE NODE
                                    */
                                    if ($k != 'Unread' && ! in_array($document->DocumentID, $unreadDocs)) {
                                        //STORE TO TEMP ARRAY WITH KEY BEING UNIX TS OF DOCUMENT CREATED
                                        $tmp[strtotime($document->Created)][] =
                                        ['DocumentID' => $document->DocumentID,
                                            'Title' => $document->Title,
                                            'Created' => date('M d, Y', strtotime($document->Created)),
                                            'Marked' => $status];
                                    } elseif ($k == 'Unread') {
                                        //STORE TO TEMP ARRAY WITH KEY BEING UNIX TS OF DOCUMENT CREATED
                                        $tmp[strtotime($document->Created)][] =
                                        ['DocumentID' => $document->DocumentID,
                                            'Title' => $document->Title,
                                            'Created' => date('M d, Y', strtotime($document->Created)),
                                            'Marked' => $status];
                                    }
                                }
                            }
                        }
                    }

                    //SORT DESC SO NEWEST NOTIFICATIONS ON TOP
                    ksort($tmp);

                    $notifications['documents'] = $tmp;

                    return $notifications;
                } else {
                    return $notifications;
                }
            } else {
                $this->fnError('Invalid Document type passed', 'Document type passed to fnGetDocuments is invalid', $docType);

                return false;
            }
        } else {
            return $notifications;
        }
    }

    public function fnClearUserCache()
    {
        //CLEAR PROFILE
        $cid = 'get_user_profile'.$this->uid;
        $this->fnUpdateCache($cid, 'cache_block', true);

        //CLEARS APPLICATIONS CACHE SO WE GET A TRUE UPDATE TO DATE VIEW OF OUR APPS AT LOGIN
        $this->fnClearApplicationsCache();
    }

    /*
        fnGetDocument:  Call document WS or use cache response to load a particular document for a user
    */
    public function fnGetDocument($letterID, $inTransition = false, $documentType = null)
    {
        $host = HOST;
        if (! empty(session()->get(env('ADMIN_SESSION_VAR')))) {
            $host = session()->get(env('ADMIN_SESSION_VAR'));
        } else {
            $host = env('APP_URL_CLEAN');
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

                    header('Content-Type: application/pdf');
                    $protocal = 'http://';
                    if ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'))) {
                        $protocal = 'https://';
                    }

                    return redirect()->to($protocal.$host.$dURL);
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

                    //LOG ERRORS
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

                header('Content-Type: application/pdf');
                return redirect()->to($array['pdfDocument']);
            } else {
                Log::alert('There was an error trying to retrieve the requested document');

                return redirect()->to('/');
            }
        } else {
            return false;
        }
    }

    /*
        CLEAR APPLICATION CACHE
    */
    public function fnClearApplicationsCache()
    {
        $this->fnUpdateCache('get_application_list'.$this->uid, 'cache_block', true);
        $this->fnUpdateCache('app_details'.$this->uid, 'cache_block', true);
        $this->fnUpdateCache('get_appendix_list'.$this->uid, 'cache_block', true);
        $this->fnUpdateCache('get_appendix_details'.$this->uid, 'cache_block', true);
    }

    public function format_phone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) == 7) {
            return preg_replace('/([0-9]{3})([0-9]{4})/', '$1-$2', $phone);
        } elseif (strlen($phone) == 10) {
            return preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1) $2-$3', $phone);
        } else {
            return $phone;
        }
    }

    public function fnSanitize($v)
    {
        $s = ['drop', 'delete', 'select', 'union', 'from', '--', '*', '='];
        $r = ['', '', '', '', '', '', '- ', '', ''];

        return addslashes(htmlentities(str_replace($s, $r, $v)));
    }

    public function fnGetEstAmountBorrowed()
    {
        //CALL THE APPROPRIATE WEB SERVICE
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_APPLICATION_DEFAULT');

        $est = $this->fnRequest('getTotalLoansIssuedEstimate', ['userGUID' => $this->uid], 'amt_borrowed'.$this->uid, 14400);
        if (! isset($est->faultcode) && isset($est->totalLoansIssueEstimate)) {
            return ($est->totalLoansIssueEstimate > 0) ? number_format($est->totalLoansIssueEstimate) : 0;
        } else {
            if (isset($est->detail->ApplicationFault)) { //valid errors so we don't care about outputting the errors.
                return false;
            } else {
                $this->fnError('SYSTEM ERROR :: USER_APPLICATION_DEFAULT -> getTotalLoansIssuedEstimate', $est->getMessage(), $est,

                    $triggerDefault = true);

                return false;
            }
        }
    }

    public function fnGetCurlRequest($url, $get_vars = false, $cid = null, $cacheExpire = 7200, $cookie_vals = '', $ret_cookies = false, $trace = false, $header = [])
    {
        $r = [];

        if (! empty($cookie_vals) && is_array($cookie_vals)) {
            $cookie = implode('&', $cookie_vals);
        } else {
            if (! empty($cookie_vals)) {
                $cookie = $cookie_vals;
            } else {
                $cookie = null;
            }
        }

        if ($trace == true) {
            if (headers_sent() == false) {
                header('Content-type: text/plain');
            }
        }

        //if($this->fnGetCache($cid) == false || empty($cid)){

        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 800); //timeout in seconds
        curl_setopt($ch, CURLOPT_HEADER, 'Content-Type:application/xml');

        if (! empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        if ($trace == true) {
            curl_setopt($ch, CURLOPT_STDERR, fopen('php://output', 'w'));
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        } else {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if ($trace == true) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        } else {
            curl_setopt($ch, CURLOPT_HEADER, 0);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (! empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
        }

        //try & catch for curl request to get url
        try {
            // grab URL and pass it to the browser
            $ret = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                //session()->push('DEBUG', now() . ": fnGetCurlRequest() ret: " . json_encode($ret));
                session()->push('DEBUG', now().': fnGetCurlRequest() url: '.$url);
                session()->push('DEBUG', now().': fnGetCurlRequest() httpcode: '.json_encode($httpcode));
            }
            if ($httpcode == 500) {
                Log::error(': fnGetCurlRequest() url: '.$url);
                Log::error(': fnGetCurlRequest() We have an issue with E-Services');

                //try again
                sleep(3);
                $ret = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpcode == 500) {
                    Log::error(': fnGetCurlRequest() 2nd trial failed');

                    return false;
                } else {
                    Log::error(': fnGetCurlRequest() 2nd trial SUCCESSFUL');
                }
            }

            if ($httpcode == 200) {
                if ($get_vars == true) {
                    $info = curl_getinfo($ch);
                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': fnGetCurlRequest() info: '.json_encode($info));
                    }

                    if (isset($info['url'])) {
                        $url = parse_url($info['url']);

                        parse_str($url['query'], $r);
                        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                            session()->push('DEBUG', now().': fnGetCurlRequest() url: '.json_encode($url));
                        }

                        $r['query_string'] = $r;
                    }
                }

                if ($ret_cookies == true) {
                    // get cookie
                    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $ret, $m);

                    $r['cookies'] = $m;
                }

                $r['response'] = $ret;

                return $r;
            }

            return false;
        } catch (Exception $e) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnGetCurlRequest() Exception e: '.json_encode($e));
            }

            return false;
        }
//			} else{
//				$r = $this->fnGetCache($cid);
//			}
            return $r;
    }

    /*
    *	function fnRESTRequest
    * @params:
    *		$url: url of service
    *		$rt: Request Type. Defaults to GET
    * 	$params: parameters passed along with request
    */
    public function fnRESTRequest($url, $rt = 'GET', $params = null, $user = null, $pass = null, $return = null, $debug = false)
    {
        //get url parameters and build url
        if ($rt == 'GET') {
            if (! empty($params) && is_array($params)) {
                $url = $url.'?'.http_build_query($params);
            }
        }

        $data = '';
        if ($user) {
            //set username and password if set
            $data = [
                'user' => $user,
                'pass' => $pass,
            ];
            $data = http_build_query($data, '', '&');
        }

        $headers = [];
        //setup headers
        $options = [
            //'method' => 'GET',
            'data' => $data,
        ];

        $client = new \GuzzleHttp\Client(['timeout' => '60.0']);

        try {
            //Change request method	if set
            if (strtoupper($rt) == 'POST') {
                //set value to post
                //				$options['method'] = 'POST';
                $response = $client->post($url, $options);
            } elseif (strtoupper($rt) == 'GET') {
                //set value to put
                //				$options['method'] = 'PUT';
                $response = $client->get($url, $options);
            } elseif (strtoupper($rt) == 'PUT') {
                //set value to put
                //				$options['method'] = 'PUT';
                $response = $client->put($url, $options);
            } elseif (strtoupper($rt) == 'DELETE') {
                //set value to delete
                //				$options['method'] = 'DELETE';
                $response = $client->delete($url, $options);
            }
            //				$options['timeout'] = '60.0';
            //				$response = drupal_http_request($url, $options);

            //$response = $client->get($url, $options);
//                echo "<pre>";
//                echo $response->getStatusCode(); // 200
//                echo $response->getBody(); // { "type": "User", ....
//                echo $response->getReasonPhrase(); // { "type": "User", ....
//                var_dump($response);

            //				if(isset($response->error)){
            if ($response->getStatusCode() !== 200) {
                //					$this->fnError('LC_SYSTEM_RESPONSE_ERROR!', $response->getBody(), $response);
                //					$this->fnError('LC_SYSTEM_ERROR****', $url, $url);

                return false;
            } else {
                if ($response->getStatusCode() == 200) {
                    if ($return == 'XML') {
                        $response = new \SimpleXMLElement($response->getBody());
                        $json_string = json_encode($response);

                        return json_decode($json_string);

                    //$xml = simplexml_load_string($response->getBody());
//                            $json = json_encode($xml);
//                            return json_decode($json,TRUE);
                    } else {
                        return $response->getBody();
                    }
                }
            }
        } catch(\Exception $e) {
            $this->fnError('SYSTEM_ERROR :: LC Error', 'An unexpected error occurred on url'.$url.'');

            return false;
        }
    }

    /*
    *	function fnBuildLivecycleUrl
    * 	@params:
    *	$url: url of service
    * 	$params: parameters passed along with request
    */
    public function fnBuildLiveCycleUrl($url, $params = null)
    {
        if (! empty($params) && is_array($params)) {
            $type = $params['type'];

            //build first part of livecycle url
            $livecycleURL = $params['livecycleDetails'];
            $url = $url.$type.'?'.http_build_query($livecycleURL);

            //build second part of livecycle url with the user parameters
            $userDetails = $params['userDetails'];
            //encode the query and concatenate it with the url
            $url = $url.'?'.urlencode(http_build_query($userDetails));
        }

        return $url;
    }

    public function fnVerifyGUID()
    {
        //check to see if uid exists and isset.
        if (isset($this->uid) && ! empty($this->uid)) {
            //verify length and alpha numeric
            preg_match('/^[A-Za-z0-9]{32}$/', $this->uid, $match);

            //if valid inject uid information into cache
            if (! empty($match) && count($match) == 1) {
                return $this->uid;
            } else {
                $uid = $this->fnDecrypt($this->uid);

                //try to decrypt uid incase it was encrypted and double check
                preg_match('/^[A-Za-z0-9]{32}$/', $uid, $match2);

                //if valid inject uid information into cache
                if (! empty($match2) && count($match2) == 1) {
                    return $uid;
                } else {
                    //watchdog('GUID Validation Error on Get Cache', 'GUID failed length and alpha numeric check. loaded GUID: '.$uid.'', NULL, WATCHDOG_ERROR, $link = NULL);
                    //drupal_goto('user/logout');
                    redirect('/logout');
                }
            }
        }
    }

    public function fnUpdateCache($cid, $bin, $wildcard = false)
    {
        $verifyGUID = $this->fnVerifyGUID();
        if (! empty($verifyGUID)) {
            $cid = $cid.'--'.$verifyGUID;
        }

        //cache_clear_all($cid, $bin, $wildcard);
    }


    public function fnGetCache($cid, $reset = false)
    {
        return false;
    }

    public function fnRequest($action, $params = [], $cid = null, $cacheExpire = 7200, $debug = false)
    {
        ini_set('soap.wsdl_cache_enabled', 0);
        $config = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), null);
        if (is_null($config)) {
            $config = parse_ini_file(config_path().env('APP_CONFIG_FILE'), true);
            Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), $config, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
        }

        //flag to see if we have cache
        $cache = false;

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnRequest(): this->WSDL: '.$this->WSDL);
            session()->push('DEBUG', now().': fnRequest(): substr(this->WSDL, 0, 4): '.substr($this->WSDL, 0, 4));
            session()->push('DEBUG', now().': fnRequest(): params: '.json_encode($params));
            session()->push('DEBUG', now().': fnRequest(): params count: '.count($params));
            session()->push('DEBUG', now().': fnRequest(): action: '.$action);

            Session::save();

            Log::debug(': fnRequest(): this->WSDL: '.$this->WSDL);
            Log::debug(': fnRequest(): substr(this->WSDL, 0, 4): '.substr($this->WSDL, 0, 4));
            Log::debug(': fnRequest(): params: '.json_encode($params));
            Log::debug(': fnRequest(): params count: '.count($params));
            Log::debug(': fnRequest(): action: '.$action);
        }

        //if(!empty($this->uid) || arg(1) == 'login' || arg(1) == 'login2' || $this->valid == true){
        //IF DEBUG IS TRUE OUTPUT ANY ERRORS
        if ($debug == true) {
            ini_set('display_errors', 1);
            $options = ['trace' => 1, 'exception' => true];
        } else {
            $options = ['trace' => 1, 'exceptions' => false];
        }

        try {
            if (substr($this->WSDL, 0, 4) != 'http') {
                $service_name = $config['REV-SERVICES'][$this->WSDL];
                $this->WSDL = $this->fnWS($service_name, $config['SERVICES'][$this->WSDL]);

            }

            if (! empty($options)) {
                $client = new \SoapClient($this->WSDL, $options);
            } else {
                $client = new \SoapClient($this->WSDL);
            }
//                if(is_array($params)){
//                    $params = json_encode($params);
//                }
//                echo 'wsdl: ';
//                var_dump($this->WSDL);
//                echo '<hr/>';
//                echo 'options: ';
//                var_dump($options);
//                echo '<hr/>';
//                echo 'client: ';
//                var_dump($client);
//                echo '<hr/>';
//                echo 'action: ';
//                var_dump($action);
//                echo '<hr/>';
//                echo 'params: ';
//                var_dump($params);
            ////                echo '<hr/>';
            ////                echo 'response: ';
            ////                var_dump($client
            ////                    ->{$action}
            ////                    ($params));
//                echo '<hr/>';
//                return null;

            if (count($params) > 0) {
                $response = $client->{$action}($params);

                if ($action == 'getUserProfile' || $action == 'updateUserProfile') {
                    //$m = "SOAP Fault: (faultcode: {$response->faultcode}, faultstring: {$response->faultstring}) \n";

                    if (env('LOG_ESERVICES') == true) {
                        $m = 'REQUEST: '.print_r($params, true).'';
                        Log::error('000 E-Services on action: '.$action.': '.$m);
                        Log::error('This WSDL: '.$this->WSDL);
                        Log::error('001 E-Services response: '.json_encode($response));
                        Log::error(' ');
                    }
                }
            } else {
                $response = $client->{$action}();
            }

            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                Log::error('111 wsError client '.json_encode($client));
                Log::error('222 wsError response '.json_encode($response));

            }
        }
        catch(\Exception $e) {
            Log::error('333 Exception error:'.print_r($e, true));
            Log::error('444 E-Services Error on action: '.$action.'');
        }

        if (isset($response) && ! empty($response)) {
            //CHECK TO MAKE SURE OUR REQUEST DOESN'T HAVE ANY ERRORS
            if (is_soap_fault($response)) {
                $headers = 'From: aved.webservices@gov.bc.ca'."\r\n";
                $message = "SOAP Fault: (faultcode: {$response->faultcode}, faultstring: {$response->faultstring}) \n";
                $message .= 'REQUEST: '.print_r($params, true).'';

                if (env('LOG_ESERVICES') == true) {
                    Log::error('555 E-Services Error on action: '.$action.': '.$message);
                    Log::error('SOAP Fault: (faultcode: {'.$response->faultcode.'}, faultstring: {'.$response->faultstring.'}');
                    Log::error('This WSDL: '.$this->WSDL);
                    Log::error(' ');
                }

                return $response;
            } else {
                if (is_object($response) && ! empty($response)) {
                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        Log::info('666 E-Service action: '.$action.' has been resolved');
                    }

                    return $response;
                } else {
                    Session::flash('We are sorry there was a problem processing your request. Please refresh and try again.', 'wsError error');
                    Log::error('777 E-Services Error on action: '.$action.'. We did not receive a valid response from web service. Object expected.');
                    exit;
                }
            }

        } else {
            Log::error('999 We are currently experiencing technical difficulties and are working on restoring service.  Please check back shortly.');
            Session::flash('We are currently experiencing technical difficulties and are working on restoring service.  Please check back shortly.', 'wsError error');
        }
    }

    public function array_to_objecttree($array)
    {
        if (is_numeric(key($array))) { // Because Filters->Filter should be an array
            foreach ($array as $key => $value) {
                $array[$key] = $this->array_to_objecttree($value);
            }

            return $array;
        }

        $Object = new \stdClass;

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $Object->$key = $this->array_to_objecttree($value);
            } else {
                $Object->$key = $value;
            }
        }

        return $Object;
    }

    public function fnError($title, $val, $additionalOutput = null, $triggerDefault = true)
    {
        //			$output = print_r($_SERVER, true);
        $additionalOutput = (is_array($additionalOutput)) ? print_r($additionalOutput, true) : 'No additional output';

        $message = 'ERROR Message: '.$val."\n\n";
        $message .= 'USER GUID: '.$this->uid."\n\n";
        //			$message .= "REQUEST: {$output} \n";
        $message .= "VARIABLES: {$additionalOutput} \n";

        if ($triggerDefault == true) {
            Session::flash('We are currently experiencing technical difficulties and are working on restoring service.  Please check back shortly', 'wsError error', false);
        }
        $headers = 'From: aved.webservices@gov.bc.ca'."\r\n";
        Log::error('Dashboard Drupal Error: '.$title.' ('.$message.' - uid: '.$this->uid.')');
        //mail('AVED.systemuser@gov.bc.ca', 'Dashboard Drupal Error: '.$title.' Userguid: '.$this->uid.'', $message, $headers, '-f aved.webservices@gov.bc.ca');
        return false;
    }

    public function fnSanitizeData($text, $type = 'text')
    {
        if ($type == 'email') {
            //return str_replace(['(', ')', '[', ']', ':', '<', '>', "'", '"'], '', $text);
            $text = filter_var($text, FILTER_SANITIZE_EMAIL);

            return trim($text);
        }

        return str_replace(['"'], '', $text);
    }

    /**
     * Returns the requested URL path of the page being viewed.
     *
     * Examples:
     * - http://example.com/node/306 returns "node/306".
     * - http://example.com/drupalfolder/node/306 returns "node/306" while
     *   base_path() returns "/drupalfolder/".
     * - http://example.com/path/alias (which is a path alias for node/306) returns
     *   "path/alias" as opposed to the internal path.
     * - http://example.com/index.php returns an empty string (meaning: front page).
     * - http://example.com/index.php?page=1 returns an empty string.
     *
     * @return
     *   The requested Drupal URL path.
     *
     * @see current_path()
     */
    public function request_path()
    {
        static $path;

        if (isset($path)) {
            return $path;
        }

        if (isset($_GET['q']) && is_string($_GET['q'])) {
            // This is a request with a ?q=foo/bar query string. $_GET['q'] is
            // overwritten in drupal_path_initialize(), but request_path() is called
            // very early in the bootstrap process, so the original value is saved in
            // $path and returned in later calls.
            $path = $_GET['q'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            // This request is either a clean URL, or 'index.php', or nonsense.
            // Extract the path from REQUEST_URI.
            $request_path = strtok($_SERVER['REQUEST_URI'], '?');
            $base_path_len = strlen(rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/'));
            // Unescape and strip $base_path prefix, leaving q without a leading slash.
            $path = substr(urldecode($request_path), $base_path_len + 1);
            // If the path equals the script filename, either because 'index.php' was
            // explicitly provided in the URL, or because the server added it to
            // $_SERVER['REQUEST_URI'] even when it wasn't provided in the URL (some
            // versions of Microsoft IIS do this), the front page should be served.
            if ($path == basename($_SERVER['PHP_SELF'])) {
                $path = '';
            }
        } else {
            // This is the front page.
            $path = '';
        }

        // Under certain conditions Apache's RewriteRule directive prepends the value
        // assigned to $_GET['q'] with a slash. Moreover we can always have a trailing
        // slash in place, hence we need to normalize $_GET['q'].
        $path = trim($path, '/');

        return $path;
    }

    /**
     * Returns a component of the current Drupal path.
     *
     * When viewing a page at the path "admin/structure/types", for example, arg(0)
     * returns "admin", arg(1) returns "structure", and arg(2) returns "types".
     *
     * Avoid use of this function where possible, as resulting code is hard to
     * read. In menu callback functions, attempt to use named arguments. See the
     * explanation in menu.inc for how to construct callbacks that take arguments.
     * When attempting to use this function to load an element from the current
     * path, e.g. loading the node on a node page, use menu_get_object() instead.
     *
     * @param $index
     *   The index of the component, where each component is separated by a '/'
     *   (forward-slash), and where the first component has an index of 0 (zero).
     * @param $path
     *   A path to break into components. Defaults to the path of the current page.
     * @return
     *   The component specified by $index, or NULL if the specified component was
     *   not found. If called without arguments, it returns an array containing all
     *   the components of the current path.
     */
    public function arg($index = null, $path = null)
    {
        // Even though $arguments doesn't need to be resettable for any functional
        // reasons (the result of explode() does not depend on any run-time
        // information), it should be resettable anyway in case a module needs to
        // free up the memory used by it.
        // Use the advanced drupal_static() pattern, since this is called very often.

        $url = $this->request_path();
        $arguments = explode('/', $url);
        if (isset($index)) {
            return $arguments[$index];
        }

        return $arguments;
    }

    public function fnWS($service, $action, $customBase = null)
    {
        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnWS(): service: '.$service);
            session()->push('DEBUG', now().': fnWS(): action: '.$action);
            Session::save();
        }

        if (isset($_SESSION['admin_login'])) {
            $customBase = $_SESSION[env('ADMIN_SESSION_VAR')];
        }

        if (! empty(session()->get(env('ADMIN_SESSION_VAR')))) {
            $customBase = session()->get(env('ADMIN_SESSION_VAR'));
        }

        if (is_file(config_path().env('APP_CONFIG_FILE'))) {
            $host = env('APP_FNWS_HOST');

            $config = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), null);
            if (is_null($config)) {
                $config = parse_ini_file(config_path().env('APP_CONFIG_FILE'), true);
                Cache::put(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR10'), $config, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
            }

            if (! is_null($customBase)) {
                $host = $config['REV-ENV'][$customBase];
            }

            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnWS(): host: '.$host);
                Session::save();
            }

            if (isset($action) && ! empty($action)) {
                return (isset($config[$service])) ? $config[$service][$host].'/'.$config['SERVICES'][$action] : $service.'/'.$config['SERVICES'][$action];
            } else {
                return $config[$service][$host];
            }
        } else {
            return false;
        }
    }

    public function fetchGetWebDeclaration($role, $applicationNumber, $GUID)
    {
        $getWebDeclaration = $this->fnRequest('getWebDeclaration', ['applicationNumber' => $applicationNumber, 'role' => $role, 'userGUID' => $GUID], null, 100);

        /*
        04 CSAP E-Consent - Applicant
        05 CSXS E-Consent - Spouse
        06 ESAP Valid "E" Signature On File - Applicant
        07 ESXP Valid "E" Signature On File - Sponsor
        08 ESXS Valid "E" Signature On File - Spouse
        09 SSAP Submit Stamp For Application Proper
        10 SSXP Submit Stamp For Parent/Sponsor Appendix
        11 SSXS Submit Stamp For Spouse Appendix
        */
        $show_econsent = false;
        $processing_consent = true;
        //Ignore if the response has error string
        if (isset($getWebDeclaration->faultstring) || is_null($getWebDeclaration)) {
            $processing_consent = false;
        } else {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fetchGetWebDeclaration(): getWebDeclaration->WebDeclaration: '.json_encode($getWebDeclaration->WebDeclaration));
                Session::save();
            }

            if (isset($getWebDeclaration->WebDeclaration->timeStampTypeCodes->code)) {
                foreach ((array) $getWebDeclaration->WebDeclaration->timeStampTypeCodes->code as $code) {
                    if ($code == 'CSXS') {
                        $show_econsent = true;
                        $processing_consent = false;
                    }
                    if ($code == 'SSXS' || $code == 'ESXS') {
                        $processing_consent = false;
                    }
                }
            }
        }

        return [$processing_consent, $show_econsent];
    }

    /*
    *		USED TO VALIDATE USER/ RETRIEVE USERS GUID FROM THE IDENTITY MANAGEMENT STORE
    *		@params: $u (username) $p (password)
    *		@return: $user which is an array that contains users GUID and full name
    */
    public function fnLogin($request)
    {
        $u = filter_var($request->user_id, FILTER_SANITIZE_STRING);
        $p = filter_var($request->password, FILTER_SANITIZE_STRING);

        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
            session()->push('DEBUG', now().': fnLogin(): user_id: '.$request->user_id);
            session()->push('DEBUG', now().': fnLogin(): u: '.$u);
        }

        //GET APPROPRIATE URL FOR WEB SERVICE METHOD
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_AUTH');
        $login = $this->fnRequest('logon', ['userID' => $u, 'userPassword' => $p]);

        //CHECK TO MAKE SURE WE GET A RESPONSE
        if (! empty($login) && is_object($login)) {
            if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                session()->push('DEBUG', now().': fnLogin(): login: '.json_encode($login));
            }

            //CHECK TO MAKE SURE WE DON'T HAVE ERRORS
            if (! isset($login->faultcode)) {
                // CHANGE TO "Y" LATER (API KINDA BROKEN, may 2014)
                if ($login->logonReturn->updateProfile == 'Y') {
                    return false;
                } else {
                    $this->uid = $login->logonReturn->userGUID;
                    $encrypted_guid = $this->fnEncrypt($login->logonReturn->userGUID);
                    $this->user = User::where('name', $encrypted_guid)->with('roles')->first();

                    //if this->user is null then the postgres db is missing that record but the record does exist on SFAS
                    if (is_null($this->user)) {
                        if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                            session()->push('DEBUG', now().': fnLogin(): this user does not exist in PG.');
                            session()->push('DEBUG', now().': fnLogin(): encrypted_guid: '.$encrypted_guid);
                            session()->push('DEBUG', now().': fnLogin(): returned GUID: '.$login->logonReturn->userGUID);
                        }

                        //$next_id = User::select('id')->orderBy('id', 'desc')->first();
                        $next_uid = User::select('uid')->orderBy('uid', 'desc')->first();
                        $old_uid = User::select('uid')->where('name', 'like', $encrypted_guid)->first();

                        $user = new User();
                        //$user->id = intval($next_id->id) + 1;
                        $user->uid = $next_uid->uid + 1;

                        //if the user has an account recreate it with the same UID
                        if (! is_null($old_uid)) {
                            $user->uid = $old_uid->uid;
                        }

                        $user->name = $encrypted_guid;
                        $user->password = $user->user_hash_password($p, DRUPAL_HASH_COUNT);
                        $user->created = strtotime('now');
                        $user->status = 1;
                        $user->save();

                        $user_role = Role::where('name', 'student')->first();
                        $user->roles()->attach($user_role);

                        $this->user = User::where('name', $encrypted_guid)->with('roles')->first();
                    }

                    if (env('APP_DEBUG') == true && env('APP_ENV') != 'production') {
                        session()->push('DEBUG', now().': fnLogin(): this->uid: '.$this->uid);
                        session()->push('DEBUG', now().': fnLogin(): encrypted_guid: '.$encrypted_guid);
                        session()->push('DEBUG', now().': fnLogin(): login is null: '.is_null($this->user));
                    }

                    if (! is_null($this->user)) {
                        Auth::login($this->user);
                        session(['guid' => $login->logonReturn->userGUID]);

                        //GET USER ACCOUNT DETAILS - LOOKING FOR FIRSTNAME AND FAMILY NAME TO DISPLAY ON THE DASHBOARD
                        $up = $this->fnGetUserProfile($request);
                        if (is_array($up) && $up['status'] === false) {
                            //failed to fetch user profile data (most likely this is an admin user
                            return ['status' => false, 'username' => $up['msg']];
                        }

                        $name = isset($up->userProfile->firstName) ? $up->userProfile->firstName.' '.$up->userProfile->familyName : $up->userProfile->familyName;

                        return ['uid' => $this->uid, 'name' => $name, 'status' => true];
                    } else {
                        $errors = [];
                        $errors['status'] = false;
                        $errors['username'] = 'LV1: User does not exist';

                        return $errors;
                    }
                }
            } else {
                $errors = [];
                $errors['status'] = false;
                //CHECK TO SEE IF THIS IS A LOGIN ERROR
                if (isset($login->detail->CredentialFault)) {
                    $errors['username'] = $login->getMessage();
                }
                //NOT A LOGIN IN ERROR SO TRIGGER SYSTEM ERROR
                else {
                    Log::error('Request IP: '.$request->ip());
                    $this->fnError('SYSTEM ERROR', $login->getMessage(), $login, $triggerDefault = true);

                    //if we get ns0:Client error ie:
                    //(faultcode: {ns0:Client}, faultstring: {org.xml.sax.SAXParseException; cvc-maxLength-valid: Value '&#34;$aids100293doki$&#34;' with length = '26' is not facet-valid with respect to maxLength '20' for type '#AnonType_userPasswordlogon'.}
                    $errors['username'] = 'Log in failed. Please make sure you enter the correct User ID and Password.';
                }

                return $errors;
            }
        } else {
            //ERRORS IN OUR WEB SERVICE CALL NOT A VALID RESPONSE
            $errors = [];
            $errors['status'] = false;
            $errors['username'] = 'Login request failed!';

            return $errors;
        }
    }

    /*
    *		USED TO GET USER PROFILE
    *		@params: $request
    *		@return (object) or void if system errors
    */
    public function fnGetUserProfile($request)
    {
        $this->user->roles = $this->user->roles->pluck('name')->toArray();
        //CALL GET USER PROFILE SOAP WEB SERVICE
        if (in_array('bcsc_student', $this->user->roles) || in_array('bcsc_parent', $this->user->roles) || in_array('bcsc_spouse', $this->user->roles)) {
            $this->WSDL = $this->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');
        } else {
            $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_ACCOUNT');
        }

        //verify the user
        $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_VERIFY');
        $verifyAccount = $this->fnRequest('getUserProfile', ['userGUID' => $this->uid], 'get_user_profile'.$this->uid, 14400);

        //OVERRIDE if we know already that the account is a BCSC account
        if (isset($verifyAccount->userProfile->assuranceLevel)) {
            $this->WSDL = $this->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');
        } else {
            $this->WSDL = $this->fnWS('WS-HOSTS', 'USER_ACCOUNT');
        }

        $usrProfile = $this->fnRequest('getUserProfile', ['userGUID' => $this->uid], 'get_user_profile'.$this->uid, 14400);

        //MAKE SURE IT IS NOT AN ERROR
        if (! is_null($usrProfile) && ! isset($usrProfile->faultcode)) {
            $usrProfile->status = true;
            $usrProfile->userProfile->SIN = $this->fnEncrypt($usrProfile->userProfile->SIN);
            $usrProfile->userProfile->userGUID = $this->fnEncrypt($usrProfile->userProfile->userGUID);
            $usrProfile->userProfile->userConsent = (! isset($usrProfile->userProfile->userConsent) || $usrProfile->userProfile->userConsent == 'N') ? false : true;

            return $usrProfile;
        } elseif (is_null($usrProfile)) {
            $errors = [];
            $errors['status'] = false;
            $errors['msg'] = 'Could not find the user!';
            $errors['username'] = 'Could not find the user!';

            return $errors;
        } else {
            //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
            if (isset($usrProfile->detail->ProfileFault)) {
                $errors = [];
                $errors['status'] = false;
                $errors['msg'] = $usrProfile->getMessage();
                $errors['username'] = $usrProfile->getMessage();

                return $errors;
            }
            //NOT A VALID PROFILE ERROR SO TRIGGER SYSTEM ERROR
            else {
                $this->fnError($request, 'SYSTEM ERROR :: USER_ACCOUNT ->getUserProfile', $usrProfile->getMessage(), $usrProfile, $triggerDefault = true);
            }
        }
    }

    public function fnBcscLogin()
    {
        if (isset($_SESSION['SAMLResponse'])) {
            unset($_SESSION['SAMLResponse']);
        }
        if (isset($_SESSION['role'])) {
            unset($_SESSION['role']);
        }

        $up = $this->fnGetBCSCUserProfile();

        if (! empty($up->status)) {
            $user['status'] = true;
            $user['uid'] = $this->uid;
            $name = null;

            if (isset($up->userProfile->firstName)) {
                $name .= $up->userProfile->firstName;
            }
            if (isset($up->userProfile->familyName)) {
                $name .= $up->userProfile->familyName;
            }

            $user['login_name'] = $name;
            //$this->fnClearUserCache();

            return $up;
        }
    }

    public function fnGetBCSCUserProfile()
    {
        $uid = $this->fnDecrypt($this->uid);

        //CALL GET USER PROFILE SOAP WEB SERVICE
        $this->WSDL = $this->fnWS('WS-HOSTS', 'BCSC_USER_ACCOUNT');
        $bcscusrProfile = $this->fnRequest('getUserProfile', ['userGUID' => $uid], 'get_user_profile'.$this->uid, 14400);

        //MAKE SURE IT IS NOT AN ERROR
        if (! isset($bcscusrProfile->faultcode)) {
            $bcscusrProfile->status = true;
            $bcscusrProfile->userProfile->SIN = $this->fnEncrypt($bcscusrProfile->userProfile->SIN);
            $bcscusrProfile->userProfile->userGUID = $this->fnEncrypt($bcscusrProfile->userProfile->userGUID);

            return $bcscusrProfile;
        } else {
            //WE HAVE AN ERROR AND IT IS A VALID PROFILE ERROR
            if (isset($bcscusrProfile->detail->ProfileFault)) {
                $errors = [];
                $errors['status'] = false;
                $errors['msg'] = $bcscusrProfile->getMessage();

                return $errors;
            } //NOT A VALID PROFILE ERROR SO TRIGGER SYSTEM ERROR
            else {
                $this->fnError('SYSTEM ERROR :: USER_ACCOUNT->getUserBcscProfile', $bcscusrProfile->getMessage(), $bcscusrProfile, $triggerDefault = true);
            }
        }

        $errors = [];
        $errors['status'] = false;
        $errors['msg'] = 'We could not fetch BCSC Profile.';

        return $errors;
    }/**
 * @return array
 */
    public function opensslParamsExtracted(): array {
        $key = config('services.aeit.salt');
        $cipher = config('services.aeit.cipher');
        $iv = config('services.aeit.iv');
        return [$key, $cipher, $iv];
    }

}
