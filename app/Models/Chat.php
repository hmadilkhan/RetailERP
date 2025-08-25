<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
	protected $fillable = ['title'];

	public function messages(): HasMany
	{
		return $this->hasMany(ChatMessage::class)->orderBy('created_at');
	}
}
