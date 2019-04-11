<?php

namespace App\Http\Controllers;

use App\Rate;
use Illuminate\Http\Request;
use JWTAuth;

class RateController extends Controller
{
    public function getRate(Request $request, $postid)
    {
        $rates = Rate::where('postid', $postid)->sum('rate');
        $rate_count = Rate::where('postid',$postid)->count();
        return response()->json([
            "rate" => $rates/$rate_count
        ],200);
    }

    public function rate(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
//        return response()->json([
//            "user_id" => $user->id,
//            "postid" => $request->input('postid'),
//            "rate" => $request->input('rate')
//        ],200);
        $rate = new Rate([
            'postid' => $request->input('postid'),
            'userid' => $user->id,
            'rate' => $request->input('rate')
        ]);
        $rate->save();
        return response()->json([
            'message' => 'Rate successfully'
        ],200);
    }
}
