<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid email'], 401);
        }
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        $token = JWTAuth::attempt($request->only('email', 'password'));
        if (!$token) {
            return response()->json(['message' => 'Could not create token'], 500);
        }


        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'group_id' => $user->group_id,
                'token' => $token,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            return response()->json([
                'message' => 'unique email'
            ], 400);
        }

        $group_id = null;
        if ($request->has('group_id')) {
            $group = Group::where('name', $request->group_id)->first();

            if ($group) {
                $group_id = $group->id;
            }
            // dd($group);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'group_id' => $group_id,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'token' => $token,
                'phone_number' => $user->phone_number,
                'group_id' => $group_id ? $group->name : null,
            ],
        ]);
    }



    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->exists();

        if ($user) {
            return response()->json(['message' => 'true'], 200);
        }

        return response()->json(['message' => 'email not exist'], 404);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['message' => 'Password has been reset successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while resetting the password. Please try again later.'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {

            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message' => 'Successfully logged out'
            ], 200);
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'message' => 'Token is already invalid'
            ], 400);
        }
        catch(Exception $e){  return response()->json([
            'message' => $e
        ], 500);}
    }

    public function test()
    {
        return 'success';
    }

    public function addgroup()
    {
        return  Group::create(['name' => "S300"]);
    }
}
