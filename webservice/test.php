<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
require "crud.php";
require "printtest.php";
date_default_timezone_set("Asia/Karachi");

error_reporting(E_ALL);

$crud = new Crud();

function getInventory($id)
{
    if (!empty($id) && $id > 0) {

        $result = $GLOBALS['crud']->runQuery("SELECT b.id,b.item_code,b.department_id,b.product_name,b.sub_department_id,b.isPos,b.isOnline,(Select SUM(balance) from inventory_stock where product_id = a.product_id and branch_id = a.branch_id) as qty,c.name,(SELECT cost_price FROM `inventory_stock` where stock_id = (Select MAX(stock_id) from inventory_stock where product_id = a.product_id)) as cost_price,d.cost_price as new_cost_price,d.actual_price,d.tax_rate,d.tax_amount,d.retail_price,d.wholesale_price,d.discount_price,b.image,'Inventory' as status,e.reminder_qty as reminderqty,b.product_description,b.short_description,b.weight_qty as weight_qty  FROM inventory_stock a INNER JOIN inventory_general b on b.id = a.product_id INNER JOIN inventory_uom c on c.uom_id = b.uom_id INNER JOIN inventory_price d on d.product_id = b.id and d.status_id = 1 INNER JOIN inventory_qty_reminders e on e.inventory_id = b.id where a.branch_id = $id and b.status = 1 and b.isHide = 0  GROUP BY a.product_id  
											UNION
											SELECT a.product_id as id,a.item_code,b.department_id,a.item_name,b.sub_department_id,a.isPos,a.isOnline,a.quantity,c.name,(SELECT cost_price FROM `inventory_stock` where stock_id = (Select MAX(stock_id) from inventory_stock where product_id = a.product_id))as cost_price,d.cost_price as new_cost_price,d.actual_price,d.tax_rate,d.tax_amount,d.retail_price,d.wholesale_price,d.discount_price,b.image,'Open' as status,'' as reminderqty,b.product_description,b.short_description,b.weight_qty as weight_qty  FROM pos_products_gen_details a INNER JOIN inventory_general b on b.id = a.product_id LEFt JOin inventory_uom c on c.uom_id = a.uom  INNER JOIN pos_product_price d on d.pos_item_id = a.pos_item_id and d.status_id = 1 INNER JOIN inventory_price e on e.product_id = a.product_id and e.status_id = 1 WHERE a.branch_id = $id and a.status_id = 1 and a.isHide = 0");
        //    $result = $GLOBALS['crud']->runQuery("SELECT b.id,b.item_code,b.department_id,b.product_name,(Select SUM(balance) from inventory_stock where product_id = a.product_id and branch_id = a.branch_id) as qty,c.name,a.cost_price,a.retail_price,a.wholesale_price,a.discount_price,b.image FROM inventory_stock a INNER JOIN inventory_general b on b.id = a.product_id INNER JOIN inventory_uom c on c.uom_id = a.uom where a.branch_id = '$id' and b.status = 1  GROUP BY a.product_id");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}
// echo sentWhatsAppMessage("923112108156","Sabsons Distribution","Muhammad Faizan","20230807114559","2023-05-22","300000");
function sentWhatsAppMessage($number,$companyName,$customerName,$receiptNo,$receiptDate,$amount){
	$number =  $number;//$_GET['number'];
	$version =  "v15.1";//$_GET['version'];
	$phoneId = "105420688989582";//$_GET['phoneId'];
	$template_name = "receipt";//$_GET['template'];
	$code = "en_US";//$_GET['code'];
	$bearer = "EAASlFdcyGIsBO5VIwpZCPV759TMB7HQDAoKPqZAX7cwdX2NcvpSb6e0urQJMRy9WzLGroBZC5KfgX2ZBsmoSmWZBzXZArEWk0o32zReVccSBswVWX5WjzWU8ZCTZBRmAiX0TX0ouZBbQNSmJxSFJzv2ZC5AJgN9DbGEEDOwP9lMsIZAmP0ZAdnzQaJUfAa6G1yJCTY3xj2N1fXUcajzNjOCf";//$_GET['bearer'];
	
	$orderDetails = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.total_amount,a.date,b.branch_name,c.name as company_name,d.name as customer_name FROM sales_receipts a INNER JOIN branch b on b.branch_id = a.branch INNER JOIN company c on c.company_id = b.company_id INNER Join customers d on d.id = a.customer_id where receipt_no = $receiptNo");
	
	$myArray = [];
	$insideArray = [];
	
	array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $orderDetails[0]["company_name"],
	]);
	array_push($myArray, (object)[
        'type' => 'header',
        'parameters' => $insideArray,
	]);
	
	$insideArray = [];
	
	array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $orderDetails[0]["customer_name"],
	]);
	
	array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $orderDetails[0]["receipt_no"],
	]);
	
	array_push($insideArray, (object)[
        'type' => 'text',
        'text' => date("d M Y",strtotime($orderDetails[0]["date"])),
	]);
	array_push($insideArray, (object)[
        'type' => 'text',
        'text' => number_format($orderDetails[0]["total_amount"],0),
	]);
	
	array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $orderDetails[0]["id"],
	]);
	
	array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $orderDetails[0]["branch_name"],
	]);
	
	array_push($myArray, (object)[
        'type' => 'body',
        'parameters' => $insideArray,
	]);
	
	
	$insideArray = [];
	array_push($insideArray, (object)[
        'type' => 'payload',
        'payload' => $receiptNo,
	]);
	array_push($myArray, (object)[
        'type' => 'button',
		"sub_type" => "URL",
		"index" => "0",
        'parameters' => $insideArray,
	]);
	
	$template = [
		"name" => $template_name,
		"language" => [
			"code" =>  $code,
		],
		"components" => $myArray,
	];
	$myObj   = array();
    $myObj['messaging_product'] =   "whatsapp";
    $myObj['to'] = $number; //"923452670301",
    $myObj["type"] =  "template";
    $myObj["template"] = $template;
	$myobject    = json_encode($myObj);
	
	$url = "https://graph.facebook.com/v17.0/103444702767558/messages";
	$authorization = "Authorization: Bearer ".$bearer; //EAAIz8ObxruYBANXiBwoBzm56FIFHLnBZBnJHuHosRRM1fr8Iu7wZB9iUioYjl2yDQGyTCIhm3RsikAUpl19b82qOxtfBOtyOMLlMmZCCijn5Nc2cx1UuzkRsHnla9XZBiFwAHSrckepNlSY5ngYyiIZCp3VnZCEQ94kAkVsC8DtgyMKYEjsIhNRJjm6vQjQpzIWCgkjBwZCBQZDZD
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json",$authorization));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $myobject);
		$result = curl_exec($curl);
		$outPut = json_decode($result, true);
		if (curl_errno($curl)) {
			$error_msg = curl_error($curl);
		}
		if (isset($error_msg)) {
			print_r($error_msg);
		}
		
		// $result = json_decode($result);
		print_r($result);
		// echo $result["messages"];
	}


