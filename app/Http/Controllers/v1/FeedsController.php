<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Feed;
use Carbon\Carbon;
use Auth;

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
        $authUser = Auth::User();
        $rawFeeds = Feed::all();
        $parsedFeeds = [];
        foreach($rawFeeds as $f) {
            $likedByMe = false;
            $rawComments = $f->comments;
            $parsedComments = [];
            foreach($rawComments as $comment) {
                $parsedComments[] = [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'time' => Carbon::parse($comment->created_at)->toDayDateTimeString(),
                    'user' => $comment->user,
                ];
            }
            if( $authUser->likes()->where('feed_id', $f->id)->count() > 0)
                $likedByMe = true;

            $parsedFeeds[] = [
                'id' => $f->id,
                'title' => $f->title,
                'description' => $f->description,
                'amount' => $f->transaction->amount,
                'likes' => $f->likeCount(),
                'likedByMe' => $likedByMe,
                'sender' => $f->senderUser,
                'receiver' => $f->receiverUser,
                'comments' => $parsedComments,
            ];
        }
        return response()->json($parsedFeeds);
    }
}
