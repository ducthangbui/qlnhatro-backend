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

        $rate_5 = Rate::where('postid',$postid)->where('rate',5)->count();
        $rate_4 = Rate::where('postid',$postid)->where('rate',4)->count();
        $rate_3 = Rate::where('postid',$postid)->where('rate',3)->count();
        $rate_2 = Rate::where('postid',$postid)->where('rate',2)->count();
        $rate_1 = Rate::where('postid',$postid)->where('rate',1)->count();

        return response()->json([
            "rate" => $rates/$rate_count,
            "rate5" => $rate_5,
            "rate4" => $rate_4,
            "rate3" => $rate_3,
            "rate2" => $rate_2,
            "rate1" => $rate_1,
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
