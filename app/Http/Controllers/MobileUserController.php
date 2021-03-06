<?php

namespace App\Http\Controllers;

use App\MobileUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MobileUserController extends Controller
{
    public function show($user_id, Request $request)
    {
        $user = MobileUser::find($user_id);
        if (!$user) {
            abort(404);
        }
        $me = Auth::guard()->user();
        if(!$me){
            abort(401);
        }
        if($me->id != $user->id){
            abort(403);
        }

        #$qr = QrCode::format('png')->generate(json_encode(['user_id' => $user->id, 'user_name' => $user->name]));
        $qr = base64_encode(QrCode::format('png')->size(250)->generate(json_encode(['user_id' => $user->id, 'user_name' => $user->name])));


        $user->qr_code = $qr;
     #   dd($user);
        return response([
            'errors_count' => 0,
            'data' => $user,
        ]);
    }
}
