<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\TwitchApi;
use App\Models\User;
use App\Models\UserFollowedStream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function authTwitchAPI(Request $request)
    {
        $code = $request->query('code');
        $redirectToPage = $request->query('redirectTo');
        if (!empty($redirectToPage))
            session(['redirectTo' => $redirectToPage]);

        if (!empty($code)) {

            $response = Http::post('https://id.twitch.tv/oauth2/token?client_id=' . env('TWITCH_CLIENT_ID') . '&client_secret=' . env('TWITCH_CLIENT_SECRET') . '&code=' . $code . '&grant_type=authorization_code&redirect_uri=' . env('TWITCH_REDIRECT_URI'));

            if (!empty($response->object())) {
                $authData = $response->object();
                if (!empty($authData->access_token)) {

                    TwitchApi::truncate();
                    TwitchApi::create([
                        'access_token' => $authData->access_token,
                        'refresh_token' => $authData->refresh_token
                    ]);

                    session(['access_token' => $authData->access_token]);

                    $response = Http::withHeaders([
                        'Client-Id' => env('TWITCH_CLIENT_ID'),
                        'Authorization' => 'Bearer ' . $authData->access_token
                    ])->get('https://api.twitch.tv/helix/users');

                    $userDetails = $response->object();
                    if (!empty($userDetails->data)) {
                        foreach ($userDetails->data as $user) {
                            session(['logged_user_id' => $user->id]);
                            $userData = [
                                'twitch_id' => $user->id,
                                'name' => $user->display_name,
                                'email' => $user->email
                            ];
                            User::updateOrCreate(['twitch_id' => $user->id], $userData);

                            //get user followed streams
                            $response = Http::withHeaders([
                                'Client-Id' => env('TWITCH_CLIENT_ID'),
                                'Authorization' => 'Bearer ' . $authData->access_token
                            ])->get('https://api.twitch.tv/helix/streams/followed', ['user_id' => $user->id]);

                            $followedStreams = $response->object();
                            if(!empty($followedStreams))
                            {
                                foreach ($followedStreams->data as $stream) {
                                    $streamData = [
                                        'user_id' => $user->id,
                                        'stream_id' => $stream->id
                                    ];
                                    UserFollowedStream::updateOrCreate(['stream_id' => $stream->id], $streamData);
                                }
                            }
                        }


                    }
                    $redirectToPage = session('redirectTo');
                    session(['redirectTo' => null]);
                    if ($redirectToPage == 'home') {
                        return redirect('/');
                    } else if ($redirectToPage == 'streams') {
                        return redirect('/streams');
                    } else if ($redirectToPage == 'seed-streams-data') {
                        return redirect('/streams/seed-data');
                    }
                }
            }
            return "Login Failed!";
        } else {
            $redirectURL = "https://id.twitch.tv/oauth2/authorize";
            $redirectURL .= "?client_id=" . env('TWITCH_CLIENT_ID');
            $redirectURL .= "&redirect_uri=". env('APP_URL') ."/auth/twitch";
            $redirectURL .= "&response_type=code";
            $redirectURL .= "&scope=user:read:email user:read:follows";

            return redirect($redirectURL);
        }
    }
}
