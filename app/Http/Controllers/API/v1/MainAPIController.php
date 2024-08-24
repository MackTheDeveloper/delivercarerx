<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Partners;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MainAPIController extends Controller
{

    public function generateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => '400',
                'error' => 'Validation failed',
                'validation_errors' => $validator->errors(),
            ], 400);
        }

        $partner = Partners::where('username', $request->username)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$partner || !Hash::check($request->password, $partner->password)) {
            return response()->json([
                'status' => '500',
                'error' => 'Invalid credentials or partner not found',
            ], 500);
        }

        $token = Str::random(64);
        $expiryTime = now()->addHours(2);
        $partner->update(['token' => $token, 'expiry_at' => $expiryTime]);

        return response()->json([
            'status' => '200',
            'msg' => 'Token generated successfully',
            'token' => $token,
            'expiry_at' => $expiryTime->toDateTimeString(),
        ]);
    }


    public function validateToken($token)
    {
        if (empty($token)) {
            return false;
        }
        $partner = Partners::where('token', $token)->first();
        $valid = $partner && now() <= Carbon::parse($partner->expiry_at);
        return $valid ? true : false;
    }
}
