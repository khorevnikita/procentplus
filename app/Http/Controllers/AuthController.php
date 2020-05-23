<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmMail;
use App\Mail\ResetPassword;
use App\MobileUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        #   $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function register(Request $request)
    {
        $validatedData = Validator::make($request->mobile_user, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', "string", "min:6"],
            #   'name' => ['required', "string"],
            'city' => ['required', "string"],

        ]);
        if ($validatedData->fails()) {
            return response([
                'errors_count' => count($validatedData->errors()),
                'msg' => "Не все поля заполнены корректно"
            ]);
        }

        $user = MobileUser::where("email", $request->email)->first();
        if ($user) {
            return response([
                'errors_count' => 1,
                'msg' => "Email уже используется в приложении"
            ]);
        }

        $data = $request->mobile_user;

        $user = new MobileUser();
        $user->name = $data['name'] ?? null;
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->sign_in_count = 0;
        $user->city = $data['city'];
        $user->is_active = true;

        $user->confirmation_sent_at = Carbon::now();
        $token = Str::random(20);
        $user->confirmation_token = $token;
        $user->save();

        Mail::to($user)->send(new ConfirmMail($token));

        return response([
            'errors_count' => 0,
            "data" => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'city' => $user->city,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if (!$request->mobile_user) {
            return response([
                'errors_count' => 1,
                'msg' => "Некорректный запрос"
            ]);
        }
        $validatedData = Validator::make($request->mobile_user, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', "string", "min:6"],

        ]);
        if ($validatedData->fails()) {
            return response([
                'errors_count' => count($validatedData->errors()),
                'msg' => "Не все поля заполнены корректно"
            ]);
        }
        $data = $request->mobile_user;

        if ($token = $this->guard()->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return $this->respondWithToken($token);
        }

        return response()->json([
            'errors_count' => 1,
            'msg' => "Неверные данные для входа"
        ]);
    }

    public function loginPartner(Request $request)
    {
        $validatedData = Validator::make($request->mobile_user, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', "string", "min:6"],

        ]);
        if ($validatedData->fails()) {
            return response([
                'errors_count' => count($validatedData->errors()),
                'msg' => "Не все поля заполнены корректно"
            ]);
        }
        $data = $request->mobile_user;
        if ($token = $this->guard("partner_api")->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return $this->respondWithToken($token, "partner_api");
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['errors_count' => 0]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $provider = null)
    {
        $user = $this->guard($provider)->user();
        return response()->json([
            'errors_count' => 0,

            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => $user->only(['id', 'name', 'email', 'created_at', 'updated_at', "city", 'is_active', 'is_operator']),
            'partner' => $user->partner
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard($provider = null)
    {
        return Auth::guard($provider);
    }

    public function resetPasswordLink(Request $request)
    {
        $validatedData = Validator::make($request->mobile_user, [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);
        if ($validatedData->fails()) {
            return response([
                'errors_count' => count($validatedData->errors()),
                'msg' => "Не все поля заполнены корректно"
            ]);
        }

        $user = MobileUser::where("email", $request->mobile_user['email'])->first();
        if (!$user) {
            return response([
                'errors_count' => 1,
                'msg' => "Пользователь с таким email не найден"
            ]);
        }
        $token = Str::random(20);
        $user->reset_password_token = Hash::make($token);
        $user->reset_password_sent_at = Carbon::now();
        $user->save();

        Mail::to($user)->send(new ResetPassword($user,$token));
        return response([
            'errors_count' => 0
        ]);
    }
}
