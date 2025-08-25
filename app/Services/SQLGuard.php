<?php

namespace App\Services;

class SQLGuard
{
	private array $allowedVerbs = ['SELECT', 'SHOW', 'DESCRIBE', 'EXPLAIN'];
	private int $maxRows;

	public function __construct(int $maxRows = 200)
	{
		$this->maxRows = $maxRows;
	}

	public function sanitize(string $sql): string
	{
		$sql = trim($sql);
		$sql = $this->stripComments($sql);
		$sql = preg_replace('/;\s*$/', '', $sql);
		$this->assertSingleStatement($sql);
		$this->assertAllowedVerb($sql);
		$this->denyDangerous($sql);
		$sql = $this->enforceLimit($sql, $this->maxRows);
		return $sql;
	}

	private function stripComments(string $sql): string
	{
		$sql = preg_replace('#/\*.*?\*/#s', '', $sql);
		$sql = preg_replace('/--.*?(\n|$)/', '', $sql);
		$sql = preg_replace('/#.*/', '', $sql);
		return trim($sql);
	}

	private function assertSingleStatement(string $sql): void
	{
		if (preg_match('/;\s*[^\s]/', $sql)) {
			throw new \InvalidArgumentException('Multiple statements are not allowed.');
		}
	}

	private function assertAllowedVerb(string $sql): void
	{
		if (!preg_match('/^([A-Z]+)/i', ltrim($sql), $m)) {
			throw new \InvalidArgumentException('SQL must start with a verb.');
		}
		$verb = strtoupper($m[1]);
		if (!in_array($verb, $this->allowedVerbs, true)) {
			throw new \InvalidArgumentException('Only read-only queries are allowed.');
		}
	}

	private function denyDangerous(string $sql): void
	{
		$dangerPatterns = [
			'\bINTO\s+OUTFILE\b' => 'INTO OUTFILE',
			'\bINTO\s+DUMPFILE\b' => 'INTO DUMPFILE',
			'\bLOAD_FILE\s*\(' => 'LOAD_FILE()',
			'\bINFILE\b' => 'INFILE',
			'\bUNION\s+SELECT\b' => 'UNION SELECT',
			'\bCREATE\s+(TEMPORARY\s+)?TABLE\b' => 'CREATE TABLE',
			'\bDROP\s+(TEMPORARY\s+)?TABLE\b' => 'DROP TABLE',
			'\bUSE\s+[`"\w]+' => 'USE <database>',
			'\bSET\s+GLOBAL\b' => 'SET GLOBAL',
			'\bSET\s+SESSION\b' => 'SET SESSION',
			'\bINTO\s+TEMPORARY\s+TABLE\b' => 'INTO TEMPORARY TABLE',
			'\bUPDATE\b' => 'UPDATE',
			'\bDELETE\b' => 'DELETE',
			'\bINSERT\b' => 'INSERT',
			'\bREPLACE\b' => 'REPLACE',
			'\bALTER\b' => 'ALTER',
			'\bDROP\b' => 'DROP',
			'\bCREATE\b' => 'CREATE',
			'\bTRUNCATE\b' => 'TRUNCATE',
			'\bGRANT\b' => 'GRANT',
			'\bREVOKE\b' => 'REVOKE',
			'\bCALL\b' => 'CALL',
		];
		foreach ($dangerPatterns as $pattern => $label) {
			if (preg_match('/' . $pattern . '/i', $sql)) {
				throw new \InvalidArgumentException('Dangerous SQL construct detected: ' . $label . '.');
			}
		}
	}

	private function enforceLimit(string $sql, int $limit): string
	{
		if (!preg_match('/^\s*SELECT/i', $sql)) {
			return $sql;
		}
		if (preg_match('/\bLIMIT\s+\d+/i', $sql)) {
			return $sql;
		}
		return rtrim($sql) . ' LIMIT ' . $limit;
	}
}
