<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Pusher\Pusher;

class PusherAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        // Replace with your Pusher app credentials
        $pusherKey = env('PUSHER_APP_KEY');
        $pusherSecret = env('PUSHER_APP_SECRET');
        $pusherAppId = env('PUSHER_APP_ID');
        $pusherOptions = [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true,
        ];

        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');

        // Your authentication logic here
        // Example: Authenticate the user and check if they have access to the private channel

        $pusher = new Pusher($pusherKey, $pusherSecret, $pusherAppId, $pusherOptions);
        $auth = $pusher->socket_auth($channelName, $socketId);

        return response($auth);
    }
}
