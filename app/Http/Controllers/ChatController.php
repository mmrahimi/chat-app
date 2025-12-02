<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\CreateMessageRequest;
use App\Models\Message;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index()
    {
        return Inertia::render('Chat', [
            'messages' => Message::with('user')->latest()->take(40)->get()->reverse()->values(),
        ]);
    }

    public function store(CreateMessageRequest $request)
    {
        $message = $request->user()->messages()->create([
            'body' => $request->body,
        ]);

        broadcast(new MessageSent($message))->toOthers();
    }
}