// echo sendsms(7,17,2,'20230306175034','03232985464');
// echo sendsms(74,219,2,'20230620211310','03232985464');


// echo customer_registration("03112108156","03112108156",7,'None',0,'23.030','30.32','B-6T50LZ1-D9529C36');
function customer_registration($number, $password,$companyId,$clientId,$customerId,$latitude,$longitude,$device_serial)
{
	$otp = rand(1000, 9999);	
	$customer = $GLOBALS['crud']->runQuery("SELECT *  FROM `customers` where mobile = '$number' and company_id = $companyId ");
	if (count($customer) > 0) {
		$update = $GLOBALS['crud']->runQuery("update `customers` set otp = '$otp', client_id = '$clientId', password = '$password', latitude = '$latitude', longitude = '$longitude', device_serial = '$device_serial'  where id = " . $customer[0]["id"] . "");
		if ($update > 0) {
			$message = "Your registration is ".$otp;
			echo send_otp($number,$message);
			// echo sendPushNotificationToCustomerApp($clientId,$message);
			return $customer[0]["id"];
		} else {
			return 6; 
		}

	}else{

		$insert = $GLOBALS['crud']->runQuery("INSERT into `customers` (name,status_id,country_id,city_id,mobile,password,otp,client_id,latitude,longitude,device_serial,company_id) values('$number',1,170,1,'$number','$password','$otp','$clientId','$latitude','$longitude','$device_serial',$companyId)");
		if ($insert > 0) {
			echo send_otp($number,$otp);
			// echo sendPushNotificationToCustomerApp($clientId,$message);
			return 1;
		} else {
			return 0;
		}
	}
}
function send_otp($sender,$otp){
	
    $sender = ltrim($sender, '0');
    $mobile = '92'.$sender;///Recepient Mobile Number
    $sender = "Sabify";
    $api_key = "ab2ae90c15c5f1675f74aa04b2631efd";
    $message = $otp; // "Your OTP is ".$otp." ";
	
	$url = "https://bsms.iisol.pk/api.php?key=".$api_key."&receiver=".urlencode($mobile)."&sender=".$sender."&msgdata=".rawurlencode($message);

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $output=curl_exec($ch);
    // echo $output;exit;
    if(curl_errno($ch))
    {
        // return 0;//'error:' . curl_error($c);
    }
    else
    {
        // return 1;//$output;
    }
    curl_close($ch);
}

