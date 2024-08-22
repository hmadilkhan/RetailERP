<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require "printtest.php";


$serial = $_POST['serial'];
$text = $_POST['text'];
$order = $_POST['order'];
$mode = $_POST['mode'];
$logo = $_POST['logo'];
$permission = $_POST['permissions'];
$order = json_decode($order);
$permission = json_decode($permission);


if($serial != "")
{
	if($mode == "accept"){
		// echo sendVoiceToPrinter($serial,"Accepting the order. Printing Kitchen Order Ticket and Receipt","");
		if(property_exists($permission, "print_kot") && $permission->print_kot == "true"){
			echo KOTSample($order,$serial);
			sleep(1);
		}
		echo ReceiptFiver($serial,$order,$logo,$permission);
	}else if($mode == "Receipt"){
		echo ReceiptFiver($serial,$order,$logo,$permission);
	}else if($mode == "KOT"){
		echo KOTSample($order,$serial);
	}
}
function ReceiptFiver($serial,$order,$imageUrl,$permission)
{
	$printer = new SunmiCloudPrinter(540);
	$printer->selectAsciiCharFont(50);
	$printer->setLineSpacing(12);
	$printer->setPrintModes(true, true, false);
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
	$printer->appendImage($imageUrl, SunmiCloudPrinter::DIFFUSE_DITHER);
	$printer->lineFeed();
	
	$printer->setBlackWhiteReverseMode(0);
	$printer->setPrintModes(true, true, true);
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
	$printer->appendText($order->unique_order_id);
	$printer->lineFeed(2);
	$printer->setPrintModes(false, false, false);
	$printer->appendText("----------------------------------------------");
	$printer->lineFeed(2);
	
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
	$printer->setupColumns(
		[360, SunmiCloudPrinter::ALIGN_LEFT,1]
	);
	
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
	$printer->setPrintModes(true, true, true);
	if($order->delivery_type == 1){
		$printer->printInColumns((property_exists($permission, "delivery_label") ? $permission->delivery_label : 'DELIVERY' ),0);
	}else{
		$printer->printInColumns((property_exists($permission, "selfpickup_label") ? $permission->selfpickup_label : 'SELF-PICKUP' ),0);
	}

	$printer->lineFeed();
	$printer->setupColumns(
		[500, SunmiCloudPrinter::ALIGN_LEFT,0]
	);
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
	$printer->setPrintModes(true, true, true);
	$printer->setUnderlineMode(10);
	$printer->setBlackWhiteReverseMode(0);
	
	if(property_exists($permission, "show_customer_name") && $permission->show_customer_name == "true"){
	$printer->printInColumns($order->user->name);
	$printer->lineFeed();
	}
	if(property_exists($permission, "show_delivery_address") && $permission->show_delivery_address == "true"){
	$printer->printInColumns($order->address);
	$printer->lineFeed();
	}
	if(property_exists($permission, "show_customer_phone") && $permission->show_customer_phone == "true"){
	$printer->printInColumns($order->user->phone);
	$printer->lineFeed();
	}
	$printer->setPrintModes(false, false, false);
	$printer->appendText("----------------------------------------------");
	$printer->lineFeed();

	$printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
	$printer->setupColumns(
		[360, SunmiCloudPrinter::ALIGN_LEFT,0],
		[0, SunmiCloudPrinter::ALIGN_RIGHT,0]
	);
	foreach($order->orderitems as $item){
		$printer->setPrintModes(true, true, false);
		$printer->printInColumns($item->quantity." x ".$item->name, $item->price);
		$printer->lineFeed();
		if(count($item->order_item_addons) > 0){
			foreach($item->order_item_addons as $addons){
				$printer->setPrintModes(false, false, false);
				$printer->printInColumns($addons->addon_name, $addons->addon_price);
				$printer->lineFeed();
			}
		}
		$printer->appendText("----------------------------------------------");
		$printer->lineFeed();
	}

	$printer->setPrintModes(false, false, false);
	$printer->printInColumns("SUB TOTAL", $order->sub_total);
	$printer->lineFeed();
	

	$printer->setPrintModes(false, false, false);
	$printer->printInColumns((property_exists($permission, "delivery_charge_label") ? $permission->delivery_charge_label : 'DELIVERY CHARGE' ), $order->delivery_charge);
	$printer->lineFeed();


	$printer->setPrintModes(false, false, false);
	$printer->printInColumns((property_exists($permission, "tax_label") ? $permission->tax_label : 'TAX CHARGE' ), $order->tax_amount);
	$printer->lineFeed();
	
	if($order->coupon_amount > 0){
		$printer->setPrintModes(false, false, false);
		$printer->printInColumns((property_exists($permission, "coupon_label") ? $permission->coupon_label."(".$order->coupon_name.")" : 'COUPON ('.$order->coupon_name.")" ), $order->coupon_amount);
		$printer->lineFeed();
	}
	
	if($order->restaurant_charge != ""){
		$printer->setPrintModes(false, false, false);
		$printer->printInColumns((property_exists($permission, "store_charge_label") ? $permission->store_charge_label : 'STORE CHARGE'), $order->restaurant_charge);
		$printer->lineFeed();
	}

	$printer->setPrintModes(true, true, true);
	$printer->printInColumns((property_exists($permission, "total_label") ? $permission->total_label : 'TOTAL' ), $order->total);
	$printer->lineFeed();
	
	
	if($order->order_comment != ""){
		$printer->setPrintModes(false, false, false);
		$printer->appendText("----------------------------------------------");
		$printer->lineFeed(2);
		$printer->setPrintModes(true, true, true);
		$printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
		$printer->appendText("ORDER COMMENTS:");
		$printer->lineFeed(2);
		$printer->setPrintModes(false, false, false);
		$printer->appendText($order->order_comment);
		$printer->lineFeed(2);
		$printer->setPrintModes(false, false, false);
		$printer->appendText("----------------------------------------------");
		$printer->lineFeed(2);
	}

	if($order->payment_mode != "COD"){
		$printer->setPrintModes(true, true, true);
		$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
		$printer->appendText("PAID");
		$printer->lineFeed(2);
	}

	$printer->setPrintModes(false, false, false);
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
	$printer->appendText("Placed : ".date("d-m-y",strtotime($order->created_at))." ".date("H:i:s",strtotime($order->created_at)));
	$printer->lineFeed();
	$printer->appendText("----------------------------------------------");
	$printer->lineFeed();
	$printer->setPrintModes(false, false, false);

	$printer->appendText($order->restaurant->address);
	$printer->lineFeed();

 
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
	$printer->setPrintModes(true, true, true);
	$printer->appendText($order->delivery_pin);
	$printer->lineFeed();


	$printer->lineFeed(10);
	$printer->cutPaper(false);

	$printer->pushContent($serial, sprintf("%s_%010d", $serial, time()));
	$printer->clear();
}

