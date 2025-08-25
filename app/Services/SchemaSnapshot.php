<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SchemaSnapshot
{
	public function build(int $maxTokens = 8000): string
	{
		$connection = config('database.default');
		$database = config("database.connections.$connection.database");
		$driver = config("database.connections.$connection.driver");

		if ($driver !== 'mysql' && $driver !== 'mariadb') {
			return "-- Schema snapshot only supports MySQL/MariaDB in this version.";
		}

		$tables = DB::select(
			"SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME",
			[$database]
		);

		$lines = [];
		foreach ($tables as $t) {
			$table = $t->TABLE_NAME;
			$cols = DB::select(
				"SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_TYPE, COLUMN_DEFAULT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION",
				[$database, $table]
			);
			$fkRows = DB::select(
				"SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL",
				[$database, $table]
			);
			$fks = [];
			foreach ($fkRows as $fk) {
				$fks[] = $fk->COLUMN_NAME . '->' . $fk->REFERENCED_TABLE_NAME . '(' . $fk->REFERENCED_COLUMN_NAME . ')';
			}

			$colParts = [];
			foreach ($cols as $c) {
				$part = $c->COLUMN_NAME . ' ' . $c->DATA_TYPE;
				if (str_contains($c->COLUMN_TYPE, '(')) {
					$part .= '[' . $c->COLUMN_TYPE . ']';
				}
				if ($c->IS_NULLABLE === 'NO') {
					$part .= ' NOT NULL';
				}
				if ($c->COLUMN_KEY === 'PRI') {
					$part .= ' PK';
				}
				$colParts[] = $part;
			}

			$line = $table . ': ' . implode(', ', $colParts);
			if (!empty($fks)) {
				$line .= ' | FKs: ' . implode(', ', $fks);
			}
			$lines[] = $line;
		}

		$schema = implode("\n", $lines);

		// crude token budget: assume ~4 chars per token
		$maxChars = max(1000, (int)floor($maxTokens * 4 * 0.6));
		if (strlen($schema) > $maxChars) {
			$schema = substr($schema, 0, $maxChars) . "\n-- truncated due to token budget";
		}
		return $schema;
	}
}
