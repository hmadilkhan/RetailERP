<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSalePermission;
use App\Models\PushNotification;

class UserSalePermissionController extends Controller
{
	public function index(Request $request)
	{
		return view("user-sale-permission.index",[
			"permissions" => UserSalePermission::where("terminal_id",$request->id)->first(),
			"terminal_id" => $request->id,
		]);
	}
	
	public function store(Request $request)
	{
		$item = [
			"user_id" => auth()->user()->id,
			"terminal_id" => $request->terminal_id,
			"ob" => (!empty($request->ob) && $request->ob == "on" ? 1 : 0) ,
			"cb" => (!empty($request->cb) && $request->cb == "on" ? 1 : 0) ,
			"manual_close" => (!empty($request->manual_close) && $request->manual_close == "on" ? 1 : 0) ,
			"cash_sale" => (!empty($request->cash_sale) && $request->cash_sale == "on" ? 1 : 0) ,
			"card_sale" => (!empty($request->card_sale) && $request->card_sale == "on" ? 1 : 0) ,
			"wallets_sales" => (!empty($request->wallets_sales) && $request->wallets_sales == "on" ? 1 : 0) ,
			"customer_credit_sale" => (!empty($request->customer_credit_sale) && $request->customer_credit_sale == "on" ? 1 : 0) ,
			"cost" => (!empty($request->cost) && $request->cost == "on" ? 1 : 0) ,
			"r_cash" => (!empty($request->r_cash) && $request->r_cash == "on" ? 1 : 0) ,
			"r_card" => (!empty($request->r_card) && $request->r_card == "on" ? 1 : 0) ,
			"r_cheque" => (!empty($request->r_cheque) && $request->r_cheque == "on" ? 1 : 0) ,
			"sale_return" => (!empty($request->sale_return) && $request->sale_return == "on" ? 1 : 0) ,
			"discount" => (!empty($request->discount) && $request->discount == "on" ? 1 : 0) ,
			"cash_in" => (!empty($request->cash_in) && $request->cash_in == "on" ? 1 : 0) ,
			"cash_out" => (!empty($request->cash_out) && $request->cash_out == "on" ? 1 : 0) ,
			"customer" => (!empty($request->customer) && $request->customer == "on" ? 1 : 0) ,
			"delivery" => (!empty($request->delivery) && $request->delivery == "on" ? 1 : 0) ,
			"service_provider" => (!empty($request->service_provider) && $request->service_provider == "on" ? 1 : 0) ,
			"retail" => (!empty($request->retail) && $request->retail == "on" ? 1 : 0) ,
			"wholesale" => (!empty($request->wholesale) && $request->wholesale == "on" ? 1 : 0) ,
			"tables" => (!empty($request->tables) && $request->tables == "on" ? 1 : 0) ,
			"prepayment" => (!empty($request->prepayment) && $request->prepayment == "on" ? 1 : 0) ,
			"token_print" => (!empty($request->token_print) && $request->token_print == "on" ? 1 : 0) ,
			"goods" => (!empty($request->goods) && $request->goods == "on" ? 1 : 0) ,
			"item_delete_pos" => (!empty($request->item_delete_pos) && $request->item_delete_pos == "on" ? 1 : 0) ,
			"receipt_1" => (!empty($request->receipt_1) && $request->receipt_1 == "on" ? 1 : 0) ,
			"receipt_2" => (!empty($request->receipt_2) && $request->receipt_2 == "on" ? 1 : 0) ,
			"receipt_3" => (!empty($request->receipt_3) && $request->receipt_3 == "on" ? 1 : 0) ,
			"receipt_4" => (!empty($request->receipt_4) && $request->receipt_4 == "on" ? 1 : 0) ,
			"terminal_auto_sync" => (!empty($request->terminal_auto_sync) && $request->terminal_auto_sync == "on" ? 1 : 0) ,
			"fbr_sync" => (!empty($request->fbr_sync) && $request->fbr_sync == "on" ? 1 : 0) ,
			"srb_sync" => (!empty($request->srb_sync) && $request->srb_sync == "on" ? 1 : 0) ,
			"stock_deduction" => (!empty($request->stock_deduction) && $request->stock_deduction == "on" ? 1 : 0) ,
			"declaration" => (!empty($request->declaration) && $request->declaration == "on" ? 1 : 0) ,
			"isdb" => (!empty($request->isdb) && $request->isdb == "on" ? 1 : 0) ,
			"cio" => (!empty($request->cio) && $request->cio == "on" ? 1 : 0) ,
			"retail_layout" => (!empty($request->retail_layout) && $request->retail_layout == "on" ? 1 : 0) ,
			"restaurant_layout" => (!empty($request->restaurant_layout) && $request->restaurant_layout == "on" ? 1 : 0) ,
			"print_receipt" => (!empty($request->print_receipt) && $request->print_receipt > 0 ? $request->print_receipt : 0) ,
			"Colors" => (!empty($request->Colors) && $request->Colors == "on" ? 1 : 0) ,
			"print_to_server" => (!empty($request->print_to_server) && $request->print_to_server == "on" ? 1 : 0) ,
			"print_article_code" => (!empty($request->print_article_code) && $request->print_article_code == "on" ? 1 : 0) ,
			"update_item_name" => (!empty($request->update_item_name) && $request->update_item_name == "on" ? 1 : 0) ,
			"three_inch_printing" => (!empty($request->three_inch_printing) && $request->three_inch_printing == "on" ? 1 : 0) ,
			"two_inch_printing" => (!empty($request->two_inch_printing) && $request->two_inch_printing == "on" ? 1 : 0) ,
			"two_inch_Reports" => (!empty($request->two_inch_Reports) && $request->two_inch_Reports == "on" ? 1 : 0) ,
			"Department_Token_Print" => (!empty($request->Department_Token_Print) && $request->Department_Token_Print == "on" ? 1 : 0) ,
			"thank_you_voice" => (!empty($request->thank_you_voice) && $request->thank_you_voice == "on" ? 1 : 0) ,
			"print_name_on_token" => (!empty($request->print_name_on_token) && $request->print_name_on_token == "on" ? 1 : 0) ,
			"Hold_Bill_Table_Delete" => (!empty($request->Hold_Bill_Table_Delete) && $request->Hold_Bill_Table_Delete == "on" ? 1 : 0) ,
			"Customer_Display" => (!empty($request->Customer_Display) && $request->Customer_Display == "on" ? 1 : 0) ,
			"Customer_Display_BG" => (!empty($request->Customer_Display_BG) && $request->Customer_Display_BG == "on" ? 1 : 0) ,
			"Item_Listing_Count" => (!empty($request->Item_Listing_Count) && $request->Item_Listing_Count != "" ? $request->Item_Listing_Count : "") ,
			"Notifications" => (!empty($request->notifications) && $request->notifications == "on" ? 1 : 0) ,
			"Parent_Order_Print" => (!empty($request->Parent_Order_Print) && $request->Parent_Order_Print == "on" ? 1 : 0) ,
			"Note" => (!empty($request->Note) && $request->Note == "on" ? 1 : 0) ,
			"Email_Reports" => (!empty($request->Email_Reports) && $request->Email_Reports == "on" ? 1 : 0) ,
			"Token_3Inch" => (!empty($request->Token_3Inch) && $request->Token_3Inch == "on" ? 1 : 0) ,
			"Token_2Inch" => (!empty($request->Token_2Inch) && $request->Token_2Inch == "on" ? 1 : 0) ,
			"secondary_token_print" => (!empty($request->secondary_token_print) && $request->secondary_token_print == "on" ? 1 : 0) ,
			"customer_display_video" => (!empty($request->customer_display_video) && $request->customer_display_video == "on" ? 1 : 0) ,
			"video_name" => (!empty($request->video_name)  ? $request->video_name : '') ,
			"token_timer" => (!empty($request->token_timer) && $request->token_timer == "on" ? 1 : 0) ,
			"expenses" => (!empty($request->expenses) && $request->expenses == "on" ? 1 : 0) ,
			"backup_every_receipt" => (!empty($request->backup_every_receipt) && $request->backup_every_receipt == "on" ? 1 : 0) ,
			"backup_on_declaration" => (!empty($request->backup_on_declaration) && $request->backup_on_declaration == "on" ? 1 : 0) ,
			"online_sales" => (!empty($request->online_sales) && $request->online_sales == "on" ? 1 : 0) ,
			"order_booking" => (!empty($request->order_booking) && $request->order_booking == "on" ? 1 : 0) ,
			"void_receipt" => (!empty($request->void_receipt) && $request->void_receipt == "on" ? 1 : 0) ,
			"sms_booking" => (!empty($request->sms_booking) && $request->sms_booking == "on" ? 1 : 0) ,
			"sms_delivery" => (!empty($request->sms_delivery) && $request->sms_delivery == "on" ? 1 : 0) ,
			"pharmacy_layout" => (!empty($request->pharmacy_layout) && $request->pharmacy_layout == "on" ? 1 : 0) ,
			"snowhite_retail" => (!empty($request->snowhite_retail) && $request->snowhite_retail == "on" ? 1 : 0) ,
			"digital_scale_code_extract" => (!empty($request->digital_scale_code_extract) && $request->digital_scale_code_extract == "on" ? 1 : 0) ,
			"show_stock_in_pos" => (!empty($request->show_stock_in_pos) && $request->show_stock_in_pos == "on" ? 1 : 0) ,
			"sms_takeaway" => (!empty($request->sms_takeaway) && $request->sms_takeaway == "on" ? 1 : 0) ,
			"sms_reminder" => (!empty($request->sms_reminder) && $request->sms_reminder == "on" ? 1 : 0) ,
			"create_inventory" => (!empty($request->create_inventory) && $request->create_inventory == "on" ? 1 : 0),
			"label_printing" => (!empty($request->label_printing) && $request->label_printing == "on" ? 1 : 0) ,
			"whatsapp_msg" => (!empty($request->whatsapp_msg) && $request->whatsapp_msg == "on" ? 1 : 0) ,
			"cost_price_with_purchase" => (!empty($request->cost_price_with_purchase) && $request->cost_price_with_purchase == "on" ? 1 : 0) ,
			"no_receving_just_printing" => (!empty($request->no_receving_just_printing ) && $request->no_receving_just_printing  == "on" ? 1 : 0) ,
			"kot_p_q_t" => (!empty($request->kot_p_q_t) && $request->kot_p_q_t == "on" ? 1 : 0) ,
			"vat" => (!empty($request->vat) && $request->vat == "on" ? 1 : 0) ,
			"timer_terminal_auto_sync" => (!empty($request->timer_terminal_auto_sync) && $request->timer_terminal_auto_sync == "on" ? 1 : 0) ,
			"timer_terminal_auto_sync_value" => (!empty($request->timer_terminal_auto_sync_value) && $request->timer_terminal_auto_sync_value != "" ? $request->timer_terminal_auto_sync_value : "") ,
			"discount_percentage" => (!empty($request->discount_percentage) && $request->discount_percentage != "" ? $request->discount_percentage : "") ,
			"discount_amount" => (!empty($request->discount_amount) && $request->discount_amount != "" ? $request->discount_amount : "") ,
			"online_timer" => (!empty($request->online_timer) && $request->online_timer != "" ? $request->online_timer : "") ,
			"delete_local_db" => (!empty($request->delete_local_db) && $request->delete_local_db == "on" ? 1 : 0) ,
			"order_calling" => (!empty($request->order_calling) && $request->order_calling == "on" ? 1 : 0) ,
			"order_calling_display" => (!empty($request->order_calling_display) && $request->order_calling_display == "on" ? 1 : 0) ,
			"saloon" => (!empty($request->saloon) && $request->saloon == "on" ? 1 : 0) ,
			"closing_raw_inventory" => (!empty($request->closing_raw_inventory) && $request->closing_raw_inventory == "on" ? 1 : 0) ,
			"open_table_system" => (!empty($request->open_table_system) && $request->open_table_system == "on" ? 1 : 0) ,
			"limited_table_system" => (!empty($request->limited_table_system) && $request->limited_table_system == "on" ? 1 : 0) ,
			"deliveryboy_layout" => (!empty($request->deliveryboy_layout) && $request->deliveryboy_layout == "on" ? 1 : 0) ,
			"customer_measurement" => (!empty($request->customer_measurement ) && $request->customer_measurement  == "on" ? 1 : 0) ,
			"delivery_report" => (!empty($request->delivery_report ) && $request->delivery_report  == "on" ? 1 : 0) ,
			"pizzabrry_layout" => (!empty($request->pizzabrry_layout ) && $request->pizzabrry_layout  == "on" ? 1 : 0) ,
			"hotel_layout" => (!empty($request->hotel_layout ) && $request->hotel_layout  == "on" ? 1 : 0) ,
			"sindh_food_authority_layout" => (!empty($request->sindh_food_authority_layout ) && $request->sindh_food_authority_layout  == "on" ? 1 : 0),
			"print_declaration_report" => (!empty($request->print_declaration_report) && $request->print_declaration_report > 0 ? $request->print_declaration_report : 0) ,
			"print_expense_report" => (!empty($request->print_expense_report) && $request->print_expense_report > 0 ? $request->print_expense_report : 0) ,
		];
		
		$exists = UserSalePermission::where("terminal_id",$request->terminal_id)->first();
		if(empty($exists)){
			UserSalePermission::create($item);
		}else{
			UserSalePermission::where("permission_id",$exists->permission_id)->update($item);
		}
	    PushNotification::sendNotification($request->terminal_id,"Data will be here","Terminal Updated");
		return redirect()->route("user-sale-permission",$request->terminal_id);
	}
	
	public function test()
	{
		return PushNotification::sendNotification(144,"Data will be here","Terminal Updated");
	}
}