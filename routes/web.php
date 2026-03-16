<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$env = env('APP_ENV_SHORT');

Route::get('/clear-debugger', 'UserController@clearDebugger')->name('clear-debugger');

Route::get('/forgot/password', function () use ($env) {
    return view('auth.logins.forgot-password', ['env' => $env, 'step' => 1, 'data' => '']);
})->name('forgot-password');

//dashboard/confirm-reset/10256050463D4CE7ADE056FEB6558D7D
Route::get('/confirm-reset/{guid}', 'ForgotPasswordController@confirmReset')->name('confirm-reset');
Route::get('/forgot/password/{step}', 'ForgotPasswordController@redirectToStepOne')->name('forgot-password-go-back');
Route::post('/forgot/password/step-1', 'ForgotPasswordController@forgotPasswordStepOne')->name('forgot-password-post-step-1');
Route::post('/forgot/password/step-2', 'ForgotPasswordController@forgotPasswordStepTwo')->name('forgot-password-post-step-2');
Route::post('/forgot/password/step-3', 'ForgotPasswordController@forgotPasswordStepThree')->name('forgot-password-post-step-3');
Route::post('/forgot/password/step-4', 'ForgotPasswordController@forgotPasswordStepFour')->name('forgot-password-post-step-4');
Route::post('/forgot/password/step-5', 'ForgotPasswordController@forgotPasswordDelete')->name('forgot-password-post-step-5-delete-account');
Route::post('/forgot/password/step-6', 'ForgotPasswordController@forgotPasswordStepSix')->name('forgot-password-post-step-6');
Route::post('/forgot/password/step-7', 'ForgotPasswordController@forgotPasswordReset')->name('forgot-password-post-step-7-reset-account');

Route::get('/login', function () use ($env) {
    return view('auth.logins.sabc_login', ['env' => $env]);
})->name('login');

Route::get('/temporary/{step?}', function () use ($env) {
    return view('auth.bcsc_rescue.bcsc-forgot', ['env' => $env, 'step' => 1, 'data' => '']);
})->name('bcsc-rescue-get');
Route::post('/temporary/step-1', 'ForgotBcscController@forgotBcscStepOne')->name('bcsc-rescue-post-step-1');
Route::post('/temporary/step-2', 'ForgotBcscController@forgotBcscStepTwo')->name('bcsc-rescue-post-step-2');

Route::get('/create', function () use ($env) {
    return view('auth.logins.sabc_register', ['env' => $env]);
})->name('sabc-register-get');
Route::post('/create', 'UserController@registerSabcUser')->name('sabc-register-post');

Route::get('/password-request', function () use ($env) {
    return view('auth.logins.sabc_register', ['env' => $env]);
})->name('passwords-request-get');

Route::get('/bcsc', 'UserController@showCreateBcscUser')->name('show-create-bcsc-user');
Route::post('/bcsc', 'UserController@bcscHook')->name('bcsc-saml-response');
Route::post('/create-bcsc', 'UserController@createBcscUser')->name('create-bcsc-user');
Route::get('/fetch-countries', 'UserController@fetchCountries')->name('fetch-countries');
Route::get('/get-challenge-questions', 'UserController@getChallengeQuestions')->name('get-challenge-questions');

Route::get('/admin/login', function () {
    return view('auth.logins.sabc_admin_login');
})->name('sabc-admin-login-get');
Route::get('/app_support/login', function () {
    return view('auth.logins.sabc_admin_login');
})->name('sabc-app_support-login-get');

Route::post('/login', 'UserController@sabcLogin')->name('sabc-login-post');
Route::post('/admin/login', 'UserController@sabcAdminLogin')->name('sabc-admin-login-post');
Route::get('/fetch-alerts', 'AlertController@fetchDashboardAlerts')->name('dashboard-alerts');
Route::get('/fetch-side-pages', 'SidePageController@fetchDashboardSides')->name('fetch-dashboard-side-pages');

