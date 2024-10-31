<?php

use App\Http\Controllers\AddonCategoryController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\AdminBranchController;
use App\Http\Controllers\AdminCompanyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BusinessPoliciesController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DemandController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EmployeeSecurityDepositController;
use App\Http\Controllers\EobiController;
use App\Http\Controllers\ExcelExportController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HrProductController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\Inventory_DepartmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryDealController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosProductController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\purchaseController;
use App\Http\Controllers\ReceivedDemandController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ServiceProviderOrderController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SideBarController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UnitofmeasureController;
use App\Http\Controllers\UserDetailsController;
use App\Http\Controllers\VariationController;
use App\Http\Controllers\VariationProductController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VendorController;
use App\Livewire\Customers\CustomerList;
use App\Livewire\Inventory\Inventory;
use App\Livewire\StockAdjustment\ListAdjustment;
use App\Livewire\StockReport;
use App\Livewire\ViewInventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

Route::get('/storage-link', function () {
    $targetFolder = storage_path("app/public");
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . "/storage";
    symlink($targetFolder, $linkFolder);
});

Auth::routes();
Route::get('/horizontal', function () {
    $pages = DB::table("pages_details")->get();
    return view("Test.index", compact('pages'));
});
// Route::get('/','HomeController@loginPage');
Route::get('/', function () {
    if (session("branch") == "") {
        return view('auth.login');
    } else {
        return redirect("dashboard");
    }
});

Route::group(['middleware' => ['auth', 'roleChecker']], function () {
    Route::get('/impersonate/{userId}', [ImpersonateController::class, 'impersonate'])->name('impersonate');
    Route::get('/impersonate/leave', [ImpersonateController::class, 'leave'])->name('impersonate.leave');
});

Route::get('/view-inventory', ViewInventory::class);
Route::get('/view-adjustments', ListAdjustment::class);
Route::get('/stock-report', StockReport::class);
Route::get('/customers-list', CustomerList::class);

Route::resource('addons', AddonController::class);
Route::resource('addon-category', AddonCategoryController::class);
Route::post('/addons/update', [AddonController::class, "update"])->name('addons.update');
Route::post('/addons/delete', [AddonController::class, "destroy"])->name('addons.delete');
Route::post('/addons/category/update', [AddonCategoryController::class, "update"])->name('addon-category.update');
Route::post('/addons/category/delete', [AddonCategoryController::class, "destroy"])->name('addon-category.delete');
Route::post('/get-products-by-department', [AddonCategoryController::class, 'getProductByDepartment'])->name('get.products.by.department');

Route::get('/blogin', 'TestController@login');
Route::get('/border', 'TestController@create_order');

// Admin panel //
Route::get('/admin', [AdminController::class, "showLoginForm"])->name('admin.login');
Route::post('/admin/login', [AdminController::class, "loginsubmit"])->name('admin.login.submit');
Route::get('/admin/dashboard', [AdminController::class, "index"])->name('admin.home');
Route::post('/admin/logout', [AdminController::class, "logout"])->name('admin.logout');

/******************************* BARCODE PRINTING STARTS FROM HERE *******************************/
Route::post('/printBarcode', [PrintController::class, 'printBarcode']);
Route::get('/print/{receipt}', [PrintController::class, 'index']);
Route::get('/voucher/{receipt}', [PrintController::class, 'printVoucher']);
Route::get('/try/{receipt}', [PrintController::class, 'try']);
/******************************* 60 x 40 *******************************/
Route::get('/6040single', [PrintController::class, 'labelPrintingsixtyforty']);
/******************************* 19 x 28 *******************************/
Route::get('/1928single', [PrintController::class, 'labelPrinting1928']);
Route::get('/1928double', [PrintController::class, 'labeldoublePrinting1928']);
Route::get('/1928tripple', [PrintController::class, 'labeltripplePrinting1928']);
/******************************* 38 x 28 *******************************/
Route::get('/3828single', [PrintController::class, 'labelPrinting3828']);
/******************************* 40 x 20 *******************************/
Route::get('/4020single', [PrintController::class, 'labelPrinting4020']);
Route::get('/4020double', [PrintController::class, 'labelPrintingdouble4020']);
/******************************* BARCODE PRINTING ENDS HERE **********************************/
// EXCEL EXPORTS
Route::get('/export-vendor-ledger/{vendor?}/{first?}/{second?}', [ExcelExportController::class, 'VendorLedgerReportExport']);
Route::get('/export-customer-ledger/{customer?}/{first?}/{second?}', [ExcelExportController::class, 'CustomerLedgerReportExport']);
Route::get('/export-customer-balance', [ExcelExportController::class, 'CustomerBalances']);
Route::get('/export-isdb', [ExcelExportController::class, 'ItemSalesDatabaseReportInExcel']);
Route::get('/export-fbr', [ExcelExportController::class, 'FbrReportExcel']);
Route::get('/export-invoice/{id}', [ExcelExportController::class, 'receiptExport']);
Route::get('reports/pdf-export-item-sale-report-date-wise', [ExcelExportController::class, 'ItemSalesDatabaseReportDatewiseInExcel'])->name('pdfExportItemSalesDateWise');

//Discount
Route::get('/get-discount/{mode?}', [DiscountController::class, 'index']);
Route::post('/remove-discount', [DiscountController::class, 'inactiveDiscount']);
Route::post('/reactive-discount', [DiscountController::class, 'reactiveDiscount'])->name('reactiveDiscount');
Route::get('/create-discount', [DiscountController::class, 'create']);
Route::get('/edit-discount/{id}', [DiscountController::class, 'edit']);
Route::get('/load-department', [DiscountController::class, 'loadDepartment']);
Route::get('/load-products', [DiscountController::class, 'loadProducts']);
Route::get('/load-products-for-dropdown', [DiscountController::class, 'loadProductsForDropdown']);
Route::get('/load-products-for-dropdown-edit', [DiscountController::class, 'loadProductsForDropdownEdit']);
Route::get('/load-products-by-search', [DiscountController::class, 'loadProductsBySearch']);
Route::get('/load-customers', [DiscountController::class, 'loadCustomers']);
Route::post('/save-discount', [DiscountController::class, 'saveDiscount']);
Route::post('/update-discount', [DiscountController::class, 'updateDiscount']);
Route::post('/get-discount-info', [DiscountController::class, 'getDiscountInfo']);
Route::post('/get-discount-categories', [DiscountController::class, 'getDiscountCategories']);
Route::post('/get-discount-products', [DiscountController::class, 'getDiscountProducts']);
Route::post('/get-discount-customers', [DiscountController::class, 'getDiscountCustomers']);
Route::post('/get-customer-buys', [DiscountController::class, 'getCustomerBuys']);
Route::post('/get-customer-gets', [DiscountController::class, 'getCustomerGets']);




//Sidebar operations
Route::get('/pages', [sideBarController::class, "index"]);
Route::post('/insert-page', [sideBarController::class, "store"]);
Route::put('/remove-page', [sideBarController::class, "remove"]);
Route::post('/update-page', [sideBarController::class, "update"]);
Route::get('/roles', [sideBarController::class, "role_index"]);
Route::post('/insert-role', [sideBarController::class, "insertRole"]);
Route::get('/getbyroleid', [sideBarController::class, "getbyroleid"]);
Route::put('/deletepagesetting', [sideBarController::class, "deletepagesetting"]);
Route::get('/getpageschild', [sideBarController::class, "getpageschild"]);

