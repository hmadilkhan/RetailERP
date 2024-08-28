<?php

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
Auth::routes();
// Admin panel //
Route::get('/admin', 'AdminController@showLoginForm')->name('admin.login');
Route::post('/admin/login', 'AdminController@loginsubmit')->name('admin.login.submit');
Route::get('/admin/dashboard', 'AdminController@index')->name('admin.home');
Route::post('/admin/logout', 'AdminController@logout')->name('admin.logout');

//Discount
Route::get('/get-discount','DiscountController@index');
Route::get('/create-discount','DiscountController@create');
Route::get('/edit-discount/{id}','DiscountController@edit');
Route::get('/load-department','DiscountController@loadDepartment');
Route::get('/load-products','DiscountController@loadProducts');
Route::get('/load-customers','DiscountController@loadCustomers');
Route::post('/save-discount','DiscountController@saveDiscount');
Route::post('/get-discount-info','DiscountController@getDiscountInfo');
Route::post('/get-discount-categories','DiscountController@getDiscountCategories');
Route::post('/get-discount-products','DiscountController@getDiscountProducts');
Route::post('/get-discount-customers','DiscountController@getDiscountCustomers');
Route::post('/get-customer-buys','DiscountController@getCustomerBuys');
Route::post('/get-customer-gets','DiscountController@getCustomerGets');

// admin company//
Route::resource('company','AdminCompanyController');
Route::post('/insert-company', 'AdminCompanyController@store');
Route::post('/delete-company', 'AdminCompanyController@destroy');
Route::get('/company-edit/{id}', 'AdminCompanyController@edit');
Route::post('/update-company', 'AdminCompanyController@update');

// admin branch//
Route::get('/view-branch','AdminBranchController@index');
Route::get('/create-branch','AdminBranchController@create');
Route::post('/submit-branch','AdminBranchController@store');
Route::post('/remove-branch','AdminBranchController@destroy');
Route::get('/edit-branch/{id}','AdminBranchController@edit');
Route::post('/update-branch','AdminBranchController@update');

// admin users//
Route::get('/view-users','AdminUsersController@index');
Route::get('/create-users','AdminUsersController@create');
Route::post('/get-branches','AdminUsersController@getBranches');
Route::post('/store-users','AdminUsersController@store');
Route::post('/check-user','AdminUsersController@chk_user_exists');
Route::post('/delete-user','AdminUsersController@destroy');
Route::get('/edit-users/{id}','AdminUsersController@edit');
Route::post('/update-users','AdminUsersController@update');
 

// user panel //

// mainf login form show //
Route::get('/', function () {
    return view('auth.login');
});


Route::get('/dashboard', 'HomeController@index')->name('home');
Route::post('/getTerminals', 'HomeController@getTerminalsByBranch');
Route::get('/sales-details', 'HomeController@salesDetails');
Route::get('/heads-details', 'HomeController@salesHead');
// vendors module //
Route::resource('vendors','VendorController');
Route::post('/vendoremail','VendorController@emailcheck');
Route::post('/vendorname','VendorController@namecheck');
Route::put('/all-vendors-remove','VendorController@all_vendoremove');
Route::get('/ledgerlist/{id}', 'VendorController@LedgerDetails');
Route::post('/polist', 'VendorController@getPO');
Route::get('/create-payment/{id}', 'VendorController@createPayment');
Route::post('/make-payment', 'VendorController@makePayment');
Route::post('/debit-payment', 'VendorController@debitPayment');
Route::post('/get-bank-balance', 'VendorController@checkLastBalance');
Route::post('/add-credit-bank', 'VendorController@creditBank');
Route::post('/vendor-payment', 'VendorController@vendorPayment');
Route::post('/vendor-payment-details', 'VendorController@vendorPaymentDetails');
Route::post('/vendor-report-filter', 'VendorController@vendor_report_filter');
Route::get('/payable', 'VendorController@exportpDF');
Route::get('/voucher', 'VendorController@voucher');
Route::get('/voucher/{id}', 'VendorController@voucher');
Route::get('/profit-and-loss','VendorController@profitLoss');
Route::get('/profit-and-loss-panel','VendorController@profitPanel');


