<?php

namespace App\Http\Controllers;

use App\ActivityType;
use App\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard()->user();
        if (!$user) {
            abort(401);
        }
        $type = ActivityType::with(['partners' => function ($q) {
            $q->select("id", "name", "city", "activity_type_id");
        }])->where("activity_types.id", $request->activity_type_id)->get();
        if (!$type) {
            abort(404);
        }
        return response([
            'errors_count' => 0,
            'activity_type' => $type
        ]);
    }

    public function show($id, Request $request)
    {
        $user = Auth::guard()->user();
        if (!$user) {
            abort(401);
        }

        $partner = $user->partner;
        if (!$partner->id != $id) {
            abort(403);
        }

        return response([
            'errors_count' => 0,
            'partner' => $partner,
        ]);
    }
}
