<?php

return [
	// Token and row limits used by NL2SQLService
	'max_tokens' => env('NL2SQL_MAX_TOKENS', 8000),
	'max_rows' => env('NL2SQL_MAX_ROWS', 200),

	// Column alias mappings to auto-correct common user/model outputs
	// Keys are table names (or '*' for global), values are [alias => actual_column]
	'column_aliases' => [
		'*' => [
			// common generic aliases across tables
			'id' => 'id',
		],
		'sales_receipts' => [
			'branch_id' => 'branch',
			'created_at' => 'date',
			'grandtotal' => 'grand_total',
			'total' => 'total_amount',
			'totalamount' => 'total_amount',
			'receiptno' => 'receipt_no',
		],
	],
]; 