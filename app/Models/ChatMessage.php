<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
	protected $fillable = ['chat_id', 'role', 'content', 'sql', 'result', 'error'];

	protected $casts = [
		'result' => 'array',
	];

	public function chat(): BelongsTo
	{
		return $this->belongsTo(Chat::class);
	}
}
