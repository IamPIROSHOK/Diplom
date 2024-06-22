<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Models\Message;

class ChatController extends Controller
{
    public function createChat(Request $request)
    {
        $chat = Chat::create(['user_id' => $request->user()->id]);
        return response()->json($chat);
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        $message = $chat->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    public function getMessages(Chat $chat)
    {
        return response()->json($chat->messages);
    }
}
