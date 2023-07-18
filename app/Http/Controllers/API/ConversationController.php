<?php

namespace App\Http\Controllers\API;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /************ Get all conversations with user ************/

    public function getConversations()
    {
        $conversations = Conversation::where("sender_id", Auth::id())
            ->orWhere("receiver_id", Auth::id())
            ->get();
        return $conversations;
    }

    /************ Get all conversations with user ************/

    /************ Get conversation with id ************/

    public function getConversation($id)
    {
        $conversation = Conversation::find($id);
        if (!$conversation) {
            $response = [
                "status" => false,
                "message" => "cant find conversation"
            ];
            return response($response, 400);
        }
        if ($conversation->sender_id != auth()->user()->id && $conversation->receiver != auth()->user()->id) {
            $response = [
                "status" => false,
                "message" => "dont have access to conversation"
            ];
            return response($response, 400);
        }
        return $conversation;
    }

    /************ Get conversation with id ************/

    /************ Create Conversation ************/

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required',
        ]);

        if ($validator->fails()) {
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => $validator->errors()
                ];
                return response()->json($response, 400);
            }
        }
        $receiver = User::find($request->receiver_id);
        if (!$receiver) {
            return response(["status" => false, "message" => 'cant find user'], 400);
        }
        $user = auth()->user();
        if ($receiver->id == $user->id) {
            return response(["status" => false, "message" => 'cant create conversation with yourself'], 400);
        }
        $conversation = Conversation::where('sender_id', $user->id)->where('receiver_id', $receiver->id)->first();
        if (!$conversation) {
            $input = [
                "sender_id" => $user->id,
                "receiver_id" => $request->receiver_id
            ];
            $conversations = Conversation::create($input);
            $response = [
                "status" => true,
                "message" => "conversation has been created succesfully",
                "conversation" => $conversations
            ];
            return response($response, 200);
        } else {
            $response = [
                "status" => false,
                "message" => "conversation already exist",
                "conversation" => $conversation
            ];
            return response($response, 400);
        }
    }

    /************ Create Conversation ************/

    /************ Send Message ************/

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required',
            'receiver_id' => 'required',
            'message' => 'required'
        ]);
        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $conversation = Conversation::find($request->conversation_id);
        if (!$conversation) {
            $response = [
                "status" => false,
                "message" => 'Чат не найден',
            ];
        }
        $receiver_id = $request->receiver_id;
        $receiver = User::find($receiver_id);
        $user_id = $request->user();
        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'user_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);
        broadcast(new MessageSent(Auth()->user(), $message, $conversation, $receiver));
        return $message;
    }

    /************ Send Message ************/
}