//Route::delete('vendors/{id}', 'VendorController@destroy')->name('vendors.destroy');

//SMS
Route::get('/view-sms','SMSController@view');
Route::post('/insert-sms','SMSController@store');
Route::get('/getsmsdetails','SMSController@getdetails');
Route::post('/update-smsdetails','SMSController@update');
Route::post('/update-smsgeneral','SMSController@updategeneral');
Route::put('/inactive-number','SMSController@inactivenumber');
Route::put('/inactive-all','SMSController@inactiveall');
Route::get('/inactivedetails','SMSController@inactivedetails');
Route::put('/reactive','SMSController@reactive');


//Terminals
Route::get('/terminals','TerminalController@view');
Route::post('/submitterminal','TerminalController@store');
Route::put('/inactive-terminal','TerminalController@remove');
Route::get('/inactive-terminals-details','TerminalController@inactivedetails');
Route::put('/reactive-terminal','TerminalController@reactive');
Route::post('/update-terminal','TerminalController@update');



// purchase module //
Route::get('/add-purchase', 'purchaseController@add_purchaseForm')->name('add-purchase');
Route::get('/view-purchases', 'purchaseController@ViewPurchase')->name('view-purchases');
Route::post('/insert-purchases', 'purchaseController@addPurchase')->name('po-insert');
Route::post('/create-purchases', 'purchaseController@firstInsert')->name('po-create');
Route::post('/purchases', 'purchaseController@secondInsert')->name('create-po');
Route::post('/getProduct', 'purchaseController@get_items')->name('getProduct');
Route::post('/getAccounts', 'purchaseController@accounts')->name('getAccounts');
Route::get('/getPurchaseMax', 'purchaseController@getMaxId')->name('getPurchaseMax');
Route::post('/updateitems', 'purchaseController@UpdateItems')->name('updateitem');
Route::post('/AccDetails', 'purchaseController@getAccDetails')->name('AccDetails');
Route::post('/FinalSubmit', 'purchaseController@finalSubmit')->name('FinalSubmit');
Route::get('/view/{id}/', 'purchaseController@viewPO')->name('view');
Route::get('/receive/{id}/', 'purchaseController@receivepo')->name('receive');
Route::post('/create-grn', 'purchaseController@createGRN');
Route::post('/add-grn', 'purchaseController@addGrn')->name('add-grn');
Route::get('/edit/{id}/', 'purchaseController@edit')->name('edit');
Route::get('/return/{id}/', 'purchaseController@return')->name('return');
Route::post('/returnInsert', 'purchaseController@insertReturn')->name('returnInsert');
Route::post('/AccountUpdate', 'purchaseController@UpdateAccounts')->name('AccountUpdate');
Route::post('/changeStatusPo', 'purchaseController@changePOStatus')->name('changeStatusPo');
Route::post('/getReceive', 'purchaseController@getReceiveItems')->name('getReceive');
Route::post('/get', 'purchaseController@getGRNStock')->name('get');
Route::get('/update-status-po/{id}', 'purchaseController@updatePOStatus');
Route::get('/grn-details/{id}', 'purchaseController@grnDetails');
Route::post('/DetailsOfGrn', 'purchaseController@DetailsOfGrn');
Route::get('/DownloadPDF/{id}/', 'purchaseController@DownloadPDF');
Route::post('/DeletePurchaseItems', 'purchaseController@DeletePurchaseItems');
Route::post('/Draft', 'purchaseController@PurchaseDraft');
Route::post('/DeletePO', 'purchaseController@DeletePurchaseOrder');
Route::get('/exportPDF', 'purchaseController@exportpDF');


