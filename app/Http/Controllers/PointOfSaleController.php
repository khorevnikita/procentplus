<?php

namespace App\Http\Controllers;

use App\PointOfSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PointOfSaleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard("partner_api")->user();
        if (!$user) {
            abort(401);
        }

        $partner = $user->partner;

        $points = $partner->points;

        return response([
            'errors_count' => 0,
            'point_of_sales' => $points
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


        $points = PointOfSale::orderBy("id", "asc");

        foreach ($search as $parameter) {
            if (!in_array($parameter['param'], ['id', 'city', 'address', 'name'])) {
                continue;
            }
            $v = $parameter['value'];
            $points = $points->where($parameter['param'], "ILIKE", "%$v%");
        }

        $points = $points->take(30)->get();

        return response([
            'errors_count' => 0,
            'point_of_sales' => $points,
        ]);
    }

    public function show($id)
    {
        $user = Auth::guard()->user();
        if (!$user) {
            abort(403);
        }
        $point = PointOfSale::find($id);
        if (!$point) {
            abort(404);
        }
        $qr = QrCode::size(10)->format('svg')->generate(json_encode($point->get(['name', 'city', 'address'])));
        $point->qr_code = $qr;
        return response([
            'errors_count' => 0,
            "point_of_sale" => $point
        ]);
    }
}
