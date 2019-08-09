<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Feed;
use Carbon\Carbon;
use Auth;
use App\HelperClasses\BankHelper;

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
        $rawFeeds = Feed::orderBy('created_at', 'desc')->get();
        $parsedFeeds = [];
        if ($authUser->bank == null) {
            $authBank = BankHelper::createBankAccount($authUser);
        } 
        foreach($rawFeeds as $f) {
            $likedByMe = false;
            $rawComments = $f->comments;
            $parsedComments = [];
            foreach($rawComments as $comment) {
                $parsedComments[] = [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'time' => Carbon::parse($comment->created_at)->toDayDateTimeString(),
                    'user' => $comment->user->info(),
                ];
            }
            if( $authUser->likes()->where('feed_id', $f->id)->count() > 0)
                $likedByMe = true;

            $parsedFeeds[] = [
                'id' => $f->id,
                'title' => $f->title,
                'description' => $f->description,
                'time' => Carbon::parse($f->created_at)->toDayDateTimeString(),
                'amount' => $f->transaction->amount,
                'likes' => $f->likeCount(),
                'likedByMe' => $likedByMe,
                'sender' => $f->senderUser->info(),
                'receiver' => $f->receiverUser->info(),
                'comments' => $parsedComments,
            ];
        }

        $response = [
            'bank' => [
                'cooked' => $authUser->bank->cooked,
                'raw' => $authUser->bank->raw,
            ],
            'feed' => $parsedFeeds,
        ];

        return response()->json($response);
    }
}
