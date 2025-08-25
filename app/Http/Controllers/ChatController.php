<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Services\NL2SQLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ChatController extends Controller
{
	public function index(Request $request)
	{
		$chatId = $request->query('chat');
		$current = $chatId ? Chat::with(['messages' => function($q){ $q->latest()->paginate(30); }])->find($chatId) : null;
		$chats = Chat::orderByDesc('created_at')->paginate(20);
		return view('chat.index', [
			'chats' => $chats,
			'current' => $current,
		]);
	}

	public function new(Request $request)
	{
		$chat = Chat::create(['title' => null]);
		return redirect()->route('chat.index', ['chat' => $chat->id]);
	}

	public function post(Request $request, NL2SQLService $service)
	{
		$data = $request->validate([
			'chat_id' => 'required|exists:chats,id',
			'content' => 'required|string|max:2000',
		]);
		$chat = Chat::findOrFail($data['chat_id']);

		$userMsg = new ChatMessage([
			'role' => 'user',
			'content' => $data['content'],
		]);
		$chat->messages()->save($userMsg);

		if (!$chat->title) {
			$chat->title = mb_substr($data['content'], 0, 80);
			$chat->save();
		}

		$assistant = new ChatMessage([
			'role' => 'assistant',
			'content' => 'Thinking…',
		]);
		$chat->messages()->save($assistant);

		$result = $service->handleUserMessage($chat, $assistant);

		if (!$result['ok']) {
			$assistant->content = 'Error: ' . $result['error'];
			$assistant->save();
		} else {
			$assistant->content = 'OK'; // content will be rendered from result in view
			$assistant->save();
		}

		return redirect()->route('chat.index', ['chat' => $chat->id])->with('status', 'updated');
	}
}
