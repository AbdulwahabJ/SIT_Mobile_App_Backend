<?php

// في ملف app/Http/Controllers/StaffController.php
namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{

    public function getStaff()
    {
        try {
            $staffData = Staff::select('image', 'name', 'languages', 'phone_number')->get();

            if ($staffData->isEmpty()) {
                return response()->json(['message' => 'No Staff Found'],404);
            }

            $staffData->transform(function ($staff) {
                //
                if ($staff->image) {
                    $staff->image = asset('storage/' . $staff->image);
                    //
                }
                return $staff;
            });

            return response()->json(['staff_data' => $staffData], 200);
            //
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }


    // public function addStaff(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:staff',
    //         'password' => 'required|string|min:6',
    //         'phone_number' => 'required|string',
    //         'languages' => 'required|string',
    //         'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    //     ]);

    //     $data = $request->all();
    //     $data['password'] = Hash::make($data['password']);

    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('staff_images', 'public');
    //         $data['image'] = $imagePath;
    //     }

    //     $staff = Staff::create($data);

    //     return response()->json([
    //         'message' => 'Staff added successfully.',
    //         'staff' => [
    //             'name' => $staff->name,
    //             'email' => $staff->email,
    //             'phone_number' => $staff->phone_number,
    //             'languages' => $staff->languages,
    //             'image' => $staff->image,
    //         ],
    //     ], 201);
    // }

}
