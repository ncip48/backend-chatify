<?php

namespace App\Http\Controllers;

use App\Events\ChatSent;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getChats(Request $request)
    {
        $req = json_decode($request->getContent(), false);
        $authUser = auth()->user();
        $me = $authUser->id;
        $you = $req->target_user_id;
        $chats = Chat::where('user_id', $me)->where('target_id', $you)->orWhere('user_id', $you)->where('target_id', $me)->get();
        $chats = $chats->map(function ($chat) {
            $chat['status'] = $chat->user_id == auth()->user()->id ? 'sent' : 'received';
            return $chat;
        });
        return AuthController::customResponse(true, 'Success Get Chat', $chats);
    }

    public function sendChat(Request $request)
    {
        $req = json_decode($request->getContent(), false);
        $authUser = auth()->user();
        $me = $authUser->id;
        $you = $req->target_user_id;
        $chat = Chat::create([
            'user_id' => $me,
            'target_id' => $you,
            'message' => $req->message,
        ]);
        event(new ChatSent($req->message, $me, $you));
        return AuthController::customResponse(true, 'Success Send Chat', null);
    }

    public function readChat(Request $request)
    {
        $req = json_decode($request->getContent(), false);
        $authUser = auth()->user();
        $me = $authUser->id;
        $you = $req->target_user_id;
        $chats = Chat::where('user_id', $me)->where('target_id', $you)->orWhere('user_id', $you)->where('target_id', $me)->get();
        $chats = $chats->map(function ($chat) {
            $chat['status'] = $chat->user_id == auth()->user()->id ? 'sent' : 'received';
            return $chat;
        });
        $chats = $chats->map(function ($chat) {
            $chat['read'] = true;
            return $chat;
        });
        $chats->each(function ($chat) {
            $chat->save();
        });
        return AuthController::customResponse(true, 'Success Read Chat', null);
    }

    public function testBroadcast()
    {
        $event = event(new ChatSent('hello world', 1, 2));
        return AuthController::customResponse(true, 'Success Send Broadcast', $event);
    }
}
