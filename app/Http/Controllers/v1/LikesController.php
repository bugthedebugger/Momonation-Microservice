<?php

namespace App\Http\Controllers\v1;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feed;
use Auth;

class LikesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function like(Request $request) {
        $this->validate(
            $request,
            [
                'feed_id' => 'required',
            ]
        );

        $feed = Feed::find($request->input('feed_id'));

        if ($feed == null)
            return response()->json('Invalid feed id', 500);

        if ($feed->likes()->where('user_id', Auth::User()->id)->first() != null) {
            return response()->json('Already liked', 403);
        }

        try {
            \DB::connection('momonation')->beginTransaction();
            $feed->likes()->create([
                'user_id' => Auth::User()->id,
            ]);
            \DB::connection('momonation')->commit();
        } catch (\Exception $e) {
            \DB::connection('momonation')->rollback();
            return response()->json('Something went wrong', 500);
        }

        return response()->json('Feed liked successfully');
    }

    public function unlike(Request $request) {
        $this->validate(
            $request,
            [
                'feed_id' => 'required',
            ]
        );

        $feed = Feed::find($request->input('feed_id'));

        if ($feed == null)
            return response()->json('Invalid feed id', 500);

        $userLike = $feed->likes()->where('user_id', Auth::User()->id)->first();

        if ($userLike == null) {
            return response()->json('Already unliked', 403);
        }

        try {
            \DB::connection('momonation')->beginTransaction();
            $userLike->delete();
            \DB::connection('momonation')->commit();
        } catch (\Exception $e) {
            \DB::connection('momonation')->rollback();
            return response()->json('Something went wrong', 500);
        }

        return response()->json('Unliked successfully');
    }
}