// inventory module //
Route::get('/create-inventory', 'InventoryController@create')->name('create-invent');
Route::get('/inventory-list', 'InventoryController@index')->name('invent-list');
Route::get('/edit-invent/{id}/', 'InventoryController@getData')->name('edit-invent');
Route::post('/insert-inventory', 'InventoryController@insert')->name('insert');
Route::patch('/update-inventory', 'InventoryController@modify')->name('update');
Route::post('/getSubdepartBydepartID', 'InventoryController@getSubDepart')->name('getSubdepart');
Route::post('/delete-invent', 'InventoryController@deleteInvent');
Route::post('/multiple-active-invent', 'InventoryController@multipleActiveInvent');
Route::post('/chk-itemcode','InventoryController@chk_itemcode_exists');
Route::get('/stock-opening','InventoryController@stockopening');
Route::post('/get-uom-id','InventoryController@getUOMID');
Route::post('/insert-stock-opening','InventoryController@create_stock_opening');
Route::post('/uploadInventory', 'InventoryController@uploadInventory');
Route::post('/all_invent_remove', 'InventoryController@all_invent_remove');
Route::post('/update_product_department', 'InventoryController@update_department');
Route::post('/update_product_subdepartment', 'InventoryController@update_sub_department');
Route::post('/update_product_uom', 'InventoryController@update_uom');
Route::post('/get_departments', 'InventoryController@get_departments');
Route::post('/get_sub_departments', 'InventoryController@get_sub_departments');
Route::post('/get_uom', 'InventoryController@get_uom');
Route::post('/get_names', 'InventoryController@get_names');



// Orders Module //
Route::get('/orders-view', 'OrderController@ordersview');
Route::post('/get-orders', 'OrderController@getOrderById');
Route::get('/order-assign/{id}', 'OrderController@orderAssign');
Route::post('/uom-by-product', 'OrderController@getUOMByProduct');
Route::post('/insert-assign', 'OrderController@insertAssign');
Route::post('/update-assign', 'OrderController@updateAssign');
Route::post('/get-items', 'OrderController@getitemsByfinished');
Route::post('/get-items-details', 'OrderController@getitemsByDetails');
Route::post('/get-status-changed', 'OrderController@getstatusChanged');
Route::post('/get-items-qty', 'OrderController@getItemQty');
Route::post('/get-items-by-receipt', 'OrderController@getReceiptitems');
Route::post('/temp-insert-master', 'OrderController@InsertAssignTemp');
Route::post('/get-master-by-category', 'OrderController@getMasterByCategory');
Route::post('/get-master-pending-orders', 'OrderController@GetMastersPendingOrders');
Route::get('/orders-report', 'OrderController@exportPDF');


// inventory department module //
Route::resource('invent_dept','Inventory_DepartmentController');
Route::put('/invent-depart-modify','Inventory_DepartmentController@depart_update')->name('invent_deptup');
Route::put('/invent-sbdepart-modify','Inventory_DepartmentController@sb_depart_update')->name('invent_sb_deptup');
Route::post('/adddepartment','Inventory_DepartmentController@adddepartment');
Route::post('/addsubdepart','Inventory_DepartmentController@addsubdepartment');
Route::put('/updatedepart','Inventory_DepartmentController@updatedepart');
Route::get('/getsubdepart','Inventory_DepartmentController@getsubdepart');

//Inventory Stock
Route::get('/stock-list','StockController@index');
Route::get('/stock-details/{id}','StockController@getStock');
Route::get('/branchwise-stock','StockController@brnchwisestock');

// expense module //
Route::resource('expense','ExpenseController');
Route::post('/getData', 'ExpenseController@getData');
Route::get('/category', 'ExpenseController@getCategories');
Route::get('/tax', 'ExpenseController@getTax');
Route::post('/modifyExpense', 'ExpenseController@modify')->name('updatexp');
Route::get('/expense-report', 'ExpenseController@expense_report_panel');
Route::post('/expense-report-details', 'ExpenseController@expense_report_filter');
Route::get('/expense-report-pdf', 'ExpenseController@generatePDF');

// expense category module //
Route::resource('exp_category','ExpenseCategoryController');
Route::put('/expcate-update','ExpenseCategoryController@update');
Route::post('/expcate_edit','ExpenseCategoryController@edit');
Route::put('/expcate-update','ExpenseCategoryController@update');


