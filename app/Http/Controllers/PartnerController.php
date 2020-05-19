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

    public function search(Request $request)
    {
        $user = Auth::guard()->user();
        if (!$user) {
            abort(401);
        }

        $search = $request->search_params;
        if (!$search) {
            return response([
                'errors_count' => 1,
                'msg' => "Нет параметров поиска"
            ]);
        }
        $partners = Partner::orderBy("id", "asc");
        foreach ($search as $parameter) {
            if (!in_array($parameter['param'], ['id', 'city', 'company_name'])) {
                continue;
            }
            $v = $parameter['value'];
            $partners = $partners->where($parameter['param'], "ILIKE", "%$v%");
        }
        $partners = $partners->take(30)->get();
        return response([
            'errors_count' => 0,
            'partners' => $partners,
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
