<?php

namespace App\Http\Controllers;

use App\Add;
use App\Hostel;
use App\HostelRegion;
use App\HostelUser;
use App\Rate;
use App\Region;
use App\RoleUser;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use Log;

class HostelController extends Controller
{
    private function hostelAugumentation($hostels)
    {
        foreach ($hostels as $hostel) {
            $regions = HostelRegion::where('hostelid', $hostel->id)->get();
            $name_regions = "";
            if (count($regions) > 0) {
                foreach ($regions as $region) {
                    $name_region = Region::where('id', $region->regionid)->first();
                    $name_regions = $name_regions . ", " . $name_region->name;
                }
                $name_regions = ltrim($name_regions, $name_regions[0]);
            }

            $hostel->name_add = Add::where('id', $hostel->addid)->first()->name;

            $hostel->name_regions = $name_regions;
            $rate = Rate::where('postid', $hostel->id)->avg('rate');
            $rate5 = Rate::where('postid', $hostel->id)->where('rate', 5)->count();
            $rate4 = Rate::where('postid', $hostel->id)->where('rate', 4)->count();
            $rate3 = Rate::where('postid', $hostel->id)->where('rate', 3)->count();
            $rate2 = Rate::where('postid', $hostel->id)->where('rate', 2)->count();
            $rate1 = Rate::where('postid', $hostel->id)->where('rate', 1)->count();
            if ($rate == null) {
                $hostel->rate = 0;
            } else {
                $hostel->rate = $rate;
            }
            $hostel->rate5 = $rate5;
            $hostel->rate4 = $rate4;
            $hostel->rate3 = $rate3;
            $hostel->rate2 = $rate2;
            $hostel->rate1 = $rate1;

            $hostel_user = HostelUser::where('hostelid', $hostel->id)->first();
//            echo('Showing user profile for user: '.$hostel_user->userct);
            $user = User::where('id', $hostel_user->userct)->first();
            $hostel->landlords = $user->name;
            $hostel->landlords_phone = $user->phonenumber;
        }
        return $hostels;
    }

    private function hostelAugumentationCT($hostels, $useridct)
    {
        foreach ($hostels as $hostel) {
            $user_tt = HostelUser::where('hostelid', $hostel->id)
                ->where('userct', $useridct)
                ->first('usertt');
            $hostel->usertt = User::where('id', $user_tt->usertt)->first();
        }
        return $hostels;
    }

    public function getHostels(Request $request, $offset)
    {
        $hostels = Hostel::where('status', 0)->skip($offset)->take(9)->get();
        $hostels = $this->hostelAugumentation($hostels);
        $hostels = collect($hostels)->sortByDesc('rate');
        $hostels = $hostels->values()->all();
        return response()->json([
            "hostels" => $hostels
        ], 200);
    }

    public function getHostel(Request $request, $hostelid)
    {
        $hostel = Hostel::where('id', $hostelid)->get();
        $hostel = $this->hostelAugumentation($hostel);
        $hostel = collect($hostel)->sortByDesc('rate');
        $hostel = $hostel->values()->all();
        return response()->json([
            "hostel" => $hostel
        ], 200);
    }

