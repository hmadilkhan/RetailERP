<?php
require "crud.php";
date_default_timezone_set("Asia/Karachi");
// error_reporting(E_ALL);

$crud = new Crud();
 // echo customer_Registration("03101234567","03101234567","7","123456","0","03101234567","03101234567","tst-1345");
function customer_registration($number, $password,$companyId,$clientId,$customerId,$latitude,$longitude,$device_serial)
{
	$otp = rand(1000, 9999);	
	$customer = $GLOBALS['crud']->runQuery("SELECT *,count(*) as count  FROM `customers` where mobile = '$number' and company_id = $companyId ");

	if ($customer[0]["count"] > 0) {
		$update = $GLOBALS['crud']->runQuery("update `customers` set otp = '$otp', client_id = '$clientId', password = '$password', latitude = '$latitude', longitude = '$longitude', device_serial = '$device_serial'  where id = " . $customer[0]["id"] . "");
		if ($update > 0) {
			$message = "Your OTP is  # ".$otp."";
			echo sendPushNotificationToCustomerApp($clientId,$message);
			return $customer[0]["id"];
		} else {
			return 2; 
		}

	}else{
		$insert = $GLOBALS['crud']->runQuery("INSERT into `customers` (status_id,country_id,city_id,mobile,password,otp,client_id,latitude,longitude,device_serial,company_id) values(1,170,1,'$number','$password','$otp','$clientId','$latitude','$longitude','$device_serial',$companyId)");
		if (!empty($insert)) {
			echo sendPushNotificationToCustomerApp($clientId,$message);
			return 1;
		} else {
			return 0;
		}
	}
	
	// if ($customer[0]["count"] > 0) {
		
		// if($checkDeviceExists[0]["count"] == 0){
			// if ($customerId > 0) {
			// echo "if";
			// $update = $GLOBALS['crud']->runQuery("update `customers` set otp = '$otp', client_id = '$clientId', password = '$password', latitude = '$latitude', longitude = '$longitude', device_serial = '$device_serial'  where id = " . $customer[0]["id"] . "");
			// if ($update > 0) {
				// $message = "Your OTP is  # ".$otp."";
				// echo sendPushNotificationToCustomerApp($clientId,$message);
				// return $customer[0]["id"];
			// } else {
				// return 0; 
			// }
		// }else{
			// echo "else";
			// $insert = $GLOBALS['crud']->runQuery("INSERT into `customers` (status_id,country_id,city_id,mobile,password,otp,client_id,latitude,longitude,device_serial) values(1,170,1,'$number','$password','$otp','$clientId','$latitude','$longitude','$device_serial')");
			// if (!empty($insert)) {
				// echo sendPushNotificationToCustomerApp($clientId,$message);
				// return 1;
			// } else {
				// return 0;
			// }
		// }
	// } else {
		// return 2;
	// }

}

// echo customer_Login("03232985464","03232985464","5521");
function customer_Login($number, $password,$device_serial)
{
    if (!empty($number) && !empty($password)) {
		
		$checkDeviceExists =  $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM `customers` WHERE device_serial = '$device_serial'");

		if($checkDeviceExists[0]["count"] == 1){

			$result = $GLOBALS['crud']->runQuery("SELECT * from customers where mobile = '$number' and password = '$password'");
			if (!empty($result) && sizeof($result) > 0) {
				return json_encode($result[0]);
			} else {
				return 0;
			}
		}else{
			return 0;
		}
    } else {

        return 0;
    }
}
// echo updateCustomerClientId("kfjweirweireryuwe",815);
function updateCustomerClientId($clientId,$customerId)
{
	if($clientId != "" && $customerId != 0){
		$count = $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM `customers` where id = '$customerId' and client_id = '$clientId'");
        if ($count[0]["count"] == 0) {
			$result = $GLOBALS['crud']->runQuery("UPDATE `customers` SET `client_id` = '$clientId' where id = $customerId ");
			if ($result > 0) {
				return 1;
			} else { 
				return 0;
			}
		}else{
			return 0;
		}
	}	
}