function create_log($message){
	// error message to be logged
	$error_message = "\n".date("Y-m-d H:i:s")." ".$message;
  
	// path of the log file where errors need to be logged
	$log_file = "./my-errors.log";
  
	// logging error message to given log file
	error_log($error_message, 3, $log_file);
}
// echo getAllInventoryReferences(17);
function getAllInventoryReferences($branchId)
{
	if (!empty($branchId)) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM `inventory_reference` where product_id IN (SELECT product_id FROM `inventory_stock` where branch_id = '$branchId' group by product_id) and refrerence != ''");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}
// echo callingPrint();
function callingPrint()
{

	$order = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.bill_print_name,b.terminal_name,b.terminal_id,a.date,a.time,c.payment_mode FROM sales_receipts a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN sales_payment c on c.payment_id = a.payment_id WHERE a.id = 349196");
	$departments = $GLOBALS['crud']->runQuery("SELECT d.kitchen_department_name FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code LEFT JOIN kitchen_department_details b on b.inventory_department_id = c.department_id LEFT JOIN kitchen_departments_general d on d.id = b.kitchen_depart_id where a.receipt_id = 349196 group by d.kitchen_department_name");
	$items = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,a.item_code,c.product_name,a.total_qty,b.kitchen_depart_id,c.department_id ,d.kitchen_department_name FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code LEFT JOIN kitchen_department_details b on b.inventory_department_id = c.department_id LEFT JOIN kitchen_departments_general d on d.id = b.kitchen_depart_id where a.receipt_id = 349196");
	$print = new PrintReceipt();

	foreach($departments as $department){
		$departmentItems = array_filter($items, function($value) use ($department) {
		  return $value['kitchen_department_name'] == $department[0];  
		});
		$print->KOTSample($order[0],$department[0],$departmentItems);
	}

	sleep(1);
	$print->ReceiptSample($order[0],$items);
	sleep(1);
	$print->ServicePrint($order[0],$items);
}
// echo cloudReceipt(357865,"https://i.ibb.co/YX8qvDf/Rizwan-Traders.jpg");
function cloudReceipt($receiptId,$imageUrl)
{
	$order = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.bill_print_name,b.terminal_name,b.terminal_id,a.date,a.time,c.payment_mode,d.username,a.userid,e.total_amount,e.receive_amount,e.amount_paid_back FROM sales_receipts a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN sales_payment c on c.payment_id = a.payment_id INNER JOIN user_details d on d.id = a.userid INNER JOIN sales_account_general e on e.receipt_id = a.id WHERE a.id = ".$receiptId);
	$departments = $GLOBALS['crud']->runQuery("SELECT d.kitchen_department_name FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code LEFT JOIN kitchen_department_details b on b.inventory_department_id = c.department_id LEFT JOIN kitchen_departments_general d on d.id = b.kitchen_depart_id where a.receipt_id = ".$receiptId." group by d.kitchen_department_name");
	$items = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,a.item_code,c.product_name,a.item_name,a.total_qty,a.item_price,a.total_amount,d.`name` as uom FROM sales_receipt_details a INNER JOIN inventory_general c ON c.id = a.item_code INNER JOIN `inventory_uom` d ON d.`uom_id` = c.`uom_id`  WHERE a.receipt_id = ".$receiptId);
	// $print = new PrintReceipt(500,"N421224NT0788");
	$print = new PrintReceipt(500,"N411231A00721");
	// $print->ReceiptSample($order[0],$items,$imageUrl);
	echo sendVoiceToPrinter('You have a new order, check Sabify','');
	$print->ReceiptSample($order[0],$items,$imageUrl);
}

// echo cloudFiverReceipt(357865,"https://i.pinimg.com/originals/36/cf/a0/36cfa01b357313f964a5d4d07ce63c7b.png");
function cloudFiverReceipt($receiptId,$imageUrl)
{
	$order = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.bill_print_name,b.terminal_name,b.terminal_id,a.date,a.time,c.payment_mode,d.username,a.userid,e.total_amount,e.receive_amount,e.amount_paid_back FROM sales_receipts a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN sales_payment c on c.payment_id = a.payment_id INNER JOIN user_details d on d.id = a.userid INNER JOIN sales_account_general e on e.receipt_id = a.id WHERE a.id = ".$receiptId);
	$departments = $GLOBALS['crud']->runQuery("SELECT d.kitchen_department_name FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code LEFT JOIN kitchen_department_details b on b.inventory_department_id = c.department_id LEFT JOIN kitchen_departments_general d on d.id = b.kitchen_depart_id where a.receipt_id = ".$receiptId." group by d.kitchen_department_name");
	$items = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,a.item_code,c.product_name,a.total_qty,a.item_price,a.total_amount FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code  where a.receipt_id = ".$receiptId);
	// $print = new PrintReceipt(500,"N421224NT0788");
	$print = new PrintReceipt(550,"N43422AR01164");
	// $print->ReceiptFiver($order[0],$items,$imageUrl);
	$print->ReceiptSample($order[0],$items,$imageUrl);
}
// echo cloudKitchenPrint(358737);
// echo cloudKitchenPrint(315904);
// echo callingQasreSherien(382467);
function callingQasreSherien($receiptId)
{
	echo cloudKitchenPrint($receiptId);
	sleep(1);
	echo cloudServicePrint($receiptId);
}
function cloudKitchenPrint($receiptId)
{
	$order = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.bill_print_name,b.terminal_name,b.terminal_id,a.date,a.time,c.payment_mode,d.username,a.userid FROM sales_receipts a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN sales_payment c on c.payment_id = a.payment_id INNER JOIN user_details d on d.id = a.userid WHERE a.id = ".$receiptId);
	$departments = $GLOBALS['crud']->runQuery("SELECT d.kitchen_department_name FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code LEFT JOIN kitchen_department_details b on b.inventory_department_id = c.department_id LEFT JOIN kitchen_departments_general d on d.id = b.kitchen_depart_id where a.receipt_id = ".$receiptId." group by d.kitchen_department_name");
	$items = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,a.item_code,c.product_name,a.total_qty,b.kitchen_depart_id,c.department_id ,d.kitchen_department_name,e.name as uom FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code LEFT JOIN kitchen_department_details b on b.inventory_department_id = c.department_id LEFT JOIN kitchen_departments_general d on d.id = b.kitchen_depart_id INNER JOIN inventory_uom e on e.uom_id = c.uom_id where a.receipt_id = ".$receiptId."  and d.branch_id = (SELECT branch FROM `sales_receipts` WHERE `id` = ".$receiptId.")");
	$printer = $GLOBALS['crud']->runQuery("SELECT * FROM `cloud_printers` where terminal_id = ".$order[0]['terminal_id']." and status = 1");
	$print = new PrintReceipt(500,$printer[0]["serial_number"]);
	// $print = new PrintReceipt(500);
	foreach($departments as $department){
		$departmentItems = array_filter($items, function($value) use ($department) {
		  return $value['kitchen_department_name'] == $department[0];  
		});
		$print->KOTSample($order[0],$department[0],$departmentItems);
		sleep(1);
	}
}
function cloudServicePrint($receiptId)
{
	$order = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.bill_print_name,b.terminal_name,b.terminal_id,a.date,a.time,c.payment_mode FROM sales_receipts a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN sales_payment c on c.payment_id = a.payment_id WHERE a.id = ".$receiptId);
	$departments = $GLOBALS['crud']->runQuery("SELECT d.kitchen_department_name FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code LEFT JOIN kitchen_department_details b on b.inventory_department_id = c.department_id LEFT JOIN kitchen_departments_general d on d.id = b.kitchen_depart_id where a.receipt_id = ".$receiptId." group by d.kitchen_department_name");
	$items = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,a.item_code,c.product_name,a.total_qty,a.total_amount,a.item_price,b.kitchen_depart_id,c.department_id ,d.kitchen_department_name FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code LEFT JOIN kitchen_department_details b on b.inventory_department_id = c.department_id LEFT JOIN kitchen_departments_general d on d.id = b.kitchen_depart_id where a.receipt_id = ".$receiptId."  and d.branch_id = (SELECT branch FROM `sales_receipts` WHERE `id` = ".$receiptId.")");
	$printer = $GLOBALS['crud']->runQuery("SELECT * FROM `cloud_printers` where terminal_id = ".$order[0]['terminal_id']." and status = 1");
	$print = new PrintReceipt(500,$printer[0]["serial_number"]);
	// $print = new PrintReceipt(500);
	$print->ServicePrint($order[0],$items);
	sleep(1);
}
// echo void_receipt(344943);
function void_receipt($receiptId)
{
	if ($receiptId > 0){
		$update = $GLOBALS['crud']->modify_mode("void_receipt =1","sales_receipts","id = '$receiptId' ");
		if ($update > 0) {
			return 1;
		} else {
			return 0;
		}
	}else {
		return 0;
	}
}
// echo sendVoiceToPrinter('You have a new order, check Harrogate Eats','');//http://codeskulptor-demos.commondatastorage.googleapis.com/GalaxyInvaders/pause.wav
// echo sendVoiceToPrinter('','http://codeskulptor-demos.commondatastorage.googleapis.com/GalaxyInvaders/pause.wav');
function sendVoiceToPrinter($content,$link)
{
	$printer = new SunmiCloudPrinter();
	// $sn = "N411229300488";
	$sn = "N411231A00721";
	$printer->pushVoice($sn,$content,$link);
}
// echo add_customer_membership(1,'GYM-1002');
function add_customer_membership($customerId,$memberShip)
{
	if($customerId != ""){
		$count = $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM `customer_membership` where customer_id = ".$customerId);
		$count = $count[0]["count"];
		if($count == 0){
			$fixcolum = "(customer_id,membership_number)";
			$colum = "($customerId,'$memberShip')";
			$result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "customer_membership", true);
			if($result > 0){
				return $result;
			} else {
				return 505;
			}
			
		}else{
			$update = $GLOBALS['crud']->modify_mode("membership_number = '$memberShip'","customer_membership","customer_id = '$customerId' ");
			if ($update > 0) {
				return 1;
			} else {
				return 606;
			}
		}
	}else{
		return 0;
	}
}
function addClosingInventory($userId,$inventoryId,$stock,$date){
	if($userId != "" && $inventoryId != "" && $stock != "" && $date != "" ){
		
		$fixcolum = "(user_id,inventory_id,stock,entry_date)";
        $colum = "($userId,$inventoryId,$stock,'$date')";
        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "physical_stock_taking", false);

        if ($result) {
            return 1;
        } else {
            return 0;
        }
	}else{
		return 505;
	}
}
// echo addStockAdjustment(17,195608,1,1);
function addStockAdjustment($branch,$productId,$stock,$userId)
{
	$grn = $GLOBALS['crud']->runQuery('SELECT COUNT(rec_id) as count FROM purchase_rec_gen');
	$grn = $grn[0]["count"] + 1;
	
	$fixcolum = "(GRN,user_id)";
	$colum = "($grn,$userId)";
	$result1 = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "purchase_rec_gen", false);
	
	$fixcolum1 = "(GRN,item_id,qty_rec)";
	$colum1 = "($grn,$productId,$stock)";
	$result2 = $GLOBALS['crud']->insert_mode($fixcolum1, $colum1, "purchase_rec_stock_adjustment", false);
	
	$fixcolum2 = "(grn_id,product_id,uom,cost_price,retail_price,wholesale_price,discount_price,qty,balance,status_id,branch_id)";
	$colum2 = "($result1,$productId,3,0.00,0.00,0.00,0.00,$stock,$stock,1,$branch)";
	$result3 = $GLOBALS['crud']->insert_mode($fixcolum2, $colum2, "inventory_stock", false);
	
	$fixcolum3 = "(date,product_id,foreign_id,branch_id,qty,stock,cost,retail,narration,adjustment_mode)";
	$colum3 = "('".date('Y-m-d H:s:i')."',$productId,$result3,$branch,$stock,$stock,0.00,0.00,'(Stock Adjustment)',1)";
	$result3 = $GLOBALS['crud']->insert_mode($fixcolum3, $colum3, "inventory_stock_report_table", false);
	
	echo 1;
}

