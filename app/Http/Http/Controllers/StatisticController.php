<?php

namespace App\Http\Controllers;

use App\Hostel;
use App\RoleUser;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function statisticView(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $user_role = RoleUser::where('id',$user->id)->first();
        if ($user_role->id != 1) {
            return response()->json([
                "message" => "must be landlord"
            ],300);
        }

        $hostel_id = $request->input('hostelid');
        $statistic = Hostel::where('id',$hostel_id)->first('statistic');
        return response()->json([
            "statistic" => $statistic->statistic
        ],200);
    }

}
