<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class StreamController extends Controller
{
    public function seedData(Request $request)
    {
        if (!empty(session('access_token'))) {

            $paginationNext = NULL;
            Stream::truncate();
            for ($i = 0; $i < 10; $i++) {

                $twitchURL = 'https://api.twitch.tv/helix/streams?first=100';

                if ($i != 0)
                    $twitchURL .= '&after=' . $paginationNext;


                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $twitchURL,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Client-Id: ' . env('TWITCH_CLIENT_ID'),
                        'Authorization: Bearer ' . session('access_token')
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $streams = json_decode($response);
                if (!empty($streams->data)) {
                    foreach ($streams->data as $stream) {
                        $streamData = [
                            'id' => $stream->id,
                            'user_id' => $stream->user_id ?: NULL,
                            'channel_name' => $stream->user_name ?: NULL,
                            'game_id' => $stream->game_id ?: NULL,
                            'game_name' => $stream->game_name ?: NULL,
                            'title' => $stream->title ?: NULL,
                            'type' => $stream->type ?: NULL,
                            'viewer_count' => $stream->viewer_count ?: NULL,
                            'started_at' => $stream->started_at ?: NULL
                        ];

                        $stream = Stream::updateOrCreate(['id' => $stream->id], $streamData);
                    }
                    $paginationNext = $streams->pagination->cursor;
                }
            }
            return "1000 latest streams completed seeding";
        }
        return redirect('/auth/twitch?redirectTo=seed-streams-data');
    }
}