Route::get('/appendix/confirm/{program_year}/{appendix_type}/{access_code}', 'UserController@appendixClaimViaUrl')->name('appendix-claim-via-url');
//NO-SIN appendix claim url
//dashboard/appendix/login/20202021/APPENDIX2
Route::get('/appendix/login/{program_year}/{appendix_type}', 'AppendixController@appendixNoSinClaim')->name('appendix-no-sin-claim');
Route::post('/appendix/login/{program_year}/{appendix_type}', 'AppendixController@appendixNoSinClaimSubmit')->name('appendix-no-sin-claim-submit');

Route::get('/institution-data-load-p', 'InstitutionController@instDataLoadPublic')->name('institution-data-load-p');
Route::get('/institution-details-p/{id}', 'InstitutionController@instDetailsPublic')->name('institution-details-p');

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', 'UserController@logoutUser')->name('logout-user');

    //authenticated Student OR Spouse OR Parent
    Route::group(['middleware' => ['student_spouse_parent']], function () {
        Route::group(['middleware' => ['got_sin']], function () {
            Route::get('/fetch-navbar', 'UserController@fetchNavbarData')->name('fetch-navbar-data');
            Route::get('/', 'UserController@dashboard')->name('dashboard');
            Route::get('/fetch-dashboard', 'UserController@fetchDashboardData')->name('fetch-dashboard-data');

            Route::get('/student-loans/check-application-status/{application}/{program_year?}', 'UserController@applicationPage')->name('application-page');
            Route::get('/student-loans/check-appendix-status/{application}/{formGuid?}/{program_year?}', 'UserController@appendixPage')->name('appendix-page');
            Route::get('/student-loans/delete-application/{documentGUID}/{app_id}', 'ApplicationController@deleteApplication')->name('delete-application');

            Route::get('/student-loans/fetch-application-status', 'UserController@fetchApplicationData')->name('fetch-application-data');
            Route::get('/student-loans/fetch-appendix-status', 'UserController@fetchAppendixData')->name('fetch-appendix-data');
            Route::get('/student-loans/remove-appendix/{app_id}/{formGUID}', 'AppendixController@deleteAppendix')->name('delete-appendix');

            Route::get('/profile', 'UserController@profile')->name('profile');
            Route::get('/fetch-profile', 'UserController@fetchProfileData')->name('fetch-profile-data');
            Route::get('/fetch-profile-question-pool', 'UserController@fetchProfileQuestionPool')->name('fetch-profile-question-pool');
            Route::post('/profile', 'UserController@updateProfile')->name('update-profile');
            Route::post('/bcsc-profile', 'UserController@updateBcscProfile')->name('update-bcsc-profile');

            Route::get('/notifications', 'UserController@notifications')->name('notifications');
            Route::get('/notifications/{notificationId}/{inTransition?}/{documentType?}', 'UserController@fetchNotification')->name('fetch-notification');
            Route::get('/fetch-notifications', 'UserController@fetchNotificationsData')->name('fetch-notifications-data');

            Route::get('/file-uploads', 'UserController@uploads')->name('uploads');
            Route::get('/fetch-uploads-list', 'UserController@fetchUploadsListData')->name('fetch-uploads-list-data');
            Route::get('/fetch-uploads', 'UserController@fetchUploadsData')->name('fetch-uploads-data');
            Route::post('/file-uploads', 'UserController@sabcFileManagerFormSubmit')->name('submit-file-uploads');

            Route::get('/student-loans/apply', 'UserController@studentApply')->name('student-loans-apply');

            Route::get('/institution-data-load', 'InstitutionController@instDataLoad')->name('institution-data-load');
            Route::get('/institution-details/{id}', 'InstitutionController@instDetails')->name('institution-details');

            Route::get('/student-loans/verification/bcsc', 'UserController@bcscRequired')->name('bcsc-verification-required');
            Route::get('/student-loans/interest-free', 'UserController@interestFree')->name('interest-free');

            Route::get('/appendix/claim/{appendix_type}/{access_code?}', 'AppendixController@appendixClaim')->name('appendix-claim');
            Route::get('/fetch-appendix-claim', 'AppendixController@appendixClaimData')->name('fetch-appendix-claim-data');
            Route::post('/appendix/claim/{appendix_type}', 'AppendixController@appendixClaimSubmit')->name('appendix-claim-submit');

            Route::get('/apply/confirm/full-time/{school_code}', 'ApplicationController@applyConfirmProfile')->name('full-time-application-confirm-profile');
            Route::post('/apply/confirm/full-time/{school_code}', 'ApplicationController@applyUpdateProfile')->name('full-time-application-update-profile');
            Route::post('/apply/confirm-bcsc/full-time/{school_code}', 'ApplicationController@applyUpdateBCSCProfile')->name('full-time-application-update-bcsc-profile');

            Route::get('/apply/application/full-time/{school_code}/{program_year}/{document_type?}', 'ApplicationController@applyFulltimeApplication')->name('full-time-application-start-application');

            Route::get('/edit/application/{document_guid}/{program_year}', 'ApplicationController@editFulltimeApplication')->name('full-time-application-edit-application');
            Route::get('/application-submit-checklist/{program_year}/{app_id}/{document_guid?}', 'ApplicationController@applicationSubmitChecklist')->name('application-submit-checklist');
            Route::post('/application-submit-checklist/{program_year}/{app_id}/{document_guid?}', 'ApplicationController@confirmSubmitChecklist')->name('post-application-submit-checklist');

            //submit application
//        dashboard/application-submit-checklist/20212022/2021300000/9162ac11666c4257bbe7265f1ae67d64

            //start new application
//        dashboard/apply/confirm/full-time/54427042

            //must be handled later
//        Document Centre links
//        <a :href="'/dashboard/notifications/' + value.letterID + '/' + (app.applicationDetails.applicationStatus == 'SUBMPROC' ? 'T' : 'R') + ''" target="_blank">{{value.letterDescription}}</a>

            //download appendix 3 button
            //dashboard/student-loans/request-appendix-3/f789fba620ec4c57a003c4a9ba2478a2/20202021
            Route::get('/student-loans/request-appendix-3/{document_guid}/{program_year}', 'AppendixController@downloadAppendix3')->name('request-appendix-3');
//            Route::get('/apply/appendix/{appendix_type}/{document_guid}/{program_year}/{format_type?}', 'AppendixController@applyAppendix')->name('appendix-apply');

            //resend appendix 2 email
            //dashboard/student-loans/resend-appendix-email/eecec402d6a54f129273853fdd4e5e00/Appendix2/2020300044
            Route::get('/student-loans/resend-appendix-email/{document_guid}/{appendix_type}/{application_id}', 'UserController@resendAppendixEmail')->name('resend-appendix-email');

            //(Re)Print And Sign Application Declaration button
            //dashboard/declaration/signature/applicant/2018300095
            Route::get('/declaration/no-signature/{role_type}/{application_number}/', 'DeclarationController@noSignature')->name('load-declaration-no-signature');
            Route::get('/declaration/no-signature-required/{role_type}/{application_number}/', 'DeclarationController@noSignatureRequired')->name('load-declaration-no-signature-required');
            Route::get('/declaration/no-signature-received/{role_type}/{application_number}/', 'DeclarationController@noSignatureReceived')->name('load-declaration-no-signature-received');
            Route::get('/declaration/econsent/{role_type}/{application_number}/', 'DeclarationController@loadEconsent')->name('load-econsent');
            Route::get('/categories/fetch-all', 'CategoryController@fetchAll')->name('fetch-cats-with-forms');
            Route::get('/appeal-forms', 'FormController@forms')->name('appeal');
            Route::get('/appeal-forms/new', 'FormController@startNew')->name('new-appeal');
            Route::get('/appeal-forms/new-form/{uuid}', 'FormController@newForm')->name('new-form');
            Route::post('/appeal-forms/create-form', 'FormController@createForm')->name('create-form');
            Route::get('/fetch-forms-list', 'FormController@fetchFormsListData')->name('fetch-forms-list-data');
            Route::get('/appeal-forms/{notificationId}/{inTransition?}/{documentType?}', 'FormController@fetchForm')->name('fetch-form');
            Route::get('/fetch-published-forms-list/{uuid?}', 'FormController@fetchPublishedFormList')->name('fetch-published-forms-list');
        });

        //###########
        // the following routes require authentication but NO SIN
        //###########

        Route::get('/apply/appendix/{appendix_type}/{document_guid}/{program_year}/{format_type?}', 'AppendixController@applyAppendix')->name('appendix-apply');
        //submit appendix
        //dashboard/appendix-submit-checklist/APPENDIX2/20202021/2020300050/0ad5190d9d4a44eeb0cdda0ea88bdffd/20202021

        Route::get('/appendix-submit-checklist/{appendix_type}/{program_year}/{app_id}/{document_guid?}/{prog_year?}', 'AppendixController@appendixSubmitChecklist')->name('appendix-submit-checklist');
        Route::post('/appendix-submit-checklist/{appendix_type}/{program_year}/{app_id}/{document_guid?}/{prog_year?}', 'AppendixController@confirmSubmitChecklist')->name('post-appendix-submit-checklist');
        Route::get('/appendix-submit-success/{app_id}/{appendix_type}/{document_guid?}', 'AppendixController@waitAfterAppxSubmit')->name('appendix-submit-success');
        Route::get('/appendix-submit-success-redirect/{app_id}/{appendix_type}/{document_guid?}', 'AppendixController@redirectAfterWait')->name('appendix-submit-success-redirect');
        //NO-SIN appendix claim url does NOT require authentication

        Route::get('/declaration/download/{appendix_type}/{document_guid}/{application_number}/', 'DeclarationController@downloadDeclaration')->name('download-declaration');

        //dashboard/declaration/signature/spouse/2020300050
        Route::get('/declaration/signature/{role_type}/{application_number}/', 'DeclarationController@signatureRequired')->name('load-declaration-signature-required');
    });

    //authenticated ADMIN routes
    Route::group(['middleware' => ['admin']], function () {
        Route::get('/admin', 'AdminController@adminDashboard')->name('admin-dashboard');
        Route::get('/admin/users', 'AdminController@showUsers')->name('admin-show-users-pages');
        Route::get('/admin/fetch-users', 'AdminController@fetchUsers')->name('admin-fetch-users');
        Route::get('/admin/users/edit/{user_id}', 'AdminController@showUser')->name('admin-users-show-user');
        Route::post('/admin/users/edit/{user_id}', 'AdminController@updateUser')->name('admin-users-update-user');
        Route::get('/admin/users/new', 'AdminController@newUser')->name('admin-users-new-user');
        Route::post('/admin/users/new', 'AdminController@createUser')->name('admin-users-create-user');
        Route::get('/admin/side-pages', 'SidePageController@sidePages')->name('admin-side-pages');
        Route::get('/admin/side-pages/new', 'SidePageController@sidePagesNew')->name('admin-side-pages-new');
        Route::get('/admin/side-pages/edit/{sidePage}', 'SidePageController@show')->name('admin-side-pages-show');
        Route::get('/admin/fetch-side-pages', 'SidePageController@fetchSidePages')->name('admin-fetch-side-pages');
        Route::post('/admin/side-pages/new', 'SidePageController@store')->name('admin-side-pages-create');
        Route::post('/admin/side-pages/edit/{sidePage}', 'SidePageController@update')->name('admin-side-pages-update');
        Route::get('/admin/alerts', 'AlertController@alerts')->name('admin-alerts');
        Route::get('/admin/alerts/new', 'AlertController@alertsNew')->name('admin-alerts-new');
        Route::get('/admin/alerts/edit/{alert}', 'AlertController@show')->name('admin-alerts-show');
        Route::get('/admin/fetch-alerts', 'AlertController@fetchAlerts')->name('admin-fetch-alerts');
        Route::post('/admin/alerts/new', 'AlertController@store')->name('admin-alerts-create');
        Route::post('/admin/alerts/edit/{alert}', 'AlertController@update')->name('admin-alerts-update');
        Route::get('/admin/declarations', 'DeclarationController@declarations')->name('admin-declarations');
        Route::get('/admin/declarations/new', 'DeclarationController@declarationsNew')->name('admin-declarations-new');
        Route::get('/admin/declarations/edit/{declaration}', 'DeclarationController@show')->name('admin-declarations-show');
        Route::get('/admin/fetch-declarations', 'DeclarationController@fetchDeclarations')->name('admin-fetch-declarations');
        Route::post('/admin/declarations/new', 'DeclarationController@store')->name('admin-declarations-create');
        Route::post('/admin/declarations/edit/{declaration}', 'DeclarationController@update')->name('admin-declarations-update');
        Route::get('/admin/forms', 'FormController@adminForms')->name('admin-forms');
        Route::get('/admin/forms/new', 'FormController@adminFormsNew')->name('admin-forms-new');
        Route::get('/admin/forms/edit/{form}', 'FormController@adminShow')->name('admin-forms-show');
        Route::get('/admin/forms/delete/{form}', 'FormController@adminDelete')->name('admin-forms-delete');
        Route::get('/admin/forms/fetch', 'FormController@adminFetchForms')->name('admin-fetch-forms');
        Route::post('/admin/forms/new', 'FormController@adminStore')->name('admin-forms-create');
        Route::post('/admin/forms/edit/{form}', 'FormController@adminUpdate')->name('admin-forms-update');
        Route::get('/admin/forms/edit-weight/{form}/{weight}', 'FormController@adminEditWeight')->name('admin-forms-edit-weight');
        Route::get('/admin/categories', 'CategoryController@adminCats')->name('admin-categories');
        Route::get('/admin/categories/new', 'CategoryController@adminCatNew')->name('admin-categories-new');
        Route::get('/admin/categories/edit/{category}', 'CategoryController@adminShow')->name('admin-categories-show');
        Route::get('/admin/categories/delete/{category}', 'CategoryController@adminDelete')->name('admin-categories-delete');
        Route::get('/admin/categories/fetch', 'CategoryController@adminFetchCats')->name('admin-categories-forms');
        Route::post('/admin/categories/new', 'CategoryController@adminStore')->name('admin-categories-create');
        Route::post('/admin/categories/edit/{category}', 'CategoryController@adminUpdate')->name('admin-categories-update');
        Route::get('/admin/categories/fetch', 'CategoryController@fetchCategories')->name('admin-categories-fetch');
        Route::get('/admin/categories/edit-weight/{category}/{weight}', 'CategoryController@adminEditWeight')->name('admin-categories-edit-weight');
        Route::get('/admin/settings', 'SettingController@index')->name('admin-settings');
        Route::post('/admin/settings-fetch/{type}', 'SettingController@fetch')->name('admin-settings-fetch');
        Route::post('/admin/settings-dec-update/{declarationField}', 'SettingController@decUpdate')->name('admin-settings-dec-update');
        Route::post('/admin/settings-side-page-update/{sidePage}', 'SettingController@sidePageUpdate')->name('admin-settings-side-page-update');


    });

    //authenticated App_Support routes
    Route::group(['middleware' => ['app_support']], function () {
        Route::get('/app_support', 'AppSupportController@appSupportDashboard')->name('app_support-dashboard');
        Route::get('/app_support/access', 'AppSupportController@access')->name('app_support-access-get');
        Route::post('/app_support/find_user', 'AppSupportController@findUser')->name('app_support-find-user-post');
        Route::post('/app_support/login_sabc_user', 'AppSupportController@loginSabcUser')->name('app_support-login-sabc-user-post');
        Route::get('/app_support/profile', 'AppSupportController@userProfile')->name('app_support-show-profile');
        Route::get('/app_support/applications/{application_number?}', 'AppSupportController@showApplications')->name('app_support-show-applications');
        Route::get('/app_support/fetch-applications', 'AppSupportController@fetchApplications')->name('app_support-fetch-applications');
        Route::get('/app_support/fetch-application-detail/{application_number}', 'AppSupportController@fetchApplicationDetail')->name('app_support-fetch-application-detail');
        Route::get('/app_support/appendix_list/{application_number?}', 'AppSupportController@showAppendixList')->name('app_support-show-appendix_list');
        Route::get('/app_support/fetch-appendix_list', 'AppSupportController@fetchAppendixList')->name('app_support-fetch-appendix_list');
        Route::get('/app_support/fetch-appendix-detail/{application_number}', 'AppSupportController@fetchAppendixDetail')->name('app_support-fetch-appendix-detail');
        Route::get('/app_support/declarations', 'AppSupportController@showDeclarations')->name('app_support-show-declarations');

        //dashboard/notifications/1820509001/R
        Route::get('/app_support/fetch-application-notification/{notificationId}/{inTransition?}/{documentType?}', 'AppSupportController@fetchNotification')->name('app_support-fetch-notification');
    });
});
