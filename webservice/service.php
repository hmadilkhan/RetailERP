<?php 
require 'lib/nusoap.php';

require 'function.php';

$server = new nusoap_server();

$server->configureWSDL('server', 'urn:server');


// get login
$server->register(
    'login',
    array('username' => 'xsd:string', 'password' => 'xsd:string', 'serial' => 'xsd:string', 'terminal' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#loginServer'
);                   // description

// get login
$server->register(
    'getBranchId',
    array('username' => 'xsd:string', 'password' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getBranchIdServer'
);

// chk system details
$server->register(
    'chk_system_details',
    array(
        'id' => 'xsd:int',
        'statusid' => 'xsd:int',
        'plateform' => 'xsd:string',
        'device_manufacturer' => 'xsd:string',
        'device_model' => 'xsd:string',
        'device_serial' => 'xsd:string',
        'serial_key' => 'xsd:string'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#chk_system_detailsServer'
);

// add user Authentication (Logs)
$server->register(
    'add_user_authentication',
    array(
        'user_id' => 'xsd:int',
        'authorization_id' => 'xsd:int',
        'username' => 'xsd:string',
        'password' => 'xsd:string',
        'created' => 'xsd:string',
        'updated' => 'xsd:string'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_user_authenticationServer'
);


// chk system details
$server->register(
    'chk_serial_device_key',
    array(
        'id' => 'xsd:int',
        'serial_key' => 'xsd:string',
        'device_serial' => 'xsd:string'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#chk_serial_device_keyServer'
);




// get inventory //
$server->register(
    'getInventory',
    array('id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getInventoryServer'
);                   // description


// get updated inventory //
$server->register(
    'getUpdatedInventory',
    array('terminal_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getUpdatedInventoryServer'
);  // description

// get update inventory status //
$server->register(
    'getUpdatedInventoryStatus',
    array('terminal_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getUpdatedInventoryServer'
);  // description

// get Company inventory //
$server->register(
    'getCompanyInventory',
    array('id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getCompanyInventoryServer'
);

// get Department //
$server->register(
    'getDepartment',
    array('id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getDepartmentServer'
);

// get SUB Department //
$server->register(
    'getSubDepartment',
    array('id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getSubDepartmentServer'
);

// get Order inventory //
$server->register(
    'getOrderInventory',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getOrderInventoryServer'
);

// get inventory General//
$server->register(
    'getInventoryGeneral',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getInventoryGeneralServer'
);

// get Masters//
$server->register(
    'getMasters',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getMastersServer'
);

// get Recipy General//
$server->register(
    'getRecipyGeneral',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getRecipyGeneralServer'
);

// get Recipy Details//
$server->register(
    'getRecipyDetails',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getRecipyDetailsServer'
);

// get Recipy Accounts//
$server->register(
    'getRecipyAccount',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getRecipyAccountServer'
);

// get customer //
$server->register(
    'get_customer',
    array('uid' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_customerServer'
); 

// get customer  all receipt//
$server->register(
    'get_customer_all_receipt',
    array(
        'custId' => 'xsd:int',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_customer_all_receiptServer'
);

// get customer  status 0 pending payment status 1 received payment//
$server->register(
    'get_customer_pending_payment',
    array(
        'custId' => 'xsd:int',
        'status' => 'xsd:int',
        'sort_order' => 'xsd:string',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_customer_pending_paymentServer'
); 
// description

// get custome no with company id and customer idr //
$server->register(
    'get_customer_contact_or_verify_no',
    array('custId' => 'xsd:int','company_id' => 'xsd:int','mobile' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_customer_contact_or_verify_noServer'
);


// get customer account //
$server->register(
    'get_customer_account',
    array('custId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_customer_accountServer'
);                   // description


// add customer  //
$server->register(
    'add_cust',
    array(
        'user_id' => 'xsd:int',
        'status_id' => 'xsd:int',
        'country_id' => 'xsd:string',
        'city_id' => 'xsd:int',
        'name' => 'xsd:string',
        'mobile' => 'xsd:string',
        'phone' => 'xsd:string',
        'nic' => 'xsd:string',
        'address' => 'xsd:string',
        'image' => 'xsd:string',
        'credit_limit' => 'xsd:int',
        'discount' => 'xsd:int',
        'email' => 'xsd:string'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_custServer'
);

// add Customer Measurement //
$server->register(
    'add_cust_measurement',
    array(
        'customer_id' => 'xsd:int',
        'chest' => 'xsd:string',
        'waist' => 'xsd:string',
        'abdomen' => 'xsd:string',
        'hips' => 'xsd:string',
        'shoulder' => 'xsd:string',
        'sleeves' => 'xsd:string',
        'neck' => 'xsd:string',
        'kurta_length' => 'xsd:string',
        'shirt_length' => 'xsd:string',
        'jacket_length' => 'xsd:string',
        'sherwani' => 'xsd:string',
        'pentshalwar' => 'xsd:string',
        'arm_hole' => 'xsd:string',
        'bicep' => 'xsd:string',
        'wc_length' => 'xsd:string',
        'pwaist' => 'xsd:string',
        'phip' => 'xsd:string',
        'pthy' => 'xsd:string',
        'pknee' => 'xsd:string',
        'pcaff' => 'xsd:string',
        'pfly' => 'xsd:string',
        'plength' => 'xsd:string',
        'pbottom' => 'xsd:string',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_cust_measurementServer'
);


// add customer account //
$server->register(
    'add_cust_account',
    array(
        'custId' => 'xsd:int',
        'receipt_no' => 'xsd:int',
        'total_amount' => 'xsd:string',
        'debit' => 'xsd:string',
        'credit' => 'xsd:string',
        'balance' => 'xsd:string',
        'terminal' => 'xsd:int',
        'paymentMode' => 'xsd:int',
        'received' => 'xsd:int',
        'opening' => 'xsd:int'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_cust_accountServer'
);                   // description

// add Master account //
$server->register(
    'add_master_account',
    array(
        'masterID' => 'xsd:int',
        'receipt_no' => 'xsd:int',
        'total_amount' => 'xsd:string',
        'debit' => 'xsd:string',
        'credit' => 'xsd:string',
        'balance' => 'xsd:string',
        'date' => 'xsd:string'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_master_accountServer'
);

$server->register(
    'create_customer_account',
    array(
        'custid' => 'xsd:int',
        'receipt_no' => 'xsd:int',
        'total_amount' => 'xsd:string',
        'debit' => 'xsd:string',
        'credit' => 'xsd:string',
        'balance' => 'xsd:string'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#create_customer_accountServer'
);

// get customer //
$server->register(
    'get_orderMode',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_orderModeServer'
);                   // description


// sales openning //
$server->register(
    'sales_openning',
    array(
		'uid' => 'xsd:int', 
		'balance' => 'xsd:string', 
		'updateid' => 'xsd:int',
		'terminal' => 'xsd:int',
		'date' => 'xsd:string', 
		'time' => 'xsd:string', 
	),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#sales_openningServer'
);                   // description



// sales openning //
$server->register(
    'sales_closing',
    array('opening_id' => 'xsd:int', 'balance' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#sales_closingServer'
);                   // description


// check sales receipt //
$server->register(
    'chk_salesReceipt',
    array('uid' => 'xsd:int', 'receipt' => 'xsd:string', 'openingId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#chk_salesReceiptServer'
);                   // description


// add sales receipt //
$server->register(
    'add_salesReceipt',
    array(
        'uid' => 'xsd:int', 
		'receipt' => 'xsd:string',
		'openingId' => 'xsd:int', 
		'orderMode_id' => 'xsd:int', 
		'customerId' => 'xsd:int', 
		'paymentId' => 'xsd:int',
		'actual_amount' => 'xsd:string',
		'total_amount' => 'xsd:string',
		'totalItem_qty' => 'xsd:string',
		'delivery' => 'xsd:string', 
		'branch' => 'xsd:int', 
		'date' => 'xsd:string', 
		'time' => 'xsd:string', 
		'terminal' => 'xsd:int',
		'salesPerson' => 'xsd:int',
		'webid' => 'xsd:int',
		'due_date' => 'xsd:string',
		'delivery_person_name' => 'xsd:string',
		'contact_no' => 'xsd:string',
		'vehicle_no' => 'xsd:string',
		'service_provider_order_no' => 'xsd:string',
		'onlineorder' => 'xsd:int',
		'ridercharges' => 'xsd:int',
		'bill_print_name' => 'xsd:string',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_salesReceiptServer'
);                   // description


// add sales receipt details//
$server->register(
    'add_salesReceiptDetails',
    array(
        'receipt' => 'xsd:int', 'itemCode' => 'xsd:int',
        'totalQty' => 'xsd:string', 'totalAmount' => 'xsd:string', 'status' => 'xsd:string', 'total_cost' => 'xsd:string', 'total_discount' => 'xsd:string', 'note' => 'xsd:string', 
        'webid' => 'xsd:int',
        'item_name' => 'xsd:string', 
        'item_price' => 'xsd:string',
        'tax_rate' => 'xsd:string',
        'tax_amount' => 'xsd:string',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_salesReceiptDetailsServer'
);                   // description



// add sales general Account//
$server->register(
    'addReceipt_generalAccount',
    array(
        'receipt' => 'xsd:int',
        'receiveAmount' => 'xsd:string',
        'amountPaid_back' => 'xsd:string',
        'total_amount' => 'xsd:string',
        'web_id' => 'xsd:int'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#addReceipt_generalAccountServer'
);                   // description


// add sales subdetails Account//
$server->register(
    'addSales_subdetailsAccount',
    array(
        'receipt' => 'xsd:int',
        'discount_amount' => 'xsd:string',
        'coupon' => 'xsd:string',
        'promo_code' => 'xsd:string',
        'sales_tax_amount' => 'xsd:string',
        'service_tax_amount' => 'xsd:string',
        'credit_card_transaction_id' => 'xsd:int',
        'delivery_charges_id' => 'xsd:int',
        'delivery_charges_amount' => 'xsd:string',
        'bank_discount_id' => 'xsd:int',
        'web_id' => 'xsd:int',
        'srb' => 'xsd:string'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#addSales_subdetailsAccountServer'
);                   // description


// add sales credit card details//
$server->register(
    'addSales_creditcard',
    array(
        'receipt' => 'xsd:int',
        'creditno' => 'xsd:string',
        'bank_discount_id' => 'xsd:int'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#addSales_creditcardServer'
);                   // description

// TABLE HOLD AND UNHOLD DATA//
$server->register(
    'addSales_hold_unhold',
    array(
        'receipt' => 'xsd:int',
        'floorid' => 'xsd:string',
        'tableno' => 'xsd:string',
        'gop' => 'xsd:string',
        'hold_dt' => 'xsd:string',
        'unhold_dt' => 'xsd:string',
        'hold_status' => 'xsd:string',
        'web_id' => 'xsd:string',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#addSales_hold_unholdServer'
);                   // description

//Getting Receipt General Details for Sales Return
$server->register(
    'getReceiptGeneralByReceiptNo',
    array('receipt_no' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getReceiptGeneralByReceiptNoServer'
);                   // description

//Getting Receipt Details for Sales Return
$server->register(
    'getReceiptDetailsByReceiptNo',
    array('receipt' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getReceiptDetailsByReceiptNoServer'
);                   // description

//Getting Receipt Account General for Sales Return
$server->register(
    'getReceiptAccountGenralByReceiptNo',
    array('receipt' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getReceiptAccountGenralByReceiptNoServer'
);                   // description

//Getting Receipt Account Sub Details for Sales Return
$server->register(
    'getReceiptAccountGenralSubDetailsByReceiptNo',
    array('receipt' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getReceiptAccountGenralSubDetailsByReceiptNoServer'
);

//Getting Receipt Credit Card Details for Sales Return
$server->register(
    'getReceiptCreditCardDetailsByReceiptNo',
    array('receipt' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getReceiptCreditCardDetailsByReceiptNoServer'
);

//Insert into Sale Return
$server->register(
    'addSaleReturn',
    array(
        'opening_id' => 'xsd:int',
        'receipt' => 'xsd:int',
        'itemid' => 'xsd:int',
        'qty' => 'xsd:int',
        'amount' => 'xsd:string',
        'branch' => 'xsd:int'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#addSaleReturnServer'
);


//Update Status On Sale Return
$server->register(
    'updateStatusOnSaleReturn',
    array(
        'receipt_id' => 'xsd:int',
        'item_id' => 'xsd:int'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#updateStatusOnSaleReturnServer'
);

//Add Stock  On Sale Return
$server->register(
    'AddStockOnSaleReturn',
    array(
        'branch_id' => 'xsd:int',
        'item_id' => 'xsd:int', 'qty' => 'xsd:int'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#AddStockOnSaleReturnServer'
);

//Add Stock Deduction On Sale Return
$server->register(
    'invent_stock_detection',
    array(
        'branch_id' => 'xsd:int',
        'item_id' => 'xsd:int', 'qty' => 'xsd:string', 'status' => 'xsd:string'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#invent_stock_detectionServer'
);

//Add Customers
$server->register(
    'add_customer',
    array(
        'user_id' => 'xsd:int',
        'name' => 'xsd:string',
        'mobile' => 'xsd:string',
        'phone' => 'xsd:string',
        'nic' => 'xsd:string',
        'address' => 'xsd:string',
        'creditLimit' => 'xsd:int',
        'dicount' => 'xsd:int',
        'email' => 'xsd:int',
        'company_id' => 'xsd:int',
		'latitude' => 'xsd:string',
        'longitude' => 'xsd:string',
        'clientId' => 'xsd:string',
		'device_serial' => 'xsd:string',
		'membership_card' => 'xsd:string',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_customerServer'
);

//Add Customers Address
$server->register(
    'add_customer_address',
    array(
        'customer_id' => 'xsd:int',
		'latitude' => 'xsd:string',
		'longitude' => 'xsd:string',
        'address' => 'xsd:string',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_customer_addressServer'
);

//Update Client ID OF CUSTOMER
$server->register(
    'update_customer_client_id',
    array(
        'customer_id' => 'xsd:int',
        'clientId' => 'xsd:string',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#update_customer_client_idServer'
);

// get customer address //
$server->register(
    'get_customer_addresses',
    array('custId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_customer_addressesServer'
);                   // description


//Add Expense
$server->register(
    'add_expense',
    array(
        'branch_id' => 'xsd:int',
        'exp_cat' => 'xsd:int',
        'tax_id' => 'xsd:int',
        'expense_details' => 'xsd:string',
        'tax_amount' => 'xsd:int',
        'amount' => 'xsd:int',
        'net_amount' => 'xsd:int',
        'date' => 'xsd:string',
        'terminal_id' => 'xsd:int',
        'opening_id' => 'xsd:int',
        'web_id' => 'xsd:int',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_expenseServer'
);

//Add Expense
$server->register(
    'add_expense_category',
    array(
        'branch_id' => 'xsd:int',
        'exp_category_name' => 'xsd:string',
        'web_id' => 'xsd:int',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_expense_categoryServer'
);

//Remove Expense
$server->register(
    'deleteExpense',
    array('web_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#deleteExpenseServer'
);

//Add Expense Category
$server->register(
    'getExpenseCategory',
    array('user_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getExpenseCategoryServer'
);

//G Expense Category
$server->register(
    'getExpenseDetails',
    array('branch_id' => 'xsd:int','terminal_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getExpenseDetailsServer'
);

/*-----------------------------JOB ORDER INSERTS START FROM HERE -----------------------------*/
// add Job Order General //
$server->register(
    'add_joborderGeneral',
    array(
        'finished_good_id' => 'xsd:int', 'Total_qty' => 'xsd:string', 'Received_qty' => 'xsd:int', 'status_id' => 'xsd:int', 'job_status_id' => 'xsd:int', 'created_at' => 'xsd:string'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_joborderGeneralServer'
);

// add Job Order Assign //
$server->register(
    'add_joborderAssign',
    array('job_id' => 'xsd:int', 'master_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#vServer'
);

// add Job Order Account //
$server->register(
    'add_joborderAccount',
    array('job_id' => 'xsd:int', 'cost' => 'xsd:string', 'master_cost' => 'xsd:string', 'retail_cost' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_joborderAccountServer'
);

// add Job Order Customer //
$server->register(
    'add_joborderCustomer',
    array('job_id' => 'xsd:int', 'customer_id' => 'xsd:string', 'receipt_no' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_joborderCustomerServer'
);

/*-----------------------------JOB ORDER INSERTS END HERE -----------------------------*/

// Attendance  //
$server->register(
    'add_attendance',
    array('acc' => 'xsd:string', 'dateIn' => 'xsd:string', 'dateOut' => 'xsd:string', 'clockIn' => 'xsd:string', 'clockOut' => 'xsd:string', 'mode' => 'xsd:int', 'branch' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_attendanceServer'
);

// Get Ready Orders  //
$server->register(
    'getReadyOrders',
    array('receipt' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getReadyOrdersServer'
);

// Get Update Orders  //
$server->register(
    'updateSalesStatus',
    array(
        'id' => 'xsd:int',
        'totalAmount' => 'xsd:int'
    ),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#updateSalesStatusServer'
);

// Get Sales Details Orders  //
$server->register(
    'updateSalesDetailsStatus',
    array(
        'receipt_id' => 'xsd:int',
        'id' => 'xsd:int',
        'qty' => 'xsd:int',
        'totalAmount' => 'xsd:int'
    ),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#updateSalesDetailsStatusServer'
);

// Get Download Discount  //
$server->register(
    'download_discount',
    array(),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#download_discountServer'
);

// Get Download Discount  //
$server->register(
    'download_period',
    array(),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#download_periodServer'
);

// Get Download Discount  //
$server->register(
    'download_product',
    array(),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#download_productServer'
);

// Get Download Discount  //
$server->register(
    'download_category',
    array(),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#download_categoryServer'
);

// ADD CASH IN  //
$server->register(
    'add_cashIn',
    array('user_id' => 'xsd:int', 'opening' => 'xsd:int', 'terminal' => 'xsd:int', 'amount' => 'xsd:int', 'narration' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_cashInServer'
);

// ADD CASH OUT  //
$server->register(
    'add_cashOut',
    array('user_id' => 'xsd:int', 'opening' => 'xsd:int', 'terminal' => 'xsd:int', 'amount' => 'xsd:int', 'narration' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_cashOutServer'
);

// FETCH PRINTER DETAILS  //
$server->register(
    'getPrintDetails',
    array('terminal_id' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getPrintDetailsServer'
);

// FETCH SALES PERSON DETAILS  //
$server->register(
    'getSalesPerson',
    array('branch_id' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getSalesPersonServer'
);

// getReceiptGeneralById //
$server->register(
    'getReceiptGeneralById',
    array('receipt_no' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getReceiptGeneralByIdServer'
);

// getReceiptGeneralById //
$server->register(
    'getReceiptDetailsById',
    array('receipt_no' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getReceiptDetailsByIdServer'
);

// getHeaderFooterPrinter //
$server->register(
    'getHeaderFooterPrinter',
    array('terminal' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getHeaderFooterPrinterServer'
);

// checkTerminal //
$server->register(
    'checkTerminal',
    array('terminal' => 'xsd:int', 'branch' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#checkTerminalServer'
);

// GET PRODUCT WISE DISCOUNT BY CUSTOMER //
$server->register(
    'getProductWiseDiscountByCustomer',
    array('cust_id' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getProductWiseDiscountByCustomerServer'
);

// ADD MACHINE EMPLOYEE //
$server->register(
    'add_machine_employee',
    array('acc' => 'xsd:string', 'register' => 'xsd:string', 'raw' => 'xsd:string', 'branch' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_machine_employeeServer'
);

// GET MACHINE EMPLOYEE //
$server->register(
    'get_machine_employee',
    array('branch' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_machine_employeeServer'
);

// add cheque details
$server->register(
    'add_cheque_details',
    array(
        'cust_id' => 'xsd:int',
        'no' => 'xsd:int',
        'date' => 'xsd:string',
        'amount' => 'xsd:string',
        'type' => 'xsd:string',
        'bankname' => 'xsd:string',   // parameter
        'narration' => 'xsd:string',
        'branch_id' => 'xsd:int',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_cheque_detailsServer'
);

// GET MACHINE EMPLOYEE //
$server->register(
    'add_cheque_information',
    array('cheque_no' => 'xsd:string', 'receipt_no' => 'xsd:string', 'amount' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#add_cheque_informationServer'
);

// GET BOUNCED CHEQUE //
$server->register(
    'getBouncedCheque',
    array(),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getBouncedChequeServer'
);

// GET BOUNCED CHEQUE //
$server->register(
    'updateBouncedChequeStatus',
    array('id' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#updateBouncedChequeStatusServer'
);


// RESET OPENING AMOUNT //
$server->register(
    'reset_opening',
    array('id' => 'xsd:int', 'amount' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#reset_openingServer'
);

// get customer Ledger //
$server->register(
    'get_customer_ledger',
    array('custid' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_customer_ledgerServer'
);

// get customer Ledger //
$server->register(
    'get_vendor_ledger',
    array('vendorid' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_vendor_ledgerServer'
);

// get customer Ledger //
$server->register(
    'get_customers',
    array('company_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_customersServer'
);

// get Taxes By Company //
$server->register(
    'getTaxes',
    array('company_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getTaxesServer'
);

// get Delivery Charges By Branch //
$server->register(
    'getDeliveryCharges',
    array('company_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getDeliveryChargesServer'
);

// get Inventory Reference By Company //
$server->register(
    'getInventoryReference',
    array('company_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getInventoryReferenceServer'
);

// get Inventory Images By Product ID //
$server->register(
    'getInventoryImages',
    array('item_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getInventoryImagesServer'
);


// REGISTER CUSTOMER FOR APP //
$server->register(
    'registerCustomerForApp',
    array('cust_id' => 'xsd:int', 'plateform' => 'xsd:string', 'device_manufacturer' => 'xsd:string', 'device_model' => 'xsd:string', 'device_serial' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#registerCustomerForAppServer'
);

// VERIFY OTP FOR APP //
$server->register(
    'verify_otp',
    array('cust_id' => 'xsd:int', 'otp' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#verify_otpServer'
);

// VERIFY OTP FOR APP //
$server->register(
    'update_password',
    array('userid' => 'xsd:int', 'password' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#update_passwordServer'
);

// VERIFY OTP FOR APP //
$server->register(
    'download_floors',
    array('branch_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#download_floorsServer'
);

// VERIFY OTP FOR APP //
$server->register(
    'download_bank_discount',
    array('branch_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#download_bank_discountServer'
);

// DOWnLOAD SALES ORDEr MODE //
$server->register(
    'download_sales_order_mode',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#download_sales_order_modeServer'
);

// DOWnLOAD SALES ORDEr MODE //
$server->register(
    'download_kitchen_departments',
    array('branch_id' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#download_kitchen_departmentsServer'
);

// DOWnLOAD SALES ORDEr MODE //
$server->register(
    'download_kitchen_departments_printers',
    array('branch_id' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#download_kitchen_departments_printersServer'
);

// DELETE SALES DETAILS //
$server->register(
    'delete_sales_details_items',
    array('web_id' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#delete_sales_details_itemsServer'
);

// DOWNLOAD SERVICE PROVIDERS //
$server->register(
    'download_service_providers',
    array('branch_id' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#download_service_providersServer'
);

// DOWNLOAD PERMISSIONS //
$server->register(
    'download_permission',
    array('terminal_id' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#download_permissionServer'
);

// CUSTOMER REGISTRATION //
// $server->register(
    // 'customer_Registration',
    // array('name' => 'xsd:string','username' => 'xsd:string','password' => 'xsd:string'),    // parameter
    // array('return' => 'xsd:string'),     // output
    // 'urn:server',                        // namespace
    // 'urn:server#customer_RegistrationServer'
// );

// CUSTOMER LOGIN //
$server->register(
    'customer_Login',
    array('username' => 'xsd:string','password' => 'xsd:string','device_serial' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#customer_LoginServer'
);

// CUSTOMER REGISTRATION //
$server->register(
    'logout',
    array('authorization_id' => 'xsd:int'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#logoutServer'
);

// CHECK USERNAME AND PASSWORD //
$server->register(
    'chkUsernameOrPassword',
    array('username' => 'xsd:string','password' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#chkUsernameOrPasswordServer'
);

//  FBR UPLOAD SERVICE //
$server->register(
    'verifyFBRDetails',
    array('orderId' => 'xsd:string','branchId' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#verifyFBRDetailsServer'
);

//  SRB UPLOAD SERVICE //
$server->register(
    'sentInvoiceToSRB',
    array('companyId' => 'xsd:string','branchId' => 'xsd:string','orderId' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#sentInvoiceToSRBServer'
);

//MOBILE PROMOTION IMAGES
$server->register(
    'getMobilePromoImages',
    array(
        'company_id' => 'xsd:int',
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getMobilePromoImagesServer'
);

// get login
$server->register(
    'sendPushNotification',
    array('clientId' => 'xsd:string','orderId' => 'xsd:string','amount' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#sendPushNotificationServer'
);

// UPDATE USER CLIENT ID
$server->register(
    'updateClientId',
    array('clientId' => 'xsd:string','terminalId' => 'xsd:int','branchId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#updateClientIdServer'
);

// CUSTOMER REGISTRATION
$server->register(
    'customer_registration',
    array('number' => 'xsd:string','password' => 'xsd:string','companyId' => 'xsd:string','clientId' => 'xsd:string','customerId' => 'xsd:string','latitude' => 'xsd:string','longitude' => 'xsd:string','device_serial' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#customer_registrationServer'
);

// UPDATE USER CUSTOMER ID
$server->register(
    'updateCustomerClientId',
    array('clientId' => 'xsd:string','userId' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#updateCustomerClientIdServer'
);

// DOWnLOAD COMPANIES //
$server->register(
    'downloadCompanies',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#downloadCompaniesServer'
);

// add sales receipt //
$server->register(
    'sync_salesReceipt',
    array(
        'uid' => 'xsd:int', 
		'receipt' => 'xsd:string',
		'openingId' => 'xsd:int', 
		'orderMode_id' => 'xsd:int', 
		'customerId' => 'xsd:int', 
		'paymentId' => 'xsd:int',
		'actual_amount' => 'xsd:string',
		'total_amount' => 'xsd:string',
		'totalItem_qty' => 'xsd:string',
		'delivery' => 'xsd:string', 
		'branch' => 'xsd:int', 
		'date' => 'xsd:string', 
		'time' => 'xsd:string', 
		'terminal' => 'xsd:int',
		'salesPerson' => 'xsd:int',
		'webid' => 'xsd:int',
		'due_date' => 'xsd:string',
		'delivery_person_name' => 'xsd:string',
		'contact_no' => 'xsd:string',
		'vehicle_no' => 'xsd:string',
		'service_provider_order_no' => 'xsd:string',
		'onlineorder' => 'xsd:int',
		'receiveAmount' => 'xsd:string',
        'amountPaid_back' => 'xsd:string',
		'discount_amount' => 'xsd:string',
        'coupon' => 'xsd:string',
        'promo_code' => 'xsd:string',
        'sales_tax_amount' => 'xsd:string',
        'service_tax_amount' => 'xsd:string',
        'credit_card_transaction_id' => 'xsd:int',
        'delivery_charges_id' => 'xsd:int',
        'delivery_charges_amount' => 'xsd:string',
        'bank_discount_id' => 'xsd:int',
        'srb' => 'xsd:string',
		'rider_charges' => 'xsd:string',
		'bill_print_name' => 'xsd:string',
		'machine_terminal_count' => 'xsd:string'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#sync_salesReceiptServer'
);                   // description


// DOWnLOAD UPDATED DEPARTMENT //
$server->register(
    'downloadUpdateDepartment',
    array('departmentId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#downloadUpdateDepartmentServer'
);

// DOWnLOAD UPDATED CUSTOMER //
$server->register(
    'downloadUpdateCustomer',
    array('CustomerId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#downloadUpdateCustomerServer'
);

// DOWnLOAD STOCK//
$server->register(
    'getUpdatedStock',
    array('productId' => 'xsd:int','branchId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getUpdatedStockServer'
);


// DOWnLOAD PROMOTION//
$server->register(
    'getPromotions',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getPromotionsServer'
);

// DOWnLOAD Service Provider Orders//
$server->register(
    'getServiceProviderOrders',
    array('providerId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getServiceProviderOrdersServer'
);

// INSERT CUSTOMER HEADERS //
$server->register(
    'add_custom_headers',
    array(
		'mobile' => 'xsd:string',
		'columnOne' => 'xsd:string',
		'columnTwo' => 'xsd:string',
		'columnThree' => 'xsd:string',
		'columnFour' => 'xsd:string',
		'image' => 'xsd:string'
	),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getadd_custom_headersServer'
);

// get login
$server->register(
    'getTokenFromPayMob',
    array('orderId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getTokenFromPayMobServer'
);

// Branch Emails
$server->register(
    'getBranchEmails',
    array('branchId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getBranchEmailsServer'
);

// PUSH NOTIFICATIONS
$server->register(
    'getParentTerminal',
    array('terminalId' => 'xsd:int','receiptId' => 'xsd:int','userId' => 'xsd:int','mode' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getParentTerminalServer'
);

// CHECK LOGGED IN
$server->register(
    'checkLoggedIn',
    array('userId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#checkLoggedInServer'
);

// UPDATE FIREBASE TOKEN IN USER TABLES
$server->register(
    'updateUserClientId',
    array('userId' => 'xsd:int','device_token' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#updateUserClientIdServer'
);

// MESSAGE WEBSERVICE
$server->register(
    'sendSms',
    array('companyId' => 'xsd:int',
			'branchId' => 'xsd:int',
			'mode' => 'xsd:int',
			'orderId' => 'xsd:int',
			'customerNumber' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#sendSmsServer'
);

// DOWNLOAD CUSTOMER LEDGER //
/*$server->register(
    'get_customer_ledger',
    array('customer_id' => 'xsd:string'),    // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#get_customer_ledgerServer'
);*/

// Inventory References Download
$server->register(
    'getAllInventoryReferences',
    array('branchId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getAllInventoryReferencesServer'
);

// VOID RECEIPT 
$server->register(
    'void_receipt',
    array('receiptId' => 'xsd:int','status' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#void_receiptServer'
);

// CLOUD  RECEIPT 
$server->register(
    'cloudReceipt',
    array('receiptId' => 'xsd:int','imageUrl' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#cloudReceiptServer'
);

// CLOUD KITCHEN RECEIPT 
$server->register(
    'cloudKitchenPrint',
    array('receiptId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#cloudKitchenPrintServer'
);

// CLOUD SERVICE RECEIPT 
$server->register(
    'cloudServicePrint',
    array('receiptId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#cloudServicePrintServer'
);

// SEND VOICE TO PRINTER
$server->register(
    'sendVoiceToPrinter',
    array('text' => 'xsd:string','url' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#sendVoiceToPrinterServer'
);

// APPLICATION UPDATES
$server->register(
    'getApplicationUpdates',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getApplicationUpdatesServer'
);


// WHATSAPP SEND MESSAGES 
$server->register(
    'sentWhatsAppMessage',
    array(
		'number' => 'xsd:string',
		'companyName' => 'xsd:string',
		'customerName' => 'xsd:string',
		'receiptNo' => 'xsd:string',
		'receiptDate' => 'xsd:string',
		'amount' => 'xsd:string',
	),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#sentWhatsAppMessageServer'
);

// APPLICATION UPDATES
$server->register(
    'orderStatusChange',
    array(
	'receiptId' => 'xsd:string',
	'status' => 'xsd:int',
	),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#orderStatusChangeServer'
);

// WHATSAPP SEND MESSAGES 
$server->register(
    'createInventory',
    array(
		'companyId' => 'xsd:string',
		'departmentId' => 'xsd:string',
		'subdepartmentId' => 'xsd:string',
		'branchId' => 'xsd:string',
		'productName' => 'xsd:string',
		'costprice' => 'xsd:string',
		'price' => 'xsd:string',
		'stock' => 'xsd:string',
	),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#createInventoryServer'
);

// ENTER PHYSICAL STOCK TAKING 
$server->register(
    'addClosingInventory',
    array(
		'user_id' => 'xsd:string',
		'inventory_id' => 'xsd:string',
		'stock' => 'xsd:string',
		'entry_date' => 'xsd:string',
	),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#addClosingInventoryServer'
);

// ENTER STOCK MANAGEMENT
$server->register(
    'addStockAdjustment',
    array(
		'branch' => 'xsd:int',
		'productId' => 'xsd:int',
		'stock' => 'xsd:string',
		'userId' => 'xsd:int',
	),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#addStockAdjustmentServer'
);


// get login
$server->register(
    'currency',
    array('company' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#currencyServer'
);

// CHANGE ORDER STATUS
$server->register(
    'updateOrderStatus',
    array(
		'receiptId' => 'xsd:int',
		'status' => 'xsd:int',
	),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#updateOrderStatusServer'
);


// CHANGE DELIVERY PERSON/RIDER
$server->register(
    'changeRider',
    array(
		'receiptId' => 'xsd:int',
		'ridderId' => 'xsd:int',
	),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#changeRiderServer'
);

// get inventory //
$server->register(
    'getVoidReceipts',
    array('opening_id' => 'xsd:int','temrinal_id' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getVoidReceiptsServer'
);  

$server->register(
    'getSalesTaxMode',
    array(),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#getSalesTaxModeServer'
);


// get Download Sales Receipts for web
$server->register(
    'DownloadSalesReceipts',
    array('branchId' => 'xsd:string', 'terminalId' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#DownloadSalesReceipts'
);


// get Download Sales Receipts for web
$server->register(
    'DownloadSalesReceiptsVariations',
    array('orderId' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#DownloadSalesReceiptsVariations'
);

// get Download Sales Receipts for web
$server->register(
    'DownloadSalesReceiptsAddons',
    array('orderId' => 'xsd:string'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#DownloadSalesReceiptsAddons'
);

// RIZWAN TRADERS CLOUD RECEIPT 
$server->register(
    'RizwanTradersCloudPrinter',
    array('receiptId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#RizwanTradersCloudPrinterServer'
);

// add sales general Account//
$server->register(
    'update_customer',
    array(
        'customer_id' => 'xsd:int',
        'name' => 'xsd:string',
        'mobile' => 'xsd:string',
        'address' => 'xsd:string',
        'membership_card_no' => 'xsd:string'
    ),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#update_customerServer'
);   

// RIZWAN TRADERS CLOUD RECEIPT 
$server->register(
    'sendStockPushNotification',
    array('productId' => 'xsd:int','companyId' => 'xsd:int','branchId' => 'xsd:int'),   // parameter
    array('return' => 'xsd:string'),     // output
    'urn:server',                        // namespace
    'urn:server#sendStockPushNotificationServer'
);

// Use the request to invoke the service
$server->service(file_get_contents("php://input"));
