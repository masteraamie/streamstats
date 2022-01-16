<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\StreamTag;
use App\Models\UserFollowedStream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class StreamController extends Controller
{
    public function index()
    {
        //return session(['access_token' => NULL]);
        if (!empty(session('access_token'))) {
            return view('streams.index');
        }
        return redirect('/auth/twitch?redirectTo=home');
    }

    public function getStreamsByGameName()
    {
        $streamsByGameName = [];
        if (!empty(session('access_token'))) {
            $streamsByGameName =  DB::table('streams')
                ->selectRaw('game_name, count(id) as number_of_streams')
                ->groupBy('game_name')
                ->whereNotNull('game_name')
                ->get();
        }
        return response()->json(['data' => $streamsByGameName]);
    }

    public function getTopGamesByViewerCount()
    {
        $topGamesByViewerCount = [];
        if (!empty(session('access_token'))) {
            $topGamesByViewerCount =  DB::table('streams')
                ->selectRaw('game_name, SUM(viewer_count) as viewer_count')
                ->groupBy('game_name')
                ->whereNotNull('game_name')
                ->orderBy('viewer_count', 'desc')
                ->limit(10)
                ->get();
        }
        return response()->json(['data' => $topGamesByViewerCount]);
    }

    public function getViewerCountMedian()
    {
        $viewerCountMedian = 0;
        if (!empty(session('access_token'))) {
            $streams = Stream::all();
            $viewerCountMedian = $streams->median('viewer_count');
        }
        return response()->json(['data' => $viewerCountMedian]);
    }


    public function getTopStreamsByViewerCount()
    {
        $topStreamsByViewerCount = [];
        if (!empty(session('access_token'))) {
            $streams = Stream::all();
            $topStreamsByViewerCount =  $streams->sortBy('viewer_count', SORT_REGULAR, true)->take(100)->values();
        }
        return response()->json(['data' => $topStreamsByViewerCount]);
    }

    public function getStreamsGroupedByStartTime()
    {
        $streamsGroupedByStartTime = [];
        if (!empty(session('access_token'))) {
            $streams = Stream::all();

            $streamsGroupedByStartTime = [];
            foreach ($streams as $stream) {
                $startTime = $stream->started_at;

                $startTimeRoundedToHour = date("H", strtotime($startTime));

                if (date("M", strtotime($startTime)) > 30 && $startTimeRoundedToHour < 23)
                    $startTimeRoundedToHour++;

                $streamsGroupedByStartTime[$startTimeRoundedToHour][] = $stream;
            }
        }
        return response()->json(['data' => $streamsGroupedByStartTime]);
    }


    public function getTopStreamsFollowedByUser()
    {
        $topStreamsFollowedByUser = [];
        if (!empty(session('access_token'))) {
            $streams = Stream::all();

            $streamsFollowedByUser = UserFollowedStream::where('user_id', session('logged_user_id'))->get();
            $topStreams = $streams->pluck('id')->toArray();
            $topStreamsFollowedByUser = [];
            foreach ($streamsFollowedByUser as $stream) {
                if (in_array($stream->stream_id, $topStreams))
                    $topStreamsFollowedByUser[] = $streams->where('id', $stream->stream_id)->first();
            }
        }
        return response()->json(['data' => $topStreamsFollowedByUser]);
    }

    public function getViewersRequiredToReachTop()
    {
        $viewersRequiredToReachTop = 0;
        if (!empty(session('access_token'))) {
            $streams = Stream::all();

            $topStreamViewerCount = 0;
            foreach ($streams as $stream) {
                if ($topStreamViewerCount == 0 || $topStreamViewerCount < $stream->viewer_count) {
                    $topStreamViewerCount = $stream->viewer_count;
                }
            }
            $lowestUserFollowedStreamViewerCount = 0;
            $streamsFollowedByUser = UserFollowedStream::where('user_id', session('logged_user_id'))->get();
            $streamsFollowedByUser = $streamsFollowedByUser->pluck('stream_id')->toArray();
            if (!empty($streamsFollowedByUser)) {
                foreach ($streams as $stream) {
                    if (in_array($stream->id, $streamsFollowedByUser)) {
                        if ($stream->viewer_count < $lowestUserFollowedStreamViewerCount || $lowestUserFollowedStreamViewerCount == 0) {
                            $lowestUserFollowedStreamViewerCount = $stream->viewer_count;
                        }
                    }
                }
            }

            $viewersRequiredToReachTop = $topStreamViewerCount - $lowestUserFollowedStreamViewerCount + 1;
        }
        return response()->json(['data' => $viewersRequiredToReachTop]);
    }

    public function getTopStreamsUserSharedTags()
    {
        $tagsShared = [];
        if (!empty(session('access_token'))) {
            $streams = Stream::all();

            $streamTags = [];
            foreach (StreamTag::all() as $tag) {
                $streamTags[$tag->stream_id][] = $tag->tag_id;
            }
            $streamsFollowedByUser = UserFollowedStream::where('user_id', session('logged_user_id'))->get();
            $streamsFollowedByUser = $streamsFollowedByUser->pluck('stream_id')->toArray();
            $tagsShared = [];
            if (!empty($streamsFollowedByUser)) {
                foreach ($streams as $stream) {
                    if (in_array($stream->id, $streamsFollowedByUser) && !empty($streamTags[$stream->id])) {
                        $tagsShared = array_merge($tagsShared, $streamTags[$stream->id]);
                    }
                }
            }

            $tagQuery = "";
            for ($i = 0; $i < count($tagsShared); $i++) {
                if ($i == 0)
                    $tagQuery .= "tag_id=" . $tagsShared[$i];
                else
                    $tagQuery .= "&tag_id=" . $tagsShared[$i];
            }

            if (!empty($tagQuery)) {
                $tagData = Http::withHeaders([
                    'Client-Id' => env('TWITCH_CLIENT_ID'),
                    'Authorization' => 'Bearer ' . session('access_token'),
                ])->get('https://api.twitch.tv/helix/tags/streams?' . $tagQuery);

                $tagData = $tagData->json();

                if (!empty($tagData['data'])) {
                    $tagsShared = [];
                    foreach ($tagData['data'] as $tag) {
                        $tagsShared[] = ['tag_id' => $tag['tag_id'], 'tag_name' => $tag['localization_names']['en-us']];
                    }
                }
            }
        }
        return response()->json(['data' => $tagsShared]);
    }
}
