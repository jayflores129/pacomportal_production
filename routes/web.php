<?php

use App\Http\Controllers\DbSeederController;
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
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    return 'config-cache';
});

Route::get('/', function () {
    return view('welcome');
});

/**
*  Login and Registration Page
*  Front End
*
**/
Route::get('/login','SessionController@store');

Route::get('/main', [ 'as' => 'main', 'uses' => 'MainController@login']);

Route::get('/password-hint','RegisterController@password_hint');
Route::post('/send_password_hint', ['as' => 'password/send_password_hint', 'uses' =>'RegisterController@send_password_hint']);

Route::get('/registration','RegisterController@index');
Route::post('/registration-store', ['as' => 'reg', 'uses' =>'RegisterController@store']);
Auth::routes();

// Test Route
Route::get('/test/create-role', '_TestController@createRole');
Route::get('/test/create-permission', '_TestController@createPermission');

//Privacy Policy
Route::get('/privacy-policy', 'RegisterController@privacy_policy');

Route::get('/seed/change_user', "DbSeederController@change_user");

/**
*  Authenticated Access page
*  Back End
*
**/
// 'middleware' => 
Route::group(['prefix' => 'admin', 'middleware' => ['role:admin']], function(){

     //Create user Page
    Route::resource('/users', 'UserController');
    Route::get('/user-confirm-delete/{id}', ['as' => 'users.confirm-delete', 'uses' => 'UserController@confirmDelete']);
    Route::resource('/customers', 'CustomerController');

    // Users Routing
    Route::get('/for-approval', 'UserController@pending');
    Route::post('/approving-user/{id}', 'UserController@process_approve');
    Route::post('/disapproving-user/{id}', 'UserController@disapprove');
    // Route::get('/change-permission', 'UserController@handle_permission');
    Route::post('/process-permission/{id}', 'UserController@process_permission');
    Route::get('/user-password/{id}', ['as' => 'users.edit-password','uses' =>'UserController@customerResetPassword']);
    Route::patch('/update_password/{id}', ['as' => 'users.update_password', 'uses' => 'UserController@update_password']);
    Route::patch('/block-user/{id}', ['as' => 'users.block_user', 'uses' => 'UserController@block_user']);
    Route::patch('/unblock-user/{id}', ['as' => 'users.unblock_user', 'uses' => 'UserController@unblock_user']);
    Route::get('/search-user', 'UserController@searchUsers');

    Route::get('/find-user-by-id', ['as' => 'users.find-user-by-id', 'uses' => 'UserController@find_user']);

    //Create Issue Page
    Route::resource('/issues', 'IssuesController');

    //Product Page
    Route::resource('/products', 'ProductsController');


    //Root Cause Page
    Route::resource('/rootcause', 'RootCauseController');

    //RMA Item Status Page
    Route::resource('/itemstatus', 'ItemStatusController');

    // Setting Routes
    Route::resource('/settings', 'SettingController');

    // Softwares Routes
    Route::get('/task-status', 'SoftwareController@tableView');
    Route::get('/software-revision', 'SoftwareController@revision');


    // Company Routes
    Route::get('/searchCompany', 'CompanyController@search');
    Route::post('/storeCompany', ['as' => 'company.store', 'uses' => 'CompanyController@store']);
    Route::resource('/companies', 'CompanyController');
    Route::get('/search-companies', 'CompanyController@searchCompanies');

    // Settings API
    Route::get('/setting-api', 'SettingController@setting_api');
    Route::get('/email-setting', 'SettingController@settingEmail');
    Route::patch('/update-setting-api', ['as' => 'settings.updateAPI', 'uses' => 'SettingController@update_api']);


    //Email Campgain
    Route::resource('/campaign', 'EmailCampaignController');
    Route::get('/search-subscriber', 'EmailCampaignController@searchSubscriber');
    Route::get('/subscriber/{id}', 'EmailCampaignController@unsubscribing');
    Route::get('/unsubscribe', [ 'as' => 'emailcampaign.unsubscribes', 'uses'  =>'EmailCampaignController@unsubscribes']);
    Route::patch('/subscribe-user/{id}', [ 'as' => 'subscribeUser', 'uses'  =>'EmailCampaignController@subscribeUser']);
    Route::post('/updateGeneral', ['as' => 'campaign.updateGeneral', 'uses' => 'EmailCampaignController@updateGeneral']);
    Route::post('/updateNewTicketAdmin', ['as' => 'campaign.updateNewTicketAdmin', 'uses' => 'EmailCampaignController@updateNewTicketAdmin']);
    Route::post('/updateNewTicketCustomer', ['as' => 'campaign.updateNewTicketCustomer', 'uses' => 'EmailCampaignController@updateNewTicketCustomer']);

    Route::post('/updateNewTaskAdmin', ['as' => 'campaign.updateNewTaskAdmin', 'uses' => 'EmailCampaignController@updateNewTaskAdmin']);
    Route::post('/updateNewTaskCustomer', ['as' => 'campaign.updateNewTaskCustomer', 'uses' => 'EmailCampaignController@updateNewTaskCustomer']);
    Route::post('/newTaskCommentCustomer', ['as' => 'campaign.newTaskCommentCustomer', 'uses' => 'EmailCampaignController@newTaskCommentCustomer']);
    Route::post('/newTaskAttachment', ['as' => 'campaign.newTaskAttachment', 'uses' => 'EmailCampaignController@newTaskAttachment']);
    Route::post('/newTaskStatus', ['as' => 'campaign.newTaskStatus', 'uses' => 'EmailCampaignController@newTaskStatus']);

    Route::post('/updateTaskResolve', ['as' => 'campaign.updateTaskResolve', 'uses' => 'EmailCampaignController@updateTaskResolve']);


    Route::post('/updateNewFile', ['as' => 'campaign.updateNewFile', 'uses' => 'EmailCampaignController@updateNewFile']);


    Route::post('/newRegistrationAdmin', ['as' => 'campaign.newRegistrationAdmin', 'uses' => 'EmailCampaignController@newRegistrationAdmin']);
    Route::post('/newRegistrationCustomer', ['as' => 'campaign.newRegistrationCustomer', 'uses' => 'EmailCampaignController@newRegistrationCustomer']);

    //Monitoring
    Route::get('/monitoring', 'MonitoringController@index');

 });
 Route::group(['middleware' => ['role:admin']], function(){

    Route::get('/rma/create', ['as' => 'repairs.create-rma','uses' =>'RepairsController@createRMA']);
 });