//Customer Module
Route::resource('customer','CustomersController');
Route::get('/editcustomers/{id}', 'CustomersController@edit');
Route::put('/updatecustomers', 'CustomersController@update');
Route::put('/inactivecustomer', 'CustomersController@remove');
Route::post('/getCityById', 'CustomersController@getCity');
Route::get('/ledgerDetails/{id}', 'CustomersController@LedgerDetails');
Route::get('/discount-panel/{id}', 'CustomersController@discountPanel');
Route::post('/discount-insert', 'CustomersController@discountInsert');
Route::post('/discount-update', 'CustomersController@discountUpdate');
Route::post('/get-insert', 'CustomersController@loadDiscount');
Route::post('/get-products', 'CustomersController@getProducts');
Route::post('/delete-products', 'CustomersController@deleteProduct');
Route::get('/measurement/{id}', 'CustomersController@createMeasurement');
Route::post('/measurementUpdate', 'CustomersController@measurementUpdate');
Route::post('/measurementPantUpdate', 'CustomersController@updatePantMeasurement');
Route::get('/customer-report', 'CustomersController@customer_report');
Route::post('/customer-report-filter', 'CustomersController@customer_report_filter');
Route::get('/receivable', 'CustomersController@exportpDF');
Route::post('/uploadFile', 'CustomersController@uploadFile');
Route::post('/all_customers_remove', 'CustomersController@all_customers_remove');
Route::post('/active-customer', 'CustomersController@activeCustomer');
Route::post('/multiple-active-customer', 'CustomersController@multipleactiveCustomer');
Route::post('/customer-names', 'CustomersController@get_names');

//Master
Route::get('/get-masters', 'MasterController@index');
Route::get('/create-master', 'MasterController@create');
Route::post('/store-master', 'MasterController@store');
Route::put('/updatemasters', 'MasterController@update');
Route::post('/remove-master', 'MasterController@remove');
Route::get('/ledger-details/{id}', 'MasterController@LedgerDetails');
Route::get('/edit-master/{id}', 'MasterController@edit');
Route::get('/ledger-payment/{id}', 'MasterController@LedgerPayment');
Route::post('/debit-insert', 'MasterController@debitInsert');
Route::post('/createPayment', 'MasterController@ledgerInsert');
Route::get('/category/{id}', 'MasterController@category');
Route::post('/get-categories', 'MasterController@getcategory');
Route::post('/addCategory', 'MasterController@insertCategory');
Route::post('/get-master', 'MasterController@getMaster');
Route::post('/master-rate-insert', 'MasterController@MasterRateInsert');
Route::post('/master-rate-list', 'MasterController@getRateList');
Route::post('/rate-update', 'MasterController@MasterRateUpdate');
Route::post('/get-receipt', 'MasterController@getReceipt');
Route::get('/work-load', 'MasterController@workload');
Route::get('/work-load/{id}', 'MasterController@workloadDetails');
Route::post('/received-from-master', 'MasterController@updateMasterAssign');
Route::get('/master-report', 'MasterController@master_report');
Route::post('/master-report-filter', 'MasterController@master_report_filter');
Route::get('/masterpayable', 'MasterController@exportPDF');
Route::get('/workloadreport', 'MasterController@exportWorkLoadPDF');

//Demand Module
Route::get('/demand', 'DemandController@index');
Route::get('/create-demand', 'DemandController@add_demand');
Route::post('/additems', 'DemandController@insert_item_details');
Route::post('/viewitems','DemandController@get_demandlist');
Route::put('/updateitem','DemandController@update_qty');
Route::delete('/deleteitem','DemandController@del_item');
Route::put('/updatestatus','DemandController@update_status');
Route::get('/demand-details/{id}','DemandController@show');
Route::get('/edit-demand/{id}','DemandController@edit');
Route::put('/removedemand','DemandController@update_status');
Route::put('/all-demand-remove','DemandController@all_demand_state_up');
Route::put('/updatestatusdemand','DemandController@update_status');


