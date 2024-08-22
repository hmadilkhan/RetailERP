<?php
require "crud.php";
date_default_timezone_set("Asia/Karachi");
require "printnew.php";
// error_reporting(E_ALL);


$crud = new Crud();

// echo login("raza.drh","1234","03-7A8F4F2BD6B4E1D4-FFFFFFFFA042E909",111);
// get login //
function login($username, $password, $serial, $terminal)
{

    if (!empty($username) && !empty($password)) {

        $result = $GLOBALS['crud']->runQuery("SELECT user.id,user.fullname,user.username,user.password,user.image as user_image,user.show_password,authoz.*,b.branch_name as branch_name,b.branch_address,b.branch_logo,b.code as branch_code,c.name as company_name,d.mac_address as mac,d.model_no,d.terminal_name,c.pos_background FROM user_details as user INNER JOIN user_authorization as authoz ON authoz.user_id = user.id INNER JOIN branch b on b.branch_id = authoz.branch_id INNER JOIN company c on c.company_id = authoz.company_id INNER JOIN terminal_details d on d.branch_id = b.branch_id and d.mac_address = '$serial' and d.terminal_id = $terminal WHERE user.username = '" . $username . "' and authoz.status_id = 1 and (authoz.isLoggedIn IS NULL OR authoz.isLoggedIn = 0)");
        if (!empty($result) && sizeof($result) > 0) {
			$checkIsLoggedIn = $GLOBALS['crud']->runQuery("SELECT * FROM `user_authorization` where user_id = ".$result[0]["id"]);
            if($checkIsLoggedIn[0]["isLoggedIn"] == 0){
				for ($c = 0; $c < sizeof($result); $c++) {
					if (password_verify($password, $result[$c]["password"])) {
						$update = $GLOBALS['crud']->runQuery("Update user_authorization set isLoggedIn = 1 where authorization_id = " . $result[0]["authorization_id"] . "");
						return json_encode($result[$c]);
					}
				}
			}else{
				return 0;
			}
        } else {
            return 0;
        }
    } else {

        return 0;
    }
}

function logout($authorization_id)
{
    $update = $GLOBALS['crud']->runQuery("Update user_authorization set isLoggedIn = 0 where authorization_id = " . $authorization_id . "");
    if (sizeof($update) > 0) {
        return 1;
    } else {
        return 0;
    }
}