function KOTSample($order,$serial)
{
	$printer = new SunmiCloudPrinter(500);
	$printer->selectAsciiCharFont(50);
	$printer->setLineSpacing(10);
	$printer->setPrintModes(true, true, false);
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
	$printer->appendText("Order KOT Print\n");
	$printer->appendText("Order # ".$order->unique_order_id."\n");
	$printer->appendText("-----------------------------------------");
	$printer->lineFeed();
	
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
	$printer->setPrintModes(false, false, false);

	$printer->setupColumns(
		[160, SunmiCloudPrinter::ALIGN_LEFT, 0],
		[96, SunmiCloudPrinter::ALIGN_CENTER, 0],
		[0, SunmiCloudPrinter::ALIGN_RIGHT, 0]
	);
	$printer->printInColumns(date("d M Y",strtotime($order->created_at)), "",date("h:i a",strtotime($order->created_at)));
	$printer->appendText("------------------------------------------");
	$printer->lineFeed();

	// $printer->restoreDefaultLineSpacing();
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);

	$printer->setupColumns(
		[320, SunmiCloudPrinter::ALIGN_LEFT,SunmiCloudPrinter::COLUMN_FLAG_BW_REVERSE],
		[90, SunmiCloudPrinter::ALIGN_CENTER, SunmiCloudPrinter::COLUMN_FLAG_BW_REVERSE],
		[0, SunmiCloudPrinter::ALIGN_RIGHT,SunmiCloudPrinter::COLUMN_FLAG_BW_REVERSE]
	);
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
	$printer->setPrintModes(false, false, false);
	$printer->printInColumns("Description", "", "Qty");

	$printer->setupColumns(
		[320, SunmiCloudPrinter::ALIGN_LEFT,0],
		[90, SunmiCloudPrinter::ALIGN_CENTER,0],
		[0, SunmiCloudPrinter::ALIGN_RIGHT,0]
	);
	$printer->appendText("------------------------------------------");
	$printer->lineFeed();
	foreach($order->orderitems as $item){
		$printer->printInColumns($item->name, "", $item->quantity);
		$printer->lineFeed();
	}
	$printer->appendText("------------------------------------------");
	$printer->lineFeed();
	// $printer->restoreDefaultLineSpacing();
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
	$printer->setPrintModes(false, false, false);
	$printer->setupColumns(
		[130, SunmiCloudPrinter::ALIGN_LEFT,0],
		[0, SunmiCloudPrinter::ALIGN_RIGHT,0]
	);
	$printer->setLineSpacing(10);
	$printer->printInColumns("Order Type ", ($order->delivery_type == 1 ? 'Delivery' : 'Pickup'));
	$printer->lineFeed();
	$printer->printInColumns("Receipt # ", $order->unique_order_id);
	$printer->lineFeed();

	$printer->appendText("------------------------------------------");
	$printer->lineFeed();

	$printer->lineFeed(6);
	$printer->cutPaper(false);

	$printer->pushContent($serial, sprintf("%s_%010d", $serial, time()));
	$printer->clear();
}

function sendVoiceToPrinter($sn,$content,$link)
{
	$printer = new SunmiCloudPrinter();
	// $sn = "N411231A00721";
	$printer->pushVoice($sn,$content,$link);
}
?>