<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Group;
use App\Models\Program;


class ProgramController extends Controller
{
    public function addProgram(Request $request)
    {

        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['message' => 'User not found'], 404);
        }
        // dd($request->all());
        if ($user->role == 'admin') {

            try {
                $Group = Group::where('name', $request->group_name)->first();
                // dd($Group->programs);
                Program::create([
                    'name' => $request->name,
                    'group_id' => $Group->id,
                    'date' => $request->date,
                    'time' => $request->time,
                ]);

                return response()->json([
                    'message' => 'Program added successfully.',
                ], 200);


            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Failed to add Program.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'unauthorized user',
            ], 401);
        }
    }

    public function getProgram(Request $request)
    {

        // dd($request->all());

        if (!JWTAuth::parseToken()->authenticate()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            $group = Group::where('name', $request->group_name)->first();
            // dd($group->id);
            $program = Program::select('name', 'date', 'time')
                ->where('name', $request->name)
                ->where('group_id', $group->id)
                ->first();

            return response()->json(
                [
                    'data' => [
                        $program->name,
                        $program->date,
                        $program->time,
                    ],
                ],
                200
            );


        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to get Program.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }
}