// echo sendPushNotificationToCustomerApp("eZD9HdKSS1qUWNHMVEU9Of:APA91bH5Qj0I5BdsZtEpLYBZ0ZgFs502-SQkhCG-0hGFUjc1PJHwWygDCwF9peaBOTDJEmxVm1UfIDOnxn54ohDtoW3kDwbuFYo1QDfBAOAEWil83l3vl2cC5u3Nw9GL_g8HLHhzTWOu",1234);
function sendPushNotificationToCustomerApp($clientId,$otp){ 
		$message = "Your OTP is  # ".$otp."";

        $firebaseToken = [];//["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
		array_push($firebaseToken,$clientId);
		$SERVER_API_KEY = 'AAAA8AK3eRE:APA91bF68jDQZ_O1zw6i3oWbjAToxndEGNc2zP4jsZ1_JwTs6G2cqDj_YGypwMEff_k0pAwHbYkRDxFvHK8JbIvkDeT6tWJlJca01lQ7QCfKhRysdrQzXVXb6Hapr1A4xBvq1TKADscb';
		   
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => "Customer Connect",
                "body" => $message,
				"icon" => "https://sabsoft.com.pk/Retail/public/assets/images/desktop-notify-icon.png",
                "content_available" => true,
                "priority" => "high"
            ]
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

        $response = curl_exec($ch);
		
		// return json_encode($response);
}

