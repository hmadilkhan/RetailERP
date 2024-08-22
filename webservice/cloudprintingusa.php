<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require "printtest.php";

$order = $_POST['order'];
$serial = $_POST['serial'];
$text = $_POST['text'];
$mode = $_POST['mode'];
$logo = $_POST['logo'];
$order = json_decode($order);

if($serial != "")
{
	if($mode == "Receipt"){
		echo ReceiptFiver($serial,$order,$logo);
	}
}

function ReceiptFiver($serial,$order,$imageUrl)
{
	$printer = new SunmiCloudPrinter(540);
	$printer->selectAsciiCharFont(50);
	$printer->setLineSpacing(12);
	$printer->setPrintModes(true, true, false);
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
	$printer->appendImage($imageUrl, SunmiCloudPrinter::DIFFUSE_DITHER,356);
	$printer->lineFeed();
	
	$printer->setBlackWhiteReverseMode(0);
	$printer->setPrintModes(true, true, true);
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
	$printer->appendText($order->payload->order_id);
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
	
	if($order->payload->order_type_label == "Pickup"){
		$printer->printInColumns((property_exists($permission, "selfpickup_label") ? $permission->selfpickup_label : 'PICKUP' ),0);
	}else{
		$printer->printInColumns((property_exists($permission, "delivery_label") ? $permission->delivery_label : 'DELIVERY' ),0);
	}

	$printer->lineFeed();
	$printer->setupColumns(
		[500, SunmiCloudPrinter::ALIGN_LEFT,0]
	);
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
	$printer->setPrintModes(true, true, true);
	$printer->setUnderlineMode(10);
	$printer->setBlackWhiteReverseMode(0);
	
	$printer->printInColumns($order->payload->user->full_name);
	$printer->lineFeed();

	$printer->printInColumns($order->payload->delivery_address);
	$printer->lineFeed();
	
	$printer->printInColumns($order->payload->user->mobile);
	$printer->lineFeed();
	
	$printer->setPrintModes(false, false, false);
	$printer->appendText("----------------------------------------------");
	$printer->lineFeed();

	$printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
	$printer->setupColumns(
		[360, SunmiCloudPrinter::ALIGN_LEFT,0],
		[0, SunmiCloudPrinter::ALIGN_RIGHT,0]
	);
	foreach($order->payload->cart->cart_items as $item){
		$printer->setPrintModes(true, true, false);
		$printer->printInColumns($item->quantity." x ".$item->product_name, $item->product_price);
		$printer->lineFeed();
		// if(count($item->order_item_addons) > 0){
			// foreach($item->order_item_addons as $addons){
				// $printer->setPrintModes(false, false, false);
				// $printer->printInColumns($addons->addon_name, $addons->addon_price);
				// $printer->lineFeed();
			// }
		// }
		$printer->appendText("----------------------------------------------");
		$printer->lineFeed();
	}

	$printer->setPrintModes(false, false, false);
	$printer->printInColumns("SUB TOTAL", $order->payload->cart->sub_total_amount);
	$printer->lineFeed();
	

	$printer->setPrintModes(false, false, false);
	$printer->printInColumns('DELIVERY CHARGE', $order->payload->cart->delivery_fee_formatted );
	$printer->lineFeed();


	// $printer->setPrintModes(false, false, false);
	// $printer->printInColumns((property_exists($permission, "tax_label") ? $permission->tax_label : 'TAX CHARGE' ), $order->tax_amount);
	// $printer->lineFeed();
	
	// if($order->coupon_amount > 0){
		// $printer->setPrintModes(false, false, false);
		// $printer->printInColumns((property_exists($permission, "coupon_label") ? $permission->coupon_label."(".$order->coupon_name.")" : 'COUPON ('.$order->coupon_name.")" ), $order->coupon_amount);
		// $printer->lineFeed();
	// }
	
	// if($order->restaurant_charge != ""){
		// $printer->setPrintModes(false, false, false);
		// $printer->printInColumns((property_exists($permission, "store_charge_label") ? $permission->store_charge_label : 'STORE CHARGE'), $order->restaurant_charge);
		// $printer->lineFeed();
	// }

	$printer->setPrintModes(true, true, true);
	$printer->printInColumns('TOTAL', $order->payload->order_amount);
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

	if($order->payload->payment_mode->payment_mode->name != "cod"){
		$printer->setPrintModes(true, true, true);
		$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
		$printer->appendText("PAID");
		$printer->lineFeed(2);
	}

	$printer->setPrintModes(false, false, false);
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
	$printer->appendText("Placed : ".date("d-m-y",strtotime($order->payload->local_created_at))." ".date("H:i:s",strtotime($order->payload->local_created_at)));
	$printer->lineFeed();
	$printer->appendText("----------------------------------------------");
	$printer->lineFeed();
	$printer->setPrintModes(false, false, false);

	$printer->appendText($order->payload->merchant->merchant_address_location);
	$printer->lineFeed();

 
	$printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
	$printer->setPrintModes(true, true, true);
	$printer->appendText($order->payload->order_id);
	$printer->lineFeed();


	$printer->lineFeed(10);
	$printer->cutPaper(false);

	$printer->pushContent($serial, sprintf("%s_%010d", $serial, time()));
	$printer->clear();
}