//Demand Received Module
Route::get('/received-demand', 'receiveddemandController@index');
Route::get('/received-demandpanel/{id}', 'receiveddemandController@show');
Route::put('/update-status','receiveddemandController@update_status');
Route::post('/stock','receiveddemandController@getstock');
Route::post('/transfer','receiveddemandController@insert');

Route::post('/chk','receiveddemandController@check');
Route::put('/updateitemstatus','receiveddemandController@updatedemanditem');

//view transfer order
Route::get('/view-transfer/{id}','TransferController@index');
Route::post('/transferordershow','TransferController@show');

Route::get('/transferlist','TransferController@transferlist');
Route::get('/createdeliverychallan/{id}','TransferController@deliverychallan');
Route::post('/stockdetails','TransferController@getstock');
Route::put('/updatetransferitem','TransferController@updatetransferitem');
Route::post('/insertdeliverchallan', 'TransferController@insert');
Route::put('/updatechallan','TransferController@updatechllan');
Route::get('/challanlist','TransferController@challanlist');
Route::get('/challandetails/{id}', 'TransferController@challandetails');
Route::get('/createGRN/{id}','TransferController@createGRN');
Route::post('/submitgrn','TransferController@grn_insert');
Route::put('/edit_transfer','TransferController@edit_transfer');
Route::get('/gettransferorders','TransferController@gettransferorders');
Route::put('/removetransferorder','TransferController@removetransferorder');

//Direct Transfer Without Demand
Route::get('/create-transferorder','TransferController@create_transferorder');
Route::post('/trf_stock','TransferController@trf_stock');
Route::post('/get_products','TransferController@get_products');
Route::post('/insert_trf','TransferController@insert_trf');
Route::get('/trf_details','TransferController@trf_details');
Route::get('/trf_delete','TransferController@trf_delete');
Route::put('/trf_change_status','TransferController@trf_submit_update');
Route::get('/trf_list','TransferController@trf_list');
Route::get('/trforder_delete','TransferController@trforder_delete');
Route::get('/get_trf_details/{id}','TransferController@get_trf_details');
Route::put('/qty_update','TransferController@qty_update_trf');
Route::post('/insert_direct_chalan','TransferController@insert_direct_chalan');
Route::get('/edit_trf_details/{id}','TransferController@edit_trf_details');

Route::get('/insert-po/{id}','TransferController@getdetails_po');
Route::post('/submitpo','TransferController@purchaseorder_insert');
Route::get('/showtransferdetails/{id}','TransferController@show_transferdetails');

//Accounts
Route::get('/bankaccounts-details','BankController@index');
Route::post('/submitbankdetails','BankController@submit_details');
Route::post('/createaccount','BankController@insert_account');
Route::get('/view-accounts','BankController@show');
Route::get('/create-deposit/{id}','BankController@show_deposit');
Route::post('/depositamount','BankController@insert_deposit');
Route::get('/getaccountdetails/{id}','BankController@getdetails');
Route::put('/updateaccount','BankController@updateaccountdetails');
Route::get('/customer-ledger','CustomersController@LedgerDetails');
Route::post('/ledger-details','CustomersController@LedgerDetailsByID');
Route::get('/vendor-ledger','VendorController@LedgerDetails');
Route::get('/vendor-report','VendorController@vendor_report_panel');
Route::post('/vendor-ledger-details','VendorController@LedgerDetailsByID');
Route::get('/master-ledger','MasterController@LedgerDetails');
Route::post('/master-ledger-details','MasterController@LedgerPayment');



//Users
Route::resource('usersDetails','UserDetailsController');
Route::get('/user-edit/{id}','UserDetailsController@edit');
Route::put('/user-update','UserDetailsController@update');
Route::put('/user-delete','UserDetailsController@delete_user');
Route::post('/chk-user','UserDetailsController@chk_user_exists');
Route::post('/add-role','UserDetailsController@addrole');
// Route::get('/create-user','UserDetailsController@index');


