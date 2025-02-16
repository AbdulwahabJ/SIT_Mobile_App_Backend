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

        // dd($user->name);
        if ($user) {
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid password'], 401);
            }

            $token = JWTAuth::fromUser($user);
            if (!$token) {
                return response()->json(['message' => 'Could not create token'], 500);
            }

            if ($user->image) {
                $user->image = asset('storage/' . $user->image);
                //
            }
            return response()->json([
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'group_id' => $user->group->name ?? null,
                    'role' => $user->role,
                    'languages' => $user->languages,
                    'image' => $user->image,
                    'token' => $token,
                ],
                'message' => 'User logged in successfully.'
            ], 200);
        }

        return response()->json(['message' => 'Invalid email'], 401);



    }


    public function register(Request $request)
    {
        // dd(vars: $request->all());
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json(['message' => 'Email is already in use'], 400);
        }

        $group_id = null;
        if ($request->has('group_id')) {
            $group = Group::where('name', $request->group_id)->first();
            if ($group) {
                $group_id = $group->id;
            }
        }

        if ($request->hasFile('image')) {

            $imagePath = $request->file('image')->store('staff_images', 'public');
            $request->image = $imagePath;
        }



        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'group_id' => $group_id ?? null,
            'role' => $request->role,
            "image" => $request->image ?? null,
            'languages' => $request->languages ?? null,
        ]);

        // $token = JWTAuth::fromUser($user);



        return response()->json([
            'message' => 'User registered successfully.',
        ], 201);

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
            //
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while resetting the password. Please try again later.'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // if (!$token = JWTAuth::getToken()) {
            //     return response()->json(['message' => 'Token not provided'], 400);
            // }
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['message' => 'Token not provided'], 400);
            }

            // JWTAuth::invalidate($token);
            JWTAuth::setToken($token)->invalidate();

            return response()->json([
                'message' => 'Successfully logged out'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while logging out.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function test()
    {
        return 'success';
    }

    public function addgroup()
    {
        return Group::create(['name' => "S100"]);
    }
}
