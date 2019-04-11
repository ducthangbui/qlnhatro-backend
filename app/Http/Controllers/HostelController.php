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
}
