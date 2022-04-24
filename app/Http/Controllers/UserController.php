<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function allUsers()
    {
        $authUser = auth()->user();
        $users = User::where('id', '!=', $authUser->id)->get();
        $users = $users->map(function ($user) {
            $user['target_chat'] = auth()->user()->id . '-' . $user->id;
            $user['recent_chat_me'] = false;
            $user['recent_chat'] = Chat::where('user_id', auth()->user()->id)->where('target_id', $user->id)->orWhere('user_id', $user->id)->where('target_id', auth()->user()->id)->orderBy('id', 'desc')->first();
            return $user;
        });
        return AuthController::customResponse(true, 'Success', $users);
    }

    public function searchUsers(Request $request)
    {
        $req = json_decode($request->getContent(), false);
        $authUser = auth()->user();
        $users = User::where('id', '!=', $authUser->id)->where('name', 'like', '%' . $req->name . '%')->get();
        $users = $users->map(function ($user) {
            $user['target_chat'] = auth()->user()->id . '-' . $user->id;
            $user['recent_chat_me'] = false;
            $user['recent_chat'] = Chat::where('user_id', auth()->user()->id)->where('target_id', $user->id)->orWhere('user_id', $user->id)->where('target_id', auth()->user()->id)->orderBy('id', 'desc')->first();
            return $user;
        });
        return AuthController::customResponse(true, 'Success', $users);
    }

    public function profile(Request $request)
    {
        return AuthController::customResponse(true, 'Success Get User', $request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $user->update($request->all());
        return AuthController::customResponse(true, 'Success Update User', $user);
    }

    public function getContacts()
    {
        $authUser = auth()->user();
        $allChatMe = Chat::pluck('user_id')->all();
        $allChatYou = Chat::pluck('target_id')->all();
        $users = User::whereIn('id', $allChatMe)->orWhereIn('id', $allChatYou)->get();
        $users = $users->map(function ($user) {
            $chat = Chat::select('*')->selectRaw("DATE_FORMAT(created_at, '%H:%i') as time_parse")->where('user_id', auth()->user()->id)->where('target_id', $user->id)->orWhere('user_id', $user->id)->where('target_id', auth()->user()->id)->orderBy('id', 'desc')->first();
            $chat_status = $chat ?? ['user_id' => auth()->user()->id];
            $user['target_chat'] = $user->id . '-' . auth()->user()->id;
            $user['recent_chat_me'] = $chat_status['user_id'] == auth()->user()->id  ? true : false;
            $user['recent_chat'] = $chat;
            return $user;
        });
        $filtered_collection = $users->filter(function ($item) {
            return $item->recent_chat != null;
        })->values();
        return AuthController::customResponse(true, 'Success', $filtered_collection);
    }
}