//Branches
Route::get('/branches','BranchController@show');
Route::get('/createbranch','BranchController@index');
Route::post('/submitbranch','BranchController@store');
Route::put('/removebranch','BranchController@remove');
Route::get('/branch-edit/{id}','BranchController@edit');
Route::post('/updatebranch','BranchController@update');

//Company
Route::get('/companies','CompanyController@show');
Route::get('/createcompany','CompanyController@index');

//uom
Route::post('/adduom','UnitofmeasureController@store');


//Business Policy
Route::get('/BusinessPolicy','BusinessPoliciesController@index');
Route::get('/Tax-create','BusinessPoliciesController@tax_create');
Route::post('/tax-insert','BusinessPoliciesController@insert_tax');
Route::put('/delete_tax','BusinessPoliciesController@delete_tax');
Route::get('/show-tax/{id}','BusinessPoliciesController@show_tax');
Route::post('/update-tax','BusinessPoliciesController@update_tax');

/******************************************** HR ROUTES *************************************************************/
//Departments
Route::get('/view-departments','DepartmentController@view');
Route::get('/show-departments','DepartmentController@show');
Route::post('/insert-departments','DepartmentController@store');
Route::get('/edit-departments-show/{id}','DepartmentController@edit');
Route::post('/edit-departments','DepartmentController@update');
Route::put('/remove-departments','DepartmentController@remove');

//Designation
Route::get('/view-designation','DesignationController@view'); 
Route::get('/show-designation','DesignationController@show'); 
Route::post('/insert-designation','DesignationController@store');
Route::put('/remove-designation','DesignationController@remove');
Route::get('/edit-designation-show/{id}','DesignationController@edit');
Route::post('/edit-designation','DesignationController@update');

//Office Shift
Route::get('/view-shift','AttendanceController@shiftview'); 
Route::get('/show-shift','AttendanceController@shiftshow'); 
Route::post('/insert-shift','AttendanceController@shiftinsert');
Route::post('/delete-shift','AttendanceController@deleteshift');
Route::get('/show-editshift/{id}','AttendanceController@shiftedit'); 
Route::post('/update-shift','AttendanceController@shiftupdate');

//Over Time Formula
Route::get('/view-ot','AttendanceController@OTtview'); 
Route::get('/show-ot','AttendanceController@OTshow'); 
Route::post('/insert-ot','AttendanceController@otinsert');
Route::get('/show-editot/{id}','AttendanceController@otedit'); 
Route::post('/update-ot','AttendanceController@otupdate');
Route::post('/delete-ot','AttendanceController@deleteot');
Route::post('/insert-otamount','AttendanceController@otamountinsert');
Route::post('/insert-otduration','AttendanceController@otdurationinsert');


//Attendance
Route::get('/dailyattendance-view','AttendanceController@attendanceview'); 
Route::get('/dailyattendance-edit','AttendanceController@attendanceedit'); 
Route::get('/getemployees','AttendanceController@getemployees'); 
Route::get('/getattendancedetails','AttendanceController@getattendetails'); 
Route::get('/getgracetime','AttendanceController@getgracetime'); 
Route::post('/dailyattendance-update','AttendanceController@attendanceupdate');
Route::get('/getdatabybranchid','AttendanceController@getdata'); 
Route::post('/attendanceupload','AttendanceController@uploadattendance');
Route::get('/getsheet','AttendanceController@getsheet'); 
Route::get('/manuallyattendance','AttendanceController@show'); 
Route::post('/submitattendance','AttendanceController@store');
Route::get('/getpdfattendancesheet','AttendanceController@attendancesheet_pdf'); 
Route::get('/attnotify','AttendanceController@attendance_notify'); 
Route::post('/updatenotifyatt','AttendanceController@update_att_notify');
Route::get('/attnotify_chkout','AttendanceController@att_notify_chkout');
Route::post('/updatenotifyattclckout','AttendanceController@update_att_notify_clckout'); 
Route::post('/absent_delete','AttendanceController@deleteabsent');
Route::post('/absent_insert','AttendanceController@insertabsent');

