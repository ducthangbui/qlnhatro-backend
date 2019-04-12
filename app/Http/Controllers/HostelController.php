<?php

namespace App\Http\Controllers;

use App\Hostel;
use App\HostelUser;
use App\RoleUser;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;

class HostelController extends Controller
{
    public function getHostels(Request $request, $offset)
    {
        $hostels = Hostel::where('status',1)->skip($offset)->take(4)->get();
        return response()->json([
            "hostels" => $hostels
        ],200);
    }

    public function getHostel(Request $request, $hostelid)
    {
        $hostel = Hostel::where('id',$hostelid)->first();
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
    public function findByRangeDate(Request $request)
    {
        $from = date($request->input("from"));
        $to = date($request->input("to"));
        $offset = $request->input("offset");
        $hostels = Hostel::whereBetween('created_at',[$from,$to])
                    ->skip($offset)
                    ->take(4)
                    ->get();

        return response()->json([
            "hostels" => $hostels
        ],200);
    }

    public function getHirredHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $hostel_id = $request->input('hostelid');

        $user_role = RoleUser::where('id',$user->id)->first();
        if ($user_role->id != 2) {
            return response()->json([
                "message" => "must be customer"
            ],300);
        }
        $offset = $request->input('offset');
        $hostel_ids = HostelUser::where('usertt', $user->id)
                    ->skip($offset)
                    ->take(4)
                    ->get('hostelid');
        $hostels = Hostel::whereArray($hostel_ids)->get();
        return response([
            "hostels" => $hostels
        ],200);
    }

    // Nguoi thue tro huy nha tro
    public function cancelHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $user_id = $user->id;
        $hostel_id = HostelUser::where('usertt',$user_id)->first('hostelid');
        if ($hostel_id == null){
            return response([
                "message" => "not found hostel id in hostel_user"
            ],400);
        }
        $hostel = Hostel::where('id',$hostel_id->hostelid)->first();
        if($hostel == null){
            return response([
                "message" => "not found"
            ],201);
        }
        $hostel->status = 0;
        $hostel->save();
        HostelUser::where('hostelid',$hostel_id)->delete();
        return response([
            "message" => $hostel_id
        ],200);
    }

    // Thue tro
    public function hirred(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $hostel_id = $request->input('hostelid');
        $hostel_user = HostelUser::where('hostelid',$hostel_id)->first();
        $hostel_user->usertt = $user->id;
        $hostel_user->save();

        $hostel = Hostel::where('hostleid',$hostel_id)->first();
        $hostel->status = 1;
        $hostel->save();

        return response()->json([
            "message" => "success"
        ],200);
    }

    // chu tro Them nha tro
    public function addHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $user_role = RoleUser::where('id',$user->id)->first();
        if ($user_role->id != 1) {
            return response()->json([
                "message" => "must be landlord"
            ],300);
        }

        $hostel_user = new HostelUser([
            "userct" => $user->id
        ]);
        $hostel_user->save();

        $data = $request->all();
        $hostel = Hostel::created($data);
        if ($hostel){
            return response()->json([
                "message" => "success"
            ],200);
        }

        return response()->json([
            "message" => "not success"
        ],500);

    }

    //Chu tro sua nha tro
    public function updateHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $user_role = RoleUser::where('id',$user->id)->first();
        if ($user_role->id != 1) {
            return response()->json([
                "message" => "must be landlord"
            ],300);
        }

        $hostel_id = $request->input('hostelid');
        $hostel = Hostel::where('id', $hostel_id)->first();
        $hostel->electric_price = $request->input('electric_price');
        $hostel->waterprice = $request->input('waterprice');
        $hostel->sanitationcost = $request->input('sanitationcost');
        $hostel->closedtime = $request->input('closedtime');
        $hostel->price = $request->input('price');
        $hostel->regionid = $request->input('regionid');
        $hostel->addid = $request->input('addid');
        $hostel->haslanlords = $request->input('haslanlords');
        $hostel->save();

        return response()->json([
            "message" => "success"
        ],200);
    }

    // Xoa nha tro
    public function deleteHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $user_role = RoleUser::where('id',$user->id)->first();
        if ($user_role->id != 1) {
            return response()->json([
                "message" => "must be landlord"
            ],300);
        }

        $hostel_id = $request->input('hostelid');
        $hostel = Hostel::where('id',$hostel_id)->delete();
        if ($hostel){
            return response([
                "message" => "success"
            ],200);
        }

        return response([
            "message" => "not success"
        ],500);
    }

    // Lay danh sach cac nha tro
    public function getHostelsLL(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $user_role = RoleUser::where('id',$user->id)->first();
        if ($user_role->id != 1) {
            return response()->json([
                "message" => "must be landlord"
            ],300);
        }

        $offset = $request->input('offset');
        $hostel_ids = HostelUser::where('userct', $user->id)
                        ->skip($offset)
                        ->take(4)
                        ->get();

        $hostels = Hostel::whereArray($hostel_ids)
                    ->skip($offset)
                    ->take(4)
                    ->get();
        return response([
            "hostels" => $hostels
        ],200);
    }

}
