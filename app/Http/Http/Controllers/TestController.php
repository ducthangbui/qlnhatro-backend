<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller {
    public function getTest(Request $request) {
        return response()->json(['key'=>"zzz"],200);
    }
}
