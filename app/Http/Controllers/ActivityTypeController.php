<?php

namespace App\Http\Controllers;

use App\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityTypeController extends Controller
{
    public function index()
    {
        $user = Auth::guard()->user();
        if (!$user) {
            abort(401);
        }

        $types = ActivityType::select('id','name','description')->get();
        return response([
            'errors_count' => 0,
            'activity_types' => $types
        ]);
    }
}