// echo currency(7);
function currency($companyId)
{
	$count = $GLOBALS['crud']->runQuery("SELECT data FROM `settings` where company_id = ".$companyId);
	if(!empty($count)){
		$currency = json_decode($count[0]['data']);
		return $currency->currency;
	}
}

// echo sendsms(74,239,2,'20231026142646','3052392257');
function sendSms($company,$branch,$mode,$orderId,$receiverNumber)
{	
	$receiverNumber = ltrim($receiverNumber, '0');
    $mobile = '92'.$receiverNumber;///Recepient Mobile Number
	
	$orderDetails = $GLOBALS['crud']->runQuery("SELECT a.id,b.name as customer,c.branch_name,a.total_amount,d.receive_amount,e.discount_amount,e.sales_tax_amount,e.delivery_charges,e.service_tax_amount FROM sales_receipts a INNER Join customers b on b.id = a.customer_id INNER JOIN branch c on c.branch_id = a.branch INNER JOIN sales_account_general d on d.receipt_id = a.id INNER JOIN sales_account_subdetails e on e.receipt_id = a.id where a.receipt_no = $orderId");
	$smsTemplate = $GLOBALS['crud']->runQuery("SELECT * FROM `sms_orders`  where company_id = $company and branch_id = $branch and sms_mode_id = $mode");
	$sms_api = $GLOBALS['crud']->runQuery("SELECT * FROM `sms_apis`  where company_id = $company and branch_id = $branch");

	$vars = array(
	  '{$customername}'  => $orderDetails[0]["customer"],
	  '{$branch}'  => $orderDetails[0]["branch_name"],
	  '{$orderId}' => $orderDetails[0]["id"],
	  '{$amount}'  => $orderDetails[0]["total_amount"] ,
	);
	
	$text =  strtr($smsTemplate[0]['sms'],$vars);
	$text =  rawurlencode($text);
	echo smsApiCall($mobile,$text,$sms_api[0]['api'],$sms_api[0]['api_key'],$sms_api[0]['masking']);
}