    public function findByPrice(Request $request)
    {
        $price_ = $request->input('price_');
        $offset = $request->input('offset');
        $type_search = $request->input('typesearch');

        if ($type_search == 1) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_ct = HostelUser::where('userct', $user->id)->get('hostelid')->toArray();

            if ($price_ == 1) {
                $hostels = Hostel::where('price', "<=", 1200000)
                    ->whereIn('id', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($price_ == 2) {
                $hostels = Hostel::where('price', ">=", 1200000)
                    ->where('price', "<=", 2500000)
                    ->whereIn('id', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($price_ == 3) {
                $hostels = Hostel::where('price', ">=", 2500000)
                    ->where('price', '<=', 3500000)
                    ->whereIn('id', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($price_ == 4) {
                $hostels = Hostel::where('price', ">=", 3500000)
                    ->whereIn('id', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            }
            $hostels = $this->hostelAugumentation($hostels);

            $hostels = $this->hostelAugumentationCT($hostels, $user->id);
            return response()->json([
                "hostels" => $hostels
            ], 200);

        } elseif ($type_search == 2) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_tt = HostelUser::where('usertt', $user->id)->get('hostelid')->toArray();

            if ($price_ == 1) {
                $hostels = Hostel::where('price', "<=", 1200000)
                    ->whereIn('id', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($price_ == 2) {
                $hostels = Hostel::where('price', ">=", 1200000)
                    ->where('price', "<=", 2500000)
                    ->whereIn('id', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($price_ == 3) {
                $hostels = Hostel::where('price', ">=", 2500000)
                    ->where('price', '<=', 3500000)
                    ->whereIn('id', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($price_ == 4) {
                $hostels = Hostel::where('price', ">=", 3500000)
                    ->whereIn('id', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            }
            $hostels = $this->hostelAugumentation($hostels);

            return response()->json([
                "hostels" => $hostels
            ], 200);

        } else {
            if ($price_ == 1) {
                $hostels = Hostel::where('price', "<=", 1200000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
                $hostels = $this->hostelAugumentation($hostels);
                return response()->json([
                    "hostels" => $hostels
                ], 200);
            } elseif ($price_ == 2) {
                $hostels = Hostel::where('price', ">=", 1200000)
                    ->where('price', "<=", 2500000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
                $hostels = $this->hostelAugumentation($hostels);
                return response()->json([
                    "hostels" => $hostels
                ], 200);
            } elseif ($price_ == 3) {
                $hostels = Hostel::where('price', ">=", 2500000)
                    ->where('price', '<=', 3500000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
                $hostels = $this->hostelAugumentation($hostels);
                return response()->json([
                    "hostels" => $hostels
                ], 200);
            } elseif ($price_ == 4) {
                $hostels = Hostel::where('price', ">=", 3500000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
                $hostels = $this->hostelAugumentation($hostels);
                return response()->json([
                    "hostels" => $hostels
                ], 200);
            }
        }


        return response()->json([
            "hostels" => []
        ], 400);
    }

    public function findByRegion(Request $request)
    {
        $region = $request->input('region');
        $region = explode(',', $region);
//        return response()->json([
//            "hostels" => (int)$region
//        ],200);
        $offset = $request->input('offset');
        $type_search = $request->input('typesearch');

        if ($type_search == 1) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_ct = HostelUser::where('userct', $user->id)->get('hostelid')->toArray();
            $hostel_ids = HostelRegion::whereIn('regionid', $region)
                ->whereIn('hostelid', $hostels_ct)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            $hostels = $this->hostelAugumentationCT($hostels, $user->id);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($type_search == 2) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_tt = HostelUser::where('usertt', $user->id)->get('hostelid')->toArray();
            $hostel_ids = HostelRegion::whereIn('regionid', $region)
                ->whereIn('hostelid', $hostels_tt)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } else {
            $hostel_ids = HostelRegion::whereIn('regionid', $region)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);

            return response()->json([
                "hostels" => $hostels
            ], 200);
        }

    }

    public function findByAdd(Request $request)
    {
        $add = $request->input('add');
        $offset = $request->input('offset');
        $type_search = $request->input('typesearch');
        if ($type_search == 1) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_ct = HostelUser::where('userct', $user->id)->get('hostelid')->toArray();
            $hostels = Hostel::where('addid', $add)
                ->whereIn('id', $hostels_ct)
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            $hostels = $this->hostelAugumentationCT($hostels, $user->id);
            return response()->json([
                "hostels" => $hostels
            ], 200);

        } elseif ($type_search == 2) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_ct = HostelUser::where('usertt', $user->id)->get('hostelid')->toArray();
            $hostels = Hostel::where('addid', $add)
                ->whereIn('id', $hostels_ct)
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        }

        $hostels = Hostel::where('addid', $add)
            ->skip($offset)
            ->take(9)
            ->get();
        $hostels = $this->hostelAugumentation($hostels);
        return response()->json([
            "hostels" => $hostels
        ], 200);
    }

    public function findByAddPrice(Request $request)
    {
        $addprice = $request->input('addprice');
        $addprice = explode(',', $addprice);
//        return response()->json([
//            "hostels" => (int)$region
//        ],200);
        $offset = $request->input('offset');
        $type_search = $request->input('typesearch');

        if ($type_search == 1) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_ct = HostelUser::where('userct', $user->id)->get('hostelid')->toArray();
            if ($addprice[1] == 1) {
                $hostels = Hostel::where('price', "<=", 1200000)
                    ->where('addid', $addprice[0])
                    ->whereIn('id', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
            } elseif ($addprice[1] == 2) {
                $hostels = Hostel::where('price', ">=", 1200000)
                    ->where('price', "<=", 2500000)
                    ->where('addid', $addprice[0])
                    ->whereIn('id', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($addprice[1] == 3) {
                $hostels = Hostel::where('price', ">=", 2500000)
                    ->where('price', '<=', 3500000)
                    ->where('addid', $addprice[0])
                    ->whereIn('id', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($addprice[1] == 4) {
                $hostels = Hostel::where('price', ">=", 3500000)
                    ->where('addid', $addprice[0])
                    ->whereIn('id', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            }
            $hostels = $this->hostelAugumentation($hostels);
            $hostels = $this->hostelAugumentationCT($hostels, $user->id);
            return response()->json([
                "hostels" => $hostels
            ], 200);

        } elseif ($type_search == 2) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_tt = HostelUser::where('usertt', $user->id)->get('hostelid')->toArray();
            if ($addprice[1] == 1) {
                $hostels = Hostel::where('price', "<=", 1200000)
                    ->where('addid', $addprice[0])
                    ->whereIn('id', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = $this->hostelAugumentation($hostels);
                return response()->json([
                    "hostels" => $hostels
                ], 200);
            } elseif ($addprice[1] == 2) {
                $hostels = Hostel::where('price', ">=", 1200000)
                    ->where('price', "<=", 2500000)
                    ->where('addid', $addprice[0])
                    ->whereIn('id', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get();
                $hostels = $this->hostelAugumentation($hostels);
                return response()->json([
                    "hostels" => $hostels
                ], 200);
            } elseif ($addprice[1] == 3) {
                $hostels = Hostel::where('price', ">=", 2500000)
                    ->where('price', '<=', 3500000)
                    ->where('addid', $addprice[0])
                    ->whereIn('id', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get();
                $hostels = $this->hostelAugumentation($hostels);
                return response()->json([
                    "hostels" => $hostels
                ], 200);
            } elseif ($addprice[1] == 4) {
                $hostels = Hostel::where('price', ">=", 3500000)
                    ->where('addid', $addprice[0])
                    ->whereIn('id', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get();
                $hostels = $this->hostelAugumentation($hostels);
                return response()->json([
                    "hostels" => $hostels
                ], 200);
            }
        }

        if ($addprice[1] == 1) {
            $hostels = Hostel::where('price', "<=", 1200000)
                ->where('addid', $addprice[0])
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($addprice[1] == 2) {
            $hostels = Hostel::where('price', ">=", 1200000)
                ->where('price', "<=", 2500000)
                ->where('addid', $addprice[0])
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($addprice[1] == 3) {
            $hostels = Hostel::where('price', ">=", 2500000)
                ->where('price', '<=', 3500000)
                ->where('addid', $addprice[0])
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($addprice[1] == 4) {
            $hostels = Hostel::where('price', ">=", 3500000)
                ->where('addid', $addprice[0])
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        }

        return response()->json([
            "hostels" => []
        ], 400);
    }

    public function findByRegionPrice(Request $request)
    {
        $regionprice = $request->input('regionprice');
        $regionprice = explode('|', $regionprice);
        $regions = explode(',', $regionprice[0]);
//        return response()->json([
//            "hostels" => (int)$region
//        ],200);
        $offset = $request->input('offset');
        $type_search = $request->input('typesearch');

        if ($type_search == 1) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_ct = HostelUser::where('userct', $user->id)->get('hostelid')->toArray();
            if ($regionprice[1] == 1) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', "<=", 1200000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($regionprice[1] == 2) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 1200000)
                    ->where('price', "<=", 2500000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($regionprice[1] == 3) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 2500000)
                    ->where('price', '<=', 3500000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($regionprice[1] == 4) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->where('hostelid', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 3500000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            }
            $hostels = $this->hostelAugumentation($hostels);
            $hostels = $this->hostelAugumentationCT($hostels, $user->id);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($type_search == 2) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_tt = HostelUser::where('usertt', $user->id)->get('hostelid')->toArray();
            if ($regionprice[1] == 1) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', "<=", 1200000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($regionprice[1] == 2) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 1200000)
                    ->where('price', "<=", 2500000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($regionprice[1] == 3) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 2500000)
                    ->where('price', '<=', 3500000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($regionprice[1] == 4) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->where('hostelid', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 3500000)
                    ->skip($offset)
                    ->take(9)
                    ->get();
            }
            $hostels = $this->hostelAugumentation($hostels);
            $hostels = $this->hostelAugumentationCT($hostels, $user->id);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        }

        if ($regionprice[1] == 1) {
            $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->where('price', "<=", 1200000)
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($regionprice[1] == 2) {
            $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->where('price', ">=", 1200000)
                ->where('price', "<=", 2500000)
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($regionprice[1] == 3) {
            $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->where('price', ">=", 2500000)
                ->where('price', '<=', 3500000)
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($regionprice[1] == 4) {
            $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->where('price', ">=", 3500000)
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        }

        return response()->json([
            "hostels" => []
        ], 400);
    }

    public function findByAddRegion(Request $request)
    {
        $addregion = $request->input('addregion');
        $addregion = explode('|', $addregion);
        $regions = explode(',', $addregion[0]);
//        return response()->json([
//            "hostels" => (int)$region
//        ],200);
        $offset = $request->input('offset');
        $type_search = $request->input('typesearch');

        if ($type_search == 1) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_ct = HostelUser::where('userct', $user->id)->get('hostelid')->toArray();
            $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                ->whereIn('hostelid', $hostels_ct)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->where('addid', $addregion[1])
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            $hostels = $this->hostelAugumentationCT($hostels, $user->id);
            return response()->json([
                "hostels" => $hostels
            ], 200);

        } elseif ($type_search == 2) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_tt = HostelUser::where('usertt', $user->id)->get('hostelid')->toArray();
            $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                ->whereIn('hostelid', $hostels_tt)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->where('addid', $addregion[1])
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            $hostels = $this->hostelAugumentationCT($hostels, $user->id);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        }

        $hostel_ids = HostelRegion::whereIn('regionid', $regions)
            ->skip($offset)
            ->take(9)
            ->get('hostelid');
        $hostels = Hostel::whereIn('id', $hostel_ids)
            ->where('addid', $addregion[1])
            ->skip($offset)
            ->take(9)
            ->get();
        $hostels = $this->hostelAugumentation($hostels);
        return response()->json([
            "hostels" => $hostels
        ], 200);
    }

    public function find(Request $request)
    {
        $all = $request->input('all');
        $all = explode('|', $all);
        $regions = explode(',', $all[0]);
//        return response()->json([
//            "hostels" => (int)$region
//        ],200);
        $offset = $request->input('offset');
        $type_search = $request->input('typesearch');

        if ($type_search == 1) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_ct = HostelUser::where('userct', $user->id)->get('hostelid')->toArray();
            if ($all[1] == 1) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', "<=", 1200000)
                    ->where('addid', $all[2])
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($all[1] == 2) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 1200000)
                    ->where('price', "<=", 2500000)
                    ->where('addid', $all[2])
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($all[1] == 3) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 2500000)
                    ->where('price', '<=', 3500000)
                    ->where('addid', $all[2])
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($all[1] == 4) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_ct)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 3500000)
                    ->where('addid', $all[2])
                    ->skip($offset)
                    ->take(9)
                    ->get();
            }
            $hostels = $this->hostelAugumentation($hostels);
            $hostels = $this->hostelAugumentationCT($hostels, $user->id);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($type_search == 2) {
            $user = JWTAuth::parseToken()->toUser();
            $hostels_tt = HostelUser::where('usertt', $user->id)->get('hostelid')->toArray();
            if ($all[1] == 1) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', "<=", 1200000)
                    ->where('addid', $all[2])
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($all[1] == 2) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 1200000)
                    ->where('price', "<=", 2500000)
                    ->where('addid', $all[2])
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($all[1] == 3) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 2500000)
                    ->where('price', '<=', 3500000)
                    ->where('addid', $all[2])
                    ->skip($offset)
                    ->take(9)
                    ->get();
            } elseif ($all[1] == 4) {
                $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                    ->whereIn('hostelid', $hostels_tt)
                    ->skip($offset)
                    ->take(9)
                    ->get('hostelid');
                $hostels = Hostel::whereIn('id', $hostel_ids)
                    ->where('price', ">=", 3500000)
                    ->where('addid', $all[2])
                    ->skip($offset)
                    ->take(9)
                    ->get();
            }
            $hostels = $this->hostelAugumentation($hostels);
            $hostels = $this->hostelAugumentationCT($hostels, $user->id);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        }

        if ($all[1] == 1) {
            $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->where('price', "<=", 1200000)
                ->where('addid', $all[2])
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($all[1] == 2) {
            $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->where('price', ">=", 1200000)
                ->where('price', "<=", 2500000)
                ->where('addid', $all[2])
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($all[1] == 3) {
            $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->where('price', ">=", 2500000)
                ->where('price', '<=', 3500000)
                ->where('addid', $all[2])
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        } elseif ($all[1] == 4) {
            $hostel_ids = HostelRegion::whereIn('regionid', $regions)
                ->skip($offset)
                ->take(9)
                ->get('hostelid');
            $hostels = Hostel::whereIn('id', $hostel_ids)
                ->where('price', ">=", 3500000)
                ->where('addid', $all[2])
                ->skip($offset)
                ->take(9)
                ->get();
            $hostels = $this->hostelAugumentation($hostels);
            return response()->json([
                "hostels" => $hostels
            ], 200);
        }

        return response()->json([
            "hostels" => []
        ], 400);
    }

    // findByRangeDate
    public function findByRangeDate(Request $request)
    {
        $from = date($request->input("from"));
        $to = date($request->input("to"));
        $offset = $request->input("offset");
        $hostels = Hostel::whereBetween('created_at', [$from, $to])
            ->skip($offset)
            ->take(9)
            ->get();

        return response()->json([
            "hostels" => $hostels
        ], 200);
    }

    public function getHirredHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();

        $user_role = $user->roleid;
        if ($user_role != 2) {
            return response()->json([
                "message" => "must be customer"
            ], 401);
        }
        $offset = $request->input('offset');
        $hostel_ids = HostelUser::where('usertt', $user->id)
            ->skip($offset)
            ->take(9)
            ->get('hostelid');
        $hostels = Hostel::whereIn('id', $hostel_ids)->get();
        $hostels = $this->hostelAugumentation($hostels);
        return response([
            "hostels" => $hostels
        ], 200);
    }

    // Nguoi thue tro huy nha tro
    public function cancelHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
//        $user_role = $user->roleid;
//        if ($user_role != 2) {
//            return response()->json([
//                "message" => "must be customer"
//            ],401);
//        }
        $user_id = $user->id;
        $hostel_id = $request->input('hostelid');
        $hostel = Hostel::where('id', $hostel_id)->first();
        if ($hostel == null) {
            return response([
                "message" => "not found"
            ], 201);
        }
        $hostel->userid = $hostel->userid;
        $hostel->electricprice = $hostel->electricprice;
        $hostel->waterprice = $hostel->waterprice;
        $hostel->sanitationcost = $hostel->sanitationcost;
        $hostel->securitycost = $hostel->securitycost;
        $hostel->closedtime = $hostel->closedtime;
        $hostel->price = $hostel->price;
        $hostel->img = $hostel->img;
        $hostel->addid = $hostel->addid;
        $hostel->haslandlords = $hostel->haslandlords;
        $hostel->status = 0;
        $hostel->save();

        $hostel_user = HostelUser::where('hostelid', $hostel_id)->first();
        $hostel_user->usertt = 0;
        $hostel_user->userct = $hostel_user->userct;
        $hostel_user->hostelid = $hostel_user->hostelid;
        $hostel_user->save();

        return response([
            "message" => $hostel_id
        ], 200);
    }

    // Thue tro
    public function hirred(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        if ($user->roleid != 2) {
            return response()->json([
                "message" => "must be tt"
            ], 401);
        }
        $hostel_id = $request->input('hostelid');

        $hostel_user = HostelUser::where('hostelid', $hostel_id)->first();
        $hostel_user->userct = $hostel_user->userct;
        $hostel_user->hostelid = $hostel_id;
        $hostel_user->usertt = $user->id;
        $hostel_user->save();

        $hostel = Hostel::where('id', $hostel_id)->first();
        $hostel->status = 1;
        $hostel->save();

        return response()->json([
            "message" => "success"
        ], 200);
    }

    // chu tro Them nha tro
    public function addHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $user_role = User::where('id', $user->id)->first();
        if ($user_role->roleid != 1) {
            return response()->json([
                "message" => "must be landlord"
            ], 300);
        }

        $rules = ['image' => 'image'];
        $posts = ['image' => $request->file('image')];

        $valid = Validator::make($posts, $rules);
        if ($valid->fails()) {
            return response()->json([
                'message' => 'failed'
            ], 500);
        }

        if ($request->file('image')->isValid()) {
            $fileExtension = $request->file('image')->getClientOriginalExtension(); // Lấy . của file

            // Filename cực shock để khỏi bị trùng
            $fileName = time() . "_" . rand(0, 9999999) . "_" . md5(rand(0, 9999999)) . "." . $fileExtension;

            // Thư mục upload
            $uploadPath_ = public_path('/upload'); // Thư mục upload
            $uploadPath = env('UPLOAD_PATH', $uploadPath_);
            // Bắt đầu chuyển file vào thư mục
            $request->file('image')->move($uploadPath, $fileName);

            $hostel = new Hostel([
                "userid" => $user->id,
                "electricprice" => $request->input('electricprice'),
                "waterprice" => $request->input('waterprice'),
                "sanitationcost" => $request->input('sanitationcost'),
                "securitycost" => $request->input('securitycost'),
                "closedtime" => $request->input('closedtime'),
                "status" => 0,
                "views" => 0,
                "price" => $request->input('price'),
                "addid" => $request->input('addid'),
                "haslandlords" => $request->input('haslandlords'),
                "img" => $fileName
            ]);
            $hostel->floorarea = $request->input('floorarea');
            $hostel->views = 0;
            $hostel->save();
//            return response()->json([
//                "message" => $hostel->id
//            ],500);
            if (!$hostel) {
                return response()->json([
                    "message" => "not success"
                ], 500);
            }
            $hostelId = $hostel->id;
            $hostelUser = new HostelUser([
                'userct' => $user->id,
                'usertt' => 0,
                'hostelid' => $hostel->id
            ]);
            $hostelUser->save();

            $regionsIds = $request->input('regions');
            if ($regionsIds != null) {
                $regionsIds = explode(",", $regionsIds);
                foreach ($regionsIds as $regionId) {
//                return response()->json([
//                    "message" => (int)$regionId
//                ],500);
                    $hostelRegion = new HostelRegion();
                    $hostelRegion->regionid = (int)$regionId;
                    $hostelRegion->hostelid = $hostelId;
                    $hostelRegion->save();
                    if (!$hostelRegion) {
                        return response()->json([
                            "message" => "not success"
                        ], 500);
                    }
                }
            }

        }

        return response()->json([
            "message" => "success"
        ], 200);

    }

    //Chu tro sua nha tro
    public function updateHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
//        $user_role = RoleUser::where('id',$user->id)->first();
        if ($user->roleid != 1) {
            return response()->json([
                "message" => "must be landlord"
            ], 401);
        }

        $rules = ['image' => 'image'];
        $posts = ['image' => $request->file('image')];

        $hostel = Hostel::where('id', $request->input('hostelid'))->first();
        if ($request->input('image_name') == $hostel->img) {
            $fileName = $hostel->img;
        } else {
            $valid = Validator::make($posts, $rules);
            if ($valid->fails()) {
                return response()->json([
                    'message' => 'failed'
                ], 500);
            }

            if ($request->file('image')->isValid()) {

                $fileExtension = $request->file('image')->getClientOriginalExtension(); // Lấy . của file

                // Filename cực shock để khỏi bị trùng
                $fileName = time() . "_" . rand(0, 9999999) . "_" . md5(rand(0, 9999999)) . "." . $fileExtension;
                // Thư mục upload
                $uploadPath_ = public_path('/upload'); // Thư mục upload
                $uploadPath = env('UPLOAD_PATH', $uploadPath_);

                // Bắt đầu chuyển file vào thư mục
                $request->file('image')->move($uploadPath, $fileName);
            }
        }

        $hostel->userid = $hostel->userid;
        $hostel->electricprice = $request->input('electricprice');
        $hostel->waterprice = $request->input('waterprice');
        $hostel->sanitationcost = $request->input('sanitationcost');
        $hostel->securitycost = $request->input('securitycost');
        $hostel->closedtime = $request->input('closedtime');
        $hostel->status = $hostel->status;
        $hostel->price = $request->input('price');
        $hostel->addid = $request->input('addid');
        $hostel->haslandlords = $request->input('haslandlords');
        $hostel->img = $fileName;
        $hostel->floorarea = $request->input('floorarea');
        $hostel->save();

//            return response()->json([
//                "message" => $hostel->id
//            ],500);
        if (!$hostel) {
            return response()->json([
                "message" => "not success"
            ], 500);
        }
        $hostelId = $hostel->id;
//            $hostelUser = new HostelUser([
//                'userct' => $user->id,
//                'usertt' => 0,
//                'hostelid' => $hostel->id
//            ]);
//            $hostelUser->save();

        $regionsIds = $request->input('regions');
        $regionsIds = explode(",", $regionsIds);
        HostelRegion::where('hostelid', $hostelId)->delete();
        foreach ($regionsIds as $regionId) {
//                return response()->json([
//                    "message" => (int)$regionId
//                ],500);

            $hostelRegion = new HostelRegion;
            $hostelRegion->regionid = (int)$regionId;
            $hostelRegion->hostelid = $hostelId;
            $hostelRegion->save();
            if (!$hostelRegion) {
                return response()->json([
                    "message" => "not success"
                ], 500);
            }
        }


        return response()->json([
            "message" => "success"
        ], 200);
    }

    // Xoa nha tro
    public function deleteHostel(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
//        $user_role = RoleUser::where('id',$user->id)->first();
        if ($user->roleid != 1) {
            return response()->json([
                "message" => "must be landlord"
            ], 401);
        }

        $hostel_id = $request->input('hostelid');
        $hostel = Hostel::where('id', $hostel_id)->delete();
        if ($hostel) {
            return response([
                "message" => "success"
            ], 200);
        }

        $hostel_user = HostelUser::where('hostelid', $hostel_id)->delete();
        if ($hostel_user) {
            return response([
                "message" => "success"
            ], 200);
        }

        return response([
            "message" => "not success"
        ], 500);
    }

    // Lay danh sach cac nha tro
    public function getHostelsLL(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
//        $user_role = RoleUser::where('id',$user->id)->first();
        if ($user->roleid != 1) {
            return response()->json([
                "message" => "must be landlord"
            ], 401);
        }

        $offset = $request->input('offset');
        $hostel_ids = HostelUser::where('userct', $user->id)
            ->skip($offset)
            ->take(9)
            ->get('hostelid');

        $hostels = Hostel::whereIn('id', $hostel_ids)
            ->skip($offset)
            ->take(9)
            ->get();
        $hostel_usertt = HostelUser::whereIn('hostelid', $hostel_ids)
            ->skip($offset)
            ->take(9)
            ->get('usertt');


        $hostels = $this->hostelAugumentation($hostels);

        foreach ($hostels as $hostel) {
            $user_tt = HostelUser::where('hostelid', $hostel->id)
                ->where('userct', $user->id)
                ->first('usertt');
            $hostel->usertt = User::where('id', $user_tt->usertt)->first();
        }
        return response([
            "hostels" => $hostels
        ], 200);
    }

    public function increaseViews(Request $request)
    {
        $hostelid = $request->input('hostelid');
        $hostel = Hostel::where('id', $hostelid)->first();
        $hostel->userid = $hostel->userid;
        $hostel->electricprice = $hostel->electricprice;
        $hostel->waterprice = $hostel->waterprice;
        $hostel->sanitationcost = $hostel->sanitationcost;
        $hostel->securitycost = $hostel->securitycost;
        $hostel->closedtime = $hostel->closedtime;
        $hostel->status = $hostel->status;
        $hostel->price = $hostel->price;
        $hostel->addid = $hostel->addid;
        $hostel->haslandlords = $hostel->haslandlords;
        $hostel->img = $hostel->img;
        $hostel->views = $hostel->views + 1;
        $hostel->save();

        if ($hostel) {
            return response([
                "message" => "success"
            ], 200);
        }

        return response([
            "message" => "not success"
        ], 500);
    }

}