// echo getReceiptDetailsById(167471);
function getReceiptDetailsById($receiptId)
{
    $result = $GLOBALS['crud']->runQuery("SELECT sales_receipt_details.*,b.product_name FROM sales_receipt_details INNER JOIN (Select id,product_name from inventory_general) b on b.id = sales_receipt_details.item_code where receipt_id = " . $receiptId . "");

    if (!empty($result) && sizeof($result) > 0) {
        $response = array();
        foreach ($result as $key => $value) {
            $response[] = array(
                'total_qty' => $value['total_qty'],
                'total_amount' => $value['total_amount'],
                'discount' => $value['discount'],
                'note' => $value['note'],
                'item_code' => $value['item_code'],
                'item_name' => $value['product_name'],
                'item_price' => $value['item_price'],
                'is_sale_return' => $value['is_sale_return'],
            );
        }
        // print_r($response);exit;
        return json_encode($response);
    } else {
        return 0;
    }
}
// echo sync_salesReceipt(33, 987654321, 1474, 1, 785, 1, 9999, 15, '20211224', 17, "2021-12-24", "17:28:22", 87, 0, 0,"20211224","None","None","None","None",0,"3200",0,0, 0, 0, 0, 0, 0, 0,0,0,0);
function sync_salesReceipt($uid, $receipt, $openid, $order_mode_id, $customer_id, $payment_id, $total_amount, $total_item_qty, $delivery, $branch, $date, $time, $terminal, $sales, $web_id,$due_date,$delivery_person_name,$contact_no,$vehicle_no,$service_provider_order_no,$web,$receive_amount,$amount_paid_back,$discount_amount, $coupon, $promo_code, $sales_tax_amount, $service_tax_amount, $creditTrans, $deliveryCharges,$deliveryChargesAmount,$bank_discount_id,$srb)
{

    if (!empty($uid) && $uid > 0) {
        //If Web Id found then will perform update
        if ($web_id > 0) {
            $columns = "opening_id = '" . $openid . "',order_mode_id = '" . $order_mode_id . "',userid = '" . $uid . "',customer_id = '" . $customer_id . "',payment_id = '" . $payment_id . "',total_amount = '" . $total_amount . "',total_item_qty = '" . $total_item_qty . "',delivery_date = '" . $delivery . "',sales_person_id = '" . $sales . "',due_date = '".$due_date."' ";
            $update = $GLOBALS['crud']->modify_mode($columns, 'sales_receipts', "id = " . $web_id . " ");
            if (is_array($update) && sizeof($update) > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            //CHECKING IF RECEIPT EXISTS
            $select = "SELECT receipt_no FROM sales_receipts where receipt_no = " . $receipt . " AND  terminal_id = ".$terminal." ";
            $checkReceipt =  $GLOBALS['crud']->runQuery($select);

            if(is_array($checkReceipt) && sizeof($checkReceipt) > 0){
                return 0;
            }else{
                // RECEIPT IS NOT FOUND SYSTEM WILL GO TO INSERT RECEIPT
                if ($order_mode_id == 1) {
                    $mode = 4;
                } else {
                    $mode = 1;
                }
                $fixcolum = "(receipt_no,opening_id,order_mode_id,userid,customer_id,payment_id, total_amount,total_item_qty,is_sale_return,status,delivery_date,branch,terminal_id,sales_person_id,date,time,due_date,isSeen,is_notify,web)";

                $colum = "('$receipt','$openid',$order_mode_id,$uid,$customer_id,$payment_id,'$total_amount','$total_item_qty',0,$mode,'$delivery',$branch,$terminal,$sales,'$date','$time','$due_date',1,1,'$web')";

                $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_receipts", true);

                if (!empty($result)) {

					 // ADDING IN SALES RECIPT GENERAL
					 $balance = $total_amount - $receive_amount;
					 $status = ($balance == 0 ? 1 : 0);
					 $fixcolum = "(receipt_id,receive_amount,amount_paid_back,total_amount,balance_amount,status) ";

					 $colum = "($result,'$receive_amount','$amount_paid_back','$total_amount','$balance',$status)";

					 $resultAccountGeneral = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_account_general", true);

					// if (!empty($resultAccountGeneral) && sizeof($resultAccountGeneral) > 0) {
						// ADDING IN SALES RECIPT SUB DETAILS
						$fixcolum = "(receipt_id,discount_amount,coupon,promo_code,sales_tax_amount,service_tax_amount,credit_card_transaction,delivery_charges,delivery_charges_amount,bank_discount_id,srb) ";

						$colum = "($result,$discount_amount,$coupon,$promo_code,$sales_tax_amount,$service_tax_amount,$creditTrans,$deliveryCharges,$deliveryChargesAmount,$bank_discount_id,$srb)";

						$resultaccountsub = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_account_subdetails", true);
					// } 
					
					$clientId = $GLOBALS['crud']->runQuery("SELECT client_id FROM `customers` where id = $customer_id");
					if(!empty($clientId))
					{
						$paymentId = $GLOBALS['crud']->runQuery("SELECT payment_mode FROM `sales_payment` where payment_id = $payment_id");
						$message = "Order ID # ".$result." (".$paymentId[0]["payment_mode"].") Rs.".number_format($total_amount,2);
						echo sendPushNotificationToCustomerApp($clientId[0]["client_id"],$message);
					}

                    $provider = $GLOBALS['crud']->runQuery("SELECT a.provider_id,c.percentage FROM user_salesprovider_relation a INNER JOIN service_provider_details b on b.id = a.provider_id INNER JOIn service_agreement_percentages c on c.percentage_id = b.percentage_id  where a.user_id =  " . $sales . "");
                    $lastBalance = $GLOBALS['crud']->runQuery("SELECT balance FROM service_provider_ladger where ladger_id = (Select Max(ladger_id) from service_provider_ladger where provider_id = " . $provider[0]["provider_id"] . ") ");
                    $amount = ($total_amount * ($provider[0]["percentage"] / 100));
                    $bal = $lastBalance[0]["balance"] + $amount;
					
                    $fix = "(provider_id,debit,credit,balance,order_id,date, narration,receipt_id,receipt_no,receipt_total_amount,delivery_person_name,contact_no,vehicle_no,service_provider_order_no)";
                    $col = "(" . $provider[0]["provider_id"] . ",0," . $amount . "," . $bal . ",1,'" . date("Y-m-d") . "', 'Narration',".$result.",'".$receipt."','".$total_amount."','".$delivery_person_name."','".$contact_no."','".$vehicle_no."','".$service_provider_order_no."')";
                    $pro = $GLOBALS['crud']->insert_mode($fix, $col, "service_provider_ladger", true);
                    
					return $result;

                } else {
                    return 0;
                }
            }

        }
    } else {
        return 0;
    }
}


// echo verifyFBRDetails(170117,155);
function verifyFBRDetails($orderId,$branchId){
    $fbrdetails = $GLOBALS['crud']->runQuery("SELECT * FROM `fbr_details` where branch_id = $branchId and status = 1");

    if (!empty($fbrdetails)){ //& count($fbrdetails) > 0
//        return $fbrdetails[0]['token_id'];
        return getOrderDetailsForFBR($orderId,$fbrdetails);
    }else{
        return 0;
    }

}
function getOrderDetailsForFBR($orderId,$fbrData){
    $orders = $GLOBALS['crud']->runQuery("SELECT a.id,a.date,a.time,IFNULL(c.total_amount,0) as 'total_amount',IFNULL(b.discount_amount,0) as 'discount_amount',IFNULL(b.sales_tax_amount,0) as 'sales_tax_amount' FROM sales_receipts a LEFT JOIN  sales_account_subdetails b on b.receipt_id = a.id LEFT JOIN  sales_account_general c on c.receipt_id = a.id  where a.id = '$orderId'");
    $orderDetails = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,b.product_name,a.total_qty,a.total_amount FROM sales_receipt_details a Inner join inventory_general b on b.id = a.item_code where a.receipt_id = $orderId");
    return getDataForFBR($orders,$orderDetails,$fbrData);
//    print_r($orders);
}
function getDataForFBR($order,$orderItems,$fbrData){
    $orderDetails = array();
	$taxrate = $fbrData[0]['tax_rate'];
	$taxpercentage = $taxrate / 100;

    foreach ($orderItems as $key => $products) {
//        $price = $products['price']  * $products['quantity'];
//        $taxamount = round($price * ($products['taxrate'] / 100), 0);

        $arrayVar = array(
            "ItemCode" => $key + 1,
            "ItemName" => $products['product_name'],
            "Quantity" =>  $products['total_qty'],
            "PCTCode" => "11001010",
            "TaxRate" =>  $taxrate,//$taxrate,
            "SaleValue" =>  $products['total_amount'],//$total,
            "TotalAmount" =>  $products['total_amount'],//$total + $taxamount,
            "TaxCharged" =>  round($products['total_amount'] * $taxpercentage),//$taxamount,
            "Discount" =>  0,
            "FurtherTax" =>  0,
            "InvoiceType" =>  1,
            "RefUSIN" =>  null
        );
        array_push($orderDetails, $arrayVar);
    }

//    //Creating the object to submit the data
    $myObj   = array();
    $myObj['InvoiceNumber'] =  "";
    $myObj['POSID'] = $fbrData[0]['pos_id'];
    $myObj["USIN"] =  $order[0]["id"]; //invoice number
    $myObj["DateTime"] = $order[0]["date"] . ' ' . $order[0]["time"];
    $myObj["BuyerNTN"] = "";
    $myObj["BuyerName"] = "";
    $myObj["BuyerPhoneNumber"] = "";
    $myObj["TotalBillAmount"] =  $order[0]["total_amount"] - $order[0]['discount_amount'];
    $myObj["TotalQuantity"] = count($orderDetails);
    $myObj["TotalSaleValue"] =  $order[0]["total_amount"];
    $myObj["TotalTaxCharged"] =  $order[0]['sales_tax_amount'];
    $myObj["Discount"] =  $order[0]['discount_amount'];
    $myObj["FurtherTax"] = 0;
    $myObj["PaymentMode"] =  1;
    $myObj["RefUSIN"] = "NULL"; //child of the or the return invoce invoive will be 3
    $myObj["InvoiceType"] = 1;
    $myObj["Items"] = $orderDetails;

    $myobject    = json_encode($myObj);
    return sendRequestToFbr($myobject,$order[0]["id"],$fbrData);
   // return  $myobject;
}

//echo test();
function sendRequestToFbr($myobject,$orderId,$fbrData)
{
    //SANDBOX
    // $fbrUrl = "https://esp.fbr.gov.pk:8244/FBR/v1/api/Live/PostData";
	
	// LIVE
	$fbrUrl = "https://gw.fbr.gov.pk/imsp/v1/api/Live/PostData";
    $fbrToken = $fbrData[0]["token_id"];

    $authorization = "Authorization: Bearer ".$fbrToken;
    $url =  $fbrUrl;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json", $authorization));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $myobject);
    $result = curl_exec($curl);
    $outPut = json_decode($result, true);
    $invoiceNumber  = $outPut['InvoiceNumber'];
    $code = $outPut['Code'];
    if ($code ==  100){
        return updateInvoiceNumberToTable($invoiceNumber,$orderId);
    }
   // print_r($outPut );
}

function updateInvoiceNumberToTable($invoiceNumber,$orderId){
    $update = $GLOBALS['crud']->runQuery("Update sales_receipts set fbrInvNumber = '$invoiceNumber' where id  = $orderId");
    return $invoiceNumber;

}
// echo testing();
function testing()
{
	// return /home/admin/web/sabsoft.com.pk/public_html/Retail/App/Http/Controllers/TestController::Test();
}
// echo invent_stock_detection(17,195630,1,''); 
function invent_stock_detection($branchId, $itemCode, $totalQty, $status)
{
  
    if (!empty($branchId) && $branchId > 0 && !empty($itemCode) && $itemCode > 0) {

        // $fixcolum = "(receipt_id,item_code,total_qty,total_amount)";

        // $colum = "($receipt,$item_code,'$total_qty','$total_amount')";

        $result = $GLOBALS['crud']->runQuery("SELECT * FROM inventory_stock WHERE product_id = $itemCode and branch_id = $branchId and status_id IN(1,3) ");

        $updatedstock = 0;

        if (!empty($result)) {

            if ($status == "Open") {
                $weightQty = $GLOBALS['crud']->runQuery("SELECT weight_qty FROM `inventory_general` where id = '$itemCode'");
                $qty = $totalQty / $weightQty[0]["weight_qty"];
                $updatedstock = $qty;
            } else {
                $updatedstock = $totalQty;
            }

            for ($s = 0; $s < sizeof($result); $s++) {

                $value = $GLOBALS['crud']->runQuery("SELECT * FROM inventory_stock WHERE product_id = $itemCode and branch_id = $branchId and status_id  IN(1,3)");
                $updatedstock = ($updatedstock - $value[0]["balance"]);
                // return  $updatedstock;

                if ($updatedstock > 0) {
                    $columns = "balance = 0,status_id = 2";
                    $update = $GLOBALS['crud']->modify_mode($columns, 'inventory_stock', "stock_id = " . $value[0]["stock_id"] . " ");
                } else if ($updatedstock < 0) {
                    $updatedstock = $updatedstock * (-1);
                    $columns = "balance = " . $updatedstock . ",status_id = 1";
//                    echo  $updatedstock;
                    $update = $GLOBALS['crud']->modify_mode($columns, 'inventory_stock', "stock_id = " . $value[0]["stock_id"] . " ");
                    break;
                } else if ($updatedstock == 0) {
                    $columns = "balance = 0,status_id = 2";
                    $update = $GLOBALS['crud']->modify_mode($columns, 'inventory_stock', "stock_id = " . $value[0]["stock_id"] . " ");
                    break;
                }
            }
            return 1;
        } else {
            return 2;
        }
    } else {
        return 0;
    }
}
// echo getUpdatedStock(194826,17);
function getUpdatedStock($productId,$branchId)
{
	if($productId != "" ){
		$result = $GLOBALS['crud']->runQuery("Select SUM(balance) as stock from inventory_stock where product_id = $productId and branch_id = $branchId and status_id = 1");
		if (!empty($result) && sizeof($result) > 0) {
			return $result[0]["stock"];
		} else { 
			return 0;
		}
	}
}

echo add_salesReceipt(1, "123499999999", 0, 4, 815, 5, 1, 1, 0, 0,'', '', 0, 0, 615,"","0","0","0","0",0); 
function add_salesReceipt($uid, $receipt, $openid, $order_mode_id, $customer_id, $payment_id, $total_amount, $total_item_qty, $delivery, $branch, $date, $time, $terminal, $sales, $web_id,$due_date,$delivery_person_name,$contact_no,$vehicle_no,$service_provider_order_no,$web)
{

    if (!empty($uid) && $uid > 0) {
        //If Web Id found then will perform update
        if ($web_id > 0) {
            $columns = "opening_id = '" . $openid . "',order_mode_id = '" . $order_mode_id . "',userid = '" . $uid . "',customer_id = '" . $customer_id . "',payment_id = '" . $payment_id . "',total_amount = '" . $total_amount . "',total_item_qty = '" . $total_item_qty . "',delivery_date = '" . $delivery . "',sales_person_id = '" . $sales . "',due_date = '".$due_date."' ";
            $update = $GLOBALS['crud']->modify_mode($columns, 'sales_receipts', "id = " . $web_id . " ");
            if (is_array($update) && sizeof($update) > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            //CHECKING IF RECEIPT EXISTS
            $select = "SELECT receipt_no FROM sales_receipts where receipt_no = " . $receipt . " AND  terminal_id = ".$terminal." ";
            $checkReceipt =  $GLOBALS['crud']->runQuery($select);

            if(is_array($checkReceipt) && sizeof($checkReceipt) > 0){
                return 0;
            }else{
                // RECEIPT IS NOT FOUND SYSTEM WILL GO TO INSERT RECEIPT
                if ($order_mode_id == 1) {
                    $mode = 4;
                } else {
                    $mode = 1;
                }
                $fixcolum = "(receipt_no,opening_id,order_mode_id,userid,customer_id,payment_id, total_amount,total_item_qty,is_sale_return,status,delivery_date,branch,terminal_id,sales_person_id,date,time,due_date,isSeen,is_notify,web)";

                $colum = "('$receipt','$openid',$order_mode_id,$uid,$customer_id,$payment_id,'$total_amount','$total_item_qty',0,$mode,'$delivery',$branch,$terminal,$sales,'$date','$time','$due_date',1,1,'$web')";

                $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_receipts", true);

                if (!empty($result)) {
					
					$clientId = $GLOBALS['crud']->runQuery("SELECT client_id FROM `customers` where id = $customer_id");
					if(!empty($clientId))
					{
						$paymentId = $GLOBALS['crud']->runQuery("SELECT payment_mode FROM `sales_payment` where payment_id = $payment_id");
						$message = "Order ID # ".$result." (".$paymentId[0]["payment_mode"].") Rs.".number_format($total_amount,2);
						echo sendPushNotificationToCustomerApp($clientId[0]["client_id"],$message);
					}

                    $provider = $GLOBALS['crud']->runQuery("SELECT a.provider_id,c.percentage FROM user_salesprovider_relation a INNER JOIN service_provider_details b on b.id = a.provider_id INNER JOIn service_agreement_percentages c on c.percentage_id = b.percentage_id  where a.user_id =  " . $sales . "");
                    $lastBalance = $GLOBALS['crud']->runQuery("SELECT balance FROM service_provider_ladger where ladger_id = (Select Max(ladger_id) from service_provider_ladger where provider_id = " . $provider[0]["provider_id"] . ") ");
                    $amount = ($total_amount * ($provider[0]["percentage"] / 100));
                    $bal = $lastBalance[0]["balance"] + $amount;
					
					$fix = "(service_provider_id,receipt_id,date)";
                    $col = "(" . $provider[0]["provider_id"] . ",".$result.",'" . date("Y-m-d H:i:s") . "')";
                    $spOrders = $GLOBALS['crud']->insert_mode($fix, $col, "service_provider_orders", true);

                    $fix = "(provider_id,debit,credit,balance,order_id,date, narration,receipt_id,receipt_no,receipt_total_amount,delivery_person_name,contact_no,vehicle_no,service_provider_order_no)";
                    $col = "(" . $provider[0]["provider_id"] . ",0," . $amount . "," . $bal . ",1,'" . date("Y-m-d") . "', 'Narration',".$result.",'".$receipt."','".$total_amount."','".$delivery_person_name."','".$contact_no."','".$vehicle_no."','".$service_provider_order_no."')";
                    $pro = $GLOBALS['crud']->insert_mode($fix, $col, "service_provider_ladger", true);
                    // echo 1;exit;
					// if($web == 1){
						// $clientId = $GLOBALS['crud']->runQuery("SELECT client_id FROM `customers` WHERE id = " . $customer_id . "");
						// if(!empty($clientId) && $clientId[0]["client_id"] != ""){
							// echo sendPushNotification($clientId[0]["client_id"],$result,$total_amount);  
						// }
					// }
                    return $result;

                    //        return $result;
                } else {
                    return 0;
                }
            }

        }
    } else {
        return 0;
    }
}

?>
