<?php

namespace App\Http\Controllers;

use App\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function addRegion(Request $request)
    {
        $region = new Region([
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
