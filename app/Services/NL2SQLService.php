<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;

class NL2SQLService
{
	public function __construct(
		private readonly SchemaSnapshot $schemaSnapshot,
		private readonly OpenAIService $openAI,
		private readonly SQLGuard $sqlGuard,
	) {}

	public function handleUserMessage(Chat $chat, ChatMessage $message): array
	{
		$maxTokens = (int)config('nl2sql.max_tokens', 8000);
		$maxRows = (int)config('nl2sql.max_rows', 200);

		$schema = $this->schemaSnapshot->build($maxTokens);
		$history = $chat->messages()->latest()->take(10)->get()->reverse();
		$historyText = '';
		foreach ($history as $m) {
			$role = strtoupper($m->role);
			$historyText .= "{$role}: {$m->content}\n";
		}

		$prompt = <<<PROMPT
You are to produce ONE safe, read-only SQL statement for MySQL.
Constraints:
- Allowed verbs: SELECT, SHOW, DESCRIBE, EXPLAIN.
- No mutations, no multiple statements, no dangerous functions, no comments.
- Prefer simple joins and explicit columns. If aggregating, alias columns clearly.
- If no LIMIT is provided, add LIMIT {$maxRows}.

Database schema (compact):
{$schema}

Chat context:
{$historyText}

User request:
{$message->content}

Return only SQL. No markdown, no explanation.
PROMPT;

		try {
			$sql = $this->openAI->generateSql($prompt);
			$sql = $this->sqlGuard->sanitize($sql);

			$result = $this->executeWithTimeout($sql, 15);

			$message->sql = $sql;
			$message->result = $this->truncateResult($result, $maxRows);
			$message->save();

			return ['ok' => true, 'sql' => $sql, 'rows' => $message->result];
		} catch (\Throwable $e) {
			$message->sql = $message->sql ?? null;
			$message->error = $e->getMessage();
			$message->save();
			return ['ok' => false, 'error' => $e->getMessage(), 'sql' => $message->sql];
		}
	}

	private function executeWithTimeout(string $sql, int $seconds): array
	{
		$seconds = max(1, min(60, $seconds));
		$driver = config('database.default');
		if (in_array(config("database.connections.$driver.driver"), ['mysql','mariadb'], true)) {
			DB::statement('SET SESSION MAX_EXECUTION_TIME = ?', [$seconds * 1000]);
		}
		return DB::select($sql);
	}

	private function truncateResult(array $rows, int $maxRows): array
	{
		if (count($rows) <= $maxRows) return $rows;
		return array_slice($rows, 0, $maxRows);
	}
}