//company vise modules settings
Route::middleware(['roleChecker'])->group(function () {

    Route::get('/modules-permissions', [SettingsController::class, "index"]);
    Route::post('/insert-modules', [SettingsController::class, "store"]);
    Route::get('/getbycompanyid', [SettingsController::class, "getbycompanyid"]);
    Route::put('/deletemodules', [SettingsController::class, "deletemodules"]);

    // admin company//
    Route::resource('company', AdminCompanyController::class);
    Route::post('/insert-company', [AdminCompanyController::class, 'store']);
    Route::post('/delete-company', [AdminCompanyController::class, 'destroy']);
    Route::get('/company-edit/{id}', [AdminCompanyController::class, 'edit']);
    Route::post('/update-company', [AdminCompanyController::class, 'update']);

    // admin branch//
    Route::get('/view-branch', [AdminBranchController::class, 'index']);
    Route::get('/create-branch', [AdminBranchController::class, 'create']);
    Route::post('/submit-branch', [AdminBranchController::class, 'store']);
    Route::post('/remove-branch', [AdminBranchController::class, 'destroy']);
    Route::get('/edit-branch/{id}', [AdminBranchController::class, 'edit']);
    Route::post('/update-branch', [AdminBranchController::class, 'update']);

    // admin users//
    Route::get('/view-users', [AdminUsersController::class, 'index']);
    Route::get('/create-users', [AdminUsersController::class, 'create']);
    Route::post('/get-branches', [AdminUsersController::class, 'getBranches']);
    Route::post('/store-users', [AdminUsersController::class, 'store']);
    Route::post('/check-user', [AdminUsersController::class, 'chk_user_exists']);
    Route::post('/delete-user', [AdminUsersController::class, 'destroy']);
    Route::get('/edit-users/{id}', [AdminUsersController::class, 'edit']);
    Route::post('/update-users', [AdminUsersController::class, 'update']);

    //Company
    Route::get('/companies', [CompanyController::class, 'show']);
    Route::get('/createcompany', [CompanyController::class, 'index']);

    Route::get('/create-permission', [UserDetailsController::class, 'sales_permission_insert']);
    Route::get('/permission/{id}', [UserDetailsController::class, 'sales_permission']);

    //Terminals
    Route::get('/terminals', [TerminalController::class, 'view']);
    Route::post('/submitterminal', [TerminalController::class, 'store']);
    Route::put('/inactive-terminal', [TerminalController::class, 'remove']);
    Route::post('/inactive-terminals-details', [TerminalController::class, 'inactivedetails']);
    Route::put('/reactive-terminal', [TerminalController::class, 'reactive']);
    Route::post('/update-terminal', [TerminalController::class, 'update']);

    // TERMINALS
    Route::get('/terminals/{id}', [TerminalController::class, 'view']);
    Route::get('/bind-terminals/{id}/{branch}', [TerminalController::class, 'bindTerminals']);
    Route::post('/save-bind-terminals', [TerminalController::class, 'saveBindTerminal']);
    Route::post('/delete-bind-terminals', [TerminalController::class, 'deleteBindTerminal']);
    Route::get('/printing-details/{id}', [TerminalController::class, 'getPrintingDetails']);
    Route::post('/store-printer-details', [TerminalController::class, 'storePrintDetails']);

    // BRANCHES
    Route::get('/branches', [BranchController::class, 'show']);
    Route::get('/createbranch', [BranchController::class, 'index']);
    Route::post('/submitbranch', [BranchController::class, 'store']);
    Route::put('/removebranch', [BranchController::class, 'remove']);
    Route::get('/branch-edit/{id}', [BranchController::class, 'edit']);
    Route::post('/updatebranch', [BranchController::class, 'update']);
    Route::get('/branch-emails/{id}', [BranchController::class, 'getEmail']);
    Route::post('/save-email', [BranchController::class, 'saveEmail']);
    Route::post('/delete-email', [BranchController::class, 'deleteEmail']);
});
Route::get('/send-report', [ReportController::class, 'generatedSystematicReport']);


