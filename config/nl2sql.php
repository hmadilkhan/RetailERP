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

	// Natural-language entity resolution (name -> id) rules
	// Each rule describes how to detect and resolve a named entity into its id
	'entities' => [
		[
			'entity' => 'branch',
			'table' => 'branch',
			'id_column' => 'branch_id',
			'name_columns' => ['branch_name'],
			// detect phrases like: branch Karachi, branch "Head Office"
			'detect_regex' => '/\bbranch\s+\"?([A-Za-z0-9 _\-\.]+)\"?/i',
			// where to use the id in target tables
			'target_tables' => [
				'sales_receipts' => 'branch',
			],
			'join_hint' => 'JOIN branch ON branch.branch_id = sales_receipts.branch AND branch.branch_name LIKE CONCAT("%", :resolved_name, "%")',
		],
		[
			'entity' => 'customer',
			'table' => 'customers',
			'id_column' => 'id',
			'name_columns' => ['name'],
			'detect_regex' => '/\bcustomer\s+\"?([A-Za-z0-9 _\-\.]+)\"?/i',
			'target_tables' => [
				'sales_receipts' => 'customer_id',
			],
			'join_hint' => 'JOIN customers ON customers.id = sales_receipts.customer_id AND customers.name LIKE CONCAT("%", :resolved_name, "%")',
		],
	],
]; 