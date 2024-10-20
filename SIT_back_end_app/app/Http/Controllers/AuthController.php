<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Staff;

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

        $staff = Staff::where('email', $request->email)->first();
        $user = User::where('email', $request->email)->first();

        if ($staff) {
            if (!Hash::check($request->password, $staff->password)) {
                return response()->json(['message' => 'Invalid password'], 401);
            }

            $token = JWTAuth::fromUser($staff);
            if (!$token) {
                return response()->json(['message' => 'Could not create token'], 500);
            }

            return response()->json([
                'message' => 'Staff logged successfully.',
                'staff' => [
                    'name' => $staff->name,
                    'email' => $staff->email,
                    'phone_number' => $staff->phone_number,
                    'languages' => $staff->languages,
                    'image' => $staff->image,
                    'role' => $staff->role,
                    'token' => $token,


                ],
            ], 200);
        }

        if ($user) {
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid password'], 401);
            }

            $token = JWTAuth::fromUser($user);
            if (!$token) {
                return response()->json(['message' => 'Could not create token'], 500);
            }

            return response()->json([
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'group_id' => $user->group_id,
                    'role' => $user->role,
                    'token' => $token,
                ],
                'message' => 'User logged in successfully.'
            ], 200);
        }

        return response()->json(['message' => 'Invalid email'], 401);
    }


    public function register(Request $request)
    {
        //staff
        if ($request->has('role') && $request->role === 'staff') {

            $existingStaff = Staff::where('email', $request->email)->first();
            if ($existingStaff) {
                return response()->json(['message' => 'Email is already in use'], 400);
            }

            $data = $request->all();
            $data['password'] = Hash::make($data['password']);

            if ($request->hasFile('image')) {
                // $request->validate([
                //     'image' => 'image|mimes:jpg,jpeg,png,gif|max:2048',
                // ]);
                $imagePath = $request->file('image')->store('staff_images', 'public');
                $data['image'] = $imagePath;
            }

            $staff = Staff::create($data);
        }

        //user
        if ($request->has('role') && $request->role === 'user') {

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

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'group_id' => $group_id,
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'User registered successfully.',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $token,
                    'phone_number' => $user->phone_number,
                    'group_id' => $group_id ? $group->name : null,
                    'role' => 'user',
                ],
            ]);
        }
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
            if (!$token = JWTAuth::getToken()) {
                return response()->json(['message' => 'Token not provided'], 400);
            }


            JWTAuth::invalidate($token);

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

    public function logoutStaff(Request $request)
    {
        try {
            if (!$token = JWTAuth::getToken()) {
                return response()->json(['message' => 'Token not provided'], 400);
            }

            JWTAuth::invalidate($token);

            return response()->json([
                'message' => 'Successfully logged out'
            ], 200);
            //
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
        return  Group::create(['name' => "S300"]);
    }
}