function smsApiCall($senderMobile,$text,$apiUrl,$apiKey,$masking){
	$key = $apiKey;
	$receiver = $senderMobile;
	$sender = $masking;
	$url = $apiUrl."?key=$key&receiver=$receiver&sender=$sender&msgdata=".$text;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $output=curl_exec($ch);
    // echo $output;exit;
    if(curl_errno($ch))
    {
        // return 0;//'error:' . curl_error($c);
    }
    else
    {
        // return 1;//$output;
    }
    curl_close($ch);
	return $output;
}

// echo updateOrderStatus(398633,4);
function updateOrderStatus($receiptId,$status)
{
	if($receiptId != "" && $status != ""){
		$update = $GLOBALS['crud']->modify_mode("status = $status","sales_receipts","id = $receiptId");
		if ($update > 0) {
			$dbcolumn = "(order_id,status_id,date,time)";
			$values = "($receiptId,$status,'".date('Y-m-d')."','".date('H:s:i')."')";
			$result3 = $GLOBALS['crud']->insert_mode($dbcolumn, $values, "sales_online_order_status", true);
			return 1;
		}else{
			return 0;
		}
	}else{
		return 5;
	}
}
// echo changeRider(398633,22);
function changeRider($receiptId,$riderId)
{
	if($receiptId != "" && $riderId != ""){
		$update1 = $GLOBALS['crud']->modify_mode("service_provider_id = $riderId","service_provider_orders","receipt_id = $receiptId");
		$update2 = $GLOBALS['crud']->modify_mode("provider_id = $riderId","service_provider_ladger","receipt_id = $receiptId");
		return 1;
	}else{
		return 0;
	}
}
// echo sentWhatsAppOTP("03012541277");
function sentWhatsAppOTP($number,$code){
	$number =  $number;//$_GET['number'];
	$version =  "v15.1";//$_GET['version'];
	$phoneId = "105420688989582";//$_GET['phoneId'];
	$template_name = "otp";//$_GET['template'];
	$code = "en";//$_GET['code'];
	$bearer = "EAASlFdcyGIsBO5VIwpZCPV759TMB7HQDAoKPqZAX7cwdX2NcvpSb6e0urQJMRy9WzLGroBZC5KfgX2ZBsmoSmWZBzXZArEWk0o32zReVccSBswVWX5WjzWU8ZCTZBRmAiX0TX0ouZBbQNSmJxSFJzv2ZC5AJgN9DbGEEDOwP9lMsIZAmP0ZAdnzQaJUfAa6G1yJCTY3xj2N1fXUcajzNjOCf";//$_GET['bearer'];
	
	$myArray = [];
	$insideArray = [];
	
	array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $code,
	]);
	
	array_push($myArray, (object)[
        'type' => 'body',
        'parameters' => $insideArray,
	]);
	
	$insideArray = [];
	array_push($insideArray, (object)[
        'type' => 'text',
        'text' => $code,
	]);
	array_push($myArray, (object)[
        'type' => 'button',
		"sub_type" => "URL",
		"index" => "0",
        'parameters' => $insideArray,
	]);
	
	$template = [
		"name" => $template_name,
		"language" => [
			"code" =>  $code,
		],
		"components" => $myArray,
	];
	
	$myObj   = array();
    $myObj['messaging_product'] =   "whatsapp";
    $myObj['to'] = $number; //"923452670301",
    $myObj["type"] =  "template";
    $myObj["template"] = $template;
	$myobject    = json_encode($myObj);

	$url = "https://graph.facebook.com/v17.0/103444702767558/messages";
	$authorization = "Authorization: Bearer ".$bearer; //EAAIz8ObxruYBANXiBwoBzm56FIFHLnBZBnJHuHosRRM1fr8Iu7wZB9iUioYjl2yDQGyTCIhm3RsikAUpl19b82qOxtfBOtyOMLlMmZCCijn5Nc2cx1UuzkRsHnla9XZBiFwAHSrckepNlSY5ngYyiIZCp3VnZCEQ94kAkVsC8DtgyMKYEjsIhNRJjm6vQjQpzIWCgkjBwZCBQZDZD
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json",$authorization));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $myobject);
		$result = curl_exec($curl);
		$outPut = json_decode($result, true);
		if (curl_errno($curl)) {
			$error_msg = curl_error($curl);
		}
		if (isset($error_msg)) {
			print_r($error_msg);
		}
		
		print_r($result);
		// echo $result["messages"];
}

