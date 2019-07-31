<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Feed;

class FeedsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function allFeed() {
        $rawFeeds = Feed::all();
        $parsedFeeds = [];
        foreach($rawFeeds as $f) {
            $parsedFeeds[] = [
                'title' => $f->title,
                'description' => $f->description,
                'likes' => $f->likeCount(),
                'sender' => User::find($f->sender),
                'receiver' => User::find($f->receiver),
            ];
        }
        return response()->json($parsedFeeds);
    }
}
