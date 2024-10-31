<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;  // Import TokenExpiredException
use Tymon\JWTAuth\Exceptions\TokenInvalidException;  // Import TokenInvalidException
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addGroup(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $isGroupExsit = Group::where('name', $request->name)->first();
            // dd($isGroupExsit);
            if ($isGroupExsit) {
                return response()->json([
                    'message' => 'Group already exist'
                ], 500);
            }

            $group = Group::create($data);

            return response()->json([
                'message' => 'Group added successfully.',
                'group' => [
                    'name' => $group->name,
                ],
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to add group.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getGroup(): JsonResponse
    {
        try {
            $allGroups = [];
            $groups = Group::select('name')->get();
            foreach ($groups as $group) {
                $allGroups[] = $group->name;
            }
            // dd($allGroups);

            return response()->json([
                'groups' =>
                    $allGroups,

            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to get groups.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateUserGroup(Request $request)
    {
         if (!$user=JWTAuth::parseToken()->authenticate()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            $group = Group::where('name', $request->group_name)->first();

            if (!$group) {
                return response()->json(['message' => 'Group not found'], 404);
            }

            $user->group_id = $group->id;
            $user->save();

            return response()->json([
                'message' => 'Group updated successfully.',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'group_id' => $group->name,  // استخدام اسم المجموعة الجديد
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the group.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function updateGroupName(Request $request)
    {

        // التحقق من صحة التوكن والحصول على المستخدم
        if (!JWTAuth::parseToken()->authenticate()) {
            return response()->json(['message' => 'User not found'], 404);
        }
        // 'old_group_name'
// 'new_group_name'

        try {
            $group = Group::where('name', $request->old_group_name)->first();

            // تحديث group_id للمستخدم
            $group->name = $request->new_group_name;
            $group->save();

            return response()->json([
                'message' => 'Group Name updated successfully.',
                [
                    'id' => $group->id,
                    'oldname' => $request->old_group_name,
                    'newName' => $group->name,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the group.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function deleteGroupName(Request $request)
    {

        // if (!JWTAuth::parseToken()->authenticate()) {
        //     return response()->json(['message' => 'User not found'], 404);
        // }

        try {
            $isGroupExist = Group::where('name', $request->name)->first();
            // dd($isGroupExsit);

            if ($isGroupExist) {
                //
                User::where('group_id', $isGroupExist->id)->update(['group_id' => null]);

                $isGroupExist->delete();
                return response()->json([
                    'message' => 'Group deleted successfully'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Group not found'
                ], 404);
            }

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the group.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
