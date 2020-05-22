<?php

namespace App\Http\Controllers;

use App\Partner;
use App\SaleRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SaleRecordController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::guard()->user();
        if (!$user) {
            abort(401);
        }
        $data = $request->sale_record;
        $validatedData = Validator::make($data, [
            'discount' => ['required', 'string', 'max:255'],
            'original_price' => ['required', 'string', 'max:255'],
            'date' => ['required', 'string', 'max:255'],
            #'revenue' => ['required', 'string', 'max:255'],
        ]);
        if ($validatedData->fails()) {
            return response([
                'errors_count' => count($validatedData->errors()),
                'msg' => "Не все поля заполнены корректно"
            ]);
        }

        if (!$user->partner_id) {
            return response([
                'errors_count' => 1,
                'msg' => "Контрагент не указан"
            ]);
        }

        $partner_id = $data['partner_id'] ?? $user->partner_id;
        $partner = Partner::find($partner_id);
        $balance = $user->sales->where("partner_id", $partner->id)->sum('original_price');
        $bonus = $partner->bonuses->where("sum_from", "<", $balance)->sortBy("sum_from")->first();
        $revenue = $data['original_price'] * ( 1- $bonus->percent / 100 );

        $sale = new SaleRecord();
        $sale->mobile_user_id = $user->id;
        $sale->partner_id = $data['partner_id'] ?? $user->partner_id;
        $sale->discount = $data['discount'];
        $sale->original_price = $data['original_price'];
        $sale->point_of_sale_id = $data['point_of_sale_id'] ?? $user->point_of_sale_id;
        $sale->date = $data['date'];
        $sale->revenue = $data['revenue'] ?? $revenue;
        $sale->save();

        return response([
            'errors_count' => 0,
            'data' => $sale
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::guard("partner_api")->user();
        if (!$user) {
            abort(401);
        }

        $partner = $user->partner;
        if (!$partner) {
            return response([
                'errors_count' => 1,
                'msg' => "Партнер не найден"
            ]);
        }


        $limit = $request->limit ?: 100;
        $offset = $request->offset ?: 0;


        $sales = $partner->sales->skip($offset)->take($limit);

        if ($request->point_of_sale_id) {
            $sales = $sales->where("point_of_sale_id", $request->point_of_sale_id);
        }

        return response([
            'errors_count' => 0,
            'sale_records' => $sales,
        ]);

    }
}