//Salary
Route::get('/branchwise-view','SalaryController@branchwiseview'); 
Route::get('/departwise-view','SalaryController@departwisesalary'); 
Route::get('/empwise-view','SalaryController@employeewiseview'); 
Route::get('/getempdetails','SalaryController@getempdetails'); 
Route::get('/getgross','SalaryController@getgrossdetails'); 
Route::post('/insert-specialallowance','SalaryController@insert_specialallowance');
Route::post('/insert-payslip','SalaryController@insert_payslip');
Route::get('/salary-details','SalaryController@show'); 
Route::get('/getsalarydetails','SalaryController@getdetails'); 
Route::get('/getallowance','SalaryController@getallowance'); 
Route::get('/getpdf','SalaryController@createpdf'); 






//Employee
Route::get('/view-employee','EmployeeController@view'); 
Route::get('/show-employee','EmployeeController@show'); 
Route::post('/insert-employee','EmployeeController@store');
Route::get('/chk-employee','EmployeeController@empacccheck'); 
Route::post('/store-desg','EmployeeController@store_desg');
Route::post('/store-depart','EmployeeController@store_depart');
Route::post('/remove-employee','EmployeeController@remove');
Route::get('/view-inaciveemployee','EmployeeController@viewinactive'); 
Route::get('/details-employee/{id}','EmployeeController@empdetails'); 
Route::get('/edit-employee-show/{id}','EmployeeController@edit');
Route::post('/update-employee','EmployeeController@update');
Route::get('/switch-branch','EmployeeController@switchbranch'); 
Route::put('/emp-branch-change','EmployeeController@branchupdate');
Route::get('/fire-emp-show','EmployeeController@fireshow'); 
Route::get('/getshifts','EmployeeController@getshifts'); 
Route::post('/hire-employee','EmployeeController@hireagain');
Route::post('/insert-category','EmployeeController@storecat');
Route::get('/showholiday','EmployeeController@show_holiday'); 
// Route::get('/viewholiday','EmployeeController@view_holiday'); 
Route::get('/getempmonthly','EmployeeController@getempmonthly'); 
Route::post('/insert-holiday','EmployeeController@storeholiday');
Route::post('/update-holiday','EmployeeController@updateholiday');
Route::get('/showevent','EmployeeController@show_event'); 
Route::post('/insert-events','EmployeeController@storeevents');
Route::post('/update-events','EmployeeController@updateevents');
Route::put('/delete-events','EmployeeController@deleteevents');

//Loan
Route::get('/view-loandeduct','LoanController@view'); 
Route::get('/show-loandeduct','LoanController@show'); 
Route::post('/insert-loandeduct','LoanController@store'); 
Route::post('/delete-loandeduct','LoanController@deletededuct'); 
Route::get('/edit-loandeduct/{id}','LoanController@edit'); 
Route::post('/update-loandeduct','LoanController@updatededuct'); 
Route::get('/loandetails','LoanController@viewdetails'); 
Route::get('/show-issueloan','LoanController@showloan'); 
Route::post('/get-employee','LoanController@getempbybranch'); 
Route::post('/issueloan','LoanController@issueloan'); 
Route::post('/insert-loandeduct-modal','LoanController@insert'); 
Route::post('/previousdata','LoanController@getpreivousdetails'); 
Route::put('/remove-loan','LoanController@remove'); 
Route::get('/getinstallments','LoanController@getinstallments'); 
Route::put('/loandeduction','LoanController@loandeduction'); 

//Advance Salary
Route::get('/view-advancelist','AdvanceSalaryController@view'); 
Route::get('/show-advancesal','AdvanceSalaryController@show'); 
Route::post('/get-employeebybranch','AdvanceSalaryController@getempbybranch'); 
Route::post('/insert-advance','AdvanceSalaryController@store'); 
Route::post('/previousdetails','AdvanceSalaryController@getpreivousdetails'); 

//Reports
Route::get('/attrpt-show','ReportController@attreport_show');
Route::get('/attendancerpt','ReportController@attendancereport');
Route::get('/pdfattendance','ReportController@pdf_attendance');

