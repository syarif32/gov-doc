<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // 1. Show the list of users and chats
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        $conversations = auth()->user()->conversations()->with('lastMessage')->get();

        return view('chat.index', compact('users', 'conversations'));
    }

    // 2. Start or find a conversation
    public function startConversation($userId)
    {
        $authId = auth()->id();

        // Find if a private conversation between these two already exists
        $conversation = Conversation::where('type', 'private')
            ->whereHas('users', function ($q) use ($authId) {
                $q->where('users.id', $authId);
            })
            ->whereHas('users', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create(['type' => 'private']);
            $conversation->users()->attach([$authId, $userId]);
        }

        return redirect()->route('chat.show', $conversation->id);
    }

    // 3. Show a specific conversation (Using the same index view)
    public function show(Conversation $conversation)
    {
        if (!$conversation->users()->where('users.id', auth()->id())->exists()) {
            abort(403);
        }

        $users = User::where('id', '!=', auth()->id())->get();
        $conversations = auth()->user()->conversations()->with('lastMessage')->get();
        $messages = $conversation->messages()->with('sender')->oldest()->get();

        // We return 'chat.index' even here!
        return view('chat.index', compact('users', 'conversations', 'conversation', 'messages'));
    }

    // 4. Send a message
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'body' => $request->body
        ]);

        return response()->json(['message' => $message]);
    }
}
