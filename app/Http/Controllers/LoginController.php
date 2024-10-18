<?php

namespace App\Http\Controllers;

use App\Jobs\EmailJob;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function store(Request $request){
        try{
            DB::beginTransaction();

            $request->validate([
                "email" => [
                    "required",
                    "unique:users,email"
                ],
                "password" => "required"
            ],[
                "email.unique" => "Email already taken"
            ]);

            $data = User::create([
                "name" => 'Backend',
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "email_verified_at" => Carbon::now(),
                "remember_token" => null
            ]);

            $emailData = [
                "email" => $request->email,
            ];

            dispatch(new EmailJob($emailData));

            DB::commit();

            return response()->json(["message" => "User Successfully Registered"], 201);

        }catch(Exception $e){
            DB::rollback();

            return response()->json(["message" => $e->getMessage()],400);
            throw $e;
        }
    }

    public function login(Request $request){
        try{
            $request->validate([
                "email" => "required",
                "password" => "required"
            ]);

            $credentials = $request->only('email', 'password');

            $key = 'login_key.' . $request->email . '.' . $request->ip();

            if (RateLimiter::tooManyAttempts($key, 5)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json(['message' => 'Too many attempts. Please try again in ' . $seconds . ' seconds.'], 401);
            }

            $access_token = JWTAuth::attempt($credentials);

            try {
                if (!$access_token) {
                    RateLimiter::hit($key, 300);
                    return response()->json(["message" => "Invalid Credentials"], 401);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            RateLimiter::clear($key);

            return response()->json(['access_token'=>$access_token], 201);

        }catch(Exception $e){
            return response()->json(['message'=>$e->getMessage()]);
        }
    }
}
