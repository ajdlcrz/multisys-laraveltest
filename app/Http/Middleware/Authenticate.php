<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User; // Make sure to include your User model

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        // VERIFY IF BEARER TOKEN
        if (!$authHeader || !preg_match('/^Bearer\s(\S+)$/', $authHeader, $matches)) {
            return response()->json(['error' => 'Unauthorized Access'], 401);
        }

        $token = $matches[1];

        //VALIDATING IF TOKEN IS VALID
        try {
            $decoded = JWTAuth::setToken($token)->getPayload();

            $id = $decoded->get('sub');
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'No user found with this token'], 404);
            }

        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is not valid'], 401);
        }

        return $next($request);
    }
}
