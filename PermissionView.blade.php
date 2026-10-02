@extends('layouts.master')

@section('title')
    Permissions
@endsection
@section('content')
    <h2 class="intro-y text-lg font-medium mt-10">
       Permissions List
    </h2>
	<div class="intro-y col-span-12 overflow-auto lg:overflow-visible" id="terminalsTable">
	<form id="terminal-form" method="post" action="{{route('user-sale-permission-store')}}" enctype="multipart/form-data">
    @csrf
	<input type="hidden" name="terminal_id" value="{{$terminal_id}}"/>
	
	<!-- BEGIN: Modal Body -->
	<div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="opening" class="form-check-input" type="checkbox" name="ob" {{!empty($permissions) && $permissions->ob == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="opening">Opening Balance</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="closing" class="form-check-input" type="checkbox" name="cb" {{!empty($permissions) && $permissions->cb == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="closing">Closing Balance</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="manual_close" class="form-check-input" type="checkbox" name="manual_close" {{!empty($permissions) && $permissions->manual_close == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="manual_close">Manual Close Button</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="cash_sale" class="form-check-input" type="checkbox" name="cash_sale" {{!empty($permissions) && $permissions->cash_sale == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="cash_sale">Cash Sales</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="card_sale" class="form-check-input" type="checkbox" name="card_sale" {{!empty($permissions) && $permissions->card_sale == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="card_sale">Credit Card Sales</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="customer_credit_sale" class="form-check-input" type="checkbox" name="customer_credit_sale" {{!empty($permissions) && $permissions->customer_credit_sale == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="customer_credit_sale">Customer Credit Sales</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="wallets_sales" class="form-check-input" type="checkbox" name="wallets_sales" {{!empty($permissions) && $permissions->wallets_sales == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="wallets_sales">Wallet Sales</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="cost" class="form-check-input" type="checkbox" name="cost" {{!empty($permissions) && $permissions->cost == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="cost">Costing</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="r_cash" class="form-check-input" type="checkbox" name="r_cash" {{!empty($permissions) && $permissions->r_cash == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="r_cash">Customer Credit Return (Cash)</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="r_card" class="form-check-input" type="checkbox" name="r_card" {{!empty($permissions) && $permissions->r_card == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="r_card">Customer Credit Return (Credit Card)</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="r_cheque" class="form-check-input" type="checkbox" name="r_cheque" {{!empty($permissions) && $permissions->r_cheque == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="r_cheque">Customer Credit Return (Cheque)</label>
			</div>
		</div>
		
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="sale_return" class="form-check-input" type="checkbox" name="sale_return" {{!empty($permissions) && $permissions->sale_return == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="sale_return">Sale Return </label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="discount" class="form-check-input" type="checkbox" name="discount" {{!empty($permissions) && $permissions->discount == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="discount">Discount</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="cash_in" class="form-check-input" type="checkbox" name="cash_in" {{!empty($permissions) && $permissions->cash_in == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="cash_in">Cash In</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="cash_out" class="form-check-input" type="checkbox" name="cash_out" {{!empty($permissions) && $permissions->cash_out == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="cash_out">Cash Out</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="customer" class="form-check-input" type="checkbox" name="customer" {{!empty($permissions) && $permissions->customer == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="customer">Customer (Add Or Select)</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="delivery" class="form-check-input" type="checkbox" name="delivery" {{!empty($permissions) && $permissions->delivery == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="delivery">Delivery</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="service_provider" class="form-check-input" type="checkbox" name="service_provider" {{!empty($permissions) && $permissions->service_provider == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="service_provider">Service Provider</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="retail" class="form-check-input" type="checkbox" name="retail" {{!empty($permissions) && $permissions->retail == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="retail">Retail</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="wholesale" class="form-check-input" type="checkbox" name="wholesale" {{!empty($permissions) && $permissions->wholesale == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="wholesale">Wholesale</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="tables" class="form-check-input" type="checkbox" name="tables" {{!empty($permissions) && $permissions->tables == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="tables">Tables</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="token_print" class="form-check-input" type="checkbox" name="token_print" {{!empty($permissions) && $permissions->token_print == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="token_print">Token Print</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="prepayment" class="form-check-input" type="checkbox" name="prepayment" {{!empty($permissions) && $permissions->prepayment == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="prepayment">Estimate / PrePayment</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="goods" class="form-check-input" type="checkbox" name="goods" {{!empty($permissions) && $permissions->goods == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="goods">Goods Sales</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="item_delete_pos" class="form-check-input" type="checkbox" name="item_delete_pos" {{!empty($permissions) && $permissions->item_delete_pos == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="item_delete_pos">POS (Item Delete)</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="notificatons" class="form-check-input" type="checkbox" name="notifications" {{!empty($permissions) && $permissions->Notifications == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="notificatons">Notifications</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="Parent_Order_Print" class="form-check-input" type="checkbox" name="Parent_Order_Print" {{!empty($permissions) && $permissions->Parent_Order_Print == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="Parent_Order_Print">Parent Order Print</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="Hold_Bill_Table_Delete" class="form-check-input" type="checkbox" name="Hold_Bill_Table_Delete" {{!empty($permissions) && $permissions->Hold_Bill_Table_Delete == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="Hold_Bill_Table_Delete">Hold Bill Table Delete</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="thank_you_voice" class="form-check-input" type="checkbox" name="thank_you_voice" {{!empty($permissions) && $permissions->thank_you_voice == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="thank_you_voice">Thank You Voice</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="Colors" class="form-check-input" type="checkbox" name="Colors" {{!empty($permissions) && $permissions->Colors == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="Colors">Colors</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="stock_deduction" class="form-check-input" type="checkbox" name="stock_deduction" {{!empty($permissions) && $permissions->stock_deduction == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="stock_deduction">Stock Deduction</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="fbr_sync" class="form-check-input" type="checkbox" name="fbr_sync" {{!empty($permissions) && $permissions->fbr_sync == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="fbr_sync">FBR Sync</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="srb_sync" class="form-check-input" type="checkbox" name="srb_sync" {{!empty($permissions) && $permissions->srb_sync == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="srb_sync">SRB Sync</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="vat" class="form-check-input" type="checkbox" name="vat" {{!empty($permissions) && $permissions->vat == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="vat">VAT Sync</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="terminal_auto_sync" class="form-check-input" type="checkbox" name="terminal_auto_sync" {{!empty($permissions) && $permissions->terminal_auto_sync == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="terminal_auto_sync">Terminal Auto Sync</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="timer_terminal_auto_sync" class="form-check-input" type="checkbox" name="timer_terminal_auto_sync" {{!empty($permissions) && $permissions->timer_terminal_auto_sync == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="timer_terminal_auto_sync">Timer Terminal Auto Sync</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="print_to_server" class="form-check-input" type="checkbox" name="print_to_server" {{!empty($permissions) && $permissions->print_to_server == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="print_to_server">Print to Server</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="online_sales" class="form-check-input" type="checkbox" name="online_sales" {{!empty($permissions) && $permissions->online_sales == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="online_sales">Online Sales</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="order_booking" class="form-check-input" type="checkbox" name="order_booking" {{!empty($permissions) && $permissions->order_booking == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="order_booking">Order Booking</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="void_receipt" class="form-check-input" type="checkbox" name="void_receipt" {{!empty($permissions) && $permissions->void_receipt == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="void_receipt">Void Receipt</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="update_item_name" class="form-check-input" type="checkbox" name="update_item_name" {{!empty($permissions) && $permissions->update_item_name == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="update_item_name">Update Item Name</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="Note" class="form-check-input" type="checkbox" name="Note" {{!empty($permissions) && $permissions->Note == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="Note">Note</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="expenses" class="form-check-input" type="checkbox" name="expenses" {{!empty($permissions) && $permissions->expenses == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="expenses">Expense</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="digital_scale_code_extract" class="form-check-input" type="checkbox" name="digital_scale_code_extract" {{!empty($permissions) && $permissions->digital_scale_code_extract == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="digital_scale_code_extract">Sunmi Barcode Scanner</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="show_stock_in_pos" class="form-check-input" type="checkbox" name="show_stock_in_pos" {{!empty($permissions) && $permissions->show_stock_in_pos == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="show_stock_in_pos">Show Stock In POS</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="create_inventory" class="form-check-input" type="checkbox" name="create_inventory" {{!empty($permissions) && $permissions->create_inventory == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="create_inventory">Create Inventory</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="label_printing" class="form-check-input" type="checkbox" name="label_printing" {{!empty($permissions) && $permissions->label_printing == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="label_printing">Label Printing</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="cost_price_with_purchase" class="form-check-input" type="checkbox" name="cost_price_with_purchase" {{!empty($permissions) && $permissions->cost_price_with_purchase == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="cost_price_with_purchase">Cost Price With Purchase</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="no_receving_just_printing" class="form-check-input" type="checkbox" name="no_receving_just_printing" {{!empty($permissions) && $permissions->no_receving_just_printing == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="no_receving_just_printing">No Receiving Just Print</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="delete_local_db" class="form-check-input" type="checkbox" name="delete_local_db" {{!empty($permissions) && $permissions->delete_local_db == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="delete_local_db">Delete Local DB (On Declaration)</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="closing_raw_inventory" class="form-check-input" type="checkbox" name="closing_raw_inventory" {{!empty($permissions) && $permissions->closing_raw_inventory == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="closing_raw_inventory">Closing Raw Inventory</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="open_table_system" class="form-check-input" type="checkbox" name="open_table_system" {{!empty($permissions) && $permissions->open_table_system == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="open_table_system">Open Table System</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="limited_table_system" class="form-check-input" type="checkbox" name="limited_table_system" {{!empty($permissions) && $permissions->limited_table_system == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="limited_table_system">Limited Table System</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="customer_measurement" class="form-check-input" type="checkbox" name="customer_measurement" {{!empty($permissions) && $permissions->customer_measurement == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="customer_measurement">Customer Measurement </label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-group">
				<label class="form-check-label" for="Item_Listing_Count">Item Listing</label>
				<input id="Item_Listing_Count" class="form-control" type="text" name="Item_Listing_Count" value="{{!empty($permissions) && $permissions->Item_Listing_Count != "" ?  $permissions->Item_Listing_Count : ''}}">
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-group">
				<label class="form-check-label" for="timer_terminal_auto_sync_value">Timer Terminal Sync Value </label>
				<input id="timer_terminal_auto_sync_value" class="form-control" type="text" name="timer_terminal_auto_sync_value" value="{{!empty($permissions) && $permissions->timer_terminal_auto_sync_value != "" ?  $permissions->timer_terminal_auto_sync_value : ''}}">
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-group">
				<label class="form-check-label" for="discount_percentage">Discount Percentage</label>
				<input id="discount_percentage" class="form-control" type="text" name="discount_percentage" value="{{!empty($permissions) && $permissions->discount_percentage != "" ?  $permissions->discount_percentage : ''}}">
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-group">
				<label class="form-check-label" for="discount_amount">Discount Amount </label>
				<input id="discount_amount" class="form-control" type="text" name="discount_amount" value="{{!empty($permissions) && $permissions->discount_amount != "" ?  $permissions->discount_amount : ''}}">
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3" id="online_timer_div">
			<div class="form-group">
				<label class="form-check-label" for="discount_amount">Online Timer </label>
				<input id="online_timer" class="form-control" type="text" name="online_timer" value="{{!empty($permissions) && $permissions->online_timer != "" ?  $permissions->online_timer : ''}}">
			</div>
		</div>
		
		</div>
		
		<h2 class="intro-y text-lg font-medium mt-10">Layouts</h2>
		<hr/>
		<div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="retail_layout" class="form-check-input" type="checkbox" name="retail_layout" {{!empty($permissions) && $permissions->retail_layout == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="retail_layout">Retail</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="restaurant_layout" class="form-check-input" type="checkbox" name="restaurant_layout" {{!empty($permissions) && $permissions->restaurant_layout == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="restaurant_layout">Restaurant</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="pharmacy_layout" class="form-check-input" type="checkbox" name="pharmacy_layout" {{!empty($permissions) && $permissions->pharmacy_layout == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="pharmacy_layout">Pharmacy Layout</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="snowhite_retail" class="form-check-input" type="checkbox" name="snowhite_retail" {{!empty($permissions) && $permissions->snowhite_retail == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="snowhite_retail">Snowhite Layout</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="pizzabrry_layout" class="form-check-input" type="checkbox" name="pizzabrry_layout" {{!empty($permissions) && $permissions->pizzabrry_layout == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="pizzabrry_layout">Pizza Layout</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="sindh_food_authority_layout" class="form-check-input" type="checkbox" name="sindh_food_authority_layout" {{!empty($permissions) && $permissions->sindh_food_authority_layout == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="sindh_food_authority_layout">Sindh Food Authority Layout</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="order_calling" class="form-check-input" type="checkbox" name="order_calling" {{!empty($permissions) && $permissions->order_calling == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="order_calling">Order Calling</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="order_calling_display" class="form-check-input" type="checkbox" name="order_calling_display" {{!empty($permissions) && $permissions->order_calling_display == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="order_calling_display">Order Calling Display</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="saloon" class="form-check-input" type="checkbox" name="saloon" {{!empty($permissions) && $permissions->saloon == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="saloon">Saloon</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="deliveryboy_layout" class="form-check-input" type="checkbox" name="deliveryboy_layout" {{!empty($permissions) && $permissions->deliveryboy_layout == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="deliveryboy_layout">DeliveryBoy App</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="hotel_layout" class="form-check-input" type="checkbox" name="hotel_layout" {{!empty($permissions) && $permissions->hotel_layout == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="hotel_layout">Hotel Layout</label>
			</div>
		</div>
		
		</div>
		
		
		<h2 class="intro-y text-lg font-medium mt-10">Receipt Printing</h2>
		<hr/>
		<div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="print_receipt" class="form-check-input text-center" type="text" name="print_receipt" value="{{!empty($permissions) && $permissions->print_receipt > 0 ?  $permissions->print_receipt : 0}}">
				<label class="form-check-label" for="print_receipt">Print Receipt</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="two_inch_printing" class="form-check-input" type="checkbox" name="two_inch_printing" {{!empty($permissions) && $permissions->two_inch_printing == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="two_inch_printing">Two Inch Printing</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="three_inch_printing" class="form-check-input" type="checkbox" name="three_inch_printing" {{!empty($permissions) && $permissions->three_inch_printing == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="three_inch_printing">Three Inch Printing</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="receipt_1" class="form-check-input" type="checkbox" name="receipt_1" {{!empty($permissions) && $permissions->receipt_1 == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="receipt_1">Receipt 1</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="receipt_2" class="form-check-input" type="checkbox" name="receipt_2" {{!empty($permissions) && $permissions->receipt_2 == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="receipt_2">Receipt 2</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="receipt_3" class="form-check-input" type="checkbox" name="receipt_3" {{!empty($permissions) && $permissions->receipt_3 == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="receipt_3">Receipt 3</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="receipt_4" class="form-check-input" type="checkbox" name="receipt_4" {{!empty($permissions) && $permissions->receipt_4 == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="receipt_4">Receipt 4</label>
			</div>
		</div>

		</div>
		
		<h2 class="intro-y text-lg font-medium mt-10">Report Printing</h2>
		<hr/>
		<div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="print_declaration_report" class="form-check-input text-center" type="text" name="print_declaration_report" value="{{!empty($permissions) && $permissions->print_declaration_report > 0 ?  $permissions->print_declaration_report : 0}}">
				<label class="form-check-label" for="print_declaration_report">Declaration Report</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="print_expense_report" class="form-check-input text-center" type="text" name="print_expense_report" value="{{!empty($permissions) && $permissions->print_expense_report > 0 ?  $permissions->print_expense_report : 0}}">
				<label class="form-check-label" for="print_expense_report">Expense Report</label>
			</div>
		</div>
		</div>
		
		
		<h2 class="intro-y text-lg font-medium mt-10">Reports</h2>
		<hr/>
		<div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="declaration" class="form-check-input" type="checkbox" name="declaration" {{!empty($permissions) && $permissions->declaration == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="declaration">Declaration</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="isdb" class="form-check-input" type="checkbox" name="isdb" {{!empty($permissions) && $permissions->isdb == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="isdb">ISDB</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="cio" class="form-check-input" type="checkbox" name="cio" {{!empty($permissions) && $permissions->cio == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="cio">CIO</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="two_inch_Reports" class="form-check-input" type="checkbox" name="two_inch_Reports" {{!empty($permissions) && $permissions->two_inch_Reports == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="two_inch_Reports">Two Inch Report</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="Email_Reports" class="form-check-input" type="checkbox" name="Email_Reports" {{!empty($permissions) && $permissions->Email_Reports == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="Email_Reports">Email_Reports</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="delivery_report" class="form-check-input" type="checkbox" name="delivery_report" {{!empty($permissions) && $permissions->delivery_report == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="delivery_report">Delivery Report</label>
			</div>
		</div>
		</div>
		
		<h2 class="intro-y text-lg font-medium mt-10">Token Print</h2>
		<hr/>
		<div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="print_article_code" class="form-check-input" type="checkbox" name="print_article_code" {{!empty($permissions) && $permissions->print_article_code == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="print_article_code">Article Code</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="Department_Token_Print" class="form-check-input" type="checkbox" name="Department_Token_Print" {{!empty($permissions) && $permissions->Department_Token_Print == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="Department_Token_Print">Department</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="print_name_on_token" class="form-check-input" type="checkbox" name="print_name_on_token" {{!empty($permissions) && $permissions->print_name_on_token == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="print_name_on_token">Print Name On Token</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="Token_3Inch" class="form-check-input" type="checkbox" name="Token_3Inch" {{!empty($permissions) && $permissions->Token_3Inch == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="Token_3Inch">Token Three Inch</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="Token_2Inch" class="form-check-input" type="checkbox" name="Token_2Inch" {{!empty($permissions) && $permissions->Token_2Inch == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="Token_2Inch">Token Two Inch</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="secondary_token_print" class="form-check-input" type="checkbox" name="secondary_token_print" {{!empty($permissions) && $permissions->secondary_token_print == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="secondary_token_print">Secondary Token Print</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="token_timer" class="form-check-input" type="checkbox" name="token_timer" {{!empty($permissions) && $permissions->token_timer == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="token_timer">Token Timer</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="kot_p_q_t" class="form-check-input" type="checkbox" name="kot_p_q_t" {{!empty($permissions) && $permissions->kot_p_q_t == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="kot_p_q_t">KOT - Price/Qty/Total</label>
			</div>
		</div>
		
		</div>
		
		<h2 class="intro-y text-lg font-medium mt-10">Customer Display</h2>
		<hr/>
		<div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="Customer_Display" class="form-check-input" type="checkbox" name="Customer_Display" {{!empty($permissions) && $permissions->Customer_Display == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="Customer_Display">Customer Display</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="customer_display_video" class="form-check-input" type="checkbox" name="customer_display_video" {{!empty($permissions) && $permissions->customer_display_video == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="customer_display_video">Customer Display Video</label>
			</div>
		</div>
		
		<div class="col-span-12 sm:col-span-3">
			<div class="form-check form-switch">
				<input id="Customer_Display_BG" class="form-check-input" type="checkbox" name="Customer_Display_BG" {{!empty($permissions) && $permissions->Customer_Display_BG == 1 ?  'checked' : ''}}>
				<label class="form-check-label" for="Customer_Display_BG">Customer Display BG</label>
			</div>
		</div>
		<div class="col-span-12 sm:col-span-3">
			<div class="form-group ">
				<label class="form-label" for="video_name">Video Name</label>
				<input id="video_name" class="form-control" type="text" name="video_name" value="{{!empty($permissions) ? $permissions->video_name  : ''}}">
			</div>
		</div>
		</div>
		
		<h2 class="intro-y text-lg font-medium mt-10">POS Backup</h2>
		<hr/>
		<div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
			<div class="col-span-12 sm:col-span-3">
				<div class="form-check form-switch">
					<input id="backup_every_receipt" class="form-check-input" type="checkbox" name="backup_every_receipt" {{!empty($permissions) && $permissions->backup_every_receipt == 1 ?  'checked' : ''}}>
					<label class="form-check-label" for="backup_every_receipt">Make full backup on every receipt</label>
				</div>
			</div>
			<div class="col-span-12 sm:col-span-3">
				<div class="form-check form-switch">
					<input id="backup_on_declaration" class="form-check-input" type="checkbox" name="backup_on_declaration" {{!empty($permissions) && $permissions->backup_on_declaration == 1 ?  'checked' : ''}}>
					<label class="form-check-label" for="backup_on_declaration">Make full backup on declaration</label>
				</div>
			</div>
		</div>
		
		<h2 class="intro-y text-lg font-medium mt-10">SMS</h2>
		<hr/>
		
		<div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
			<div class="col-span-12 sm:col-span-3">
				<div class="form-check form-switch">
					<input id="sms_booking" class="form-check-input" type="checkbox" name="sms_booking" {{!empty($permissions) && $permissions->sms_booking == 1 ?  'checked' : ''}}>
					<label class="form-check-label" for="sms_booking">Booking SMS </label>
				</div>
			</div>
			<div class="col-span-12 sm:col-span-3">
				<div class="form-check form-switch">
					<input id="sms_delivery" class="form-check-input" type="checkbox" name="sms_delivery" {{!empty($permissions) && $permissions->sms_delivery == 1 ?  'checked' : ''}}>
					<label class="form-check-label" for="sms_delivery">Delivery SMS</label>
				</div>
			</div>
			<div class="col-span-12 sm:col-span-3">
				<div class="form-check form-switch">
					<input id="sms_takeaway" class="form-check-input" type="checkbox" name="sms_takeaway" {{!empty($permissions) && $permissions->sms_takeaway == 1 ?  'checked' : ''}}>
					<label class="form-check-label" for="sms_takeaway">Takeaway SMS </label>
				</div>
			</div>
			<div class="col-span-12 sm:col-span-3">
				<div class="form-check form-switch">
					<input id="sms_reminder" class="form-check-input" type="checkbox" name="sms_reminder" {{!empty($permissions) && $permissions->sms_reminder == 1 ?  'checked' : ''}}>
					<label class="form-check-label" for="sms_reminder">SMS Reminder</label>
				</div>
			</div>
			<div class="col-span-12 sm:col-span-3">
				<div class="form-check form-switch">
					<input id="whatsapp_msg" class="form-check-input" type="checkbox" name="whatsapp_msg" {{!empty($permissions) && $permissions->whatsapp_msg == 1 ?  'checked' : ''}}>
					<label class="form-check-label" for="whatsapp_msg">Whatsapp Msg</label>
				</div>
			</div>
		</div>
		
	<!-- END: Modal Body -->
	<!-- BEGIN: Modal Footer -->
	<div class="modal-footer">
		<button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
		<button type="submit" class="btn btn-primary w-20">Submit</button>
	</div>
	<!-- END: Modal Footer -->
</form>


	</div>

@endsection
@section("scripts")
<script>
$("online_sales").click(function(){
	if(this.checked) {
		alert();
        // Iterate each checkbox
        $(':checkbox').each(function() {
            this.checked = true;                        
        });
    } else {
        $(':checkbox').each(function() {
            this.checked = false;                       
        });
    }
})
</script>		
@endsection
