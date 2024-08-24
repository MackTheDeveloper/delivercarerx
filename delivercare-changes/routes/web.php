<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\HospiceController;
use App\Http\Controllers\HospiceUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\FacilitiesController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\NursePatientController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AssignNursesController;
use App\Http\Controllers\RefillsInController;
use App\Http\Controllers\RefillsInQueueController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\PatientPrescriptionsController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\PreventRouteAccessMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PlaceOrderController;
use App\Http\Controllers\SwaggerController;
use App\Http\Controllers\SyncController;

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

/* Route::get('/', function () {
    return view('welcome');
}); */

Route::get('/', function () {
    return redirect('securedlccontrol/login');
});


Route::get('/getState/{id}',[CityController::class,'getState']);
Route::get('/getCity/{id}',[CityController::class,'getCity']);

Route::any('admin/{slug}', [AdminController::class, 'index'])->name('htmlpages');


//Auth::routes();
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::prefix('securedlccontrol')->middleware([AdminMiddleware::class])->group(function () {

    // Login and Logout
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login')->withoutMiddleware([AdminMiddleware::class]);
    Route::post('/login', [AdminController::class, 'login'])->name('loginPost')->withoutMiddleware([AdminMiddleware::class]);
    Route::get('/logout', [AdminController::class, 'logout'])->name('logout');

    // Only used for SSO with Optum
    Route::get('/openId', [AdminController::class, 'loginWithToken'])->name('openId')->withoutMiddleware([AdminMiddleware::class]);

    // Route::get('/openId', [AdminController::class, 'loginOptum'])->name('openIdNew')->withoutMiddleware([AdminMiddleware::class]);

    Route::get('/openIdNew',[AdminController::class, 'loginOptum'])->name('openIdNew')->withoutMiddleware([AdminMiddleware::class]);
    Route::get('/postLogout', [AdminController::class, 'logout'])->name('logoutPost');
    // END SSO with Optum
    

    //API
    Route::get('/swagger',[SwaggerController::class, 'swaggerTest'])->name('swagger');


    // Admin Dashboard
    Route::get('admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    //Nurse Dashboard
    Route::get('admin/nursePatients', [NursePatientController::class, 'index'])->name('nursePatients-list');
    // Only used for SSO with Optum
    Route::get('admin/nursePatients&isFound=true', [NursePatientController::class, 'index'])->name('nursePatients-list-found');
    // END SSO with Optum
    Route::get('admin/nursePatients/list', [NursePatientController::class, 'list'])->name('nursePatientsData');
    Route::get('admin/patient-rx/{id}', [NursePatientController::class, 'patientIndex'])->name('patientsList');
    Route::get('admin/rx/list', [NursePatientController::class, 'patientList'])->name('patientsListData');
    Route::get('admin/rx/detail/{id}', [NursePatientController::class, 'patientPrescriptions'])->name('patient-prescriptions');
    Route::get('admin/rx/plist', [NursePatientController::class, 'fillHistoryData'])->name('fillHistoryData');

    //add to cart
    Route::post('admin/add-cart', [NursePatientController::class, 'addCartRx'])->name('add-cart');
    Route::get('admin/my-shopping-cart', [NursePatientController::class, 'myShoppingCart'])->name('my-shopping-cart');
    Route::post('admin/delete-cart', [NursePatientController::class, 'deleteCart'])->name('delete-cart');
    Route::get('admin/delete-cart-all', [NursePatientController::class, 'deleteCartAll'])->name('delete-cart-all');
    Route::get('admin/shipping-address', [NursePatientController::class, 'shippingAddress'])->name('shipping-address');
    Route::post('admin/shipping-address-details', [NursePatientController::class, 'shippingAddressDetails'])->name('shipping-address-details');
     Route::get('admin/cart-review', [NursePatientController::class, 'cartReview'])->name('cart-review');
     Route::get('admin/refill-place-order', [NursePatientController::class, 'placeOrder'])->name('refill-place-order');
     Route::get('admin/thank-you/{order_number}', [NursePatientController::class, 'thankYou'])->name('thank-you');
     

    //Forgot Password
    Route::get('/forgot-password', [AdminController::class, 'showForgotPassForm'])->withoutMiddleware([AdminMiddleware::class])->name('show-forgot-password');
    Route::post('/forgot-password', [AdminController::class, 'forgotPassword'])->withoutMiddleware([AdminMiddleware::class])->name('forgot-password');

    // Reset Password
    Route::get('/reset-password/{token}', [AdminController::class, 'showResetPassForm'])->withoutMiddleware([AdminMiddleware::class])->name('show-reset-password');
    Route::post('/reset-password', [AdminController::class, 'resetPassword'])->withoutMiddleware([AdminMiddleware::class])->name('reset-password');

    // Reset Password
    Route::get('/set-password/{token}', [AdminController::class, 'showSetPassForm'])->withoutMiddleware([AdminMiddleware::class])->name('show-set-password');

    Route::post('/set-password', [AdminController::class, 'setPassword'])->withoutMiddleware([AdminMiddleware::class])->name('set-password');

    //Pharmacy management
    Route::get('admin/pharmacies', [PharmacyController::class, 'index'])->name('pharmacy-list')->middleware([PreventRouteAccessMiddleware::class]);
    Route::get('admin/pharmacies/list', [PharmacyController::class, 'list'])->name('pharmacy-fetch-listing');
    Route::post('admin/pharmacy/delete', [PharmacyController::class, 'delete'])->name('delete-pharmacy');
    Route::get('admin/add-pharmacy', [PharmacyController::class, 'add'])->name('show-pharmacy-form')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/add-pharmacy', [PharmacyController::class, 'store'])->name('store-pharmacy');
    Route::get('admin/edit-pharmacy/{id}', [PharmacyController::class, 'edit'])->name('show-edit-pharmacy-form')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/pharmacy/update', [PharmacyController::class, 'update'])->name('update-pharmacy');

    // Hospice Management
    Route::get('admin/hospices', [HospiceController::class, 'index'])->name('hospice-list')->middleware([PreventRouteAccessMiddleware::class]);
    Route::get('admin/hospices/list', [HospiceController::class, 'list'])->name('hospice-fetch-listing');
    Route::post('admin/hospice/delete', [HospiceController::class, 'delete'])->name('delete-hospice');

    Route::get('admin/hospice/add', [HospiceController::class, 'add'])->name('show-hospice-form')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/hospice/store', [HospiceController::class, 'store'])->name('store-hospice');
    Route::get('admin/hospice/edit/{id}', [HospiceController::class, 'edit'])->name('show-edit-hospice-form')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/hospice/update', [HospiceController::class, 'update'])->name('update-hospice');
    Route::post('fetch-cities/{stateId}', [CommonController::class, 'fetchCities'])->name('fetch-cities');
    Route::post('fetch-states/{countryId}', [CommonController::class, 'fetchStates'])->name('fetch-states');

    // Role Management
    Route::get('admin/roles', [RoleController::class, 'index'])->name('roles');
    Route::get('admin/role/permissions/{roleId}', [RoleController::class, 'getPermissions'])->name('get-permissions');
    Route::post('admin/role/permission/update', [RoleController::class, 'updatePermissions'])->name('update-permissions');


    // Refills Management
    Route::get('admin/latest-orders', [OrdersController::class, 'indexLatestSA'])->name('latest-orders-sa');
    Route::get('admin/latest-orders-sa/list', [OrdersController::class, 'listLatestSA'])->name('latest-orders-listing');
    Route::post('admin/latest-orders/delete', [OrdersController::class, 'deleteLatestOrders'])->name('delete-latest-orders');
    Route::get('admin/all-orders', [OrdersController::class, 'indexAllSA'])->name('all-orders-sa');
    Route::get('admin/all-orders-sa/list', [OrdersController::class, 'listAllSA'])->name('all-orders-listing');
    Route::post('admin/all-orders-delete', [OrdersController::class, 'delete'])->name('delete-all-orders');
    Route::post('admin/all-orders-export', [OrdersController::class, 'exportAll'])->name('all-orders-export');
    Route::get('admin/exportOrderNumbers', [OrdersController::class, 'exportOrderNumbers'])->name('exportOrderNumbers');
    Route::get('admin/offline-orders', [OrdersController::class, 'indexOfflineOrders'])->name('index-offline-orders');
    Route::get('admin/offline-orders/list', [OrdersController::class, 'listOfflineOrders'])->name('list-offline-orders');
    Route::get('admin/generate-orders-pdf/{id}', [OrdersController::class, 'generateOrdersPDF'])->name('generateOrdersPDF');
    Route::get('admin/generate-orders-tiff/{id}', [OrdersController::class, 'generateOrdersTIFF'])->name('generateOrdersTIFF');

      // Place Orders Management
      Route::get('admin/place-order', [PlaceOrderController::class, 'index'])->name('place-order');
      Route::get('admin/place-order/list', [PlaceOrderController::class, 'list'])->name('place-orders-listing');
      Route::get('admin/place-order/calculateShipping', [PlaceOrderController::class, 'calculateShipping'])->name('calculate-shipping');
      Route::match(['get','post'],'admin/place-order/roverQuote{buttonText}', [PlaceOrderController::class, 'roverQuote'])->name('roverQuote');
      Route::get('admin/place-order/getZipData', [PlaceOrderController::class, 'getZipData'])->name('getZipData');
      Route::post('admin/all-orders-export', [PlaceOrderController::class, 'exportAll'])->name('all-orders-export');
      Route::post('admin/autocomplete', [PlaceOrderController::class, 'autoComplete'])->name('auto-complete');
      Route::post('admin/fetchData', [PlaceOrderController::class, 'fetchData'])->name('fetchData');
      Route::post('admin/fetchCareItems', [PlaceOrderController::class, 'fetchCareItems'])->name('fetchCareItems');
      Route::post('admin/autocompletePrescriber', [PlaceOrderController::class, 'autoCompletePrescriber'])->name('auto-complete-prescriber');
      Route::post('admin/fetchDataPrescriber', [PlaceOrderController::class, 'fetchDataPrescriber'])->name('fetchDataPrescriber');
      Route::post('admin/autocompleteByCustomerID', [PlaceOrderController::class, 'autocompleteByCustomerID'])->name('auto-complete-cusomerid');
      Route::post('admin/fetchDrugDetails', [PlaceOrderController::class, 'fetchDrugDetails'])->name('fetchDrugDetails');
      Route::post('admin/submitPlaceOrderForm', [PlaceOrderController::class, 'submitPlaceOrderForm'])->name('submitPlaceOrderForm');
      Route::get('admin/generate-pdf/{id}', [PlaceOrderController::class, 'generatePDF'])->name('generatePDF');
      Route::get('admin/generate-tiff/{id}', [PlaceOrderController::class, 'tiffDownload'])->name('tiffDownload');


    Route::get('admin/fetchOrderItems/{id}', [PlaceOrderController::class, 'fetchOrderItems'])->name('fetchOrderItems');
      Route::post('admin/upsDetails', [PlaceOrderController::class, 'upsDetails'])->name('upsDetails');
      Route::post('admin/fetchDrugData', [PlaceOrderController::class, 'fetchDrugData'])->name('fetchDrugData');


      Route::post('admin/order/updatestatus', [OrdersController::class, 'updateStatus']);
      Route::post('admin/shippingDetailsData', [PlaceOrderController::class, 'shippingDetailsData'])->name('shippingDetailsData');


    // User Management
    Route::get('admin/user-list', [UserController::class, 'index'])->name('user-list')->middleware([PreventRouteAccessMiddleware::class]);
    Route::get('admin/user/list', [UserController::class, 'list'])->name('user-fetch-listing');
    Route::get('admin/add-user', [UserController::class, 'add'])->name('show-user-form')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/add-store', [UserController::class, 'store'])->name('store-user');
    Route::get('admin/edit-user/{id}', [UserController::class, 'edit'])->name('edit-user')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/update-user', [UserController::class, 'update'])->name('update-user');
    Route::post('admin/delete-user', [UserController::class, 'delete'])->name('delete-user');

    // Hospice User Management
    Route::get('admin/hospice-user-list', [HospiceUserController::class, 'index'])->name('hospice-user-list')->middleware([PreventRouteAccessMiddleware::class]);
    Route::get('admin/hospice-user/list', [HospiceUserController::class, 'list'])->name('hospice-user-fetch-listing');
    Route::get('admin/hospice-add-user', [HospiceUserController::class, 'add'])->name('hospice-show-user-form')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/hospice-add-store', [HospiceUserController::class, 'store'])->name('hospice-store-user');
    Route::get('admin/hospice-edit-user/{id}', [HospiceUserController::class, 'edit'])->name('hospice-edit-user')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/hospice-update-user', [HospiceUserController::class, 'update'])->name('hospice-update-user');
    Route::post('admin/hospice-delete-user', [HospiceUserController::class, 'delete'])->name('hospice-delete-user');
    Route::post('admin/fetch-branches/{facilityId}', [HospiceUserController::class, 'fetchBranches'])->name('fetch-branches');
    Route::post('admin/fetch-facility/{hospiceId}', [HospiceUserController::class, 'fetchFacility'])->name('fetch-facility');


    // Nurse Management
    Route::get('admin/nurse-user-list', [NurseController::class, 'index'])->name('nurse-user-list')->middleware([PreventRouteAccessMiddleware::class]);
    Route::get('admin/nurse-user/list', [NurseController::class, 'list'])->name('nurse-user-fetch-listing');
    Route::get('admin/nurse-add-user', [NurseController::class, 'add'])->name('nurse-show-user-form')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/nurse-add-store', [NurseController::class, 'store'])->name('nurse-store-user');
    Route::get('admin/nurse-edit-user/{id}', [NurseController::class, 'edit'])->name('nurse-edit-user')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/nurse-update-user', [NurseController::class, 'update'])->name('nurse-update-user');
    Route::post('admin/nurse-delete-user', [NurseController::class, 'delete'])->name('nurse-delete-user');
    //assign nurses
    Route::get('admin/assign-nurse', [NurseController::class, 'assignNurse'])->name('assign-nurse')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('fetch-nurses/{id}', [NurseController::class, 'getNurseData'])->name('fetch-nurses');
    Route::post('admin/assign-nurse-update-user', [NurseController::class, 'updateAssignNurse'])->name('update-assign-nurse');
    //import nurses
    Route::get('admin/import-nurse', [NurseController::class, 'import'])->name('import-nurses')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/import-nurse-data', [NurseController::class, 'importNurse'])->name('import-nurse-data');

    //Profile
    Route::get('admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('admin/update-profile', [AdminController::class, 'updateProfile'])->name('admin.updateProfile');
    Route::post('admin/change-password', [AdminController::class, 'changePassword'])->name('admin.changePassword');

    //Email-Template
    Route::get('admin/email-template', [EmailTemplateController::class, 'index'])->name('email-template-list');
    Route::get('admin/email-template/list', [EmailTemplateController::class, 'list'])->name('email-template-fetch-listing');
    Route::post('admin/email-template/delete', [EmailTemplateController::class, 'delete'])->name('delete-email-template');

    Route::get('admin/email-template-add', [EmailTemplateController::class, 'create'])->name('admin.email-template-add');
    Route::post('admin/saveEmailTemplate', [EmailTemplateController::class, 'store'])->name('admin.saveEmailTemplate');
    Route::get('admin/email-template-edit/{id}', [EmailTemplateController::class, 'edit'])->name('admin.email-template-edit');
    Route::post('admin/updateEmailTemplate/{id}', [EmailTemplateController::class, 'update'])->name('admin.updateEmailTemplate');
    Route::get('admin/email-template-delete/{id}', [EmailTemplateController::class, 'delete'])->name('admin.email-template-delete');
    //Route::get('admin/email-template-delete/{id}', [EmailTemplateController::class, 'delete'])->name('admin.email-template-delete');

    Route::get('admin/facilities', [FacilitiesController::class, 'index'])->name('facilities-list')->middleware([PreventRouteAccessMiddleware::class]);;
    Route::get('admin/facilities/list', [FacilitiesController::class, 'list'])->name('facilities-fetch-listing');
    Route::post('admin/facilities/delete', [FacilitiesController::class, 'delete'])->name('delete-facilities');

    Route::get('admin/facilities-add', [FacilitiesController::class, 'create'])->name('admin.facilities-add')->middleware([PreventRouteAccessMiddleware::class]);;
    Route::post('admin/saveFacilities', [FacilitiesController::class, 'store'])->name('admin.saveFacilities');
    Route::get('admin/facilities-edit/{id}', [FacilitiesController::class, 'edit'])->name('admin.facilities-edit')->middleware([PreventRouteAccessMiddleware::class]);;
    Route::post('admin/updateFacilities/{id}', [FacilitiesController::class, 'update'])->name('admin.updateFacilities');


    Route::get('admin/branch', [BranchController::class, 'index'])->name('branch-list')->middleware([PreventRouteAccessMiddleware::class]);;

    Route::get('admin/branch/list', [BranchController::class, 'list'])->name('branch-listing');
    Route::get('admin/branch-add', [BranchController::class, 'create'])->name('admin.branch-add')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/saveBranch', [BranchController::class, 'store'])->name('admin.saveBranch');
    Route::get('admin/branch-edit/{id}', [BranchController::class, 'edit'])->name('admin.branch-edit')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/updateBranch/{id}', [BranchController::class, 'update'])->name('admin.updateBranch');
    Route::post('admin/branch/delete', [BranchController::class, 'delete'])->name('delete-branch');

    //import nurses
    Route::get('admin/import-branch', [BranchController::class, 'import'])->name('import-branchs')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/import-branch-data', [BranchController::class, 'importBranches'])->name('import-branches-data');



    Route::post('fetch-facilities/{hospiceId}', [CommonController::class, 'fetchFacilities'])->name('fetch-facilities');

    Route::get('admin/import-branches', [BranchController::class, 'importBranch'])->name('import-branches')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/uploadBranch',[BranchController::class,'uploadBranch'])->name('admin.uploadBranch');


    Route::post('fetch-facilities/{hospiceId}', [CommonController::class, 'fetchFacilities'])->name('fetch-facilities');


    //Shipping Carriers
     Route::get('admin/shipping', [ShippingController::class, 'index'])->name('shipping-list')->middleware([PreventRouteAccessMiddleware::class]);
    Route::get('admin/shipping/list', [ShippingController::class, 'list'])->name('shipping-fetch-listing');
     Route::post('admin/shipping/delete', [ShippingController::class, 'delete'])->name('delete-shipping');
    Route::get('admin/shipping/add', [ShippingController::class, 'add'])->name('show-shipping-form')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/shipping/store', [ShippingController::class, 'store'])->name('store-shipping');
    Route::get('admin/shipping/edit/{id}', [ShippingController::class, 'edit'])->name('show-edit-shipping-form')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/shipping/update', [ShippingController::class, 'update'])->name('update-shipping');

    // Activity Management
    Route::get('admin/activities', [ActivityController::class, 'index'])->name('activity-list')->middleware([PreventRouteAccessMiddleware::class]);
    Route::get('admin/activities/list', [ActivityController::class, 'list'])->name('activity-fetch-listing');

    // Import Patients
    Route::get('admin/import/patients', [PatientController::class, 'import'])->name('import-patients')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/import/patients', [PatientController::class, 'importData'])->name('import-patients-data');

    // Import Facility Care
    Route::get('admin/import/facility', [FacilitiesController::class, 'import'])->name('import-facility')->middleware([PreventRouteAccessMiddleware::class]);
      Route::post('admin/import/facility', [FacilitiesController::class, 'importData'])->name('import-facility-data');


    // Import Deliver Care
    Route::get('admin/import/deliver', [UserController::class, 'import'])->name('import-delivercare')->middleware([PreventRouteAccessMiddleware::class]);
      Route::post('admin/import/deliver', [UserController::class, 'importData'])->name('import-deliver-data');

    // Import NewLeaf Order IDs
    Route::get('admin/import/newleaf-order-ids', [OrdersController::class, 'import'])->name('import-newleaf')->middleware([PreventRouteAccessMiddleware::class]);
      Route::post('admin/import/newleaf', [OrdersController::class, 'importData'])->name('import-newleaf-data');

    //Patients Management
    Route::get('admin/patients', [PatientController::class, 'index'])->name('patients-list')->middleware([PreventRouteAccessMiddleware::class]);
    Route::get('admin/patients/list', [PatientController::class, 'list'])->name('patients-fetch-listing');
    Route::get('admin/patients/add', [PatientController::class, 'add'])->name('show-patients-form')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/patients/store', [PatientController::class, 'store'])->name('store-patients');
    Route::get('admin/patients/edit/{id}', [PatientController::class, 'edit'])->name('show-edit-patients-form')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/patients/delete', [PatientController::class, 'delete'])->name('delete-patients');
    Route::post('admin/patients/update', [PatientController::class, 'update'])->name('update-patients');

    Route::get('admin/patients-ship', [PatientController::class, 'shipArray'])->name('patients-ship');

    //Refills In Queue Listing
    Route::get('admin/refillsInQueue', [RefillsInQueueController::class, 'inpdex'])->name('refillsInQueue-list')->middleware([PreventRouteAccessMiddleware::class]);
    Route::get('admin/refillsInQueue/list', [RefillsInQueueController::class, 'list'])->name('refillsInQueue-fetch-listing');

    //Refills In Listing
    Route::get('admin/refills', [RefillsInController::class, 'index'])->name('refillsIn-list')->middleware([PreventRouteAccessMiddleware::class]);
    Route::get('admin/refillsIn/list', [RefillsInController::class, 'list'])->name('refillsIn-fetch-listing');
    Route::get('admin/refillOrderItems/index/{id}',[RefillsInController::class,'refillOrderItemsindex'])->name('refillOrderItemsindex');
    Route::get('admin/refillOrderItems/list',[RefillsInController::class,'refillOrderItemslist'])->name('refillOrderItemslist');
    Route::get('admin/import-hospice', [HospiceController::class, 'importHospice'])->name('import-hospice')->middleware([PreventRouteAccessMiddleware::class]);
    Route::post('admin/uploadHospice',[HospiceController::class,'uploadHospice'])->name('admin.uploadHospice');

    //partners module
    Route::get('admin/partners', [PartnersController::class, 'index'])->name('partners-list')->middleware([PreventRouteAccessMiddleware::class]);;
    Route::get('admin/partners/list', [PartnersController::class, 'list'])->name('partners-fetch-listing');
    Route::post('admin/partners/delete', [PartnersController::class, 'delete'])->name('delete-partners');

    Route::get('admin/partners-add', [PartnersController::class, 'create'])->name('partners-add')->middleware([PreventRouteAccessMiddleware::class]);;
    Route::post('admin/savePartners', [PartnersController::class, 'store'])->name('partners-store');
    Route::get('admin/partners-edit/{id}', [PartnersController::class, 'edit'])->name('partners-edit')->middleware([PreventRouteAccessMiddleware::class]);;
    Route::post('admin/updatePartners/{id}', [PartnersController::class, 'update'])->name('updatePartners');

});

Route::any('sync-data/{slug}/{pharmacy_id}/{page?}/{records?}', [SyncController::class, 'sync'])->name('syncData');

Route::get('master-sync-data/{slug}', [SyncController::class, 'syncMaster'])->name('syncMasterData');
Route::get('sync-rxs-import', [SyncController::class, 'syncRxsImport'])->name('syncRxsImport');
Route::get('sync-refills-import', [SyncController::class, 'syncRefillsImport'])->name('syncRefillsImport');
Route::get('sync-refill-shipment-import', [SyncController::class, 'syncRefillShipmentImport'])->name('syncRefillShipmentImport');
Route::get('sync-care-kit-import', [SyncController::class, 'syncCareKitImport'])->name('syncCareKitImport');
Route::get('sync-care-kit-item-import', [SyncController::class, 'syncCareKitItemImport'])->name('syncCareKitItemImport');
Route::get('info-php',[SyncController::class, 'phpInfo'])->name('phpInfo');
Route::get('hospice-id-by-zero', [HospiceController::class, 'hospiceByIdZero'])->name('hospiceByIdZero');
Route::get('sync-prescriber-import', [SyncController::class, 'syncPrescribersImport'])->name('syncPrescribersImport');
Route::get('sync-drugs-import', [SyncController::class, 'syncDrugsImport'])->name('syncDrugsImport');
Route::get('sync-newleaforders-import', [SyncController::class, 'syncNewLeafOrdersImport'])->name('syncNewLeafOrdersImport');
Route::get('sync-patients-import', [SyncController::class, 'syncPatientsImport'])->name('syncPatientsImport');
Route::get('export-order-numbers', [OrdersController::class, 'globalExportOrderNumbers'])->name('globalExportOrderNumbers');
Route::get('check-pharmacy-available', [SyncController::class, 'checkPharmacy'])->name('checkPharmacy');


// Child Sync Process
Route::get('sync-rxs-import-child', [SyncController::class, 'syncRxsImportChild'])->name('syncRxsImportChild');
Route::get('sync-refills-import-child', [SyncController::class, 'syncRefillsImportChild'])->name('syncRefillsImportChild');
Route::get('sync-refill-shipment-import-child', [SyncController::class, 'syncRefillShipmentImportChild'])->name('syncRefillShipmentImportChild');
Route::get('sync-care-kit-import-child', [SyncController::class, 'syncCareKitImportChild'])->name('syncCareKitImportChild');
Route::get('sync-care-kit-item-import-child', [SyncController::class, 'syncCareKitItemImportChild'])->name('syncCareKitItemImportChild');
Route::get('sync-prescriber-import-child', [SyncController::class, 'syncPrescribersImportChild'])->name('syncPrescribersImportChild');
Route::get('sync-drugs-import-child', [SyncController::class, 'syncDrugsImportChild'])->name('syncDrugsImportChild');
Route::get('sync-patients-import-child', [SyncController::class, 'syncPatientsImportChild'])->name('syncPatientsImportChild');
Route::get('create-refill-orders-tiff-pdf', [SyncController::class, 'createTiffAndPdf'])->name('createTiffAndPdf');

