<?php

namespace App\Http\Controllers;

use App\Add;
use Illuminate\Http\Request;

class AddController extends Controller
{
    public function addAdd(Request $request)
    {
        $region = new Add([
            "name" => $request->input("name")
        ]);
        $region->save();
        if ($region){
            return response()->json([
                'message' => 'success'
            ],200);
        }

        return response()->json([
            'message' => 'not ok'
        ], 500);
    }
}
