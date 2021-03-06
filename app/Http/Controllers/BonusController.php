<?php

namespace App\Http\Controllers;

use App\MobileUser;
use App\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard()->user();
        if (!$user) {
            abort(401);
        }

        $partner = Partner::with(['bonuses' => function ($q) {
            $q->select("id", "sum_from", "sum_to", "percent", "partner_id");
        }])->where('partners.id', $request->partner_id)->get();
        if (!$partner) {
            abort(404);
        }
        return response([
            'errors_count' => 0,
            'partner' => $partner
        ]);
    }

    public function show(Request $request)
    {
        $user = Auth::guard()->user();
        if (!$user) {
            abort(403);
        }
        $partner = Partner::find($request->partner_id);
        if (!$partner) {
            return response([
                'errors_count' => 1,
                'msg' => "Партнер не найден"
            ]);
        }

        if ($request->user_id && $request->user_id != $user->id) {
            # похоже, что мы оператор
            if ($user->partner_id != $partner->id) {
                abort(403);
            }
            $user = MobileUser::find($request->user_id);
            if (!$user) {
                abort(404);
            }
        }

        $balance = $user->sales->where("partner_id", $partner->id)->sum('revenue');
        $bonus = $partner->bonuses->where("sum_from", "<", $balance)->sortByDesc("sum_from")->first();
        $nextBonus = $partner->bonuses->where("sum_from", ">", $balance)->sortByDesc("sum_from")->first();
        return response([
            'errors_count' => 0,
            'user_bonus' => [
                'partner_id' => $partner->id,
                'user_id' => $user->id,
                'balance' => $balance,
                'current_discount' => $bonus ? (int)$bonus->percent : 0,
                'next_bonus_discount' => $nextBonus ? (int)$nextBonus->percent : 0,
                'next_bonus_from' => $nextBonus ? (int)$nextBonus->sum_from : 0,
                'text' => $nextBonus ? "" : "Пользователь имеет максимальный бонус",
                'isMax' => !$nextBonus
            ]
        ]);
    }
}