Route::get('/send-logout-notification/{id}', 'UserDetailsController@sendPushNotificationToUserDevice');
Route::middleware(['statusCheck'])->group(function () {
    Route::get('/dashboard', [HomeController::class, "index"])->name('home');
    Route::post('/getTerminals', [HomeController::class, 'getTerminalsByBranch']);
    Route::get('/sales-details', [HomeController::class, 'salesDetails']);
    Route::post('/heads-details', [HomeController::class, 'salesHead']);
    Route::post('/heads', [HomeController::class, 'heads']);
    Route::post('/last-day-heads', [HomeController::class, 'lastDayHeads']);
    Route::get('/getcheques', [HomeController::class, 'cheques_notify']);
    Route::get('/order-notify', [HomeController::class, 'getUnseenOrders'])->name('order-notify');
    Route::get('/due-date-orders', [HomeController::class, 'getDueDateOrders'])->name('due-date-orders');
    Route::post('/close-terminal', [HomeController::class, 'closeTerminal']);
    Route::post('/open-terminal', [HomeController::class, 'salesOpening']);
    Route::post('/save-token', [HomeController::class, 'saveToken'])->name("save-token");
    Route::get('/send-notification', [HomeController::class, 'sendNotification'])->name('send.notification');
    Route::get('/logout', [HomeController::class, 'logout']);
    Route::post('/get-close-declarations', [HomeController::class, 'getCloseTerminalDeclarationNumber']);


    // SALES SHOW //
    Route::get('/sales-show/{id}/{terminal}/{mode}', [HomeController::class, 'getDetailByMode']);

    // vendors module //
    Route::resource('vendors', VendorController::class);
    Route::post('/get-vendors-by-product', [VendorController::class, 'getVendorsByProduct']);
    Route::post('/vendoremail', [VendorController::class, 'emailcheck']);
    Route::post('/vendorname', [VendorController::class, 'namecheck']);
    Route::put('/all-vendors-remove', [VendorController::class, 'all_vendoremove']);
    Route::get('/ledgerlist/{id}', [VendorController::class, 'LedgerDetails']);
    Route::post('/polist', [VendorController::class, 'getPO']);
    Route::get('/create-payment/{id}', [VendorController::class, 'createPayment']);
    Route::post('/make-payment', [VendorController::class, 'makePayment']);
    Route::post('/add-into-ledger', [VendorController::class, 'addIntoLedger']);
    Route::post('/get-cash-balance', [VendorController::class, 'LastCashBalance']);
    Route::post('/make-cash-payment', [VendorController::class, 'make_cash_payment']);
    Route::post('/debit-payment', [VendorController::class, 'debitPayment']);
    Route::post('/get-bank-balance', [VendorController::class, 'checkLastBalance']);
    Route::post('/add-credit-bank', [VendorController::class, 'creditBank']);
    Route::post('/vendor-payment', [VendorController::class, 'vendorPayment']);
    Route::post('/vendor-payment-details', [VendorController::class, 'vendorPaymentDetails']);
    Route::post('/vendor-report-filter', [VendorController::class, 'vendor_report_filter']);
    Route::get('/payable', [VendorController::class, 'exportpDF']);
    Route::get('/voucher', [VendorController::class, 'voucher']);
    Route::get('/voucher/{id}', [VendorController::class, 'voucher']);
    Route::get('/profit-and-loss', [VendorController::class, 'profitLoss']);
    Route::get('/profit-and-loss-panel', [VendorController::class, 'profitPanel']);
    Route::post('/add-city', [VendorController::class, 'addCity']);
    Route::post('/active-vendor', [VendorController::class, 'activeVendor']);
    Route::get('/adjustment', [VendorController::class, 'adjustment']);
    Route::get('/add-vendor-product/{id}', [VendorController::class, 'addVendorProduct']);
    Route::get('/search-vendor-product', [VendorController::class, 'searchVendorProduct'])->name('search-vendor-product');
    Route::any('/search-vendor-by-names', [VendorController::class, 'get_vendor_names'])->name('search-vendor-by-names');
    Route::post('/save-vendor-product', [VendorController::class, 'saveProduct']);
    Route::get('/getVendorProduct', [VendorController::class, 'getVendorProduct']);
    Route::post('/inactive-vendor-product', [VendorController::class, 'inactive']);
    Route::post('/active-vendor-product', [VendorController::class, 'active']);
    Route::get('/vendor-po/{id}', [VendorController::class, 'getVendorPo']);
    Route::get('/vendor-ledger-report/{id}/{from}/{to}', [VendorController::class, 'ledgerPDF']);
    Route::post('/editvendornarration', [VendorController::class, 'editvendornarration']);
    Route::any('/vendor-payable', [VendorController::class, 'vendor_payable'])->name('vendor-payable');
    Route::any('/get-vendor-payable', [VendorController::class, 'get_vendor_payable'])->name('get-vendor-payable');

    Route::get('/vendor-payment-view', [VendorController::class, 'VendorPaymentView'])->name('vendor-payment-view');
    Route::get('/get-vendor-payments', [VendorController::class, 'getVendorPayment'])->name('get-vendor-payment');
    Route::post('/update-vendor-payment-due-date', [VendorController::class, 'updateVendorPaymentDueDate'])->name('update-vendor-payment-due-date');
    Route::post('/vendor-payment-history', [VendorController::class, 'vendorPaymentHistory'])->name('vendor-payment-history');

    Route::get('/advance-payment-view/{id}', [VendorController::class, 'adavancePaymentView'])->name("advance-payment-view");
    Route::post('/save-advance-payment', [VendorController::class, 'saveAdvancePayment']);
    Route::get('/get-advance-payment', [VendorController::class, 'getAdvancePayments']);

    //Route::delete('vendors/{id}', 'VendorController@destroy')->name('vendors.destroy');

    //PROMOTION
    Route::get('/promotion', [PromoController::class, 'index']);
    Route::get('/create-promotion', [PromoController::class, 'create']);
    Route::get('/get-customers-by-branch', [PromoController::class, 'getCustomerByBranch']);
    Route::post('/promo-save', [PromoController::class, 'store']);

    //SMS
    Route::get('/view-sms', [SMSController::class, 'view']);
    Route::post('/insert-sms', [SMSController::class, 'store']);
    Route::get('/getsmsdetails', [SMSController::class, 'getdetails']);
    Route::post('/update-smsdetails', [SMSController::class, 'update']);
    Route::post('/update-smsgeneral', [SMSController::class, 'updategeneral']);
    Route::put('/inactive-number', [SMSController::class, 'inactivenumber']);
    Route::put('/inactive-all', [SMSController::class, 'inactiveall']);
    Route::get('/inactivedetails', [SMSController::class, 'inactivedetails']);
    Route::put('/reactive', [SMSController::class, 'reactive']);




    //Delivery Managment System
    Route::any('serviceProviderLedgerPDF', [DeliveryController::class, 'serviceProviderLedgerPDF']);
    Route::any('edit-delivery-narration', [DeliveryController::class, 'updateDeliveryNarration']);
    Route::any('edit-additional-charge', [DeliveryController::class, 'updateAdditionalCharge']);
    // Route::get('/delivery-charges', [DeliveryController::class, 'view']);
    // Route::post('/insert-charges', [DeliveryController::class, 'store']);
    // Route::post('/update-charges', [DeliveryController::class, 'update']);
    // Route::PUT('/inactive-charges', [DeliveryController::class, 'inactivecharges']);
    // Route::get('/inacive-delivery-charges', [DeliveryController::class, 'inactive']);
    // Route::PUT('/reactive-charges', [DeliveryController::class, 'reactive']);
    Route::post('/store-category', [DeliveryController::class, 'store_category']);
    Route::get('/service-provider', [DeliveryController::class, 'show']);
    Route::get('/service-provider-create', [DeliveryController::class, 'show_create']);
    Route::post('/insert-serviceprovider', [DeliveryController::class, 'storeserviceprovider']);
    Route::get('/service-provider-ledger/{id}', [DeliveryController::class, 'providerledger']);
    Route::post('/insert-ledger', [DeliveryController::class, 'store_ledger']);
    Route::PUT('/inactive-serviceprovider', [DeliveryController::class, 'inactiveprovider']);
    Route::get('/inacive-getserviceprovider', [DeliveryController::class, 'getinactiveprovider']);
    Route::PUT('/reactive-serviceprovider', [DeliveryController::class, 'reactiveprovider']);
    Route::post('/store-percentage', [DeliveryController::class, 'store_per']);
    Route::get('/service-provider-edit/{id}', [DeliveryController::class, 'edit']);
    Route::post('/chk-serviceprovider-name', [DeliveryController::class, 'checkServiceProviderName']);
    Route::post('/update-serviceprovider', [DeliveryController::class, 'updateserviceprovider']);
    Route::get('/mobile-promotion', [DeliveryController::class, 'mobilePromotion']);
    Route::post('/insert-mobile-images', [DeliveryController::class, 'insertMobilePromotion'])->name("insert-mobile-images");
    Route::post('/delete-mobile-image', [DeliveryController::class, 'mobilePromoImageDelete']);



    // purchase module //
    Route::get('get-purchase', [purchaseController::class, 'get_purchaseData'])->name('get-purchase');
    Route::get('/add-purchase', [purchaseController::class, 'add_purchaseForm'])->name('add-purchase');
    Route::get('/view-purchases', [purchaseController::class, 'ViewPurchase'])->name('view-purchases');
    Route::post('/insert-purchases', [purchaseController::class, 'addPurchase'])->name('po-insert');
    Route::post('/create-purchases', [purchaseController::class, 'firstInsert'])->name('po-create');
    Route::post('/purchases', [purchaseController::class, 'secondInsert'])->name('create-po');
    Route::post('/getProduct', [purchaseController::class, 'get_items'])->name('getProduct');
    Route::post('/getAccounts', [purchaseController::class, 'accounts'])->name('getAccounts');
    Route::get('/getPurchaseMax', [purchaseController::class, 'getMaxId'])->name('getPurchaseMax');
    Route::post('/updateitems', [purchaseController::class, 'UpdateItems'])->name('updateitem');
    Route::post('/AccDetails', [purchaseController::class, 'getAccDetails'])->name('AccDetails');
    Route::post('/FinalSubmit', [purchaseController::class, 'finalSubmit'])->name('FinalSubmit');
    Route::get('/view/{id}/', [purchaseController::class, 'viewPO'])->name('view');
    Route::get('/receive/{id}/', [purchaseController::class, 'receivepo'])->name('receive');
    Route::post('/create-grn', [purchaseController::class, 'createGRN']);
    Route::post('/add-grn', [purchaseController::class, 'addGrn'])->name('add-grn');
    Route::get('/edit/{id}/', [purchaseController::class, 'edit'])->name('edit');
    Route::get('/return/{id}/', [purchaseController::class, 'return'])->name('return');
    Route::post('/returnInsert', [purchaseController::class, 'insertReturn'])->name('returnInsert');
    Route::post('/AccountUpdate', [purchaseController::class, 'UpdateAccounts'])->name('AccountUpdate');
    Route::post('/changeStatusPo', [purchaseController::class, 'changePOStatus'])->name('changeStatusPo');
    Route::post('/getReceive', [purchaseController::class, 'getReceiveItems'])->name('getReceive');
    Route::post('/get', [purchaseController::class, 'getGRNStock'])->name('get');
    Route::get('/update-status-po/{id}', [purchaseController::class, 'updatePOStatus']);
    Route::get('/grn-details/{id}', [purchaseController::class, 'grnDetails']);
    Route::post('/DetailsOfGrn', [purchaseController::class, 'DetailsOfGrn']);
    Route::get('/DownloadPDF/{id}/', [purchaseController::class, 'DownloadPDF']);
    Route::post('/DeletePurchaseItems', [purchaseController::class, 'DeletePurchaseItems']);
    Route::post('/Draft', [purchaseController::class, 'PurchaseDraft']);
    Route::post('/DeletePO', [purchaseController::class, 'DeletePurchaseOrder']);
    // Route::get('/exportPDF', 'purchaseController::class,exportpDF');
    Route::get('/purchasereport/{id}', [purchaseController::class, 'purchasereport']);




    // inventory module //
    Route::get('/create-inventory', [InventoryController::class, "create"])->name('create-invent');
    Route::get('/inventory-list', [InventoryController::class, "index"])->name('invent-list');
    Route::post('/get-inventory-department_wise', [InventoryController::class, "getDeparmtent_wise_Inventory"])->name('invent-list-department');
    Route::get('/edit-invent/{id}/', [InventoryController::class, 'getData'])->name('edit-invent');
    Route::post('/insert-inventory', [InventoryController::class, 'insert'])->name('insert');
    Route::post('/update-inventory', [InventoryController::class, 'modify'])->name('update');
    Route::post('/getSubdepartBydepartID', [InventoryController::class, 'getSubDepart'])->name('getSubdepart');
    Route::post('/delete-invent', [InventoryController::class, 'deleteInvent']);
    Route::post('/multiple-active-invent', [InventoryController::class, 'multipleActiveInvent']);
    Route::post('/chk-itemcode', [InventoryController::class, 'chk_itemcode_exists']);
    Route::get('/stock-opening', [InventoryController::class, 'stockopening']);
    Route::post('/get-uom-id', [InventoryController::class, 'getUOMID']);
    Route::post('/insert-stock-opening', [InventoryController::class, 'create_stock_opening']);
    Route::post('/uploadInventory', [InventoryController::class, 'uploadInventory']);
    Route::post('/all_invent_remove', [InventoryController::class, 'all_invent_remove']);
    Route::post('/all_invent_delete', [InventoryController::class, 'all_invent_delete']);
    Route::post('/update_product_department', [InventoryController::class, 'update_department']);
    Route::post('/update_product_subdepartment', [InventoryController::class, 'update_sub_department']);
    Route::post('/update_product_uom', [InventoryController::class, 'update_uom']);
    Route::post('/update_product_tax', [InventoryController::class, 'update_tax']);
    Route::post('/get_departments', [InventoryController::class, 'get_departments']);
    Route::post('/get_sub_departments', [InventoryController::class, 'get_sub_departments']);
    Route::post('/get_uom', [InventoryController::class, 'get_uom']);
    Route::post('/get_taxes', [InventoryController::class, 'get_taxes']);
    Route::post('/get_names', [InventoryController::class, 'get_names']);
    Route::post('/insertnewprice', [InventoryController::class, 'insertnewprice']);
    Route::post('/sunmi-cloud', [InventoryController::class, 'sunmiCloud']);
    Route::get('/get-inventory-pagewise', [InventoryController::class, 'getInventory']);
    Route::get('/get-inventory-by-page', [InventoryController::class, 'fetchData']); // this is used for Pagination
    Route::get('/get-inactive-inventory', [InventoryController::class, 'getInactiveInventory']);
    Route::get('/get-non-stock-inventory', [InventoryController::class, 'getNonStockInventory']);
    Route::get('/get-inventory-by-name', [InventoryController::class, 'getInventoryByName']);
    Route::get('/get-inactive-inventory-by-search', [InventoryController::class, 'getInactiveInventoryBySearch']);
    Route::get('/get-inventory-by-subdepartment', [InventoryController::class, 'getproducts']);
    Route::get('/stockadjustment', [InventoryController::class, 'stockadjustment_show']);
    Route::get('/getstock_value', [InventoryController::class, 'getstock_value']);
    Route::get('/getgrns', [InventoryController::class, 'getgrns']);
    Route::post('/updatestockadjustment', [InventoryController::class, 'update_stockadjustment']);
    Route::post('/delete-image', [InventoryController::class, 'getDeleteImage']);
    Route::post('/creategrnadjustmnet', [InventoryController::class, 'creategrnadjustmnet']);
    Route::get('/getcsv', [InventoryController::class, 'exportCsv']);
    Route::get('/get-export-csv-for-retail-price', [InventoryController::class, 'exportInventoryRetailPriceUpdateCsv']);
    Route::post('/uploadStockOpening', [InventoryController::class, 'uploadStockCsv']);
    Route::get('/display-inventory', [InventoryController::class, 'displayInventory']);
    Route::get('/get-sample-csv', [InventoryController::class, 'exportSampleInventoryCsv']);
    Route::get('/display-inventory', [InventoryController::class, 'displayInventory']);
    Route::get('/fetch-inventory-data', [InventoryController::class, 'fetch_data']);
    Route::post('/change-inventory-status', [InventoryController::class, 'changeInventoryStatus']);
    Route::get('/generate-inventory-slug', [InventoryController::class, 'test']);
    Route::any('/search-inventory', [InventoryController::class, 'get_product_names'])->name("search-inventory");
    Route::get('/test-invent', [InventoryController::class, 'test']);
    Route::post('/assign-product-to-vendors', [InventoryController::class, 'assignProductToVendors']);
    Route::get('/test-notification/{code}/{mode}/{message}', [InventoryController::class, 'sendPushNotificationForPermission']);
    Route::post('/get-product-code', [InventoryController::class, 'autoGenerateCode']);
    Route::post('/inventory/getProduct_attribute', [InventoryController::class, 'getProduct_attribute'])->name('getProduct_attribute');
    Route::post('/inventory/product-attribute-insert', [InventoryController::class, 'insertProduct_attribute'])->name('insertProduct_attribute');
    Route::post('/inventory/product-attribute-update', [InventoryController::class, 'setProductAttribute_update'])->name('setProductAttribute_update');
    Route::post('/inventory/update-product-tags', [InventoryController::class, 'updateProductTags'])->name('updateProductTags');
    Route::post('/product-unlink-from-website', [InventoryController::class, 'unLink_websiteProduct'])->name('website_product_unlink');
    Route::post('/all-product-unlink-from-website', [InventoryController::class, 'allWebsiteProduct_unlink'])->name('all_product_unlink_website');
    Route::get('/get-pos-orders', [OrderController::class, 'getPOSOrders']);
    Route::get('/get-pos-filter-orders', [OrderController::class, 'getPOSFilterOrders']);
    Route::get('/order-detail/{id}', [OrderController::class, 'orderdetails'])->name("order.details");
    Route::get('/change-order-status/{id}/{status}', [OrderController::class, 'orderStatusChange']);
    Route::get('/sent-to-workshop/{id}/{itemId}', [OrderController::class, 'sentToWorkshop']);
    Route::get('/change-item-status/{id}/{itemId}/{status}', [OrderController::class, 'changeItemStatus']);

    Route::get('/inventory/{id}/variable-products', [InventoryController::class, 'createVariableProduct'])->name('createVariableProduct');
    Route::post('/inventory/variable-products/get', [InventoryController::class, 'get_variableProduct'])->name('getVariableProduct');
    Route::post('/inventory/variable-products/get-general-item', [InventoryController::class, 'get_generalItem'])->name('get_generalItem');
    Route::post('/inventory/variable-products/auto-code-generate', [InventoryController::class, 'autoGenerateCode_variableProduct'])->name('autoGenerateCode_variableProduct');
    Route::post('/inventory/store-variable-products', [InventoryController::class, 'storeVariableProduct'])->name('storeVariableProduct');
    Route::post('/inventory/update-variable-products', [InventoryController::class, 'updateVariableProduct'])->name('updateVariableProduct');
    Route::post('/inventory/remove-variable-products', [InventoryController::class, 'deleteVariableProduct'])->name('removeVariableProduct');
    Route::post('/inventory/variable-products/copy-variableproduct-set-to-general-inventory', [InventoryController::class, 'VariableProduct_set_to_generalProduct'])->name('VariableProduct_set_to_generalProduct');

    Route::post('/inventory/variable-products/get-variation', [InventoryController::class, 'reloadVariation_singleProduct'])->name('VariableProduct_reloadVariation');

    Route::post('/inventory/variable-products/set-variation-all-variable-product', [InventoryController::class, 'set_variationAllVariableProduct'])->name('set_variationAllVariableProduct');
    Route::post('/inventory/variable-products/get-variation-values', [InventoryController::class, 'getInventoryVariationProduct_values'])->name('VariableProduct_VariationValues');
    Route::post('get-variation-priority', [InventoryController::class,'get_variationPriority'])->name('get_variationPriority');
    Route::post('/inventory/variable-products/store-variation', [InventoryController::class, 'addVariation'])->name('storeVariableProduct_variation');
    Route::post('/inventory/variable-products/update-variation', [InventoryController::class, 'updateVariation'])->name('updateVariableProduct_variation');
    Route::post('/inventory/variable-products/remove-variation', [InventoryController::class, 'removeVariation'])->name('removeVariation_variableProduct');


    Route::get('/inventory/{id}/deal-products', [InventoryController::class, 'createDealProduct'])->name('createDealProduct');
    Route::post('/inventory/update-deal-product', [InventoryController::class, 'updateDeal_product'])->name('updateDealProduct_up');
    Route::post('/inventory/store-dealproducts', [InventoryController::class, 'storeDeal_product'])->name('storeDeal_product');
    Route::post('/inventory/deal-products/reload', [InventoryController::class, 'reload_dealProducts'])->name('reloadDealProduct');
    Route::post('/inventory/deal-product-remove', [InventoryController::class, 'removeDeal_product'])->name('removeDeal_product');

    Route::post('/inventory/deal-products/addons', [InventoryController::class, 'get_addons_dealProduct'])->name('getAddons_dealProduct');
    Route::post('/inventory/deal-products/store-addon', [InventoryController::class, 'store_addon_dealProduct'])->name('storeAddon_dealProduct');
    Route::post('/inventory/deal-products/store-addon-single-value', [InventoryController::class, 'store_singleValue_addonDealProduct'])->name('store_singleValue_addonDealProduct');
    Route::post('/inventory/deal-products/update-addon', [InventoryController::class, 'update_addonDealProduct'])->name('update_addonDealProduct');
    Route::post('/inventory/deal-products/get-addon-values', [InventoryController::class, 'loadAddonValues'])->name('getAddonValues_dealProduct');
    Route::post('/inventory/deal-products/remove-addon', [InventoryController::class, 'removeAddon_dealProduct'])->name('removeAddon_dealProduct');
    Route::post('/inventory/deal-products/remove-addon-value', [InventoryController::class, 'removeAddonValue_dealProduct'])->name('removeAddonValue_dealProduct');


    Route::post('/inventory/store-addon', [InventoryController::class, 'storeAddon'])->name('storeAddon');
    Route::post('/inventory/store-addon-single-value', [InventoryController::class, 'store_singleValueAddon'])->name('store_singleValueAddon');
    Route::post('/inventory/update-addons', [InventoryController::class, 'updateAddon'])->name('updateAddon');
    Route::post('/inventory/get-addons', [InventoryController::class, 'loadAddons'])->name('loadAddons');
    Route::post('/inventory/get-addon-values', [InventoryController::class, 'loadAddonValues'])->name('loadAddonValues');
    Route::post('/inventory/remove-addon', [InventoryController::class, 'removeAddon'])->name('removeAddon');
    Route::post('/inventory/remove-addon-value', [InventoryController::class, 'removeAddonValue'])->name('removeAddonValue');



    // inventory deal updated
    Route::get('/inventory/deals-debug', [InventoryController::class, 'getInventoryDeals'])->name('getDeal_up');
    Route::post('/inventory/deals-product-value', [InventoryController::class, 'getInventoryDeals_prodValues'])->name('getDeal_prod_values');
    Route::post('/inventory/store-deal', [InventoryController::class, 'storeDeal'])->name('storeDeal_up');
    Route::post('/inventory/store-deal-value', [InventoryController::class, 'singleDealProduct_store'])->name('storeDealValue_single');
    Route::post('/inventory/update-deal', [InventoryController::class, 'updateDeal'])->name('updateDeal_up');
    Route::post('/inventory/remove-deals', [InventoryController::class, 'removeDeal'])->name('removeDeal_up');

    // Inventory Deal
    Route::get('/inventory/deals', [InventoryDealController::class, 'index'])->name('listInventDeal');
    Route::get('/inventory/deal/get-group-values', [InventoryDealController::class, 'getGroups_values'])->name('getGroupValues');
    Route::get('/inventory/deal/create', [InventoryDealController::class, 'create'])->name('dealCreate');
    Route::post('/inventory/deal/store', [InventoryDealController::class, 'store'])->name('dealStore');
    Route::post('/inventory/deal/update', [InventoryDealController::class, 'update'])->name('dealUpdate');
    Route::post('/inventory/deal/detail', [InventoryDealController::class, 'get_deal_detail'])->name('dealDetail');
    Route::post('/inventory/deal/remove', [InventoryDealController::class, 'destroy'])->name('dealRemove');

    // variation module //
    Route::get('/inventory/variations', [VariationController::class, 'index'])->name('listVariation');
    Route::post('/inventory/variation/created', [VariationController::class, 'store'])->name('CreateVariat');
    Route::post('/inventory/variation/single-value-add', [VariationController::class, 'singleVariationAdd'])->name('singleVariationAdd');
    Route::post('/inventory/variation/update', [VariationController::class, 'update'])->name('modifyVariat');
    Route::post('/inventory/variation/update-value', [VariationController::class, 'updateValue'])->name('modifyVariatSubValue');
    Route::delete('/inventory/variation/{id}/remove', [VariationController::class, 'destroy'])->name('DestroyVariat');
    Route::post('/inventory/variation/remove-value', [VariationController::class, 'destroyVariat_value'])->name('DestroyVariat_value');


    // variation product module //
    Route::get('/inventory/variation-products', [VariationProductController::class, 'index'])->name('listVariatProduct');
    Route::get('/inventory/variation-product/create', [VariationProductController::class, 'create'])->name('CreateVariatProd');
    Route::get('/inventory/variation-product/get-variation-values', [VariationProductController::class, 'getVariat_values'])->name('getVariat_values');
    Route::post('/inventory/variation-product/post', [VariationProductController::class, 'store'])->name('storeVariation');
    Route::get('/inventory/variation-product/{id}/edit', [VariationProductController::class, 'edit'])->name('editVariation');
    Route::patch('/inventory/variation-product/{id}/update', [VariationProductController::class, 'update'])->name('updateVariation');
    Route::delete('/inventory/variation-product/{id}/remove', [VariationProductController::class, 'destroy'])->name('removeVariation');
    Route::get('/inventory/variation-product/{filename}/image', [VariationProductController::class, 'imageView'])->name('imageVariatProduct');

    Route::resource('inventory/brands', BrandController::class);

    // Tag
    Route::resource('inventory/tags', TagController::class);

    Route::get('list/inventory', Inventory::class);

    // Orders Module //
    Route::get('/test-query', [OrderController::class, 'testQuery']);
    Route::get('/orders-view', [OrderController::class, 'ordersviewnew']);
    Route::get('/web-orders-view', [OrderController::class, 'webOrders']);
    Route::get('/sales/website-orders-list', [OrderController::class, 'websiteOrders']);
    Route::get('/sales/website-order-detail/{id}', [OrderController::class, 'websiteOrderDetail'])->name('getWebsiteSaleReceiptDetails');
    Route::get('/sales/change-order-status-from-website/{id}/{status}/{ordercode?}', [OrderController::class, 'statusUpdate_websiteOrder']);
    Route::post('/sales/change-website-order-status', [OrderController::class, 'statusUpdate_websiteOrder']);
    Route::post('/sales/check-website-order', [OrderController::class, 'checkwebsiteOrders'])->name('checkwebsiteOrders');
    Route::get('/sales/website-orders-filter', [OrderController::class, 'websiteOrdersFilter'])->name('getWebsiteOrderFilter');
    Route::get('/web-orders-filter', [OrderController::class, 'webOrdersFilter'])->name('getWebOrderFilter');
    Route::post('/get-orders', [OrderController::class, 'getOrderById']);
    Route::post('/get-web-orders', [OrderController::class, 'getWebOrders']);
    Route::post('/get-terminal', [OrderController::class, 'getTerminal']);
    Route::get('/order-assign/{id}', [OrderController::class, 'orderAssign']);
    Route::post('/uom-by-product', [OrderController::class, 'getUOMByProduct']);
    Route::post('/insert-assign', [OrderController::class, 'insertAssign']);
    Route::post('/update-assign', [OrderController::class, 'updateAssign']);
    Route::post('/get-items', [OrderController::class, 'getitemsByfinished']);
    Route::post('/get-items-details', [OrderController::class, 'getitemsByDetails']);
    Route::post('/get-status-changed', [OrderController::class, 'getstatusChanged']);
    Route::post('/get-items-qty', [OrderController::class, 'getItemQty']);
    Route::post('/get-items-by-receipt', [OrderController::class, 'getReceiptitems']);
    Route::post('/temp-insert-master', [OrderController::class, 'InsertAssignTemp']);
    Route::post('/get-master-by-category', [OrderController::class, 'getMasterByCategory']);
    Route::post('/get-master-pending-orders', [OrderController::class, 'GetMastersPendingOrders']);
    Route::get('/orders-report', [OrderController::class, 'exportPDF']);
    Route::post('/change-order-branch', [OrderController::class, 'changeOrderBranch']);
    Route::post('/change-order-status-with-logs', [OrderController::class, 'changeOrderStatuswithLogs']);
    Route::post('/change-order-status', [OrderController::class, 'changeOrderStatus']);
    Route::post('/order-change-status', [OrderController::class, 'changeOrderStatuswithLogs']);
    Route::post('/order-seen', [OrderController::class, 'orderSeen']);
    Route::post('/assign-service-provider', [OrderController::class, 'assignServiceProvider']);
    Route::post('/assign-sales-person', [OrderController::class, 'assignSalesPerson']);
    Route::get('/orders-view-new', [OrderController::class, 'ordersviewnew']);
    Route::get('/get-pos-orders-new', [OrderController::class, 'getNewPOSOrders']);
    Route::post('/make-receipt-void', [OrderController::class, 'makeReceiptVoid']);
    Route::post('/make-receipt-delivered', [OrderController::class, 'makeReceiptDelivered']);
    Route::post('/get-service-Provider-by-branch', [OrderController::class, 'getServiceProviderByBranch'])->name("sp.branch");
    Route::get('/send-email', [OrderController::class, 'sendEmail']);

    // SERVICE PROVIDERS ORDERS
    Route::get('/service-providers-orders', [ServiceProviderOrderController::class, 'index']);
    Route::post('/service-providers-orders', [ServiceProviderOrderController::class, 'getServiceProviderOrders']);
    Route::post('/update-service-providers', [ServiceProviderOrderController::class, 'updateServiceProvider']);
    Route::post('/update-order-status', [ServiceProviderOrderController::class, 'updateOrderStatus']);
    Route::post('/service-providers-orders-assign', [ServiceProviderOrderController::class, 'AssignOrders'])->name("sp.assign");
    Route::post('/service-providers-drivers-assign-orders', [ServiceProviderOrderController::class, 'getDriverOrders'])->name("driver.assign");
    Route::post('/service-providers-drivers-assign-orders-details', [ServiceProviderOrderController::class, 'getDriversItems'])->name("driver.details");
    Route::post('/save-item-narration', [ServiceProviderOrderController::class, 'saveNarration'])->name("save.narration");


    // inventory department module //
    Route::resource('invent_dept', Inventory_DepartmentController::class);
    Route::get('/create-department', [Inventory_DepartmentController::class, 'create'])->name('departmentCreate');
    Route::put('/invent-depart-modify', [Inventory_DepartmentController::class, 'depart_update'])->name('invent_deptup');
    Route::post('/invent-sbdepart-modify', [Inventory_DepartmentController::class, 'sb_depart_update'])->name('invent_sb_deptup');
    Route::post('/adddepartment', [Inventory_DepartmentController::class, 'adddepartment']);
    Route::post('/deletedepartment', [Inventory_DepartmentController::class, 'deleteDepartment']);
    Route::post('/addsubdepart', [Inventory_DepartmentController::class, 'addsubdepartment']);
    Route::post('/updatedepart', [Inventory_DepartmentController::class, 'updatedepart']);
    Route::get('/getsubdepart', [Inventory_DepartmentController::class, 'getsubdepart']);
    Route::post('/delete-subdepartment', [Inventory_DepartmentController::class, 'remove_sbdept']);
    Route::post('/department-connect-website', [Inventory_DepartmentController::class, 'departmentWebsiteConnect'])->name('department_website_connect');

    // sections
    Route::resource('inventory/department/sections', SectionController::class);

    //Inventory Stock
    Route::get('/stock-list', [StockController::class, 'index']);
    Route::get('/stock-details/{id}', [StockController::class, 'getStock']);
    Route::get('/branchwise-stock', [StockController::class, 'brnchwisestock']);
    Route::get('/stockReportPDF', [StockController::class, 'stockReportPDF']);
    Route::post('/stockFilter', [StockController::class, 'stockFilter']);

    //Inventory Stock Transfer
    Route::get('stock-tranfer', [StockController::class, 'getStockForTransfer']);
    Route::post('save-stock-tranfer', [StockController::class, 'saveStockTransfer']);
    Route::get('terminal-stock', [StockController::class, 'getTerminalStock']);
    Route::post('terminal-stock', [StockController::class, 'getTerminalStockDetails']);

    //VOUCHER GENERATE
    Route::get('stock-adjustment-voucher/{id}', [StockController::class, 'StockAdjustmentVoucher'])->name("stock.adjustment.voucher");


    // expense module //
    Route::resource('expense', ExpenseController::class);
    Route::post('/getData', [ExpenseController::class, 'getData']);
    Route::post('/delete-expense', [ExpenseController::class, 'deleteExpense'])->name("delete.expense");
    Route::get('/category', [ExpenseController::class, 'getCategories']);
    Route::get('/tax', [ExpenseController::class, 'getTax']);
    Route::post('/modifyExpense', [ExpenseController::class, 'modify'])->name('updatexp');
    Route::get('/expense-report', [ExpenseController::class, 'expense_report_panel']);
    Route::post('/expense-report-details', [ExpenseController::class, 'expense_report_filter']);
    Route::post('/expense-details-filter', [ExpenseController::class, 'expenseDetailsFilter']);
    Route::get('/expense-report-pdf', [ExpenseController::class, 'generatePDF']);
    Route::get('/expense_voucher', [ExpenseController::class, 'expense_voucher']);

    // expense category module //
    Route::resource('exp_category', ExpenseCategoryController::class);
    Route::put('/expcate-update', [ExpenseCategoryController::class, 'update']);
    Route::post('/expcate_edit', [ExpenseCategoryController::class, 'edit']);
    Route::put('/expcate-update', [ExpenseCategoryController::class, 'update']);


    //Customer Module
    Route::resource('customer', CustomersController::class);
    Route::get('/editcustomers/{id}', [CustomersController::class, 'edit']);
    Route::put('/updatecustomers', [CustomersController::class, 'update']);
    Route::put('/inactivecustomer', [CustomersController::class, 'remove']);
    Route::post('/getCityById', [CustomersController::class, 'getCity']);
    Route::get('/ledgerDetails/{id}', [CustomersController::class, 'LedgerDetails']);
    Route::get('/discount-panel/{id}', [CustomersController::class, 'discountPanel']);
    Route::post('/discount-insert', [CustomersController::class, 'discountInsert']);
    Route::post('/discount-update', [CustomersController::class, 'discountUpdate']);
    Route::post('/get-insert', [CustomersController::class, 'loadDiscount']);
    Route::post('/get-products', [CustomersController::class, 'getProducts']);
    Route::post('/delete-products', [CustomersController::class, 'deleteProduct']);
    Route::get('/measurement/{id}', [CustomersController::class, 'createMeasurement']);
    Route::post('/measurementUpdate', [CustomersController::class, 'measurementUpdate']);
    Route::post('/measurementPantUpdate', [CustomersController::class, 'updatePantMeasurement']);
    Route::get('/customer-report', [CustomersController::class, 'customer_report']);
    Route::post('/customer-report-filter', [CustomersController::class, 'customer_report_filter']);
    Route::get('/receivable', [CustomersController::class, 'exportpDF']);
    Route::post('/uploadFile', [CustomersController::class, 'uploadFile']);
    Route::post('/all_customers_remove', [CustomersController::class, 'all_customers_remove']);
    Route::post('/active-customer', [CustomersController::class, 'activeCustomer']);
    Route::post('/multiple-active-customer', [CustomersController::class, 'multipleactiveCustomer']);
    Route::post('/customer-names', [CustomersController::class, 'get_names']);
    Route::any('/search-customer-by-names', [CustomersController::class, 'get_customer_names'])->name('search-customer-by-names');
    Route::post('/get-order-general', [CustomersController::class, 'getReceiptGeneral']);
    Route::get('/create-customer-payment/{id}', [CustomersController::class, 'createPayment']);
    Route::post('/make-customer-cash-payment', [CustomersController::class, 'make_cash_payment']);
    Route::post('/make-customer-bank-payment', [CustomersController::class, 'make_bank_payment']);
    Route::get('/adjustment-customer', [CustomersController::class, 'adjustment']);
    Route::any('/edit-adjustment-customer', [CustomersController::class, 'editAdjustment']);
    Route::get('/get-customer-receipts/{id}', [CustomersController::class, 'getCustomerReceipts']);
    Route::get('/customer-ledger-report/{id}/{from?}/{to?}', [CustomersController::class, 'ledgerPDF']);
    Route::any('/customer-payment-log', [CustomersController::class, 'customer_payment_log'])->name('customer-payment-log');
    Route::any('/customer-due-date', [CustomersController::class, 'customer_due_date'])->name('customer-due-date');
    Route::any('/customer-due-payment', [CustomersController::class, 'customer_due_payment'])->name('customer-due-payment');
    Route::any('/get-customer-due-payment', [CustomersController::class, 'get_customer_due_payment'])->name('get-customer-due-payment');
    Route::get('/customers-report-pdf', [CustomersController::class, 'customerPDF']);
    Route::get('/test-tcpdf', [CustomersController::class, 'TCPDF']);
    Route::get('/customer-list', [CustomersController::class, 'customerList']);
    Route::post('/mobile-app-status', [CustomersController::class, 'changeMobileAppStatus']);

    //Master
    Route::get('/get-masters', [MasterController::class, 'index']);
    Route::get('/create-master', [MasterController::class, 'create']);
    Route::post('/store-master', [MasterController::class, 'store']);
    Route::put('/updatemasters', [MasterController::class, 'update']);
    Route::post('/remove-master', [MasterController::class, 'remove']);
    Route::get('/ledger-details/{id}', [MasterController::class, 'LedgerDetails']);
    Route::get('/edit-master/{id}', [MasterController::class, 'edit']);
    Route::get('/ledger-payment/{id}', [MasterController::class, 'LedgerPayment']);
    Route::post('/debit-insert', [MasterController::class, 'debitInsert']);
    Route::post('/createPayment', [MasterController::class, 'ledgerInsert']);
    Route::get('/category/{id}', [MasterController::class, 'category']);
    Route::post('/get-categories', [MasterController::class, 'getcategory']);
    Route::post('/addCategory', [MasterController::class, 'insertCategory']);
    Route::post('/get-master', [MasterController::class, 'getMaster']);
    Route::post('/master-rate-insert', [MasterController::class, 'MasterRateInsert']);
    Route::post('/master-rate-list', [MasterController::class, 'getRateList']);
    Route::post('/rate-update', [MasterController::class, 'MasterRateUpdate']);
    Route::post('/get-receipt', [MasterController::class, 'getReceipt']);
    Route::get('/work-load', [MasterController::class, 'workload']);
    Route::get('/work-load/{id}', [MasterController::class, 'workloadDetails']);
    Route::post('/received-from-master', [MasterController::class, 'updateMasterAssign']);
    Route::get('/master-report', [MasterController::class, 'master_report']);
    Route::post('/master-report-filter', [MasterController::class, 'master_report_filter']);
    Route::get('/masterpayable', [MasterController::class, 'exportPDF']);
    Route::get('/workloadreport', [MasterController::class, 'exportWorkLoadPDF']);

    //Demand Module
    Route::get('/demand', [DemandController::class, 'index']);
    Route::get('/create-demand', [DemandController::class, 'add_demand']);
    Route::post('/additems', [DemandController::class, 'insert_item_details']);
    Route::post('/viewitems', [DemandController::class, 'get_demandlist']);
    Route::put('/updateitem', [DemandController::class, 'update_qty']);
    Route::delete('/deleteitem', [DemandController::class, 'del_item']);
    Route::put('/updatestatus', [DemandController::class, 'update_status']);
    Route::get('/demand-details/{id}', [DemandController::class, 'show']);
    Route::get('/edit-demand/{id}', [DemandController::class, 'edit']);
    Route::put('/removedemand', [DemandController::class, 'update_status']);
    Route::put('/all-demand-remove', [DemandController::class, 'all_demand_state_up']);
    Route::put('/updatestatusdemand', [DemandController::class, 'update_status']);
    Route::get('/demandorderReport/{id}', [DemandController::class, 'demandorderReport']);


    //Demand Received Module
    Route::get('/received-demand', [ReceivedDemandController::class, 'index']);
    Route::get('/received-demandpanel/{id}', [ReceivedDemandController::class, 'show']);
    Route::put('/update-status', [ReceivedDemandController::class, 'update_status']);
    Route::post('/stock', [ReceivedDemandController::class, 'getstock']);
    Route::post('/transfer', [ReceivedDemandController::class, 'insert']);
    Route::post('/chk', [ReceivedDemandController::class, 'check']);
    Route::put('/updateitemstatus', [ReceivedDemandController::class, 'updatedemanditem']);

    //view transfer order
    Route::get('/view-transfer/{id}', [TransferController::class, 'index']);
    Route::post('/transferordershow', [TransferController::class, 'show']);

    Route::get('/transferlist', [TransferController::class, 'transferlist']);
    Route::get('/createdeliverychallan/{id}', [TransferController::class, 'deliverychallan']);
    Route::post('/stockdetails', [TransferController::class, 'getstock']);
    Route::put('/updatetransferitem', [TransferController::class, 'updatetransferitem']);
    Route::post('/insertdeliverchallan', [TransferController::class, 'insert']);
    Route::put('/updatechallan', [TransferController::class, 'updatechllan']);
    Route::get('/challanlist', [TransferController::class, 'challanlist']);
    Route::get('/challandetails/{id}', [TransferController::class, 'challandetails']);
    Route::get('/createGRN/{id}', [TransferController::class, 'createGRN']);
    Route::post('/submitgrn', [TransferController::class, 'grn_insert']);
    Route::put('/edit_transfer', [TransferController::class, 'edit_transfer']);
    Route::get('/gettransferorders', [TransferController::class, 'gettransferorders']);
    Route::put('/removetransferorder', [TransferController::class, 'removetransferorder']);
    Route::get('/transferReport/{id}', [TransferController::class, 'transferReport']);
    Route::get('/direct-transfer-report/{id}', [TransferController::class, 'directTransferReport']);
    Route::get('/dcreport/{id}', [TransferController::class, 'dcreport']);



    //Direct Transfer Without Demand
    Route::get('/create-transferorder', [TransferController::class, 'create_transferorder']);
    Route::post('/trf_stock', [TransferController::class, 'trf_stock']);
    Route::post('/get_products', [TransferController::class, 'get_products']);
    Route::post('/insert_trf', [TransferController::class, 'insert_trf']);
    Route::get('/trf_details', [TransferController::class, 'trf_details']);
    Route::get('/trf_delete', [TransferController::class, 'trf_delete']);
    Route::put('/trf_change_status', [TransferController::class, 'trf_submit_update']);
    Route::get('/trf_list', [TransferController::class, 'trf_list']);
    Route::get('/trforder_delete', [TransferController::class, 'trforder_delete']);
    Route::get('/get_trf_details/{id}', [TransferController::class, 'get_trf_details']);
    Route::put('/qty_update', [TransferController::class, 'qty_update_trf']);
    Route::post('/insert_direct_chalan', [TransferController::class, 'insert_direct_chalan']);
    Route::get('/edit_trf_details/{id}', [TransferController::class, 'edit_trf_details']);
    Route::post('/get-to-branches', [TransferController::class, 'getToBranches']);

    Route::get('/insert-po/{id}', [TransferController::class, 'getdetails_po']);
    Route::post('/submitpo', [TransferController::class, 'purchaseorder_insert']);
    Route::get('/showtransferdetails/{id}', [TransferController::class, 'show_transferdetails']);

    //ADD BANK
    Route::get('/get-banks', [BankController::class, 'getBanks']);
    Route::get('/create-bank', [BankController::class, 'addNewBank']);
    Route::post('/save-bank', [BankController::class, 'saveNewBank']);
    Route::get('/edit-bank/{id}', [BankController::class, 'editBank']);
    Route::post('/update-bank', [BankController::class, 'updateBank']);
    //Accounts
    Route::get('/bankaccounts-details', [BankController::class, 'index']);
    Route::post('/submitbankdetails', [BankController::class, 'submit_details']);
    Route::post('/createaccount', [BankController::class, 'insert_account']);
    Route::get('/view-accounts', [BankController::class, 'show']);
    Route::get('/create-deposit/{id}', [BankController::class, 'show_deposit']);
    Route::get('/cash-deposit', [BankController::class, 'cash_ledger']);
    Route::post('/cashLedgerDeposit', [BankController::class, 'insert_cashLedger']);
    Route::post('/depositamount', [BankController::class, 'insert_deposit']);
    Route::get('/getaccountdetails/{id}', [BankController::class, 'getdetails']);
    Route::put('/updateaccount', [BankController::class, 'updateaccountdetails']);
    Route::get('/customer-ledger', [CustomersController::class, 'LedgerDetails']);
    Route::post('/ledger-details', [CustomersController::class, 'LedgerDetailsByID']);
    Route::get('/vendor-ledger', [VendorController::class, 'LedgerDetails']);
    Route::get('/vendor-report', [VendorController::class, 'vendor_report_panel']);
    Route::post('/vendor-ledger-details', [VendorController::class, 'LedgerDetailsByID']);
    Route::get('/master-ledger', [MasterController::class, 'LedgerDetails']);
    Route::post('/master-ledger-details', [MasterController::class, 'LedgerPayment']);
    Route::get('/view-cheque', [BankController::class, 'chequeView']);
    Route::post('/insert-cheque', [BankController::class, 'chequeInsert']);
    Route::post('/insert-chequeStatus', [BankController::class, 'chequeStatusInsert']);
    Route::post('/save-chequeClearance', [BankController::class, 'clearance']);
    Route::get('/getdetails-cheque', [BankController::class, 'viewbychequeid']);
    Route::get('/chequemodule/{date}', [BankController::class, 'cheque_module']);
    Route::get('/filterCheques', [BankController::class, 'filter_cheque']);
    Route::post('/editledgernarration', [BankController::class, 'editledgernarration']);
    Route::post('/editbankrnarration', [BankController::class, 'editbankrnarration']);
    Route::get('/cashledgerPDF', [BankController::class, 'cashledgerPDF']);
    Route::get('/bankledgerPDF/{id}', [BankController::class, 'bankledgerPDF']);
    Route::post('/add-vendor-products-from-purchase-order', [VendorController::class, 'saveProductFromPurchaseOrder']);

    //Users
    Route::get('/usersDetails', [UserDetailsController::class, 'index']);
    Route::get('/create-user', [UserDetailsController::class, 'create']);
    Route::post('/store-user', [UserDetailsController::class, 'store']);
    Route::get('/user-edit/{id}', [UserDetailsController::class, 'edit']);
    Route::put('/user-update', [UserDetailsController::class, 'update']);
    Route::put('/user-delete', [UserDetailsController::class, 'delete_user']);
    Route::post('/chk-user', [UserDetailsController::class, 'chk_user_exists']);
    Route::post('/add-role', [UserDetailsController::class, 'addrole']);
    Route::post('/get-branches-by-company', [UserDetailsController::class, 'getBranchesByCompany']);
    Route::post('/change-loggedin-value', [UserDetailsController::class, 'changeLoggedInStatus']);
    // Route::get('/create-user','UserDetailsController@index');

    //uom
    Route::post('/adduom', [UnitofmeasureController::class, 'store']);


    //Business Policy
    Route::get('/BusinessPolicy', [BusinessPoliciesController::class, 'index']);
    Route::get('/Tax-create', [BusinessPoliciesController::class, 'tax_create']);
    Route::post('/tax-insert', [BusinessPoliciesController::class, 'insert_tax']);
    Route::put('/delete_tax', [BusinessPoliciesController::class, 'delete_tax']);
    Route::get('/show-tax/{id}', [BusinessPoliciesController::class, 'show_tax']);
    Route::post('/update-tax', [BusinessPoliciesController::class, 'update_tax']);


    Route::get('/reportdashboard', [ReportController::class, 'show']);
    Route::get('/erpreportdashboard', [ReportController::class, 'erpreportdashboard']);
    Route::get('/profitLossStandardReport', [ReportController::class, 'profitLossStandardReport']);
    Route::get('/profitLossDetailsReport', [ReportController::class, 'profitLossDetailsReport']);
    Route::get('/inventoryReport', [ReportController::class, 'inventoryReport']);
    Route::get('/customer-aging', [ReportController::class, 'customerAgingReport']);
    Route::get('/cash_voucher', [ReportController::class, 'cash_voucher']);
    Route::get('/inventory_detailsPDF', [ReportController::class, 'inventory_detailsPDF']);
    Route::get('/test', [ReportController::class, 'pdfTest']);
    Route::get('/expense_by_categorypdf', [ReportController::class, 'expense_by_categorypdf']);
    Route::get('/salesdeclerationreport', [ReportController::class, 'salesdeclerationreport']);
    Route::get('/itemsaledatabasepdf', [ReportController::class, 'itemsaledatabasepdf']);
    Route::get('/salesreturnpdf', [ReportController::class, 'salesreturnpdf']);
    Route::get('/inventoryReportPhysical', [ReportController::class, 'inventoryReportPhysical']);
    Route::get('/stockAdjustmentReport', [ReportController::class, 'stockAdjustmentReport']);
    Route::get('/fbr-report', [ReportController::class, 'fbrReport']);
    Route::get('/invoice-report', [ReportController::class, 'invoiceReport']);
    Route::get('/sales-invoices-report', [ReportController::class, 'salesInvoicesReport']);
    Route::get('/sales-invoices-report-excel', [ReportController::class, 'salesInvoicesReportExcel']);
    Route::get('/factory-operation-report', [ReportController::class, 'factoryOperationReport']);
    Route::get('/inventory-image-report', [ReportController::class, 'inventoryImageReport']);
    Route::get('/order-booking-report', [ReportController::class, 'orderBookingReport']);
    Route::get('/sales-person-report', [ReportController::class, 'salesPersonReport']);
    Route::get('/website-items-summary', [ReportController::class, 'websiteItemsSummary']);
    Route::get('/order-timings-summary', [ReportController::class, 'orderTimingsSummary']);

    Route::get('reports/item-sale-report', [ReportController::class, 'getIndex'])->name('itemSaleReport');
    Route::post('reports/search-item-sale-report', [ReportController::class, 'getItemSaleReport'])->name('SrchISReport');
    Route::get('reports/consolidated-item-sale-report', [ReportController::class, 'getConsolidatedItemSaleReport'])->name('consolidated.itemSaleReport');
    Route::post('reports/consolidated-item-sale-report', [ReportController::class, 'postConsolidatedItemSaleReport'])->name('consolidated.SrchISReport');
    Route::post('reports/getTerminals', [ReportController::class, 'getTerminals'])->name('getTerminals');
    Route::get('reports/excel-export-item-sale-report', [ReportController::class, 'getItemSaleReportExcelExport'])->name('excelExportItemSales');
    Route::get('reports/consolidated-excel-export-item-sale-report', [ReportController::class, 'getConsolidatedItemSaleReportExcelExport'])->name('excelExportItemSales');
    Route::get('reports/pdf-export-item-sale-report', [ReportController::class, 'getItemSaleReportPdfExport'])->name('pdfExportItemSales');
    Route::get('reports/excel-export-orders-report', [ReportController::class, 'getOrdersReportExcelExport'])->name('excelExportOrders');
    Route::get('reports/excel-export-sales-declartion/{from}/{to}/{branch}/{terminal?}', [ReportController::class, 'getSalesDeclarationExport'])->name('excelExportSalesDeclaration');
    Route::get('reports/excel-export-stock-report',  [ReportController::class, 'getStockReportExcelExport'])->name('excelExportStock');
    Route::get('reports/excel-export-daily-stock-report/{from}/{to}/{branch}/{department?}/{subdepartment?}',  [ReportController::class, 'dailyStockReportExport'])->name('dailyStockReportExport');
    Route::get('reports/website-items-summary/{from}/{to}',  [ReportController::class, 'websiteItemsSummaryExcel'])->name('websiteItemsSummary');
    Route::get('raw-usage-report',  [ReportController::class, 'rawUsage'])->name('raw.usage');
    Route::get('generate-daily-usage/{from}/{to}',  [ReportController::class, 'generateDailyUsage'])->name('generate.raw.usage');

    //SMS
    Route::get('/view-sms', [SMSController::class, 'view']);
    Route::post('/insert-sms', [SMSController::class, 'store']);
    Route::get('/getsmsdetails', [SMSController::class, 'getdetails']);
    Route::post('/update-smsdetails', [SMSController::class, 'update']);
    Route::post('/update-smsgeneral', [SMSController::class, 'updategeneral']);
    Route::put('/inactive-number', [SMSController::class, 'inactivenumber']);
    Route::put('/inactive-all', [SMSController::class, 'inactiveall']);
    Route::get('/inactivedetails', [SMSController::class, 'inactivedetails']);
    Route::put('/reactive', [SMSController::class, 'reactive']);

    // PFFUND
    Route::get('/view-pf-fund', 'PfundController@index');
    Route::post('/insert-pf-fund', 'PfundController@store');
    Route::post('/get-pf-fund', 'PfundController@getFunds');

    // EOBI
    Route::get('/view-eobi', [EobiController::class, 'index']);
    Route::post('/insert-eobi', [EobiController::class, 'store']);
    Route::post('/get-eobi', [EobiController::class, 'getFunds']);

    // PER PCS SLIPS
    Route::get('/view-hr-products', [HrProductController::class, 'index']);
    Route::post('/insert-hr-products', [HrProductController::class, 'store']);
    Route::post('/get-hr-products', [HrProductController::class, 'getFunds']);
    Route::post('/delete-hr-products', [HrProductController::class, 'delete']);
    Route::post('/get-hr-products', [HrProductController::class, 'getProducts']);
    Route::get('/get-emp-per-pcs', [HrProductController::class, 'getDailyEmployeeTask']);
    Route::post('/update-hr-products', [HrProductController::class, 'update']);
    Route::post('/save-perpcs-salary', [HrProductController::class, 'perpcsSalary']);

    Route::get('/view-steam-press-products', [HrProductController::class, 'getSteamPressProducts']);
    Route::post('/insert-steam-press-products', [HrProductController::class, 'steamProductStore']);
    Route::post('/update-steam-press-products', [HrProductController::class, 'steamProductUpdate']);
    Route::post('/delete-steam-press-products', [HrProductController::class, 'steamProductDelete']);
    Route::get('/get-emp-steam-per-pcs', [HrProductController::class, 'getDailySteamEmployeeTask']);
    Route::get('/get-emp-cotton-per-pcs', [HrProductController::class, 'getDailyCottonEmployeeTask']);

    // EMPLOYEE SECURITY DEPOSIT
    Route::get('/view-security-deposit/{id?}', [EmployeeSecurityDepositController::class, 'index']);
    Route::post('/store-security-deposit', [EmployeeSecurityDepositController::class, 'store']);
    Route::post('/update-security-deposit', [EmployeeSecurityDepositController::class, 'update']);
    Route::post('/delete-security-deposit', [EmployeeSecurityDepositController::class, 'delete']);


    /******************************************** HR ROUTES *************************************************************/

    //Sales Panel
    Route::get('/sales-panel', [SalesController::class, 'index']);
    Route::post('/get-inventory', [SalesController::class, 'getProducts']);

    //Job Order
    Route::get('/joborder', [JobController::class, 'getList']);
    Route::get('/create-job', [JobController::class, 'create']);
    Route::post('/get-raw-materials', [JobController::class, 'getRaw']);
    Route::post('/add-job', [JobController::class, 'addJob']);
    Route::post('/add-sub-job', [JobController::class, 'addJobDetails']);
    Route::post('/load-job', [JobController::class, 'getJobData']);
    Route::post('/calculate-cost', [JobController::class, 'getCost']);
    Route::post('/item-update', [JobController::class, 'ItemUpdate']);
    Route::post('/item-delete', [JobController::class, 'ItemDelete']);
    Route::post('/account-add', [JobController::class, 'accountAdd']);
    Route::post('/account-update', [JobController::class, 'accountUpdate']);
    Route::post('/received-product', [JobController::class, 'ReceivedProduct']);
    Route::get('/edit-job/{id}', [JobController::class, 'edit']);
    Route::get('/repeat-job', [JobController::class, 'RepeatJobOrder']);
    Route::post('/get-job-id', [JobController::class, 'getJobIdFromProduct']);
    Route::post('/get-temp', [JobController::class, 'getJobDataFromID']);
    Route::post('/get-temp-data', [JobController::class, 'getTempData']);
    Route::post('/temp-update', [JobController::class, 'TempUpdate']);
    Route::post('/insert-into-temp', [JobController::class, 'InsertIntoTemp']);
    Route::post('/calculate-temp-cost', [JobController::class, 'getTempCost']);
    Route::post('/temp-item-delete', [JobController::class, 'TempItemDelete']);
    Route::post('/chk-recipy-exists', [JobController::class, 'chk_recipy_exists']);
    Route::get('/getdetails/{id}', [JobController::class, 'getdetails']);
    Route::get('/deletejoborder', [JobController::class, 'deletejoborder']);
    Route::get('/getworkorder', [JobController::class, 'getorderdetails']);
    Route::get('/getworkorder-sum', [JobController::class, 'getorderdetailsSUM']);
    Route::post('/workorder-account', [JobController::class, 'accsubmit']);
    Route::put('/update-orderqty', [JobController::class, 'orderqty_update']);
    Route::post('/suborder-delete', [JobController::class, 'orderdetails_delete']);
    Route::get('/getunitofmessaure', [JobController::class, 'getunitofmeassure']);

    //Job Order Process
    Route::get('/job-order', [JobController::class, 'getJobDetails']);
    Route::post('/job-cancel', [JobController::class, 'jobCancel']);
    Route::post('/job-cost', [JobController::class, 'jobCost']);
    Route::post('/job-submit', [JobController::class, 'jobSubmit']);
    Route::post('/recipy-limit', [JobController::class, 'getrecipyCalculation']);
    Route::get('/getworkorderdetails/{id}', [JobController::class, 'workorderdetails']);
    Route::get('/joborder-inactive', [JobController::class, 'getList_inactive']);
    Route::GET('/createagain-joborder', [JobController::class, 'createagain']);
    Route::POST('/inactiveoldecipy', [JobController::class, 'inactiveoldecipy']);
    Route::POST('/reactiverecipy', [JobController::class, 'reactiverecipy']);
    Route::POST('/inactiverecipy', [JobController::class, 'inactiverecipy']);



    //emptydatabase
    Route::get('/deletedatabase', 'EmptyDataController@view');
    Route::post('/delete_data', 'EmptyDataController@deletedatabase');

    //Pos Produtcs
    Route::get('/posproducts', [PosProductController::class, 'show'])->name('posProducts');;
    Route::post('/insert-posproducts', [PosProductController::class, 'store']);
    Route::put('/inactive-posproducts', [PosProductController::class, 'delete']);
    Route::get('/inactive-posproducts', [PosProductController::class, 'inactiveposproducts']);
    Route::put('/reactive-posproducts', [PosProductController::class, 'reactiveposproduct']);
    Route::put('/update-posproducts', [PosProductController::class, 'update']);
    Route::get('/verifycode', [PosProductController::class, 'codeverify']);
    Route::post('/pos-product/get-variation', [PosProductController::class, 'getVariation_posproduct'])->name('getVariation_posproduct');
    Route::post('/pos-product/get-variation-product-values', [PosProductController::class, 'getVariationProduct_values'])->name('getVariationProduct_values');
    Route::post('/pos-product/store-variation', [PosProductController::class, 'storeVariation'])->name('storeVariation');
    Route::post('/pos-product/update-variation', [PosProductController::class, 'updateVariation'])->name('updateVariation');
    Route::post('/pos-product/destroy-variation', [PosProductController::class, 'destroyVariation'])->name('removeVariation_posproduct');
    Route::post('/pos-product/reload-variation', [PosProductController::class, 'reloadVariation'])->name('reloadVariation_posproduct');

    /******************************* DRIVERS STARTS HERE **********************************/
    Route::get('/drivers', [DriverController::class, 'index'])->name("driver.list");
    Route::get('/create-driver', [DriverController::class, 'create'])->name("driver.create");
    Route::post('/get-drivers', [DriverController::class, 'getDriversList'])->name("driver.get");
    Route::post('/create-driver', [DriverController::class, 'store'])->name("driver.store");
    Route::get('/driver/{id}', [DriverController::class, 'edit'])->name("driver.edit");
    Route::post('/update-driver', [DriverController::class, 'update'])->name("driver.update");
    Route::post('/delete-driver', [DriverController::class, 'inactiveOrActive'])->name("driver.delete");
    /******************************* DRIVERS ENDS HERE **********************************/

    /******************************* VEHICLES STARTS HERE **********************************/
    Route::get('/vehicles', [VehicleController::class, 'index'])->name("vehicle.list");
    Route::get('/create-vehicle', [VehicleController::class, 'create'])->name("vehicle.create");
    Route::post('/get-vehicles', [VehicleController::class, 'getVehiclesList'])->name("vehicle.get");
    Route::post('/create-vehicle', [VehicleController::class, 'store'])->name("vehicle.store");
    Route::get('/vehicle/{id}', [VehicleController::class, 'edit'])->name("vehicle.edit");
    Route::post('/update-vehicle', [VehicleController::class, 'update'])->name("vehicle.update");
    Route::post('/delete-vehicle', [VehicleController::class, 'inactiveOrActive'])->name("vehicle.delete");
    /******************************* VEHICLES ENDS HERE **********************************/

    /******************************* OPENING CLOSING STARTS HERE **********************************/
    Route::get('/opening-closing', 'OpeningClosingController@index')->name("opening.closing");
    /******************************* OPENING CLOSING ENDS HERE **********************************/
});


// optimize image link
Route::get('image-optimize/{image}', [InventoryController::class, 'imageOptimize'])->name("imageOptimize");