Route::group(['middleware' => 'auth'], function()
{
    // Profile
    Route::resource('profile', 'ProfileController');
    Route::get('your-profile', 'ProfileController@index');
    Route::get('subscription', 'ProfileController@subscription');
    Route::patch('/update_subscription/{id}', ['as' => 'update_subscription', 'uses' => 'ProfileController@updateSubscription']);
    Route::post('/update-repair/{filename}', 'RepairsController@update_repair');
    Route::put('/update-repair-status/{id}', 'RepairsController@approveQuotation'); // => Update the status from quotation. (Status: Confirmed)
    Route::get('/admin/user-task', 'SoftwareController@filter_user_task');
    Route::get('/admin/task-submitted', 'SoftwareController@task_submitted');
    Route::get('/admin/searchUser', 'SoftwareController@searchUser');
    Route::get('/admin/resolved-issues', 'SoftwareController@showResolvedTasks');
    Route::get('/admin/password-change', 'SettingController@change_password');
    Route::patch('/admin/update_password', ['as' => 'admin.update_password', 'uses' => 'SettingController@update_password']);

    Route::resource('/admin/softwares', 'SoftwareController');
    Route::get('/admin/softwares/resolving/{id}', ['as' => 'softwares.resolving', 'uses' => 'SoftwareController@resolvingTask']);
    Route::post('/addTask', 'SoftwareController@storeAjax');

    Route::get('/searchTask', 'SoftwareController@searchTask');
    Route::get('/searchUserTask', 'SoftwareController@searchUserTask');
    Route::get('/show-info',  ['as' => 'display-task', 'uses' => 'SoftwareController@show_task']);
    Route::get('/advanced-search-task', ['as' => 'advanced-search-task', 'uses' => 'SoftwareController@advancedSearch']);
    Route::get('/advanced-search-user-task', ['as' => 'advanced-search-user-task', 'uses' => 'SoftwareController@advancedSearchUserTask']);
    Route::get('/advanced-search-resolve-task', ['as' => 'advanced-search-resolve-task', 'uses' => 'SoftwareController@advancedSearchResolve']);
    Route::get('/advanced-search-user-resolve-task', ['as' => 'advanced-search-user-resolve-task', 'uses' => 'SoftwareController@advancedSearchUserResolveTask']);



    // Homepage Routing
    Route::group(['middleware' => 'revalidate'], function()
    {
        Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');
    });


    // Repairs Routing	
    Route::resource('/repairs', 'App\Http\Controllers\RepairsController');
    Route::get('/customer-edit-repair/{id}', ['as' => 'repairs.customer-edit','uses' =>'RepairsController@customerEditRepair']);
    Route::patch('/customer-update-repair/{id}', ['as' => 'repairs.customer-update-repair','uses' =>'RepairsController@customerUpdateRepair']);
    Route::get('/searchRepair', 'SearchController@searchRepair');
    Route::get('/serial-no-examples', 'PagesController@serialNo');



    //Route::get('/rma/create', ['as' => 'repairs.create-rma','uses' =>'RepairsController@createRMA']);
    Route::get('/rma/item/{id}', ['as' => 'repairs.item-edit','uses' =>'RepairsController@getItemById']);
    Route::put('/rma/{id}', 'RepairsController@update_RMA_item');

    Route::put('/rmaUpdatebyCust/{id}', 'RepairsController@updateItemByCust');
    Route::post('/rma/delete', 'RepairsController@delete_rma');
    Route::post('/rma/addItemRMA', 'RepairsController@addItemRMA');
    Route::get('/rma/getItems/{id}', 'RepairsController@refresh_fault_item');

    
    Route::get('/rma/create-rma', ['as' => 'repairs.view-rma-by-cust','uses' =>'RepairsController@viewCreateRMA']);
    Route::post('/rmaCreatebyCust', ['as' => 'repairs.create-rma-by-cust','uses' =>'RepairsController@createRMAByCust']);


    //Quotation
    Route::get('/rma-quotation/{id}', 'RepairsController@rmaQuotation');


    Route::post('/updateRMAStatus', ['as' => 'rma_status_update', 'uses' => 'RepairsController@updateRMAStatus']); 
    Route::post('/deleteRMAStatus', ['as' => 'rma_status_delete', 'uses' => 'RepairsController@deleteRMAStatus']); 
    Route::post('/deleteRMAComments', ['as' => 'rma_comments_delete', 'uses' => 'RepairsController@deleteRMAComments']); 
    
    Route::get('/searchUserRepair', 'RepairsController@searchUserRepair');
    Route::get('storeData', 'RepairsController@storeData');
    Route::post('/store-rma', 'RepairsController@storeRMA');
    Route::post('/rmaSetNotification', 'RepairsController@setUserNotification');

    //software comment
    Route::post('/admin/software-comment', ['as' => 'softwares.comment', 'uses' => 'SoftwareController@comment']);
    Route::get('/admin/get-comments', 'SoftwareController@getComments');
    Route::post('/admin/software-upload-attachment', ['as' => 'softwares.upload-attachment', 'uses' => 'SoftwareController@uploadAttachment']);
    Route::patch('/admin/software-resolve/{id}', ['as' => 'admin.softwares.resolve', 'uses' => 'SoftwareController@resolve']);
    Route::get('/searchResolveTask', 'SoftwareController@searchResolveTask');
    Route::get('/searchUserResolveTask', 'SoftwareController@searchUserResolveTask');


    // File Download Page
    Route::get('download/{filename}', ['as' => 'download', 'uses' => 'FilesController@download']);
    Route::get('download-document/{id}', ['as' => 'download-document', 'uses' => 'FilesController@download_doc']);
    Route::get('download-file-task/{id}', ['as' => 'download-task', 'uses' => 'FilesController@download_taskfile']);
    Route::get('download-comment-file/{filename}', ['as' => 'download-comment-file', 'uses' => 'FilesController@download_comment_file']);
    Route::get('download-software-file/{filename}', ['as' => 'download-software-file', 'uses' => 'FilesController@download_software_file']);


    Route::get('images/{filename}', 'FilesController@view_file');


    // Search Page
    Route::get('/search-main', 'SearchController@search');
    Route::get('/new-rma-search', 'SearchController@searchRMANew');

    Route::get('/search/repairs', 'SearchController@repairs');
    //Route::get('/advanced-search-rma', 'SearchController@advanced_search_rma');
    Route::get('/advanced-search-rma', 'SearchController@advanced_search_rma');
    Route::post('/print-search-result-rma', 'SearchController@print_search_result_csv');
    Route::post('/print-search-result-rma-pdf', 'SearchController@print_search_result_pdf');
    Route::get('/search-results', 'SearchController@show_search_results');
    Route::get('/search-rma', 'SearchController@search_rma');

    Route::resource('/search', 'SearchController' );
    Route::get('/searchAnything', 'SearchController@searchAnything');
    Route::get('/advancedSearch', ['as' => 'advanced-search', 'uses' => 'RepairsController@advancedSearch']);
    Route::get('/advancedSearchCustomerTicket', ['as' => 'search-customer-repair', 'uses' => 'RepairsController@advancedSearchCustomer']);


    // Files Routing
    Route::post('/add_comment', ['as' => 'add_comment', 'uses' => 'RepairsController@add_comment']);
    Route::resource('/files', 'FilesController');

    // Log Download
    Route::post('/file-downloaded', 'DownloadController@file');
    Route::post('/document-downloaded', 'DownloadController@document');
    Route::post('/technical-document-downloaded', 'DownloadController@technical');
    Route::post('/certificate-downloaded', 'DownloadController@certificate');
    Route::post('/task-file-download', 'DownloadController@task');

    Route::post('softwares/upload/{fileID}', 'SoftwareController@uploadSoftwareFiles');

    Route::patch('/update-company/{id}', ['as' => 'users.updateCompany', 'uses' => 'UserController@updateCompany']);
    Route::patch('/add-company/{id}', ['as' => 'users.addCompany', 'uses' => 'UserController@addCompany']);
    Route::get('/find-user-company/{id}', ['as' => 'users.find-user-company', 'uses' => 'CompanyController@searchUserCompany']);

    // Comment Routing
    Route::post('/comment', ['as' => 'rma_comment', 'uses' => 'CommentController@create']);

    // API Routing
    Route::get( 'connect-api', ['as' => 'connect-api', 'uses' => 'HomeController@connect_api'] );
    Route::get('/oauth', 'HomeController@oauth');
    Route::get('/oauth2', 'HomeController@oauth2');
    // Route::get('/email', 'EmailController@showUserInfo');
    // Route::post('/email', 'EmailController@sendEmail');

    
    // Firmware Routes
    Route::resource('/firmwares', 'FirmwareController');
    Route::get('/firmwares/create', 'FirmwareController@create')->middleware(CheckAdmin::class);
    Route::post('/firmwares/store', 'FirmwareController@store')->middleware(CheckAdmin::class);
    Route::get('/firmwares/edit-category/{id}', 'FirmwareController@editCategoryName')->middleware(CheckAdmin::class);
    Route::get('/firmwares/edit-release/{id}', 'FirmwareController@editRelease')->middleware(CheckAdmin::class);
    Route::patch('/update-firmware-release/{id}', ['as' => 'firmwares.updateRelease', 'uses' => 'FirmwareController@updateRelease'])->middleware(CheckAdmin::class);
    Route::patch('/update-firmware-name/{id}', ['as' => 'firmwares.updateName', 'uses' => 'FirmwareController@updateFirmwareName'])->middleware(CheckAdmin::class);


    // Technical Documenation Routes
    Route::resource('/technical-documentation', 'DocumentationController');
    Route::get('/technical-documentation/create', 'DocumentationController@create')->middleware(CheckAdmin::class);
    Route::post('/technical-documentation/store', 'DocumentationController@store')->middleware(CheckAdmin::class);
    Route::get('/technical-documentation/edit-category/{id}', 'DocumentationController@editCategoryName')->middleware(CheckAdmin::class);
    Route::patch('/update-technical-documentation-name/{id}', ['as' => 'technical-documentation.updateName', 'uses' => 'DocumentationController@updateTechDocsName'])->middleware(CheckAdmin::class);

    Route::get('/technical-documentation/edit-release/{id}', 'DocumentationController@editRelease')->middleware(CheckAdmin::class);
    Route::patch('/update-technical-documentation-release/{id}', ['as' => 'technical-documentation.updateRelease', 'uses' => 'DocumentationController@updateRelease'])->middleware(CheckAdmin::class);



    // Certification Routes
    Route::resource('/certificates', 'CertificateController');
    Route::get('/certificates/create', 'CertificateController@create')->middleware(CheckAdmin::class);
    Route::post('/certificates/store', 'CertificateController@store')->middleware(CheckAdmin::class);
    Route::get('/certificates/edit-name/{id}', 'CertificateController@editName')->middleware(CheckAdmin::class);
    Route::patch('/update-certificates-name/{id}', ['as' => 'certificates.updateName', 'uses' => 'CertificateController@updateName'])->middleware(CheckAdmin::class);

    Route::post('/user/document-notification', 'UserController@setDocumentationNotification');

    Route::prefix('migration')->group(function ($route) {
        // $route->get('company', 'DatabaseMigrationController@company');
        // $route->get('user', 'DatabaseMigrationController@user');
        // $route->get('rma_ticket', 'DatabaseMigrationController@rma_ticket');
        // $route->get('rma_ticket_faulties', 'DatabaseMigrationController@rma_ticket_faulties');
        // $route->get('user_companies', 'DatabaseMigrationController@user_companies');
        // $route->get('update_user_status', 'DatabaseMigrationController@update_user_status');
        // $route->get('update_rma_tickets_date', 'DatabaseMigrationController@update_rma_tickets_date');
        // $route->get('product', 'DatabaseMigrationController@product_migration');
        // $route->get('sync_user_role', 'DatabaseMigrationController@sync_user_role');
    });

    // Mark Read Notification
    Route::get('markAsRead', function(){
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back();

    })->name('markRead');


    Route::post('upload', function(){
        request()->file('file')->store(
            'my-file',
            's3'
        );
    })->name('upload');

    Route::get('uploader', function(){

        return view('upload/s3');
    });
});


// Custom User Registration
Route::get( 'registered', 'PagesController@registered' );


//Thank You Page
Route::get('/thank-you', 'HomeController@thank_you');


  //Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});