function getBranchId($username, $password)
{
    if (!empty($username) && !empty($password)) {
        $result = $GLOBALS['crud']->runQuery("SELECT user.id,user.fullname,user.username,user.password,user.show_password,authoz.*,b.branch_name as branch_name,c.name as company_name FROM user_details as user INNER JOIN user_authorization as authoz ON authoz.user_id = user.id INNER JOIN branch b on b.branch_id = authoz.branch_id INNER JOIN company c on c.company_id = authoz.company_id   WHERE user.username = '" . $username . "' and authoz.status_id = 1 LIMIT 1");
        //        return $result[0]["branch_id"];
        if (!empty($result) && sizeof($result) > 0) {

            //            for($c=0;$c < sizeof($result);$c++) {
            //                if(password_verify($password, $result[$c]["password"]))
            //                {
            return json_encode($result[0]);
            //                }
            //            }

        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// check system details //
function chk_system_details($id, $status_id, $plateform, $device_manufacturer, $device_model, $device_serial)
{


    if (!empty($id) && $id > 0) {

        $result = $GLOBALS['crud']->runQuery("SELECT * FROM user_systemdetails where user_id=$id and device_serial = '$device_serial' ");

        if (!empty($result) && sizeof($result) > 0) {
            return 1;
        } else {
            return add_system_details($id, $status_id, $plateform, $device_manufacturer, $device_model, $device_serial);
        }
    } else {
        return 0;
    }
}

// add system details //
function add_system_details($id, $status_id, $plateform, $device_manufacturer, $device_model, $device_serial, $serialKey)
{

    if (!empty($id) && $id > 0 && !empty($status_id) && $status_id > 0) {

        $serial_key = serial_generate();

        $fixcolum = "(user_id,status_id,serial_key,plateform,device_manufacturer,device_model, device_serial,created_at)";

        $colum = "($id,$status_id,'$serialKey','$plateform','$device_manufacturer','$device_model', '$device_serial','" . date("Y-m-d H:i:s") . "')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "user_systemdetails", false);

        if ($result) {
            return $serial_key;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

//Add User Authentication
function add_user_authentication($user_id, $authorization_id, $username, $password, $created, $updated)
{

    if (!empty($user_id)) {

        $fixcolum = "(user_id,authorization_id,username,password,login_datetime,logout_datetime)";

        $colum = "($user_id,$authorization_id,'$username','$password','$created','$updated')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "user_authenticationuser_authentication", false);
        echo $result;
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// serial and device serial key  //
function chk_serial_device_key($id, $serial_key, $device_serial)
{

    if (!empty($id) && $id > 0) {

        $result = $GLOBALS['crud']->runQuery("SELECT * FROM user_systemdetails where user_id=$id and device_serial = '$device_serial' and serial_key='$serial_key'");

        if (!empty($result) && sizeof($result) > 0) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// serial key generate //
function serial_generate()
{

    $num = "0123456789";
    $alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $symbal = "$@_#";

    $generate =  substr(str_shuffle($num . $alpha . $symbal), 0, 25);

    $result = $GLOBALS['crud']->runQuery("SELECT * FROM user_systemdetails WHERE serial_key = '$generate' ");


    if (!empty($result) && sizeof($result) > 0) {
        $generate =  substr(str_shuffle($num . $alpha . $symbal), 0, 25);
        return $generate;
    } else {
        return $generate;
    }
}

// OTP generate //
function otp_generate()
{

    $num = "0123456789";


    $generate =  substr(str_shuffle($num), 0, 6);

    $result = $GLOBALS['crud']->runQuery("SELECT * FROM user_systemdetails WHERE serial_key = '$generate' ");


    if (!empty($result) && sizeof($result) > 0) {
        $generate =  substr(str_shuffle($num), 0, 6);
        return $generate;
    } else {
        return $generate;
    }
}

function slug_generate()
{

    $num = "0123456789";
    $alpha = "abcdefghijklmnopqrstuvwxyz";


    $generate =  substr(str_shuffle($num), 0, 2) . substr(str_shuffle($alpha), 0, 2);

    $result = $GLOBALS['crud']->runQuery("SELECT * FROM user_systemdetails WHERE serial_key = '$generate' ");


    if (!empty($result) && sizeof($result) > 0) {
        $generate =  substr(str_shuffle($num), 0, 2) . substr(str_shuffle($alpha), 0, 2);
        return $generate;
    } else {
        return $generate;
    }
}




// get inventory //
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


// Get Updated  Inventory //
function getUpdatedInventory($terminalId)
{
    if (!empty($terminalId) && $terminalId > 0) {

        $result = $GLOBALS['crud']->runQuery("SELECT b.*,c.*,d.name as uom,(Select SUM(balance) from inventory_stock where product_id = inventory_download_status.productId and branch_id = inventory_download_status.branchId) as qty,'inventory' as status FROM `inventory_download_status` INNER JOIN (SELECT id,department_id,sub_department_id,uom_id,product_mode,item_code,product_name,product_description,short_description,image from inventory_general) b on b.id = inventory_download_status.productId INNER JOIN (Select product_id,actual_price,tax_rate,tax_amount,retail_price,wholesale_price,online_price,discount_price from inventory_price where status_id = 1 ) c on c.product_id = inventory_download_status.productId INNER JOIN (Select uom_id,name from inventory_uom ) d on d.uom_id = b.uom_id where terminalId = ".$terminalId." and status = 1 group by productId");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    } 
}


// Get Updated  Inventory //
function getUpdatedInventoryStatus($terminalId)
{
    if (!empty($terminalId) && $terminalId > 0) {

        $update = $GLOBALS['crud']->runQuery("Update `inventory_download_status` set status = 0 where terminalId = ".$terminalId." and status = 1");
		if (sizeof($update) > 0) {
			return 1;
		} else {
			return 0;
		}
    } else {
        return 0;
    }
}
// get Department //
function getDepartment($id)
{


    if (!empty($id) && $id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT a.* FROM inventory_department a INNER JOIN inventory_general b on b.department_id = a.department_id and b.status = 1 and a.company_id = '$id'");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// get SUB-Department //
function getSubDepartment($id)
{


    if (!empty($id) && $id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT a.* FROM inventory_sub_department a INNER JOIN inventory_general b on b.sub_department_id = a.sub_department_id and b.status = 1 and a.department_id IN (Select department_id from inventory_department where company_id = $id ) group by a.sub_department_id");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// get Order inventory //
function getOrderInventory()
{

    $result = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.opening_id,a.order_mode_id,a.userid,a.customer_id,a.payment_id,a.total_amount,a.total_item_qty,a.is_sale_return,a.status,a.delivery_date,a.date,a.time from sales_receipts a WHERE a.status = 3");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

function getReadyOrders($receipt)
{
    $res = $GLOBALS['crud']->runQuery("SELECT COUNT(a.id) as count from sales_receipts a WHERE a.status = 3 and a.receipt_no = '" . $receipt . "'");

    if (!empty($res) && sizeof($res) > 0) {
        return $res[0]["count"];
    } else {
        return 0;
    }
}

// get inventory General //
function getInventoryGeneral()
{


    $result = $GLOBALS['crud']->runQuery("SELECT a.id,a.item_code,a.product_name,b.name,a.product_mode FROM inventory_general a INNER JOIN inventory_uom b on b.uom_id = a.uom_id  group by a.id");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

// get Masters //
function getMasters()
{

    $result = $GLOBALS['crud']->runQuery("SELECT * FROM masters");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

// get Recipy General //
function getRecipyGeneral()
{

    $result = $GLOBALS['crud']->runQuery("SELECT * FROM recipy_general");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

// get Recipy General //
function getRecipyDetails()
{

    $result = $GLOBALS['crud']->runQuery("SELECT * FROM recipy_details");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

// get Recipy General //
function getRecipyAccount()
{

    $result = $GLOBALS['crud']->runQuery("SELECT * FROM recipy_account");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

// echo invent_stock_detection(17,197791,1,'Open'); 
// stock detection //
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
            return 0;
        }
    } else {
        return 0;
    }
}

// get customer //
function get_customer($id)
{

    if (!empty($id) && $id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM customers WHERE user_id=$id and status_id= 1");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// get customer //
function get_customer_contact_or_verify_no($custId,$company_id,$mobile_no)
{

    if (!empty($mobile_no) && $mobile_no > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM customers WHERE id = $custId and company_id = $company_id and mobile=$mobile_no and status_id= 1");
        if (!empty($result) && sizeof($result) > 0) {
            $otp = rand(1000, 9999);
            $res = send_otp($result[0]['mobile'],$otp);
            if($res == 1){
                $updateOtp = "otp = '$otp',verified = '0',date = '".date('Y-m-d H:i:s')."' ";
                $update = $GLOBALS['crud']->modify_mode($updateOtp, "user_customer", "customer_id = $custId ");
                return 1;
            }
            return 0;
        } else {
            return 0;
        }
    } else {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM customers WHERE id = $custId and company_id = $company_id and status_id= 1");
        if (!empty($result) && sizeof($result) > 0) {
            // print_r($result[0]['phone']);exit;
            return json_encode($result[0]['mobile']);
        } else {
            return 0;
        }
    }
}

//VERIFY OTP
function verify_otp($customer_id, $otp)
{
    $customer = $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM `user_customer` where customer_id = $customer_id and otp = $otp and otp_try = 0 AND `date` >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)  ");
    if ($customer[0]["count"] > 0) {
        $updateOtp = " verified = '1',otp_try = '1' ";
        $update = $GLOBALS['crud']->modify_mode($updateOtp, "user_customer", "customer_id = $customer_id ");
        return 1;
    } else {
        return 0;
    }
}
// echo send_otp("03218220521","5432");
function send_otp($sender,$otp){
    $sender = ltrim($sender, '0');
    // echo $sender;exit;
    $username = "SABSTECK";///Your Username
    $password = "786";///Your Password
    $mobile = '92'.$sender;///Recepient Mobile Number
    $sender = "Sabify";
    $api_key = "ab2ae90c15c5f1675f74aa04b2631efd";
    $message = $otp; // "Your OTP is ".$otp." ";
	$url = "https://bsms.iisol.pk/api.php?key=".$api_key."&receiver=".urlencode($mobile)."&sender=".$sender."&msgdata=".rawurlencode($message);
    // echo $url;exit;
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

// get customer account //
function get_customer_account($id)
{

    if (!empty($id) && $id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT balance as balance FROM customer_account WHERE cust_id= $id and cust_account_id = (Select MAX(cust_account_id) from customer_account where cust_id = $id)");

        if (!empty($result) && sizeof($result) > 0) {
            return $result[0]["balance"];
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// DOWNLOAD CUSTOMER LEDGER
/*function get_customer_ledger($id)
{

    if (!empty($id) && $id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM `customer_account` where cust_id = $id");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}
*/
//echo add_cust(3,1,177,3,'test slug','2123','22341','4543534','asdffsdf','dfsdfs','0','0','sdfsdf');
// add customer  //
function add_cust($user_id, $status_id, $country_id, $city_id, $name, $mobile, $phone, $nic, $address, $image, $credit_limit, $discount, $email)
{

    if (!empty($user_id) && $user_id > 0) {
        $slug = slug_generate();
        $fixcolum = "(user_id,status_id,country_id,city_id,name,mobile,phone,nic,address,image,credit_limit,discount,email,slug)";
        $colum = "($user_id,$status_id,$country_id,$city_id,'$name','$mobile','$phone','$nic','$address','$image',$credit_limit,$discount,'$email','$slug')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "customers", true);

        if (!empty($result) && sizeof($result) > 0) {
            return $result;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// add customer measurement  //
function add_cust_measurement($customer_id, $chest, $waist, $abdomen, $hips, $shoulder, $sleeves, $neck, $kurta_length, $shirt_length, $jacket_length, $sherwani, $pentshalwar, $arm_hole, $bicep, $wc_length, $pwaist, $phip, $pthy, $pknee, $pcaff, $pfly, $plength, $pbottom)
{
    //
    if (!empty($customer_id) && $customer_id > 0) {
        $count = $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM shamwarqameez where customer_id =  $customer_id");

        if ($count[0]["count"] == 0) {

            // INSERT CODE HERE
            $fixcolumsharwar = "(customer_id,chest,waist,abdomen,hips,shoulder,sleeves,neck,kurta_length,shirt_length,jacket_length,sherwani,pentshalwar,arm_hole,bicep,wc_length)";
            $columshalwar = "($customer_id,'$chest','$waist','$abdomen','$hips','$shoulder','$sleeves','$neck','$kurta_length','$shirt_length','$jacket_length','$sherwani','$pentshalwar','$arm_hole','$bicep','$wc_length')";

            $fixcolumpant = "(customer_id,waist,hip,thy,knee,caff,fly,length,bottom)";
            $columpant = "($customer_id,'$pwaist','$phip','$pthy','$pknee','$pcaff','$pfly','$plength','$pbottom')";

            $result = $GLOBALS['crud']->insert_mode($fixcolumsharwar, $columshalwar, "shamwarqameez", true);

            if (!empty($result) && sizeof($result) > 0) {
                $results = $GLOBALS['crud']->insert_mode($fixcolumpant, $columpant, "pantshirt", false);
                if (!empty($results) && sizeof($results) > 0) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return $result;
            }
        } else {

            //UPDATE CODE HERE
            $updateshalwar = "chest = '$chest',waist = '$waist',abdomen = '$abdomen',hips = '$hips',shoulder = '$shoulder',sleeves = '$sleeves',neck = '$neck',kurta_length = '$kurta_length',shirt_length = '$shirt_length',jacket_length ='$jacket_length',sherwani = '$sherwani',pentshalwar = '$pentshalwar',arm_hole = '$arm_hole',bicep = '$bicep',wc_length = '$wc_length'";


            $updatepant = "waist = '$pwaist',hip = '$phip',thy = '$pthy',knee = '$pknee',caff = '$pcaff',fly = '$pfly',length ='$plength',bottom = '$pbottom'";

            $update = $GLOBALS['crud']->modify_mode($updateshalwar, "shamwarqameez", "customer_id = $customer_id ");
            $update = $GLOBALS['crud']->modify_mode($updatepant, "pantshirt", "customer_id = $customer_id ");
        }
    } else {
        return 0;
    }
}

// echo add_cust_account(798,'8059','165000','5000','160000','160000','20','3','160000','406');
// add customer account //
function add_cust_account($cust_id, $receipt_no, $total_amount, $debit, $credit, $balance, $terminal, $payment_mode_id, $received, $opening)
{

    if (!empty($cust_id) && $cust_id > 0) {
        $advance = 0;
        $lastBalance = 0;
        $curBalance = 0;

        $lastBalances = $GLOBALS['crud']->runQuery("SELECT balance FROM `customer_account` where cust_account_id = (Select MAX(cust_account_id) from customer_account where cust_id = ".$cust_id." )");
        $balance = $lastBalances[0]["balance"] + $credit;

        $fixcolum = "(cust_account_id,cust_id,receipt_no,total_amount,debit,credit,balance,terminal_id,payment_mode_id,received,opening_id,created_at,updated_at)";
        $colum = "(0,$cust_id,$receipt_no,$total_amount,$debit,$credit,$balance,$terminal,$payment_mode_id,$received,$opening,'" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "customer_account", false);

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// add Master account //
function add_master_account($master_id, $receipt_no, $total_amount, $debit, $credit, $balance, $date)
{

    if (!empty($master_id) && $master_id > 0) {
        $lastBalance = $GLOBALS['crud']->runQuery("SELECT balance FROM master_account where master_account_id = (Select MAX(master_account_id) from master_account where master_id = $master_id)");

        // $credit = $credit * (-1);
        $bal = $lastBalance[0]["balance"] + $credit;

        $fixcolum = "(master_id,receipt_no,total_amount,debit,credit,balance,TotalBalance,created_at)";
        $colum = "($master_id,$receipt_no,$total_amount,$debit,$credit,$credit,$bal,'$date')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "master_account", false);

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

function create_customer_account($cust_id, $receipt_no, $total_amount, $debit, $credit, $balance)
{
    $fixcolum = "(cust_id,receipt_no,total_amount,debit,credit,balance)";
    $colum = "($cust_id,$receipt_no,'$total_amount','$debit','$credit','$balance')";
    if($credit > $debit){
        $advance = 0;
        $resultCus = $GLOBALS['crud']->runQuery("SELECT advance from customers where id =  ".$cust_id." ");
        if (!empty($resultCus) && sizeof($resultCus) > 0) {
            $advance = $resultCus[0]['advance'] + ($credit - $debit) ;
            $update = $GLOBALS['crud']->runQuery("Update customers set advance = ".$advance." where id = " . $cust_id . "");
        }
    }
    $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "customer_account", false);

    if ($result) {
        return 1;
    } else {
        return 0;
    }
}


// sales openning //
function sales_openning($id, $balance, $update, $terminal,$date,$time)
{

    if ($update != 0) {
        $result = $GLOBALS['crud']->modify_mode("balance = " . $balance . " ", 'sales_opening', "opening_id = " . $update . " ");

        if ($result) {
            return $result;
        } else {
            return 0;
        }
    } else {
        if (!empty($id) && $id > 0) {
            $fixcolum = "(user_id,balance,status,date,time,terminal_id)";
            $colum = "($id,'$balance',1,'$date','$time',$terminal)";

            $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_opening", true);

            if ($result) {
                return $result;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
}


// sales closing //
function sales_closing($opening_id, $balance)
{

    if (!empty($opening_id) && $opening_id > 0) {
        // CHECK EXISTENCE
        $chk = $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM `sales_closing` where opening_id = $opening_id");
        if ($chk[0]["count"] == 0) {

            $fixcolum = "(opening_id,balance,date,time)";
            $colum = "($opening_id,'$balance','" . date("Y-m-d") . "','" . date("H:i:s") . "')";

            $result = $GLOBALS['crud']->modify_mode("status = 2", 'sales_opening', "opening_id = " . $opening_id . " ");

            $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_closing", true);

            if ($result) {
                return $result;
            } else {
                return 0;
            }
        }else{
            return 2;
        }
    } else {
        return 0;
    }
}



// sales orderType //
function get_orderMode()
{

    $result = $GLOBALS['crud']->runQuery("SELECT * FROM sales_order_mode");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}



// sales orderType //
function chk_salesReceipt($uid, $receipt, $openid)
{

    $result = $GLOBALS['crud']->runQuery("SELECT * FROM sales_receipts WHERE user_id =" . $uid . "  and receipt_id = '" . $receipt . "'  and openning_id = " . $openid . " ");

    if (!empty($result) && sizeof($result) > 0) {
        return 1;
    } else {
        return 0;
    }
}


// sales add receipt //
//echo add_salesReceipt(32,'20200702144929412','1',1,1,1,2600,25,'','3','2012-02-03','06:04:00',1,538,0);
// echo add_salesReceipt(1, "12345655555", 0, 4, 815, 5, 1, 1, 0, 0,'', '', 0, 0, 0,"","0","0","0","0",0); 
function add_salesReceipt($uid, $receipt, $openid, $order_mode_id, $customer_id, $payment_id, $actual_amount,$total_amount, $total_item_qty, $delivery, $branch, $date, $time, $terminal, $sales, $web_id,$due_date,$delivery_person_name,$contact_no,$vehicle_no,$service_provider_order_no,$web,$riderCharges,$billPrintName)
{   
	$message = "Initial Data : Terminal ".$terminal." , Order Mode :".$order_mode_id.", User Id :".$uid;
	echo create_log(json_encode($message));
    if (!empty($uid) && $uid > 0) {
        //If Web Id found then will perform update
        if ($web_id > 0) {
            $columns = "opening_id = '" . $openid . "',order_mode_id = '" . $order_mode_id . "',userid = '" . $uid . "',customer_id = '" . $customer_id . "',payment_id = '" . $payment_id . "',total_amount = '" . $total_amount . "',total_item_qty = '" . $total_item_qty . "',delivery_date = '" . $delivery . "',sales_person_id = '" . $sales . "',due_date = '".$due_date."', bill_print_name = '".$billPrintName."' ";
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
                $fixcolum = "(receipt_no,opening_id,order_mode_id,userid,customer_id,payment_id,actual_amount, total_amount,total_item_qty,is_sale_return,status,delivery_date,branch,terminal_id,sales_person_id,date,time,due_date,isSeen,is_notify,web,bill_print_name)";

                $colum = "('$receipt','$openid',$order_mode_id,$uid,$customer_id,$payment_id,'$actual_amount','$total_amount','$total_item_qty',0,$mode,'$delivery',$branch,$terminal,$sales,'$date','$time','$due_date',1,1,'$web','$billPrintName')";

                $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_receipts", true);

                if (!empty($result)) {
					
					if($order_mode_id == 1){
						$message = "Going inside for the receipt ".$result;
						echo create_log(json_encode($message));
						// echo getParentTerminal($terminal,$result,$uid);
						
					}
					
					
					// $clientId = $GLOBALS['crud']->runQuery("SELECT client_id FROM `customers` where id = $customer_id");
					// if(!empty($clientId))
					// {
						// $paymentId = $GLOBALS['crud']->runQuery("SELECT payment_mode FROM `sales_payment` where payment_id = $payment_id");
						// $message = "Order ID # ".$result." (".$paymentId[0]["payment_mode"].") Rs.".number_format($total_amount,2);
						// echo sendPushNotificationToCustomerApp($clientId[0]["client_id"],$message);
					// }

                    $provider = $GLOBALS['crud']->runQuery("SELECT a.provider_id,c.id as type,b.payment_value FROM user_salesprovider_relation a INNER JOIN service_provider_details b on b.id = a.provider_id INNER JOIn service_provider_payment_type c on c.id = b.payment_type_id where a.user_id = " . $sales . "");
                    $lastBalance = $GLOBALS['crud']->runQuery("SELECT balance FROM service_provider_ladger where ladger_id = (Select Max(ladger_id) from service_provider_ladger where provider_id = " . $provider[0]["provider_id"] . ") ");
                    $amount = "";
					$bal = "";
					// IF PAYMENT TYPE IS PERCENTAGE 
					if($provider[0]["type"] == 1){
						$amount = ($total_amount * ($provider[0]["payment_value"] / 100));
						$bal = $lastBalance[0]["balance"] + $amount;
					}elseif($provider[0]["type"] == 2){
						$amount = $provider[0]["payment_value"] ;
						$bal = $lastBalance[0]["balance"] + $amount;
					}elseif($provider[0]["type"] == 3){
						$amount = ($riderCharges == "" ? 0 : $riderCharges ) ;
						$bal = $lastBalance[0]["balance"] + $amount;
					}
					
					
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

function add_sales_to_service_provider_account($serviceproviderId,$total_amount){
	
	$provider = $GLOBALS['crud']->runQuery("SELECT a.provider_id,c.id as type,b.payment_value FROM user_salesprovider_relation a INNER JOIN service_provider_details b on b.id = a.provider_id INNER JOIn service_provider_payment_type c on c.id = b.payment_type_id where a.user_id = " . $serviceproviderId . "");
	$lastBalance = $GLOBALS['crud']->runQuery("SELECT balance FROM service_provider_ladger where ladger_id = (Select Max(ladger_id) from service_provider_ladger where provider_id = " . $provider[0]["provider_id"] . ") ");
	$amount = "";
	$bal = "";
	// IF PAYMENT TYPE IS PERCENTAGE 
	if($provider[0]["type"] == 1){
		$amount = ($total_amount * ($provider[0]["payment_value"] / 100));
		$bal = $lastBalance[0]["balance"] + $amount;
	}else{
		$amount = $provider[0]["payment_value"] ;
		$bal = $lastBalance[0]["balance"] + $amount;
	}
	
	
	$fix = "(service_provider_id,receipt_id,date)";
	$col = "(" . $provider[0]["provider_id"] . ",".$result.",'" . date("Y-m-d H:i:s") . "')";
	$spOrders = $GLOBALS['crud']->insert_mode($fix, $col, "service_provider_orders", true);

	$fix = "(provider_id,debit,credit,balance,order_id,date, narration,receipt_id,receipt_no,receipt_total_amount,delivery_person_name,contact_no,vehicle_no,service_provider_order_no)";
	$col = "(" . $provider[0]["provider_id"] . ",0," . $amount . "," . $bal . ",1,'" . date("Y-m-d") . "', 'Narration',".$result.",'".$receipt."','".$total_amount."','".$delivery_person_name."','".$contact_no."','".$vehicle_no."','".$service_provider_order_no."')";
	$pro = $GLOBALS['crud']->insert_mode($fix, $col, "service_provider_ladger", true);
}

//echo add_salesReceiptDetails(1913,191070,2,600,1,'','',"",0);
// sales add receipt details //
function add_salesReceiptDetails($receipt, $item_code, $total_qty, $total_amount, $status, $totalCost, $discount, $note, $webid,$item_name,$item_price,$taxrate,$taxamount)
{
    if (!empty($receipt) && $receipt > 0) {
        //If Web Id found then will perform update
        if ($webid > 0) {
            $columns = "total_qty = '" . $total_qty . "',total_amount = '" . $total_amount . "',total_cost = '" . $totalCost . "',discount = '" . $discount . "',note = '" . $note . "',taxrate = '" . $taxrate . "',taxamount = '" . $taxamount . "'";
            $update = $GLOBALS['crud']->modify_mode($columns, 'sales_receipt_details', "receipt_detail_id = " . $webid . " ");
            if (!empty($update) && sizeof($update) > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            //Insert here
            $fixcolum = "(receipt_id,item_code,total_qty,total_amount,is_sale_return,status,total_cost,discount,note,item_name,item_price,taxrate,taxamount)";

            $colum = "($receipt,$item_code,'$total_qty','$total_amount',0,1,'$totalCost','$discount','$note','$item_name','$item_price','$taxrate','$taxamount')";

            $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_receipt_details", true);

            if ($result > 0) {

                //RECEIPT ID
                $receipt_no = $GLOBALS['crud']->runQuery("SELECT * FROM `sales_receipts` where id = '$receipt'");

                //GETTING LAST STOCK
                $lastStock = $GLOBALS['crud']->runQuery("SELECT stock,cost FROM `inventory_stock_report_table` where stock_report_id = (Select MAX(stock_report_id) from inventory_stock_report_table) and product_id = '$item_code'");
                $lStock = empty($lastStock) ? 0 : $lastStock[0]['stock'];
                $stk = $lStock - $total_qty;

                // INSERT INTO STOCK REPORT
                $col = "(stock_report_id,date,product_id,foreign_id,branch_id,qty,stock,cost,retail,narration)";
                $column = "(0,'" . date('Y-m-d H:s:i') . "',$item_code,$receipt," . $receipt_no[0]['branch'] . ",$total_qty,$stk," . $totalCost . ",$total_amount,'Sales')";
                $stock = $GLOBALS['crud']->insert_mode($col, $column, "inventory_stock_report_table", true);

                if (!empty($stock) && sizeof($stock) > 0) {
                    return $result;
                } else {
                    return 2;
                }
            } else {
                return 0;
            }
        }
    } else {
        return 0;
    }
}

//echo addReceipt_generalAccount(1,'850',100,750,1);
// sales add receipt account general Account //
function addReceipt_generalAccount($receipt, $receive_amount, $amount_paid_back, $total_amount, $web_id)
{

    if (!empty($receipt) && $receipt > 0) {
        $balance = $total_amount - $receive_amount;
        $status = ($balance == 0 ? 1 : 0);
        if ($web_id > 0) {
            $columns = "receive_amount = '" . $receive_amount . "',amount_paid_back = '" . $amount_paid_back . "',total_amount = '" . $total_amount . "',status = '" . $status . "'";
            $update = $GLOBALS['crud']->modify_mode($columns, 'sales_account_general', "account_id = " . $web_id . " ");
            if (!empty($update) && sizeof($update) > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {

            $fixcolum = "(receipt_id,receive_amount,amount_paid_back,total_amount,balance_amount,status) ";

            $colum = "($receipt,'$receive_amount','$amount_paid_back','$total_amount','$balance',$status)";

            $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_account_general", true);


            if (!empty($result) && sizeof($result) > 0) {
                return $result;
            } else {
                return 0;
            }
        }
    } else {
        return 0;
    }
}

//echo addSales_subdetailsAccount(120582,0,0,0,2840,0,0,0,0,0,0,0);
// sales add receipt account subdetails //
function addSales_subdetailsAccount($receipt, $discount_amount, $coupon, $promo_code, $sales_tax_amount, $service_tax_amount, $creditTrans, $deliveryCharges,$deliveryChargesAmount, $bank_discount_id, $web_id, $srb)
{

    if (!empty($receipt) && $receipt > 0) {

        if ($web_id > 0) {
            $columns = "discount_amount = '" . $discount_amount . "',coupon = '" . $coupon . "',promo_code = '" . $promo_code . "',sales_tax_amount = '" . $sales_tax_amount . "',service_tax_amount = '" . $service_tax_amount . "',credit_card_transaction = '" . $creditTrans . "',delivery_charges = '" . $deliveryCharges . "',delivery_charges_amount = '" . $deliveryChargesAmount . "',bank_discount_id = '" . $bank_discount_id . "',srb = '" . $srb . "'";
            $update = $GLOBALS['crud']->modify_mode($columns, 'sales_account_subdetails', "id = " . $web_id . " ");
            if (!empty($update) && sizeof($update) > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            $fixcolum = "(receipt_id,discount_amount,coupon,promo_code,sales_tax_amount,service_tax_amount,credit_card_transaction,delivery_charges,delivery_charges_amount,bank_discount_id,srb) ";

            $colum = "($receipt,$discount_amount,$coupon,$promo_code,$sales_tax_amount,$service_tax_amount,$creditTrans,$deliveryCharges,$deliveryChargesAmount,$bank_discount_id,$srb)";

            $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_account_subdetails", true);
			
			// PUSH NOTIFICATION
			$customerId =  $GLOBALS['crud']->runQuery("SELECT customer_id FROM `sales_receipts` where id = $receipt");
			$clientId = $GLOBALS['crud']->runQuery("SELECT client_id FROM `customers` where id = ".$customerId[0]["customer_id"]);
			if(!empty($clientId))
			{
				$paymentId = $GLOBALS['crud']->runQuery("SELECT payment_mode FROM `sales_payment` where payment_id = $payment_id");
				$message = "Order ID # ".$result." (".$paymentId[0]["payment_mode"].") Rs.".number_format($total_amount,2);
				echo sendPushNotificationToCustomerApp($clientId[0]["client_id"],$message,$receipt);
				// echo getParentTerminal($terminal,$result,$uid);
			}
			
            if ($result > 0) {
                return $result;
            } else {
                return 0;
            }
        }
    } else {
        return 0;
    }
}

// sales add receipt credit card details //
function addSales_creditcard($receipt, $creditno, $bankDiscuntid)
{

    if (!empty($receipt) && $receipt > 0) {

        $fixcolum = "(receipt_id,credit_card_no,bank_discount_id) ";

        $colum = "($receipt,'$creditno','$bankDiscuntid')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_creditcard_details", false);

        if (!empty($result)) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}
// echo addSales_hold_unhold(2,3,15,8,'','2021-01-29 04:10:00',1,3);
// SALES HOLD AND UNHOLD RECEIPT DETAILS //
function addSales_hold_unhold($receipt, $floorid, $tableno, $gop, $hold_dt, $unhold_dt, $hold_status, $web_id)
{

    if (!empty($receipt) && $receipt > 0) {
        if ($web_id > 0) {
            $columns = "floor_id = '" . $floorid . "',table_no = '" . $tableno . "',gop = '" . $gop . "',unhold_datetime = '" . $unhold_dt . "',hold_status = 0";

            $update = $GLOBALS['crud']->modify_mode($columns, 'sales_table_hold_unhold', "id = " . $web_id . " ");
            if ($update > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {


            $fixcolum = "(id,receipt_id,floor_id,table_no,gop,hold_datetime,unhold_datetime,hold_status) ";

            $colum = "(0,$receipt,$floorid,$tableno,$gop,'$hold_dt','$unhold_dt',$hold_status)";

            $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_table_hold_unhold", true);


            if ($result > 0) {
                return $result;
            } else {
                return 0;
            }
        }
    } else {
        return 0;
    }
}


// DELETE SALES DETAILS ITEMS  //
function delete_sales_details_items($id)
{

    if (!empty($id) && $id > 0) {


        $result = $GLOBALS['crud']->remove_mode("sales_receipt_details", "receipt_detail_id = $id");

        if ($result > 0) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

/*----------------------------- UPDATE RECEIPT STATUS -----------------------------*/

function updateSalesStatus($id, $totalAmount)
{
    if (!empty($id) && $id > 0) {

        $result = $GLOBALS['crud']->modify_mode("total_amount = " . $totalAmount . "", 'sales_receipts', "id = " . $id . " ");

        if ($result) {
            $result = $GLOBALS['crud']->modify_mode("total_amount = " . $totalAmount . "", 'sales_account_general', "receipt_id = " . $id . " ");
            if ($result) {
                return $result;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}
//echo updateSalesDetailsStatus(20200724044429518,50,1,990);
function updateSalesDetailsStatus($receipt, $id, $qty, $totalAmount)
{
    if (!empty($id) && $id > 0) {
        $receipt_no = $GLOBALS['crud']->runQuery("SELECT * FROM `sales_receipts` where receipt_no = '$receipt'");
        //        $result = $GLOBALS['crud']->modify_mode("item_code = ".$id.",total_qty = ".$qty.",total_amount = $totalAmount",'sales_receipt_details',"receipt_id = ".$receipt." ");

        $fixcolum = "(receipt_detail_id,receipt_id,item_code,total_qty,total_amount,is_sale_return,status) ";

        $colum = "(0," . $receipt_no[0]['id'] . ",$id,$qty,$totalAmount,1,1)";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_receipt_details", true);
        if ($result) {
            return $result;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

/*-----------------------------JOB ORDER INSERTS START FROM HERE -----------------------------*/

// JOB ORDER GENERAL  //
function add_joborderGeneral($finished_good_id, $Total_qty, $Received_qty, $status_id, $job_status_id, $created_at)
{

    if (!empty($finished_good_id) && $finished_good_id > 0) {

        $fixcolum = "(finished_good_id,Total_qty,Received_qty,status_id,job_status_id,created_at)";

        $colum = "($finished_good_id,$Total_qty,$Received_qty,$status_id,$job_status_id,'$created_at')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "job_order_general", true);

        if (!empty($result) && sizeof($result) > 0) {
            return $result;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

function add_joborderAssign($job_id, $master_id)
{

    if (!empty($job_id) && $job_id > 0) {

        $fixcolum = "(job_id,master_id)";

        $colum = "($job_id,$master_id)";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "job_order_assign", true);

        if (!empty($result) && sizeof($result) > 0) {
            return 1;
        } else {
            return 2;
        }
    } else {
        return 0;
    }
}

function add_joborderAccount($job_id, $cost, $master_cost, $retail_cost)
{

    if (!empty($job_id) && $job_id > 0) {

        $fixcolum = "(job_id,cost,master_cost,retail_cost)";

        $colum = "($job_id,$cost,$master_cost,$retail_cost)";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "job_order_account", true);

        if (!empty($result) && sizeof($result) > 0) {
            return 1;
        } else {
            return 2;
        }
    } else {
        return 0;
    }
}

function add_joborderCustomer($job_id, $customer_id, $receipt_no)
{

    if (!empty($job_id) && $job_id > 0) {

        $fixcolum = "(job_id,customer_id,receipt_no)";

        $colum = "($job_id,$customer_id,$receipt_no)";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "job_order_customer", true);

        if (!empty($result) && sizeof($result) > 0) {
            return 1;
        } else {
            return 2;
        }
    } else {
        return 0;
    }
}


/*-----------------------------JOB ORDER INSERTS END FROM HERE -----------------------------*/

//Getting Receipt General Details for Sales Return
function getReceiptGeneralByReceiptNo($receipt_no)
{

    if (!empty($receipt_no) && $receipt_no > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT a.* from sales_receipts  a where a.receipt_no =  '$receipt_no'");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

//Getting Receipt Details for Sales Return
function getReceiptDetailsByReceiptNo($receipt_id)
{

    if (!empty($receipt_id) && $receipt_id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT a.* from sales_receipt_details a where a.receipt_id  =  '$receipt_id'");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

//Getting Receipt Account General for Sales Return
function getReceiptAccountGenralByReceiptNo($receipt_id)
{
    if (!empty($receipt_id) && $receipt_id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT a.* from sales_account_general  a where a.receipt_id  =  '$receipt_id'");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

//Getting Receipt Account Sub Details for Sales Return
function getReceiptAccountGenralSubDetailsByReceiptNo($receipt_id)
{
    if (!empty($receipt_id) && $receipt_id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT a.* from sales_account_subdetails a where a.receipt_id  =  '$receipt_id'");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

//Getting Receipt Credit Card Details for Sales Return
function getReceiptCreditCardDetailsByReceiptNo($receipt_id)
{
    if (!empty($receipt_id) && $receipt_id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT a.* from sales_creditcard_details a where a.receipt_id  =  '$receipt_id'");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}
// echo addSaleReturn(1459,0,194826,1,2500,17);
//Insert into Sale Return
function addSaleReturn($opening_id, $receipt, $itemid, $qty, $amount, $branch)
{

    if (!empty($opening_id) && $opening_id > 0) {
        $receipt_no = $GLOBALS['crud']->runQuery("SELECT * FROM `sales_receipts` where receipt_no = '$receipt'");
        $fixcolum = "(sr_id,opening_id,receipt_id,item_id,qty,amount,timestamp) ";
        $colum = "(0,$opening_id," . ($receipt_no == 0 ? 0 : $receipt_no[0]['id']) . ",$itemid,$qty,'$amount','" . date('Y-m-d H:i:s') . "')";
        $result = $GLOBALS['crud']->insert_mode($fixcolum,$colum,"sales_return",false);

        //GETTING LAST STOCK
        $lastStock = $GLOBALS['crud']->runQuery("SELECT stock,cost FROM `inventory_stock_report_table` where stock_report_id = (Select MAX(stock_report_id) from inventory_stock_report_table where product_id = '$itemid') ");

        $lStock = ($lastStock == 0 ? 0 : $lastStock[0]['stock']);
        $stk = $lStock + $qty;


        //INSERT INTO STOCK REPORT
        $col = "(stock_report_id,date,product_id,foreign_id,branch_id,qty,stock,cost,retail,narration)";
        $column = "(0,'" . date('Y-m-d H:s:i') . "',$itemid,$receipt,$branch,$qty,$stk," . $lastStock[0]['cost'] . ",$amount,'Sales Return')";
        $stock = $GLOBALS['crud']->insert_mode($col, $column, "inventory_stock_report_table", true);

        echo AddStockOnSaleReturn($branch, $itemid, $qty);

        if (!empty($result) && sizeof($result) > 0) {

            $result = $GLOBALS['crud']->runQuery("SELECT * FROM `sales_receipt_details` where receipt_id = " . $receipt_no[0]['id'] . " and item_code = " . $itemid . "");
            $condition = "receipt_detail_id = " . $result[0]['receipt_detail_id'] . "";
            if (number_format($result[0]['total_qty']) == $qty) {
                $delete = $GLOBALS['crud']->modify_mode("is_sale_return = 1", "sales_receipt_details", $condition);
                // $delete = $GLOBALS['crud']->remove_mode("sales_receipt_details",$condition);
                return 1;
            } else {
                $diff = $result[0]['total_qty'] - $qty;
                $amt = (($result[0]['total_amount'] / $result[0]['total_qty']) * $diff);
                $update = $GLOBALS['crud']->modify_mode("total_qty = " . $diff . " , total_amount = " . $amt . ",is_sale_return = 1", 'sales_receipt_details', "receipt_id = " . $receipt_no[0]['id'] . " ");
                return 1;
            }
        } else {
            return 3;
        }
    } else {
        return 2;
    }
}

function updateStatusOnSaleReturn($receipt_id, $item_id)
{
    if (!empty($receipt_id) && $item_id > 0 && !empty($item_id) && $item_id > 0) {
        //update on sale Receipt
        $update = $GLOBALS['crud']->modify_mode("is_sale_return = 1", 'sales_receipts', "id = " . $receipt_id . " ");

        //update on sale Receipt Details
        $update = $GLOBALS['crud']->modify_mode("is_sale_return = 1", 'sales_receipt_details', "receipt_detail_id = " . $item_id . " ");
    }
}


function AddStockOnSaleReturn($branch_id, $item_id, $qty)
{
    if (!empty($branch_id) && $branch_id > 0 && !empty($item_id) && $item_id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM inventory_stock WHERE product_id = $item_id and branch_id = $branch_id and status_id = 1 LIMIT 1");
		if(!empty($result) && sizeof($result) > 0)
		{
			//Calculations
			$bal = $result[0]["balance"] + $qty;

			//update on sale Receipt Details
			$update = $GLOBALS['crud']->modify_mode("balance = " . $bal . "", 'inventory_stock', "stock_id = " . $result[0]["stock_id"] . " ");
		}
		else
		{
			$result = $GLOBALS['crud']->runQuery("SELECT * FROM inventory_stock WHERE product_id = $item_id and branch_id = $branch_id and stock_id = (Select MAX(stock_id) from inventory_stock WHERE `product_id` = $item_id and branch_id = $branch_id)");
			
			//Calculations
			$bal = $result[0]["balance"] + $qty;

			//update on sale Receipt Details
			$update = $GLOBALS['crud']->modify_mode("balance = " . $bal . ", status_id = 1", 'inventory_stock', "stock_id = " . $result[0]["stock_id"] . " ");
		}
    }
}

function AddExpense($branch_id, $item_id, $qty)
{
    if (!empty($branch_id) && $branch_id > 0 && !empty($item_id) && $item_id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM inventory_stock WHERE product_id = $item_id and branch_id = $branch_id and status_id = 1 LIMIT 1");

        //Calculations
        $bal = $result[0]["balance"] + $qty;


        //update on sale Receipt Details
        $update = $GLOBALS['crud']->modify_mode("balance = " . $bal . "", 'inventory_stock', "stock_id = " . $item_id . " ");
    }
}

// Add Customer //
function add_customer($user_id, $name, $mobile, $phone, $nic, $address, $creditLimit, $dicount, $email,$company_id,$latitude,$longitude,$clientId,$device_serial,$membership_card)
{

    if (!empty($user_id) && $user_id > 0) {

        $slug = generateRandomString();

        $fixcolum = "(user_id,status_id,country_id,city_id,name,mobile,phone,nic,address,image,credit_limit,discount,email,slug,company_id,latitude,longitude,client_id,device_serial,membership_card_no) ";

        $colum = "($user_id,1,170,1,'$name','$mobile','$phone','$nic','$address','',$creditLimit,$dicount,'$email','$slug','$company_id','$latitude','$longitude','$clientId','$device_serial','$membership_card')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "customers", true);

        if (!empty($result)) {
            // print_r($result);
            return $result;
        } else {
            return 2;
        }
    } else {
        return 0;
    }
}

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

// Add Customer Address //
function add_customer_address($customer_id,$latitude,$longitude,$address)
{
    if (!empty($customer_id) && $customer_id > 0) {

        $fixcolum = "(customer_id,address) ";

        $colum = "($customer_id,'$address')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "customer_addresses", true);

        if (!empty($result)) {
            return $result;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// DOWNLOAD CUSTOMER ADDRESSES
function get_customer_addresses($id)
{

    if (!empty($id) && $id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM `customer_addresses` where customer_id = $id");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// Update Client Id
function update_customer_client_id($customer_id,$client_id)
{
    if ($customer_id > 0) {
        $updateClientId = " client_id = '$client_id',is_mobile_app_user = 1 ";
        $update = $GLOBALS['crud']->modify_mode($updateClientId, "customers", "id = $customer_id ");
        return 1;
    } else {
        return 0;
    }
}

// GENERATING RANDOM SLUG VALUE FOR CUSTOMER
function generateRandomString($length = 4) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

//Add Expense
function add_expense($branch_id, $exp_cat, $tax_id, $expense_details, $tax_amount, $amount, $net_amount,$date,$terminal_id,$opening_id,$web_id)
{
    if (!empty($branch_id) && $branch_id > 0) {
		if ($web_id > 0) {
            $columns = "exp_cat_id = '" . $exp_cat . "',tax_id = '" . $tax_id . "',branch_id = '$branch_id',terminal_id = '$terminal_id',opening_id = '$opening_id',expense_details = '$expense_details',tax_amount = '$tax_amount',amount = '$amount',net_amount = '$net_amount'";
            $update = $GLOBALS['crud']->modify_mode($columns, 'expenses', "exp_id = " . $web_id . " ");
            if ($update > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
			$fixcolum = "(branch_id,exp_cat_id,tax_id,terminal_id,opening_id,expense_details,tax_amount,amount,net_amount,date)";
			$colum = "($branch_id,$exp_cat,$tax_id,$terminal_id,$opening_id,'$expense_details',$tax_amount,$amount,$net_amount,'$date')";
			$result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "expenses", true);

			if (!empty($result)) {
				return $result;
			} else {
				return 0;
			}
		}
        
    } else {
        return 0;
    }
}

// Add Expense Category
function add_expense_category($branch_id, $exp_cat_name,$web_id)
{
    if (!empty($branch_id) && $branch_id > 0) {
		if ($web_id > 0) {
            $columns = "branch_id = '$branch_id',expense_category = '$exp_cat_name'";
            $update = $GLOBALS['crud']->modify_mode($columns, 'expense_categories', "exp_cat_id = " . $web_id . " ");
            if (is_array($update) && sizeof($update) > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
			$fixcolum = "(branch_id,expense_category)";
			$colum = "($branch_id,'$exp_cat_name')";
			// return $fixcolum.$colum;
			$result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "expense_categories", true);
			if (!empty($result)) {
				return $result;
			} else {
				return 0;
			}
		}
        
    } else {
        return 0;
    }
}

function deleteExpense($webId){
	if ($webId > 0) {
			$result = $GLOBALS['crud']->runQuery("Delete FROM `expenses` where exp_id = $webId");
			// $result = $GLOBALS['crud']->remove_mode("expenses", "exp_id = $webId");
			if (!empty($result)) {
				return 1;
			} else {
				return 0;
			}
	}else {
		return 0;
	}
}

// get Expesne Details //
function getExpenseDetails($branchId,$terminalId)
{
    if (!empty($user_id) && $user_id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * from expenses where branch_id = '$branchId' and terminal_id = $terminalId");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// get Expesne Category //
function getExpenseCategory($user_id)
{
    if (!empty($user_id) && $user_id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * from expense_categories where branch_id = '$user_id'");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// add attendance details //
function add_attendance($acc_no, $dateIN, $dateOut, $ClockIn, $clockOut, $mode, $branch)
{

    $early = 0;
    $ot = 0;
    $att = 0;

    if ($mode == 0) {
        /*****************************INSERT DATA INTO ATTENDANCE OUT TABLE *********************************/

        $fixcolum = "(acc_no,dateIN,ClockIn,uploaded_at) ";
        $colum = "('$acc_no','$dateIN','$ClockIn','" . date("Y-m-d") . "')";
        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "attendance_in", false);

        //GETTING EMPLOYEE ID
        $count = $GLOBALS['crud']->runQuery("SELECT * FROM `employee_details` WHERE emp_acc = '$acc_no'");

        $getofficeShiftDetails = $GLOBALS['crud']->runQuery("SELECT a.id,b.* FROM employee_shift_details a INNER JOIN office_shift b on b.shift_id = a.shift_id  where a.emp_id = " . $count[0]["empid"] . "");

        //ATTENDANCE DETAILS
        $attIn = "(emp_id,branch_id,date,clock_in,clock_out,late,early,OT_time,ATT_time)";
        $attColumn = "(" . $count[0]["empid"] . ",1,'$dateIN','$ClockIn','',0,0,0,'')";

        //Late Calculation
        $to_time = strtotime($dateIN . " " . $getofficeShiftDetails[0]["shift_start"]);
        $from_time = strtotime($dateIN . " " . $ClockIn);



        $late = round(abs($to_time - $from_time) / 60, 2);
        $late = $late - $getofficeShiftDetails[0]["grace_time_in"];
        if ($late > 0) {
            $late = $late;
        } else {
            $late = 0;
        }

        //ATTENDANCE DETAILS
        $attIn = "(emp_id,branch_id,date,clock_in,clock_out,late,early,OT_time,ATT_time)";
        $attColumn = "(" . $count[0]["empid"] . ",$branch,'$dateIN','$ClockIn',''," . $late . ",0,0,'')";
        $attResult = $GLOBALS['crud']->insert_mode($attIn, $attColumn, "attendance_details", false);
        if (!empty($result) && sizeof($result) > 0) {
            // sendMessage('03232985464','Clock In : '.$count[0]["emp_name"].' '.$ClockIn.' ');
            return 1;
        } else {
            return 2;
        }
    } else {
        /*****************************INSERT DATA INTO ATTENDANCE OUT TABLE *********************************/

        $fixcolum = "(acc_no,dateOut,clockOut,uploaded_at) ";
        $colum = "('$acc_no','$dateOut','$clockOut','" . date("Y-m-d") . "')";
        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "attendance_out", false);

        if (!empty($result)) {
            /**************************** CLOCK OUT DETAILS **********************************/

            $getEmpID = $GLOBALS['crud']->runQuery("SELECT a.empid,b.shift_id FROM employee_details a INNER JOIN employee_shift_details b on b.emp_id = a.empid WHERE a.emp_acc = '$acc_no'");

            $getofficeShiftDetails = $GLOBALS['crud']->runQuery("SELECT a.id,b.* FROM employee_shift_details a INNER JOIN office_shift b on b.shift_id = a.shift_id where a.emp_id = " . $getEmpID[0]["empid"] . "");

            $getUpdateID = $GLOBALS['crud']->runQuery("SELECT * FROM `attendance_details` where attendance_id = (Select MAX(attendance_id) from attendance_details where emp_id = " . $getEmpID[0]["empid"] . ")");

            //                  sendMessage('03232985464','Clock Out : '.$getEmpID[0]["emp_name"].' '.$clockOut.' ');

            //EARLY & OT
            $out_time = strtotime($dateIN . " " . $getofficeShiftDetails[0]["shift_end"]);
            $mark_out_time = strtotime($dateIN . " " . $clockOut);

            if ($mark_out_time < $out_time) {
                $early = round(abs($mark_out_time - $out_time) / 60, 2);
            } else {
                $ot = round(abs($mark_out_time - $out_time) / 60, 2);
            }

            //ATT TIME
            $start_date = new DateTime($getUpdateID[0]["date"] . " " . $getUpdateID[0]["clock_in"]);
            $since_start = $start_date->diff(new DateTime($getUpdateID[0]["date"] . " " . $clockOut));
            $att = $since_start->h . ":" . $since_start->i . ":" . $since_start->s;

            $columns = "clock_out = '" . $clockOut . "', early = $early, OT_time = $ot , ATT_time = '" . $att . "'";
            $update = $GLOBALS['crud']->modify_mode($columns, 'attendance_details', "attendance_id = " . $getUpdateID[0]["attendance_id"] . "");

            /**************************** CLOCK OUT DETAILS **********************************/


            /**************************** MARK ABSENT **********************************/

            $absentEmp = $GLOBALS['crud']->runQuery("SELECT a.empid,a.emp_acc from employee_details a INNER JOIN employee_shift_details b on b.emp_id = a.empid where a.empid NOT IN(SELECT emp_id FROM `attendance_details` where date = CURRENT_DATE()) AND b.branch_id = $branch and b.shift_id = " . $getEmpID[0]["shift_id"] . "");
            $chkHoliday = $GLOBALS['crud']->runQuery("SELECT COUNT(*) as holiday FROM `holidays` where branch_id = $branch and day_off = '" . date("l", strtotime($dateOut)) . "'");
            $chkEvent  = $GLOBALS['crud']->runQuery("SELECT COUNT(*) as event FROM `company_events` where event_date = '$dateOut' and branch_id = $branch");
            if (!empty($absentEmp)) {

                for ($x = 0; $x < count($absentEmp); $x++) {

                    $chkabsentEmp = $GLOBALS['crud']->runQuery("SELECT count(acc_no) as count FROM `absent_details` where acc_no = '" . $absentEmp[$x]["empid"] . "' and absent_date = '$dateOut'");

                    if ($chkabsentEmp[0]["count"] == 0) {



                        $fixcolum = "(acc_no,absent_date,weekday,event)";
                        $colum = "('" . $absentEmp[$x]["empid"] . "','$dateOut'," . $chkHoliday[0]['holiday'] . "," . $chkEvent[0]['event'] . ")";
                        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "absent_details", false);
                    }
                }
            }
        } else {
            return 0;
        }
    }


    //        if($result){
    //              return 1;
    //         }else {
    //              return 0;
    //         }
}

function download_discount()
{
    $result = $GLOBALS['crud']->runQuery("SELECT a.*,b.usage_limit,b.onetimeuse,c.startdate,c.starttime,c.enddate,c.endtime FROM discount_general a INNER JOIN discount_limit b on b.discount_id = a.discount_id INNER JOIN discount_period c on c.discount_id = a.discount_id where a.status IN(1,3)");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

function download_period()
{
    $result = $GLOBALS['crud']->runQuery("SELECT * FROM discount_period a INNER JOIN discount_general b on b.discount_id = a.discount_id where b.status IN (1,3)");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

// function download_period()
// {
//       $result = $GLOBALS['crud']->runQuery("SELECT * FROM discount_period a INNER JOIN discount_general b on b.discount_id = a.discount_id where b.status IN (1,3)");

//        if(!empty($result) && sizeof($result) > 0){
//           return json_encode($result);
//        }else {
//         return 0;
//        }
// }

function download_product()
{
    $result = $GLOBALS['crud']->runQuery("SELECT * FROM discount_product  a INNER JOIN discount_general b on b.discount_id = a.discount_id where b.status IN (1,3)");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

function download_category()
{
    $result = $GLOBALS['crud']->runQuery("SELECT * FROM discount_category   a INNER JOIN discount_general b on b.discount_id = a.discount_id where b.status IN (1,3)");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}


//Add Cash In
function add_cashIn($user_id, $opening, $terminal, $amount, $narration)
{

    if (!empty($user_id)) {

        $fixcolum = "(user_id,terminal_id,opening_id,amount,narration)";

        $colum = "($user_id,$terminal,$opening,$amount,'$narration')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_cash_in", true);

        if ($result) {
            return $result;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

//Add Cash Out
function add_cashOut($user_id, $opening, $terminal, $amount, $narration)
{

    if (!empty($user_id)) {

        $fixcolum = "(user_id,terminal_id,opening_id,amount,narration)";

        $colum = "($user_id,$terminal,$opening,$amount,'$narration')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_cash_out", true);

        if ($result) {
            return $result;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// FETCH PRINTER DETAILS  //
function getPrintDetails($terminal)
{
    $result = $GLOBALS['crud']->runQuery("SELECT * FROM terminal_print_details a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id where a.terminal_id = " . $terminal);

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

function getSalesPerson($branch)
{
    $result = $GLOBALS['crud']->runQuery("SELECT * FROM user_details a INNER JOIN user_authorization b on b.user_id = a.id where b.role_id = 5 and b.branch_id = " . $branch . "");
    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

// getReceiptGeneralById //
function getReceiptGeneralById($receiptId)
{

    $result = $GLOBALS['crud']->runQuery("SELECT * FROM sales_receipts a  WHERE a.id = " . $receiptId . "");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

// getReceiptGeneralById //
function getReceiptDetailsById($receiptId)
{
	$response = array();
    $result = $GLOBALS['crud']->runQuery("SELECT sales_receipt_details.*,b.product_name FROM sales_receipt_details INNER JOIN (Select id,product_name from inventory_general) b on b.id = sales_receipt_details.item_code where receipt_id = " . $receiptId . "");

    if (!empty($result) && sizeof($result) > 0) {
        
        foreach ($result as $key => $value) {
            $response[] = array(
				'receipt_id' => $receiptId,
				'item_id' => $value['receipt_detail_id'],
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

function DownloadSalesReceipts($branchId,$terminalId){
    $result = $GLOBALS['crud']->select_mode("*","sales_receipts","branch='".$branchId."' and terminal_id = '".$terminalId."' and web=1 ");

    if($result != 0){
        return $result;
    }else{
        return 0; 
    }
}

function DownloadSalesReceiptsVariations($orderId){
    $result = $GLOBALS['crud']->select_mode("*","sales_receipt_variations","receipt_id = '".$orderId."'");

    if($result != 0){
        return $result;
    }else{
        return 0; 
    }
}

function DownloadSalesReceiptsAddons($orderId){
    $result = $GLOBALS['crud']->select_mode("*","sales_receipt_addons","receipt_id = '".$orderId."'");

    if($result != 0){
        return $result;
    }else{
        return 0; 
    }
}


// Getting Header, Footer and Printer details
function getHeaderFooterPrinter($terminal_id)
{

    $result = $GLOBALS['crud']->runQuery("Select * from terminal_print_details a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id where a.terminal_id = " . $terminal_id . "");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result[0]);
    } else {
        return 0;
    }
}


// Checking Terminal Exists or Nor
function checkTerminal($terminal_id, $branch_id)
{

    $result = $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM `terminal_details` where terminal_id = " . $terminal_id . " and branch_id = " . $branch_id . "");

    if ($result[0]['count'] > 0) {
        return 1;
    } else {
        return 0;
    }
}

// GET PRODUCT WISE DISCOUNT BY CUSTOMER //
function getProductWiseDiscountByCustomer($customer_id)
{
    $result = $GLOBALS['crud']->runQuery("SELECT * FROM customer_discount where cust_id = " . $customer_id . " and status_id = 1");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}


// add machine employee //
function add_machine_employee($acc, $register, $raw, $branch)
{

    if (!empty($acc) && $acc > 0) {

        $fixcolum = "(acc_no,register,raw,branch,created_at)";

        $colum = "('$acc','$register','$raw',$branch,'" . date("Y-m-d H:i:s") . "')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "machine_registration", true);

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// add machine employee //
function get_machine_employee($branch)
{

    if (!empty($branch) && $branch > 0) {

        $result = $GLOBALS['crud']->runQuery("SELECT * FROM machine_registration where branch = " . $branch);

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}


// add Cheque details //
function add_cheque_details($cust_id, $no, $date, $amount, $type, $bankname, $narration, $branch_id)
{

    if (!empty($cust_id) && $cust_id > 0) {

        $fixcolum = "(cheque_number,cheque_date,amount,payment_mode,bank_name,naraation,date,branch_id)";


        $colum = "('$no','$date',$amount,'$type','$bankname', '$narration','" . date("Y-m-d H:i:s") . "',$branch_id)";

        $results = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "bank_cheque_general", true);

        if ($results) {

            $fixcolum = "(cheque_id,naraation,cheque_status_id,date,status_id,bank_account_id)";

            $colum = "($results,'$narration',1,'$date',1,0)";

            $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "bank_cheque_details", true);
            if ($result) {

                $fixcolum = "(cheque_id,customer_id)";

                $colum = "($results,$cust_id)";

                $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "bank_cheque_customer", true);

                if ($result) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return $results;
        }
    } else {
        return 0;
    }
}


// add Cheque details //
function add_cheque_information($cheque_no, $receipt_no, $amount)
{

    if (!empty($cheque_no) && $cheque_no > 0) {

        $fixcolum = "(cheque_no,receipt_no,amount)";

        $colum = "('$cheque_no','$receipt_no','$amount')";

        $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_cheque_details", true);

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}


//Getting Cheque Bounces Details
function getBouncedCheque()
{

    $result = $GLOBALS['crud']->runQuery("SELECT * from cheque_bounce where Sync = 1 ");

    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}


//Update Cheque Bounces
function updateBouncedChequeStatus($id)
{

    $result =  $GLOBALS['crud']->modify_mode("Sync = 0", 'cheque_bounce', "id = " . $id . " ");

    if (!empty($result) && sizeof($result) > 0) {
        return 1;
    } else {
        return 0;
    }
}


function reset_opening($id, $bal)
{
    $result =  $GLOBALS['crud']->modify_mode("balance = '$bal'", 'sales_opening', "opening_id = " . $id . " ");

    if (!empty($result)) {
        return 1;
    } else {
        return 0;
    }
}

function get_customer_all_receipt($customer_id){
    $result = $GLOBALS['crud']->runQuery("SELECT (SELECT IFNULL(total_amount,0)  FROM `sales_account_general` WHERE receipt_id = sales_receipts.id ) AS receiptAmount,(SELECT IFNULL(balance_amount,0)  FROM `sales_account_general` WHERE receipt_id = sales_receipts.id ) AS balanceAmount, (SELECT
  ABS(IFNULL(SUM(total_amount - credit ),0) + (SELECT SUM(debit) FROM customer_account WHERE cust_id = sales_receipts.customer_id AND receipt_no = 0) - (SELECT
      SUM(credit)
    FROM
      customer_account
    WHERE cust_id = sales_receipts.customer_id
      AND receipt_no = 0))
FROM
  customer_account
WHERE cust_id = sales_receipts.customer_id  AND receipt_no != 0 ) as balance,sales_receipts.*,customers.name FROM `sales_receipts`
  INNER JOIN customers ON customers.id = sales_receipts.customer_id
  WHERE sales_receipts.`customer_id` = ".$customer_id." ORDER BY sales_receipts.date DESC ");
    if (!empty($result) && sizeof($result) > 0) {
        $response = array();
        foreach ($result as $key => $value) {
            $remainingBalance = 0;
            if($value['receiptAmount'] == $value['balanceAmount']){
                $remainingBalance = $value['receiptAmount'];
            }else{
                $remainingBalance = $value['receiptAmount'] - $value['balanceAmount'];
            }


            $sales_receipt_details = $GLOBALS['crud']->runQuery("SELECT total_qty FROM `sales_receipt_details` where receipt_id = " . $value['id'] . "");

            $response[] = array(
                'receipt_no' => $value['receipt_no'],
                'date' => $value['date'],
                'time' => date('h:i:s a',strtotime($value['time'])),
                'receipt_id' => $value['id'],
                'total_amount' => $value['total_amount'],
                'total_item_qty' => $value['total_item_qty'],
                'balance' => $value['balance'],
                'remainingBalance' => $remainingBalance,
                'sales_receipt_details' => $sales_receipt_details,
            );
        }
        // print_r($response);exit;
        return json_encode($response);
    } else {
        return 0;
    }
}

// Get customer pending Payment
function get_customer_pending_payment($customer_id,$status,$sort_order){
    // echo $customer_id;exit;
    $result = $GLOBALS['crud']->runQuery("SELECT sales_receipts.*,customers.name FROM `sales_receipts`
  INNER JOIN `sales_account_general` ON sales_account_general.`receipt_id` = sales_receipts.`id`
  INNER JOIN customers ON customers.id = sales_receipts.customer_id
  INNER JOIN customer_account ON customer_account.cust_id = customers.id
  WHERE sales_account_general.`status` = ".$status." AND sales_receipts.`customer_id` = ".$customer_id." ORDER BY sales_receipts.date ".$sort_order." ");
    if (!empty($result) && sizeof($result) > 0) {
        $response = array();
        foreach ($result as $key => $value) {
            $response[$key][] = $value;
            $cust_ledger = $GLOBALS['crud']->runQuery("SELECT debit,credit,total_amount FROM customer_account where receipt_no = ".$value['id']." ");
            if (!empty($cust_ledger) && sizeof($cust_ledger) > 0) {
                $response[$key]['customer_ledger'] = $cust_ledger;
            }
        }
        // echo  json_encode($response);exit;
        return json_encode($response);
    } else {
        return 0;
    }
}


// get customer Ledger //
function get_customer_ledger($id)
{
    if (!empty($id) && $id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT 1 as sales_type,payment_mode,cust_id,a.cust_account_id,b.name,a.total_amount,a.debit,a.credit,a.balance,a.created_at,c.receipt_no,a.received, a.narration,c.id as reciptId,c.receipt_no as receiptNo FROM customer_account a
            INNER JOIN customers b on b.id = a.cust_id
            LEFT JOIN sales_receipts c on c.id = a.receipt_no
            LEFT JOIN `sales_payment` ON sales_payment.`payment_id` = c.`payment_id` 
            where a.cust_id = '".$id."'
            UNION
            SELECT 2 as sales_type,'',a.id as cust_id,'','',0,0, amount as credit,0,b.timestamp,'',3,'Sales Return','',''  FROM  customers a INNER JOIN sales_receipts c  ON c.customer_id = a.id INNER JOIN `sales_return` b  ON b.receipt_id = c.id  WHERE a.id = '".$id."' ");

        if (!empty($result) && sizeof($result) > 0) {
            $result = set_customer_ledger($result);
            // echo json_encode($result);exit;
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}


function set_customer_ledger($result){
    $data = array();
    $result = json_decode(json_encode($result), false);
    $receipt_balance = 0;$total_amount = 0;$credit = 0;$debit = 0;
    foreach ($result as $value){
        $creditGreater = false;
        if($credit > $total_amount){
            $creditGreater = true;
        }
        if ($value->total_amount > 0) {
            $total_amount += $value->total_amount;
        } else {
            $total_amount += $value->debit;
        }
        $credit += $value->credit;
        $debit += $value->debit;

        if ($value->total_amount == 0 && $value->credit == 0) {
            if($value->receipt_no != ''){
                $receipt_balance = $receipt_balance + $value->debit;
            }else{
                if($creditGreater ==true){
                    $receipt_balance = abs($receipt_balance - $value->debit);
                }else{
                    $receipt_balance = abs($receipt_balance + $value->debit);
                }
            }
        } elseif ($value->total_amount > 0) {
            if($creditGreater ==true){
                $receipt_balance = abs($receipt_balance - $value->debit);
            }else{
                $receipt_balance += abs($value->total_amount - $value->credit);
            }
        } elseif ($value->total_amount < 1) {
            if ($value->debit != 0) {
                $receipt_balance += abs($value->credit - $value->debit);
            } else {
                if ($receipt_balance != 0) {
                    if ($credit > $total_amount) {
                        if($total_amount !=0){
                            if($creditGreater ==true){
                                $receipt_balance = $receipt_balance + $value->credit;
                            }else{
                                $receipt_balance = abs($receipt_balance - $value->credit);
                            }
                        }else{
                            $receipt_balance = $receipt_balance + $value->credit;
                        }
                    } else {
                        $receipt_balance = abs($receipt_balance - $value->credit);
                    }
                } else {
                    $receipt_balance =  $value->credit;
                }
            }
        }

        $receipt_no = $value->receipt_no == '' ?'Manual Adjustment':$value->receipt_no;
        if($value->received == 1 && $value->sales_type == 1){
            $sales_type = 'make payment';
        }elseif (($value->received == 0 || $value->received > 1 )  && $value->sales_type == 1) {
            $sales_type = 'Manual';
        }else{
            $sales_type = 'sale return';
        }
        $data[] = array(
            'created_date' => date("d F Y",strtotime($value->created_at)),
            'created_time' => date("h:i A",strtotime($value->created_at)),
            'receipt_no' => $receipt_no,
            'payment_mode' =>$value->payment_mode,
            'narration' => $value->narration,
            'total_amount' => number_format($value->total_amount,2),
            'credit' => number_format($value->credit,2),
            'debit' => number_format($value->debit,2),
            'balance'=> number_format(($receipt_balance),2),
            'balance_numeric'=> $receipt_balance,
            'sales_type' => $sales_type,
        );
    }
    return $data;
}

// get Vendor Ledger //
function get_vendor_ledger($id)
{

    if (!empty($id) && $id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM vendor_ledger where vendor_id = " . $id . "");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// echo get_customers(17);
// get Customers  //
function get_customers($id)
{

    if (!empty($id) && $id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM customers where user_id IN(Select user_id from user_authorization where company_id = " . $id . ") and status_id = 1");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

//Get Company Inventory
function getCompanyInventory($id)
{
    if (!empty($id) && $id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT a.id,a.item_code,a.department_id,a.sub_department_id,a.product_name,e.name,a.details,IFNULL((Select SUM(balance) from inventory_stock where product_id = a.id and branch_id IN (Select branch_id from branch where company_id = $id)),0) as qty,IFNULL((Select AVG(cost_price) from inventory_stock where product_id = a.id and branch_id IN (Select branch_id from branch where company_id = $id)),0) as cost_price,c.retail_price,c.wholesale_price,c.discount_price,a.image,d.reminder_qty,c.online_price from inventory_general a INNER JOIN inventory_uom b on b.uom_id = a.uom_id INNER JOIN inventory_price c on c.product_id = a.id and c.status_id = 1 INNER JOIN inventory_qty_reminders d on d.inventory_id = a.id INNER JOIN inventory_uom e on e.uom_id = a.uom_id where a.company_id = $id and a.status = 1");
        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    }
}
//echo getTaxes(7);
//GET TAXES BY COMPANY
function getTaxes($company_id)
{
    if (!empty($company_id) && $company_id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM taxes where company_id = " . $company_id . " and status_id = 1 and show_in_pos = 1");
        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    }
}

function getDeliveryCharges($branch)
{
    if (!empty($branch) && $branch > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT id,area_name,charges FROM `delivery_charges` WHERE branch_id = " . $branch . " and status_id = 1");
        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    }
}


function getInventoryReference($company)
{
    if (!empty($company) && $company > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT a.id,a.product_id,a.refrerence FROM inventory_reference a inner join inventory_general b on b.id = a.product_id where b.company_id = $company");
        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    }
}

//echo getInventoryImages(82);
function getInventoryImages($itemCode)
{
    if (!empty($itemCode) && $itemCode > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM inventory_images where item_id = $itemCode");
        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    }
}


//echo registerCustomerForApp(6,'','','','');
function registerCustomerForApp($customer_id, $plateform, $device_manufacturer, $device_model, $device_serial)
{
    if (!empty($customer_id) && $customer_id > 0) {
        $customer = $GLOBALS['crud']->runQuery("SELECT * FROM customers a INNER JOIN user_authorization b on b.user_id = a.user_id where a.id = $customer_id");
        if (!empty($customer) && sizeof($customer) > 0) {

            //INSERT INTO USER DETAILS
            $fixcolum = "(id,fullname,username,password,email,contact,country_id, city_id,address,image,remember_token,created_at,updated_at,show_password)";
            $colum = "(0,'" . $customer[0]['name'] . "'," . $customer[0]['mobile'] . ",'1234','" . $customer[0]['email'] . "'," . $customer[0]['mobile'] . "," . $customer[0]['country_id'] . "," . $customer[0]['city_id'] . ", '" . $customer[0]['address'] . "','','','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "','1234')";
            $userDetails = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "user_details", true);
            if (sizeof($userDetails) > 0) {
                //INSERT INTO USER DETAILS
                $fixcolum_authorization = "(user_id,company_id,branch_id,role_id,status_id)";
                $colum_authorization = "(" . $userDetails . "," . $customer[0]['company_id'] . "," . $customer[0]['branch_id'] . ",3,2)";
                $authorization = $GLOBALS['crud']->insert_mode($fixcolum_authorization, $colum_authorization, "user_authorization", true);

                if (sizeof($authorization) > 0) {

                    //INSERT INTO USER LOCATION
                    $fixcolum_location = "(user_id,city_id,latitud,longitud,address)";
                    $colum_location = "(" . $userDetails . "," . $customer[0]['city_id'] . ",'','','" . $customer[0]['address'] . "')";
                    $location = $GLOBALS['crud']->insert_mode($fixcolum_location, $colum_location, "user_locations", true);

                    if (sizeof($location) > 0) {
                        $serial_key = serial_generate();
                        //INSERT INTO USER SYSTEM INFORMATION
                        $fixcolum_sys_information = "(user_id,status_id,push_notification,serial_key,plateform,device_manufacturer,device_model,device_serial,created_at,updated_at)";
                        $colum_sys_information = "(" . $userDetails . ",1,'','" . $serial_key . "','" . $plateform . "','" . $device_manufacturer . "','" . $device_model . "','" . $device_serial . "','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "')";
                        $system_information = $GLOBALS['crud']->insert_mode($fixcolum_sys_information, $colum_sys_information, "user_systemdetails", true);

                        if (sizeof($system_information) > 0) {
                            //INSERT INTO USER CUSTOMER TABLE
                            $otp_key = otp_generate();
                            $fixcolum_user_customer = "(user_id,customer_id,otp,verified)";
                            $column_user_customer = "(" . $userDetails . "," . $customer[0]['id'] . ",'" . $otp_key . "',0)";
                            $user_customer = $GLOBALS['crud']->insert_mode($fixcolum_user_customer, $column_user_customer, "user_customer", true);
                            if (sizeof($user_customer) > 0) {
                                return $userDetails;
                            } else {
                                return 2;
                            }
                        } else {
                            return 3;
                        }
                    }
                }
            } else {
                return 4;
            }
        } else {
            return 5;
        }
    }
}



//echo update_password(509,6544);
//UPDATE PASSWORD
function update_password($user, $password)
{
    $columns = "password = '" . $password . "',show_password = '" . $password . "',show_password = '" . $password . "'";
    $update = $GLOBALS['crud']->modify_mode($columns, 'user_details', "id = " . $user . " ");

    $auth = $GLOBALS['crud']->runQuery("SELECT authorization_id  FROM `user_authorization` where user_id = $user ");

    $columns = "status_id = 1";
    $update = $GLOBALS['crud']->modify_mode($columns, 'user_authorization', "authorization_id = " . $auth[0]["authorization_id"] . " ");
    if ($update) {
        return 1;
    } else {
        return 0;
    }
}

function download_floors($branch)
{
    if (!empty($branch) && $branch > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT * FROM floors where branch_id = $branch");
        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    }
}

// echo download_bank_discount(157);
function download_bank_discount($branch)
{
    if (!empty($branch) && $branch > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT a.bank_discount_id,b.bank_id,b.bank_name,a.percentage,b.image FROM bank_discount a inner join banks b on b.bank_id = a.bank_id where branch_id = $branch and status_id = 1");
        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    }
}


function download_sales_order_mode()
{

    $result = $GLOBALS['crud']->runQuery("SELECT * FROM `sales_order_mode`");
    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

function download_kitchen_departments($branch)
{

    $result = $GLOBALS['crud']->runQuery("SELECT a.id,a.kitchen_department_name,c.department_name,a.branch_id FROM kitchen_departments_general a INNER JOIN kitchen_department_details b on b.kitchen_depart_id = a.id INNER JOIN inventory_department c on c.department_id = b.inventory_department_id where a.branch_id = $branch");
    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

function download_kitchen_departments_printers($branch)
{

    $result = $GLOBALS['crud']->runQuery("SELECT a.id,a.department_id,b.kitchen_department_name,d.department_id as inventory_depatment_id,d.department_name,a.printer_name,a.LAN,a.bluetooth,a.Desktop,a.cloud FROM kitchen_department_printers a inner join kitchen_departments_general b on b.id = a.department_id INNER JOIN kitchen_department_details c on c.kitchen_depart_id = a.department_id INNER JOIN inventory_department d on d.department_id = c.inventory_department_id where b.branch_id = $branch");
    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}


function download_service_providers($branch)
{

    $result = $GLOBALS['crud']->runQuery("SELECT a.id,a.provider_name,a.contact,b.category,a.cnic_ntn,a.address,a.percentage_id,a.image,c.user_id,a.payment_value FROM service_provider_details a INNER JOIN service_provider_category b on b.category_id = a.categor_id INNER JOIN user_salesprovider_relation c on c.provider_id = a.id where a.branch_id = $branch and a.status_id = 1");
    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}

function download_permission($terminal)
{
    $result = $GLOBALS['crud']->runQuery("SELECT * FROM `users_sales_permission` where terminal_id = $terminal");
    if (!empty($result) && sizeof($result) > 0) {
        return json_encode($result);
    } else {
        return 0;
    }
}
// echo customer_Registration("03112108156","03112108156","7","sample123","","","","");
function customer_registration($number, $password,$companyId,$clientId,$customerId,$latitude,$longitude,$device_serial)
{
	$otp = rand(1000, 9999);	
	$customer = $GLOBALS['crud']->runQuery("SELECT *  FROM `customers` where mobile = '$number' and company_id = $companyId ");

	if (count($customer) > 0) {
		$update = $GLOBALS['crud']->runQuery("update `customers` set otp = '$otp', client_id = '$clientId', password = '$password', latitude = '$latitude', longitude = '$longitude', device_serial = '$device_serial'  where id = " . $customer[0]["id"] . "");
		if ($update > 0) {
			$message = "Your Registration No is : ".$otp;
			echo send_otp($number,$message);
			// echo sendPushNotificationToCustomerApp($clientId,$message);
			return $customer[0]["id"];
		} else {
			return 0; 
		}

	}else{
		// return 0;
		$insert = $GLOBALS['crud']->runQuery("INSERT into `customers` (name,status_id,country_id,city_id,mobile,password,otp,client_id,latitude,longitude,device_serial,company_id) values('$number',1,170,1,'$number','$password','$otp','$clientId','$latitude','$longitude','$device_serial',$companyId)");
		if ($insert > 0) {
			echo send_otp($number,$otp);
			// echo sendPushNotificationToCustomerApp($clientId,$message);
			return 1;
		} else {
			return 0;
		}
	}
	// $otp = rand(1000, 9999);	
		
		// $customer = $GLOBALS['crud']->runQuery("SELECT *  FROM `customers` where mobile = '$number' and company_id = $companyId ");

		// if (count($customer) > 0) {
			// $checkDeviceExists =  $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM `customers` WHERE device_serial = '$device_serial'");
			// if($checkDeviceExists[0]["count"] == 0){
			
				// $update = $GLOBALS['crud']->runQuery("update `customers` set otp = '$otp', client_id = '$clientId', password = '$password', latitude = '$latitude', longitude = '$longitude', device_serial = '$device_serial'  where id = " . $customer[0]["id"] . "");
				// if ($update > 0) {
					// $message = "Your OTP is  # ".$otp."";
					// echo sendPushNotificationToCustomerApp($clientId,$message);
					// return $customer[0]["id"];
				// } else {
					// return 0; 
				// }
			// }else{
				// $insert = $GLOBALS['crud']->runQuery("INSERT into  `customers` (status_id,country_id,city_id,name,mobile,password,otp,client_id,latitude,longitude,device_serial) values(1,170,1,'$name','$number','$password','$otp','$clientId','$latitude','$longitude','$device_serial')");
				// if (!empty($insert)) {
					// echo sendPushNotificationToCustomerApp($clientId,$message);
					// return 1;
				// } else {
					// return 0;
				// }
			// }
		// } else {
			// return 0;
		// }
	// }else{
		// return 0;
	// }
}
// echo customer_Login('03111234567','1234');
function customer_Login($number, $password,$device_serial)
{
    if (!empty($number) && !empty($password)) {
		
		$checkDeviceExists =  $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM `customers` WHERE device_serial = '$device_serial'");

		if($checkDeviceExists[0]["count"] == 1){

			$result = $GLOBALS['crud']->runQuery("SELECT * from customers where mobile = '$number' and password = '$password' and is_mobile_app_user = 1");
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
// echo chkUsernameOrPassword('wild','03232985466');
function chkUsernameOrPassword($username, $password)
{
    if (!empty($username) && !empty($password)) {
        $result = $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM `user_details` where username = '$username' and show_password = '$password'");
        if ($result[0]["count"] > 0) {
            return 1;
        } else {
            return 0;
        }
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
    $orders = $GLOBALS['crud']->runQuery("SELECT a.id,a.date,a.time,IFNULL(c.total_amount,0) as 'total_amount',IFNULL(b.discount_amount,0) as 'discount_amount',IFNULL(b.sales_tax_amount,0) as 'sales_tax_amount',a.actual_amount FROM sales_receipts a LEFT JOIN  sales_account_subdetails b on b.receipt_id = a.id LEFT JOIN  sales_account_general c on c.receipt_id = a.id  where a.id = '$orderId'");
    $orderDetails = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,b.product_name,a.total_qty,a.total_amount,a.item_price,a.taxamount,a.discount FROM sales_receipt_details a Inner join inventory_general b on b.id = a.item_code where a.receipt_id = $orderId");
    return getDataForFBR($orders,$orderDetails,$fbrData);
//    print_r($orders);
}
function getDataForFBR($order,$orderItems,$fbrData){
    $orderDetails = array();
	$taxrate = $fbrData[0]['tax_rate'];
	$taxpercentage = $taxrate / 100;
	$totalProducts = 0;

    foreach ($orderItems as $key => $products) {
//        $price = $products['price']  * $products['quantity'];
//        $taxamount = round($price * ($products['taxrate'] / 100), 0);
		   $totalProducts += $products['total_qty'];
        $arrayVar = array(
            "ItemCode" => $key + 1,
            "ItemName" => $products['product_name'],
            "Quantity" =>  $products['total_qty'],
            "PCTCode" => "11001010",
            "TaxRate" =>  $taxrate,//$taxrate,
            // "SaleValue" =>  $products['total_amount'],
            // "TotalAmount" =>  $products['total_amount'],
            // "TaxCharged" =>  round($products['total_amount'] * $taxpercentage),
			"SaleValue" =>   $products['item_price'] * $products['total_qty'] , //* $products['total_qty']
            "TotalAmount" => $products['total_amount'],
            "TaxCharged" =>  $products['taxamount'],
            "Discount" =>  $products['discount'],
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
    $myObj["TotalQuantity"] = $totalProducts;
    $myObj["TotalSaleValue"] =  $order[0]["actual_amount"]; //This is for maintaing Actual Sales without tax
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
//    print_r($outPut );
}

function updateInvoiceNumberToTable($invoiceNumber,$orderId){
    $update = $GLOBALS['crud']->runQuery("Update sales_receipts set fbrInvNumber = '$invoiceNumber' where id  = $orderId");
    return $invoiceNumber;

}

// echo sendPushNotification('cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH');
// echo sendPushNotification('eWj4sW80QZOGvbVzvU9wp0:APA91bGDTFpG2TFeaA8heHOH96YnIB32vw_jnyhpmLWV7ByFcoM6JxlXyvb4trWzO_goBAP3xsTrr3QgqhVtskWqBt9liQNGVAjQgNOdgyQ0qjmUQ_dWZLI-XikkYil9GvYnMHegmnjf','123','25000');
function sendPushNotification($clientId,$orderId,$amount){ 
		$message = "Your Order # ".$orderId." (PKR ".number_format($amount,2).") has been received - Thank You";
		// $request->title = "Order "; 
		$request->body = $message;
        $firebaseToken = [];//["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
		array_push($firebaseToken,$clientId);
		// print_r($firebaseToken);
        // $SERVER_API_KEY = 'AAAAgRgRcP0:APA91bEoVPcoBab8_4Crx1rbZbhX-o_hAe9HXq7xNHaSxfo-uQyH2BYXU0sR43SODDgLVFwTYJ37FVE-jnhrSyZoAC78Gjk9lfbftqZA2u5F6MZPNGxsaj3WnIc89pEZ37iiJKVG0Slz';
		 // $SERVER_API_KEY = 'AAAA8AK3eRE:APA91bGi798Gj-ow6_BIhbwwU7scIG-khTsOwARyfaNj0lYn9sOFyo5FWTHckythVDDR7_6rQswX7JXqEUt_Hz6_sjTa2HkEtXQhoRFcvzSZSiwtClNeoGQrK9s0slJTKgKAWBY2QhtU';
		
         $SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_ ';

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                // "title" => $request->title,
                "body" => $request->body,
				"icon" => "https://sabsoft.com.pk/Retail/public/assets/images/desktop-notify-icon.png",
                "content_available" => true,
                "priority" => "high",
				// "click_action" => ,
            ],
			"data" => [
				"id" => "123456",
				"name" => "Adil",
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

        $response = curl_exec($ch);
		
		return json_encode($response);
}

function sendPushNotificationToCustomerApp($clientId,$message,$orderId){ 

		// $message = "Your OTP is  # ".$otp."";---*]\]i-]-ikp00000-77y-o78.ol
		$orderDetails = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,b.product_name,a.total_qty,a.total_amount FROM sales_receipt_details a Inner join inventory_general b on b.id = a.item_code where a.receipt_id = $orderId");
        $firebaseToken = [];//["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
		array_push($firebaseToken,$clientId);
		$SERVER_API_KEY = 'AAAA8AK3eRE:APA91bF68jDQZ_O1zw6i3oWbjAToxndEGNc2zP4jsZ1_JwTs6G2cqDj_YGypwMEff_k0pAwHbYkRDxFvHK8JbIvkDeT6tWJlJca01lQ7QCfKhRysdrQzXVXb6Hapr1A4xBvq1TKADscb';
		   
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => "Terminal Orders",
                "body" => "New Order is been added",
				"icon" => "https://sabsoft.com.pk/Retail/public/assets/images/desktop-notify-icon.png",
                "content_available" => true,
                "priority" => "high",
				// "click_action" => ,
            ],
			"data" => [
				"par1" => $message,
				"par2" => json_encode($orderDetails),
				
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

function getMobilePromoImages($companyId)
{
	if($companyId != "" && $companyId != 0){
		$result = $GLOBALS['crud']->runQuery("SELECT id,product_id,description,CONCAT('https://sabsoft.com.pk/Retail/public/assets/images/mobile/',image) as image FROM mobile_promotion_images where company_id = $companyId");
        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else { 
            return 0;
        }
	}
}
// echo updateClientId("456",596);
function updateClientId($clientId,$terminalId,$branchId)
{
	if($clientId != "" && $terminalId != 0 && $branchId != 0){
		$count = $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM `terminal_details` where terminal_id = '$terminalId' and branch_id = '$branchId' and device_token = '$clientId'");
        if ($count[0]["count"] == 0) {
			$result = $GLOBALS['crud']->runQuery("UPDATE `terminal_details` SET `device_token` = '$clientId' where terminal_id = $terminalId and branch_id = '$branchId' ");
			if (!empty($result) && sizeof($result) > 0) {
				return 1;
			} else { 
				return 0;
			}
		}else{
			return 0;
		}
	}	
}

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

function downloadCompanies()
{
	$result = $GLOBALS['crud']->runQuery("SELECT company_id,name,address,isOnlineApp FROM `company` where isOnlineApp = 1 and status_id = 1 ");
	if (!empty($result) && sizeof($result) > 0) {
		return json_encode($result);
	} else { 
		return 0;
	}
}

function sync_salesReceipt($uid, $receipt, $openid, $order_mode_id, $customer_id, $payment_id,$actual_amount ,$total_amount, $total_item_qty, $delivery, $branch, $date, $time, $terminal, $sales, $web_id,$due_date,$delivery_person_name,$contact_no,$vehicle_no,$service_provider_order_no,$web,$receive_amount,$amount_paid_back,$discount_amount, $coupon, $promo_code, $sales_tax_amount, $service_tax_amount, $creditTrans, $deliveryCharges,$deliveryChargesAmount,$bank_discount_id,$srb,$riderCharges,$billPrintName,$machine_terminal_count)
{

    if (!empty($uid) && $uid > 0) {
        //If Web Id found then will perform update
        if ($web_id > 0) {
            $columns = "opening_id = '" . $openid . "',order_mode_id = '" . $order_mode_id . "',userid = '" . $uid . "',customer_id = '" . $customer_id . "',payment_id = '" . $payment_id . "',total_amount = '" . $total_amount . "',total_item_qty = '" . $total_item_qty . "',delivery_date = '" . $delivery . "',sales_person_id = '" . $sales . "',due_date = '".$due_date."',bill_print_name = '".$billPrintName."' ";
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
                $fixcolum = "(receipt_no,opening_id,order_mode_id,userid,customer_id,payment_id,actual_amount ,total_amount,total_item_qty,is_sale_return,status,delivery_date,branch,terminal_id,sales_person_id,date,time,due_date,isSeen,is_notify,web,bill_print_name,machine_terminal_count)";

                $colum = "('$receipt','$openid',$order_mode_id,$uid,$customer_id,$payment_id,'$actual_amount','$total_amount','$total_item_qty',0,$mode,'$delivery',$branch,$terminal,$sales,'$date','$time','$due_date',1,1,'$web','$billPrintName','$machine_terminal_count')";

                $result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "sales_receipts", true);

                if (!empty($result)) {
					
					if($order_mode_id == 1){
						$message = "Going inside for the receipt ".$result;
						echo create_log(json_encode($message));
						// echo getParentTerminal($terminal,$result,$uid);
						
					}

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
					
                    $provider = $GLOBALS['crud']->runQuery("SELECT a.provider_id,c.id as type,b.payment_value FROM user_salesprovider_relation a INNER JOIN service_provider_details b on b.id = a.provider_id INNER JOIn service_provider_payment_type c on c.id = b.payment_type_id where a.user_id = " . $sales . "");
                    $lastBalance = $GLOBALS['crud']->runQuery("SELECT balance FROM service_provider_ladger where ladger_id = (Select Max(ladger_id) from service_provider_ladger where provider_id = " . $provider[0]["provider_id"] . ") ");
                    $amount = "";
					$bal = "";
					// IF PAYMENT TYPE IS PERCENTAGE 
					if($provider[0]["type"] == 1){
						$amount = ($total_amount * ($provider[0]["payment_value"] / 100));
						$bal = $lastBalance[0]["balance"] + $amount;
					}elseif($provider[0]["type"] == 2){
						$amount = $provider[0]["payment_value"] ;
						$bal = $lastBalance[0]["balance"] + $amount;
					}elseif($provider[0]["type"] == 3){
						$amount = ($riderCharges == "" ? 0 : $riderCharges ) ;
						$bal = $lastBalance[0]["balance"] + $amount;
					}
					// }else{
						// $amount = $provider[0]["payment_value"] ;
						// $bal = $lastBalance[0]["balance"] + $amount;
					// }
					
					// INSERT INTO ORDER LOGS TABLE
					$fix = "(order_id,status_id,date)";
                    $col = "(" . $result . ",".$mode.",'" . date("Y-m-d H:i:s") . "')";
                    $orderLogs = $GLOBALS['crud']->insert_mode($fix, $col, "orders_logs", true);
					
					$fix = "(service_provider_id,receipt_id,date)";
                    $col = "(" . $provider[0]["provider_id"] . ",".$result.",'" . date("Y-m-d H:i:s") . "')";
                    $spOrders = $GLOBALS['crud']->insert_mode($fix, $col, "service_provider_orders", true);

                    $fix = "(provider_id,debit,credit,balance,order_id,date, narration,receipt_id,receipt_no,receipt_total_amount,delivery_person_name,contact_no,vehicle_no,service_provider_order_no)";
                    $col = "(" . $provider[0]["provider_id"] . ",0," . $amount . "," . $bal . ",1,'" . date("Y-m-d H:i:s") . "', 'Narration',".$result.",'".$receipt."','".$total_amount."','".$delivery_person_name."','".$contact_no."','".$vehicle_no."','".$service_provider_order_no."')";
                    $pro = $GLOBALS['crud']->insert_mode($fix, $col, "service_provider_ladger", true);
					
					// $clientId = $GLOBALS['crud']->runQuery("SELECT client_id FROM `customers` where id = $customer_id");
					
					// if(!empty($clientId))
					// {
						// $paymentId = $GLOBALS['crud']->runQuery("SELECT payment_mode FROM `sales_payment` where payment_id = $payment_id");
						// $message = "Order ID # ".$result." (".$paymentId[0]["payment_mode"].") Rs.".number_format($total_amount,2);
						// echo sendPushNotificationToCustomerApp($clientId[0]["client_id"],$message);
					// } 
                    // echo getParentTerminal($terminal,$result,$uid);
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

function downloadUpdateDepartment($id)
{
	if($id != "" ){
		$result = $GLOBALS['crud']->runQuery("SELECT department_id,department_name FROM `inventory_department` where department_id = $id");
		if (!empty($result) && sizeof($result) > 0) {
			return json_encode($result[0]);
		} else { 
			return 0;
		}
	}
}

function downloadUpdateCustomer($id)
{
	if($id != "" ){
		$result = $GLOBALS['crud']->runQuery("SELECT * FROM customers where id = $id");
		if (!empty($result) && sizeof($result) > 0) {
			return json_encode($result[0]);
		} else { 
			return 0;
		}
	}
}

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

function getPromotions()
{
	$result = $GLOBALS['crud']->runQuery("SELECT a.discount_id,a.discount_code,c.type_name,d.name,b.discount_value,b.min_order,g.usage_limit,g.onetimeuse,h.startdate,h.starttime,h.enddate,h.endtime,f.applies_name,e.name as status FROM discount_general a INNER JOIN discount_general_details b on b.discount_id = a.discount_id INNER JOIN discount_type c on c.discount_type_id = a.discount_type INNER JOIN discount_customer_eligibility d on d.eligibility_id = a.customer_eligibilty INNER JOIN discount_status e on e.id = a.status INNER JOIN discount_applies_to f on f.discount_applies_id = b.applies_to LEFT JOIN discount_limit g on g.discount_limit_id = a.discount_id INNER JOIN discount_period h on h.discount_id = a.discount_id where a.status IN (1,3)");
	if (!empty($result) && sizeof($result) > 0) {
		return json_encode($result);
	} else { 
		return 0;
	}
}


function getServiceProviderOrders($providerId)
{
	$result = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,b.receipt_no FROM service_provider_orders a INNER JOIN sales_receipts b on b.id = a.receipt_id where a.service_provider_id = ".$providerId);
	if (!empty($result) && sizeof($result) > 0) {
		return json_encode($result);
	} else { 
		return 0;
	}
}

function getParentTerminal($terminalId,$receipt,$userId,$mode)
{
	 $terminal = $GLOBALS['crud']->runQuery("SELECT terminal_bind.terminal_id,b.device_token,c.Parent_Order_Print as permission FROM `terminal_bind` INNER JOIN terminal_details b on b.terminal_id = terminal_bind.terminal_id INNER JOIN users_sales_permission c on c.terminal_id = terminal_bind.terminal_id where bind_terminal_id =  '$terminalId'");
	 $user = $GLOBALS['crud']->runQuery("SELECT fullname FROM `user_details` where id =  '$userId'");
     if(!empty($terminal) && $terminal[0]["permission"]){
		 $message = "OID".$receipt.",TID".$terminalId.",UID".$userId.",UN".$user[0]["fullname"].",M".$mode;
		 echo sendPushNotificationToParentDeviceApp($terminal[0]["device_token"],$message,$receipt);
	 }
}

function sendPushNotificationToParentDeviceApp($clientId,$message,$receipt){ 
		
		// $orderDetails = $GLOBALS['crud']->runQuery("SELECT a.*,b.product_name as item_name FROM sales_receipt_details a Inner join inventory_general b on b.id = a.item_code where a.receipt_id = $receipt");
        $firebaseToken = [];
		array_push($firebaseToken,$clientId);
		// ORIGINAL KEY
		$SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';
		$SERVER_API_KEY_MOBILE = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';   
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => "Terminal Order",
                "body" => "New Order is been added.",
				"icon" => "https://sabsoft.com.pk/Retail/public/assets/images/desktop-notify-icon.png",
                "content_available" => true,
                "priority" => "high",
            ],
			"data" => [
				"par1" => $message,
				// "par2" => json_encode($orderDetails),
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
		echo create_log(json_encode($response));
		
		$headersMobile = [
            'Authorization: key=' . $SERVER_API_KEY_MOBILE,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headersMobile);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
		echo create_log(json_encode($response));
		
		
		// return json_encode($response);
}

function create_log($message){
	// error message to be logged
	$error_message = "\n".date("Y-m-d H:i:s")." ".$message;
  
	// path of the log file where errors need to be logged
	$log_file = "./my-errors.log";
  
	// logging error message to given log file
	error_log($error_message, 3, $log_file);
}

function add_custom_headers($mobile,$columnOne,$columnTwo,$columnThree,$columnFour,$image){
	if($mobile != "" ){
		$fix = "(mobile,column_one,column_two,column_three,column_four,image)";
		$col = "('$mobile','$columnOne','$columnTwo','$columnThree','$columnFour','$image')";
		$result = $GLOBALS['crud']->insert_mode($fix, $col, "add_custom_headers", true);
		if (!empty($result) && sizeof($result) > 0) {
			return 1;
		} else { 
			return 0;
		}
	}
}


function getTokenFromPayMob($orderId){
	$orders = $GLOBALS['crud']->runQuery("SELECT a.id,a.date,a.time,IFNULL(c.total_amount,0) as 'total_amount',IFNULL(b.discount_amount,0) as 'discount_amount',IFNULL(b.sales_tax_amount,0) as 'sales_tax_amount' FROM sales_receipts a LEFT JOIN  sales_account_subdetails b on b.receipt_id = a.id LEFT JOIN  sales_account_general c on c.receipt_id = a.id  where a.id = '$orderId'");
    $orderDetails = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,b.product_name,a.total_qty,a.total_amount,a.item_code FROM sales_receipt_details a Inner join inventory_general b on b.id = a.item_code where a.receipt_id = $orderId");
	
	$myObj = [
		"api_key" =>  "ZXlKaGJHY2lPaUpJVXpVeE1pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SmpiR0Z6Y3lJNklrMWxjbU5vWVc1MElpd2ljSEp2Wm1sc1pWOXdheUk2TWpReU15d2libUZ0WlNJNkltbHVhWFJwWVd3aWZRLmVvVThlRkhpYUlEdUhvcTRBbzNvbkFabDJqRFlVTTRlLUlIVU1iVlVtTUNuak5NbVZ0MWlEM1JxUmxxaFJGOVJXa0oxUTVkSXZ6SW9JX1cyaElIelFR",
	];
    $url =  "https://pakistan.paymob.com/api/auth/tokens";
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json"));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($myObj));
    $result = curl_exec($curl);
    $outPut = json_decode($result, true);
    $paymobToken  = $outPut['token'];
	echo getOrderNoFromPayMob($paymobToken,$orderDetails,$orders);

}

function getOrderNoFromPayMob($token,$orderDetails,$orders){
	$details = array();
	foreach ($orderDetails as $key => $products) {
		$arrayVar = array(
			"name"=> $products['item_code'],
			"amount_cents"=> $products['total_amount'],
			"description"=> $products['product_name'],
			"quantity"=> $products['total_qty']
		);
		array_push($details, $arrayVar);
	}

	$myObj = [
		"auth_token" =>  $token,
		"delivery_needed" => "false",
		"amount_cents" => $orders[0]["total_amount"],
		"currency"=> "PKR",
		"merchant_order_id" => $orders[0]["id"],
		"items" => $details ,
	];

	$url =  "https://pakistan.paymob.com/api/ecommerce/orders";
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json"));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($myObj));
    $result = curl_exec($curl);
    $outPut = json_decode($result, true);
    $paymobOrderId  = $outPut['id'];
	echo getPaymentToken($token,$paymobOrderId,$orders);
}

function getPaymentToken($token,$orderId,$orders){
	$details = [
			"apartment" => "NA", 
			"email" => "claudette09@exa.com", 
			"floor" => "NA", 
			"first_name" => "Clifford", 
			"street" => "NA", 
			"building"=> "NA", 
			"phone_number" => "+86(8)9135210487", 
			"shipping_method" => "NA", 
			"postal_code" => "NA", 
			"city" => "NA", 
			"country" => "NA", 
			"last_name" => "Nicolas", 
			"state" => "NA"
	];
	$myObj = [
		"auth_token" => $token,
		"amount_cents" => $orders[0]["total_amount"], 
		"expiration" => "3600", 
		"order_id" => $orderId,
		"billing_data" => $details,
		"currency"=> "PKR", 
		"integration_id"=> "2570",
		"lock_order_when_paid"=> "false"
	];
	
	$url =  "https://pakistan.paymob.com/api/acceptance/payment_keys";
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json"));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($myObj));
    $result = curl_exec($curl);
    $outPut = json_decode($result, true);
    $paymobTokenId  = $outPut['token'];
	echo "https://pakistan.paymob.com/api/acceptance/iframes/6080?payment_token=".$paymobTokenId;
	
}


function sentInvoiceToSRB($companyId,$branchId,$orderId)
{
	$details = $GLOBALS['crud']->runQuery("SELECT * FROM `srb_details` where company_id = $companyId and branch_id = $branchId ");
	$orders = $GLOBALS['crud']->runQuery("SELECT * FROM `sales_receipts` where id = $orderId");
	$myObj   = array();
    $myObj['posId'] =  $details[0]["pos_id"];
    $myObj['name'] = $details[0]["pos_name"];
    $myObj["ntn"] =  $details[0]["ntn"]; //invoice number
    $myObj["invoiceDateTime"] = $orders[0]["date"] . ' ' . $orders[0]["time"];
    $myObj["invoiceType"] = 1;
    $myObj["invoiceID"] = $orders[0]["id"];
    $myObj["rateValue"] = 13;
    $myObj["saleValue"] =  $orders[0]["total_amount"];
    $myObj["taxAmount"] = $orders[0]["total_amount"] * 0.13;
    $myObj["consumerName"] =  "N/A";
    $myObj["consumerNTN"] =  "N/A";
    $myObj["address"] =  "N/A";
    $myObj["tariffCode"] = "N/A";
    $myObj["extraInf"] =  "N/A";
    $myObj["pos_user"] = $details[0]["pos_user"]; //child of the or the return invoce invoive will be 3
    $myObj["pos_pass"] = $details[0]["pos_pass"];
	$myobject    = json_encode($myObj);
	
	//SANDBOX
    // $fbrUrl = "http://apps.srb.gos.pk/PoSService/CloudSalesInvoiceService";
	// LIVE
    $fbrUrl = "https://pos.srb.gos.pk/PoSService/CloudSalesInvoiceService";
    $url =  $fbrUrl;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json"));
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
    $code = $outPut['resCode'];
    $srbInvoiceNumber = $outPut['srbInvoceId'];
	if ($code ==  "00"){
		return updateSrbInvoiceNumberToTable($srbInvoiceNumber,$orderId);
	}
}

function updateSrbInvoiceNumberToTable($invoiceNumber,$orderId){
    $update = $GLOBALS['crud']->runQuery("Update sales_receipts set srbInvNumber = '$invoiceNumber' where id  = $orderId");
    return $invoiceNumber;

}

function getBranchEmails($branchId)
{
    if (!empty($branchId)) {
        $result = $GLOBALS['crud']->runQuery("SELECT * from branch_emails where status = 1 and branch_id = '$branchId'");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

function checkLoggedIn($userId)
{
	if (!empty($userId)) {
        $result = $GLOBALS['crud']->runQuery("SELECT isLoggedIn FROM `user_authorization` where user_id = '$userId'");

        if (!empty($result) && sizeof($result) > 0) {
            if($result[0]["isLoggedIn"] == 1){
				return 1;
			}else{
				return 0;
			}
        } else {
            return 2;
        }
    } else {
        return 5;
    }
}

function updateUserClientId($userId,$clientId)
{
	if($userId != "" && $clientId != ""){
		$count = $GLOBALS['crud']->runQuery("SELECT COUNT(*) as count FROM `user_details` where id = '$userId' and device_token = '$clientId'");
		
        if ($count[0]["count"] == 0) {
			$result = $GLOBALS['crud']->runQuery("UPDATE `user_details` SET `device_token` = '$clientId' where id = '$userId' ");
			return 1;
		}else{
			return 2;
		}
	}	
}

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

function void_receipt($receiptId,$status)
{
	if ($receiptId > 0){
		$update = $GLOBALS['crud']->modify_mode("void_receipt = '$status'","sales_receipts","id = '$receiptId' ");
		if ($update > 0) {
			return 1;
		} else {
			return 0;
		}
	}else {
		return 0;
	}
}

function getApplicationUpdates()
{
	$result = $GLOBALS['crud']->runQuery("SELECT * FROM `application_updates` where status = 1 ");

	if (!empty($result) && sizeof($result) > 0) {
		return json_encode($result);
	} else {
		return 0;
	}
}


function cloudReceipt($receiptId,$imageUrl)
{
	$order = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.bill_print_name,b.terminal_name,b.terminal_id,a.date,a.time,c.payment_mode,d.username,a.userid,e.total_amount,e.receive_amount,e.amount_paid_back FROM sales_receipts a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN sales_payment c on c.payment_id = a.payment_id INNER JOIN user_details d on d.id = a.userid INNER JOIN sales_account_general e on e.receipt_id = a.id WHERE a.id = ".$receiptId);
	$departments = $GLOBALS['crud']->runQuery("SELECT d.kitchen_department_name FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code LEFT JOIN kitchen_department_details b on b.inventory_department_id = c.department_id LEFT JOIN kitchen_departments_general d on d.id = b.kitchen_depart_id where a.receipt_id = ".$receiptId." group by d.kitchen_department_name");
	$items = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,a.item_code,c.product_name,a.total_qty,a.item_price,a.total_amount FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code  where a.receipt_id = ".$receiptId);
	$printer = $GLOBALS['crud']->runQuery("SELECT * FROM `cloud_printers` where terminal_id = ".$order[0]['terminal_id']." and status = 1");
	$print = new PrintReceipt(500,$printer[0]["serial_number"]);
	// $print = new PrintReceipt(500);
	$print->ReceiptSample($order[0],$items,$imageUrl);
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

// echo sendVoiceToPrinter('thank you for shopping with us. Good Bye','');//http://codeskulptor-demos.commondatastorage.googleapis.com/GalaxyInvaders/pause.wav
function sendVoiceToPrinter($content,$link)
{
	$printer = new SunmiCloudPrinter();
	$sn = "N411229300488";
	$printer->pushVoice($sn,$content,$link);
}

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
        'text' => $orderDetails[0]["branch_name"]." Branch",
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
	
	$result = json_decode($result);
	print_r($result);
	// echo $result["messages"];
}

function orderStatusChange($receiptId,$status)
{
	if ($receiptId > 0 && $status > 0){
		$update = $GLOBALS['crud']->modify_mode("status = '$status'","sales_receipts","id = '$receiptId' ");
		if ($update > 0) {
			return 1;
		} else {
			return 0;
		}
	}
}

function createInventory($companyId,$departId,$subdepartId,$branch,$name,$costprice,$price,$stock)
{
	$department = $GLOBALS['crud']->runQuery("SELECT * FROM `inventory_department` where department_id = '$departId' LIMIT 1");
	if(count($department) > 0){
		$subdepartment = $GLOBALS['crud']->runQuery("SELECT * FROM `inventory_sub_department` where sub_department_id = ".$subdepartId." LIMIT 1");
	}
	
	$departmentId = $department[0]["department_id"];
	$subdepartmentId = $subdepartment[0]["sub_department_id"];
	$departmentname = $department[0]["department_name"];
	$subdepartmentname = $subdepartment[0]["sub_depart_name"];
	$itemcode = mb_substr($departmentname, 0, 1). mb_substr($subdepartmentname, 0, 1). "-" . strtolower(rand(0,9999));


	$fixcolum = "(company_id,department_id,sub_department_id,uom_id,cuom,product_mode,item_code,product_name,product_description,image,status,created_at,updated_at,weight_qty,slug,short_description,details)";
	$colum = "($companyId,$departmentId,$subdepartmentId,1,1,3,'$itemcode','$name','$name','null',1,'".date('Y-m-d H:s:i')."','".date('Y-m-d H:s:i')."',1,'".strtolower(str_replace(' ', '-', $name)) . "-" . strtolower(rand(0,9999))."','$name','$name')";
	$result = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "inventory_general", true);
	if($result > 0){
		// QTY REMINDER
		$fixcolum = "(inventory_id,reminder_qty)";
		$colum = "('$result',5)";
		$resultreminder = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "inventory_qty_reminders", true);
		
		// INVENTORY PRICE
		$fixcolum = "(actual_price,tax_rate,tax_amount,retail_price,wholesale_price,online_price,discount_price,product_id,status_id)";
		$colum = "('$costprice',0,0,'$price',0,0,0,$result,1)";
		$resultreminder = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "inventory_price", true);
		
		// GRN
		$grn = $GLOBALS['crud']->runQuery("SELECT COUNT(rec_id) as count FROM purchase_rec_gen");
		$grn =  $grn[0]["count"] + 1;
		$grn = "GRN-".$grn;
		
		$fixcolum = "(GRN,user_id,created_at,updated_at)";
		$colum = "('$grn',16,'".date('Y-m-d H:s:i')."','".date('Y-m-d H:s:i')."')";
		$purchaserecgen = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "purchase_rec_gen", true);
		
		$fixcolum = "(GRN,item_id,qty_rec)";
		$colum = "('$purchaserecgen','$result',1)";
		$resultreminder = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "purchase_rec_stock_opening", true);
		
		$fixcolum = "(grn_id,product_id,uom,cost_price,retail_price,wholesale_price,discount_price,qty,balance,status_id,branch_id)";
		$colum = "('$purchaserecgen','$result',1,$price,$price,0,0,1,$stock,1,$branch)";
		$resultreminder = $GLOBALS['crud']->insert_mode($fixcolum, $colum, "inventory_stock", true);
		
		$resultInventory = $GLOBALS['crud']->runQuery("SELECT b.id,b.item_code,b.department_id,b.product_name,b.sub_department_id,b.isPos,b.isOnline,(Select SUM(balance) from inventory_stock where product_id = a.product_id and branch_id = a.branch_id) as qty,c.name,(SELECT cost_price FROM `inventory_stock` where stock_id = (Select MAX(stock_id) from inventory_stock where product_id = a.product_id)) as cost_price,d.actual_price,d.tax_rate,d.tax_amount,d.retail_price,d.wholesale_price,d.discount_price,b.image,'Inventory' as status,e.reminder_qty as reminderqty,b.product_description,b.short_description,b.weight_qty as weight_qty  FROM inventory_stock a INNER JOIN inventory_general b on b.id = a.product_id INNER JOIN inventory_uom c on c.uom_id = b.uom_id INNER JOIN inventory_price d on d.product_id = b.id and d.status_id = 1 INNER JOIN inventory_qty_reminders e on e.inventory_id = b.id where a.branch_id = $branch and b.status = 1 and b.isHide = 0 and b.id = $result GROUP BY a.product_id  
		UNION
		SELECT a.product_id as id,a.item_code,b.department_id,a.item_name,b.sub_department_id,a.isPos,a.isOnline,a.quantity,c.name,(SELECT cost_price FROM `inventory_stock` where stock_id = (Select MAX(stock_id) from inventory_stock where product_id = a.product_id))as cost_price,d.actual_price,d.tax_rate,d.tax_amount,d.retail_price,d.wholesale_price,d.discount_price,b.image,'Open' as status,'' as reminderqty,b.product_description,b.short_description,b.weight_qty as weight_qty  FROM pos_products_gen_details a INNER JOIN inventory_general b on b.id = a.product_id LEFt JOin inventory_uom c on c.uom_id = a.uom  INNER JOIN pos_product_price d on d.pos_item_id = a.pos_item_id and d.status_id = 1 INNER JOIN inventory_price e on e.product_id = a.product_id and e.status_id = 1 WHERE a.branch_id = $branch and a.status_id = 1 and a.isHide = 0  and b.id = $result");

        if (!empty($resultInventory) && sizeof($resultInventory) > 0) {
            return json_encode($resultInventory[0]);
        } else {
            return 0;
        }
		// return $result;
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

function currency($companyId)
{
	$count = $GLOBALS['crud']->runQuery("SELECT data FROM `settings` where company_id = ".$companyId);
	if(!empty($count)){
		$currency = json_decode($count[0]['data']);
		return $currency->currency;
	}
}

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

// get inventory //
function getVoidReceipts($openingId,$terminalId)
{
    if (!empty($openingId) && $openingId > 0 && !empty($terminalId) && $terminalId > 0) {

        $result = $GLOBALS['crud']->runQuery("SELECT id,receipt_no,date,void_receipt,void_date FROM `sales_receipts` WHERE opening_id = $openingId and terminal_id = $terminalId and void_receipt = 1");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

function getSalesTaxMode()
{
	$result = $GLOBALS['crud']->runQuery("SELECT * FROM sales_tax_mode");

	if (!empty($result) && sizeof($result) > 0) {
		return json_encode($result);
	} else {
		return 0;
	}
}
// OTP TEMPLATE FOR WHATSAPP
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


function RizwanTradersCloudPrinter($receiptId)
{
	$order = $GLOBALS['crud']->runQuery("SELECT a.id,a.receipt_no,a.bill_print_name,b.terminal_name,b.terminal_id,a.date,a.time,c.payment_mode,d.username,a.userid,e.total_amount,e.receive_amount,e.amount_paid_back FROM sales_receipts a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN sales_payment c on c.payment_id = a.payment_id INNER JOIN user_details d on d.id = a.userid INNER JOIN sales_account_general e on e.receipt_id = a.id WHERE a.id = ".$receiptId);
	$departments = $GLOBALS['crud']->runQuery("SELECT d.kitchen_department_name FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code LEFT JOIN kitchen_department_details b on b.inventory_department_id = c.department_id LEFT JOIN kitchen_departments_general d on d.id = b.kitchen_depart_id where a.receipt_id = ".$receiptId." group by d.kitchen_department_name");
	$items = $GLOBALS['crud']->runQuery("SELECT a.receipt_id,a.item_code,c.product_name,a.total_qty,a.item_price,a.total_amount FROM sales_receipt_details a INNER JOIN inventory_general c on c.id = a.item_code  where a.receipt_id = ".$receiptId);
	$printer = $GLOBALS['crud']->runQuery("SELECT * FROM `cloud_printers` where terminal_id = ".$order[0]['terminal_id']." and status = 1");
	$print = new PrintReceipt(500,$printer[0]["serial_number"]);
	$print->RizwanReceiptSample($order[0],$items);
}

function sendStockPushNotification($productId,$company,$branch){ 
	$statusmessage =  "updated";
	$tokens = array();
	$title = "Stock Updated";
	$stock = $GLOBALS['crud']->runQuery("SELECT SUM(balance) as balance FROM `inventory_stock` WHERE `product_id` = ".$productId." and branch_id = ".$branch);
	$message = "ID".$productId.",STOCK".$stock[0]["balance"];
	
	$firebaseToken = $GLOBALS['crud']->runQuery("Select * from terminal_details where branch_id = $branch and device_token IS NOT NULL ");
	foreach($firebaseToken as $token){
		array_push($tokens,$token["device_token"]);
	}
	
	$SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';
	$server_api_key_mobile = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';   
	   
	$data = [
		"registration_ids" => $tokens,
		"notification" => [
			"title" => $title,
			"body" => "New set of Inventory is been updated",
			"icon" => "https://sabsoft.com.pk/Retail/public/assets/images/Sabify72.png",
			"content_available" => true,
			"priority" => "high",
			// "click_action" => ,
		],
		"data" => [
			"par1" => $message,
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