<?php

namespace App\Http\Controllers;

use App\Hostel;
use Illuminate\Http\Request;

class HostelController extends Controller
{
    public function getHostels(Request $request, $offset)
    {
        $hostels = Hostel::skip($offset)->take(4)->get();
        return response()->json([
            "hostels" => $hostels
        ],200);
    }

    public function getHostel(Request $request, $hostelid)
    {
        $hostel = Hostel::where('id',$hostelid)->find(1);
        return response()->json([
            "hostels" => $hostel
        ],200);
    }

    public function findByPrice(Request $request)
    {
        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');
        $offset = $request->input('offset');
        $hostels = Hostel::where('price', ">=", $price_from)
                        ->where('price',"<=",$price_to)
                        ->skip($offset)
                        ->take(4)
                        ->get();
        return response()->json([
            "hostels" => $hostels
        ],200);
    }

    public function findByRegion(Request $request)
    {
        $region = $request->input('region');
        $offset = $request->input('offset');
        $hostels = Hostel::where('regionid', $region)
            ->skip($offset)
            ->take(4)
            ->get();
        return response()->json([
            "hostels" => $hostels
        ],200);
    }

    public function findByAdd(Request $request)
    {
        $add = $request->input('add');
        $offset = $request->input('offset');
        $hostels = Hostel::where('regionid', $add)
            ->skip($offset)
            ->take(4)
            ->get();
        return response()->json([
            "hostels" => $hostels
        ],200);
    }

    // findByRangeDate

    public function getHirredHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $hostel_id = $request->input('hostelid');
        if ($hostel_id != 2) {
            return response()->json([
                "message" => "must be landlord"
            ],300);
        }
        $offset = $request->input('offset');
        $hostels = Hostel::where('userid', $user->id)
                    ->where('hostelid', $hostel_id)
                    ->skip($offset)
                    ->take(4)
                    ->get();
        return response([
            "hostels" => $hostels
        ],200);
    }

    // Nguoi thue tro huy nha tro
    public function cancelHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $user_id = $user->id;

    }
}
