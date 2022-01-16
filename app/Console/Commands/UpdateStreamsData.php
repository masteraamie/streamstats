<?php

namespace App\Console\Commands;

use App\Models\Stream;
use App\Models\StreamTag;
use App\Models\TwitchApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateStreamsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateStreamsData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to update stream data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $accessToken = !empty(TwitchApi::first()) ? TwitchApi::first()->access_token : NULL;
        $accessError = false;
        if (!empty($accessToken)) {
            $paginationNext = NULL;
            Stream::truncate();
            for ($i = 0; $i < 10; $i++) {

                $twitchURL = 'https://api.twitch.tv/helix/streams?first=100';

                if ($i != 0)
                    $twitchURL .= '&after=' . $paginationNext;

                $response = Http::withHeaders([
                    'Client-Id' => env('TWITCH_CLIENT_ID'),
                    'Authorization' => 'Bearer ' . $accessToken
                ])->get($twitchURL);

                if ($response->status() == 401 && !$accessError) {
                    $refreshToken = TwitchApi::first()->refresh_token;
                    if (!empty($refreshToken)) {
                        $refreshTokenURL = "https://id.twitch.tv/oauth2/token?grant_type=refresh_token&refresh_token=" . $refreshToken . "&client_id=" . env('TWITCH_CLIENT_ID') . "&client_secret=" . env('TWITCH_CLIENT_SECRET');
                        $response = Http::post($refreshTokenURL);

                        if ($response->status() == 200 && !empty($response->object())) {
                            $authData = $response->object();
                            if (!empty($authData->access_token)) {
                                TwitchApi::truncate();
                                TwitchApi::create([
                                    'access_token' => $authData->access_token,
                                    'refresh_token' => $authData->refresh_token
                                ]);
                                $accessToken = $authData->access_token;
                            }
                        }
                    }
                    $accessError = true;
                    $i = 0;
                    continue;
                } else {
                    $streams = $response->object();
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

                            if(!empty($tag_ids))
                            {
                                foreach($tag_ids as $tag_id)
                                {
                                    StreamTag::create([
                                        'stream_id' => $stream->id,
                                        'tag_id' => $tag_id
                                    ]);
                                }
                            }
                        }
                        $paginationNext = $streams->pagination->cursor;
                    }
                }
            }

            if ($i >= 10) {
                echo "\n\n1000 latest streams completed seeding\n\n";
                return 1;
            }
        }
        echo "\n\nThere was an error getting the access token\n\n";
        return 0;
    }
}
