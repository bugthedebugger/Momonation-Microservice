<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\Models\Feed;
use Auth;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentsController extends Controller
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

    public function store(Request $request) {
        $this->validate(
            $request,
            [
                'comment' => 'required',
                'feed_id' => 'required',
            ]
        );

        try {
            \DB::beginTransaction();
            $feed = Feed::find($request->input('feed_id'));
            if ($feed == null)
                return response()->json('Invalid feed id', 500);
            $feed->comments()->create([
                'comment' => $request->input('comment'),
                'user_id' => Auth::User()->id
            ]);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json('Something went wrong', 500);
        }

        return response()->json('Comment added successfully.');
    }
}