//SMS
Route::get('/view-sms','SMSController@view');
Route::post('/insert-sms','SMSController@store');
Route::get('/getsmsdetails','SMSController@getdetails');
Route::post('/update-smsdetails','SMSController@update');
Route::post('/update-smsgeneral','SMSController@updategeneral');
Route::put('/inactive-number','SMSController@inactivenumber');


/******************************************** HR ROUTES *************************************************************/

//Sales Panel
Route::get('/sales-panel','SalesController@index');
Route::post('/get-inventory','SalesController@getProducts');

//Job Order
Route::get('/joborder','JobController@getList');
Route::get('/create-job','JobController@create');
Route::post('/get-raw-materials','JobController@getRaw');
Route::post('/add-job','JobController@addJob');
Route::post('/add-sub-job','JobController@addJobDetails');
Route::post('/load-job','JobController@getJobData');
Route::post('/calculate-cost','JobController@getCost');
Route::post('/item-update','JobController@ItemUpdate');
Route::post('/item-delete','JobController@ItemDelete');
Route::post('/account-add','JobController@accountAdd');
Route::post('/account-update','JobController@accountUpdate');
Route::post('/received-product','JobController@ReceivedProduct');
Route::get('/edit-job/{id}','JobController@edit');
Route::get('/repeat-job','JobController@RepeatJobOrder');
Route::post('/get-job-id','JobController@getJobIdFromProduct');
Route::post('/get-temp','JobController@getJobDataFromID');
Route::post('/get-temp-data','JobController@getTempData');
Route::post('/temp-update','JobController@TempUpdate');
Route::post('/insert-into-temp','JobController@InsertIntoTemp');
Route::post('/calculate-temp-cost','JobController@getTempCost');
Route::post('/temp-item-delete','JobController@TempItemDelete');
Route::post('/chk-recipy-exists','JobController@chk_recipy_exists');

//Job Order Process
Route::get('/job-order','JobController@getJobDetails');
Route::post('/job-cancel','JobController@jobCancel');
Route::post('/job-cost','JobController@jobCost');
Route::post('/job-submit','JobController@jobSubmit');
Route::post('/recipy-limit','JobController@getrecipyCalculation');
Route::get('/view-recipy/{id}','JobController@viewrecipy');
/*
WebService Routes
*/

Route::get('bienes-servicios', 'SoapController@BienesServicios');
Route::get('clima', 'SoapController@clima');
Route::get('/enduro/bienes-servicios', function () {
    try {
        $opts = array(
            'http' => array(
                'user_agent' => 'PHPSoapClient'
            )
        );
        $context = stream_context_create($opts);
        $wsdlUrl = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
        $soapClientOptions = array(
            'stream_context' => $context,
            'cache_wsdl' => WSDL_CACHE_NONE
        );
        $client = new SoapClient($wsdlUrl, $soapClientOptions);
        $checkVatParameters = array(
            'countryCode' => 'DK',
            'vatNumber' => '47458714'
        );
        $result = $client->checkVat($checkVatParameters);
        print_r($result);
    }
    catch(\Exception $e) {
        echo $e->getMessage();
    }
});
Route::get('/enduro/clima', function () {
    $opts = array(
        'ssl' => array('ciphers'=>'RC4-SHA', 'verify_peer'=>false, 'verify_peer_name'=>false)
    );
    $params = array ('encoding' => 'UTF-8', 'verifypeer' => false, 'verifyhost' => false, 'soap_version' => SOAP_1_2, 'trace' => 1, 'exceptions' => 1, "connection_timeout" => 180, 'stream_context' => stream_context_create($opts) );
    $url = "http://www.webservicex.net/globalweather.asmx?WSDL";
    try{
        $client = new SoapClient($url,$params);
        dd($client->GetCitiesByCountry(['CountryName' => 'Peru'])->GetCitiesByCountryResult);
    }
    catch(SoapFault $fault) {
        echo '<br>'.$fault;
    }
});