// echo update_customer("189","Faizan Akram","03232985464","Test Address","1234566");
function update_customer($id,$name,$mobile,$address,$membershipNumber)
{
	if ($id > 0) {
		$updateClientId = " name = '$name',mobile = '$mobile',address = '$address',membership_card_no = '$membershipNumber' ";
        $update = $GLOBALS['crud']->modify_mode($updateClientId, "customers", "id = '$id' ");
		 return 1;
	}else {
        return 0;
    }
}
	echo sendPushNotification(195608,7,17);
	function sendPushNotification($productId,$company,$branch){ 
		$statusmessage =  "updated";
		$tokens = array();
		$title = "Stock Updated";
        $stock = $GLOBALS['crud']->runQuery("SELECT SUM(balance) as balance FROM `inventory_stock` WHERE `product_id` = ".$productId);
		$message = "ID ".$productId.",STOCK".$stock[0]["balance"];
		
        $firebaseToken = $GLOBALS['crud']->runQuery("Select * from terminal_details where branch_id = $branch and device_token IS NOT NULL ");
		foreach($firebaseToken as $token){
			array_push($tokens,$token["device_token"]);
		}
		
		$SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';
        $server_api_key_mobile = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';   
		   
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => "Inventory Updated",
                "body" => "New set of Inventory is been updated",
				"icon" => "https://sabsoft.com.pk/Retail/public/assets/images/Sabify72.png",
                "content_available" => true,
                "priority" => "high",
				// "click_action" => ,
            ],
			"data" => [
				"para1" => $message,
			],
        ]; 
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $headers1 = [
            'Authorization: key=' . $server_api_key_mobile,
            'Content-Type: application/json',
        ];
		
        $chs = curl_init();
        curl_setopt($chs, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($chs, CURLOPT_POST, true);
        curl_setopt($chs, CURLOPT_HTTPHEADER, $headers1);
        curl_setopt($chs, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chs, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chs, CURLOPT_POSTFIELDS, $dataString);

        curl_exec($chs);
        $response = curl_exec($ch);
		
		return json_encode($response);
	}
?>
