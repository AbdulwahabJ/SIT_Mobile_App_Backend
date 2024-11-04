<?php

namespace App\Http\Controllers;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Group;
use App\Models\Program;
use Carbon\Carbon;


class ProgramController extends Controller
{
    public function addProgram(Request $request, NotificationService $notificationService)
    {

        // if (!$user = JWTAuth::parseToken()->authenticate()) {
        //     return response()->json(['message' => 'User not found'], 404);
        // }
        // dd($request->all());
        // if ($user->role == 'admin') {

        try {
            $Group = Group::where('name', $request->group_name)->first();
            // dd($Group->programs);
            Program::create([
                'name' => $request->name,
                'group_id' => $Group->id,
                'date' => $request->date,
                'time' => $request->time,
            ]);

            $title = "check out! new {$request->name} ";
            $body = " date {$request->date} ";

            $notificationSent = $notificationService->sendNotification($title, $body);


            return response()->json([
                'message' => 'Program added successfully.',
                'notification_sent' => $notificationSent ? 'success' : 'failed',

            ], 200);


        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to add Program.',
                'error' => $e->getMessage(),
            ], 500);
        }
        // } else {
        //     return response()->json([
        //         'message' => 'unauthorized user',
        //     ], 401);
        // }
    }

    public function getProgram(Request $request)
    {

        // dd($request->all());

        // if (!JWTAuth::parseToken()->authenticate()) {
        //     return response()->json(['message' => 'User not found'], 404);
        // }

        try {
            $group = Group::where('name', $request->group_name)->first();
            // dd($group->id);
            $program = Program::select('name', 'date', 'time')
                ->where('id', $request->name)
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


    public function getAllPrograms(Request $request)
    {

        // dd($request->all());

        // if (!JWTAuth::parseToken()->authenticate()) {
        //     return response()->json(['message' => 'User not found'], 404);
        // }

        try {
            $group = Group::where('name', $request->group_name)->first();
            // dd($group->id);
            $programs = Program::select('id', 'name', 'date', 'time')
                ->where('group_id', $group->id)
                ->get()
                ->map(function ($program) {
                    // $randomString = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 5)), 0, 5);
                    $updatedProgram = $program->name . ' - ' . $program->date . ' - ' . $program->time;
                    return [

                        'id' => (String) $program->id,
                        'displayText' => $updatedProgram,
                        'name' => $program->name,
                        'date' => $program->date,
                        'time' => $program->time,

                    ];
                });

            return response()->json(
                [
                    'data' =>
                        $programs,

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


    public function updateProgram(Request $request)
    {
        // dd($request->all());
        try {
            $program = Program::find($request->program_id);

            $program->name = $request->updated_program_name;
            $program->date = $request->updated_program_date ?? $program->date;
            $program->time = $request->updated_program_time ?? $program->time;
            $program->save();

            return response()->json([
                'message' => 'program  updated successfully.',

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the program.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteProgram(Request $request)
    {

        // if (!JWTAuth::parseToken()->authenticate()) {
        //     return response()->json(['message' => 'User not found'], 404);
        // }

        try {
            $isProgramExist = Program::where('id', $request->id)->first();
            // dd($isGroupExsit);

            if ($isProgramExist) {
                //

                $isProgramExist->delete();
                //
                return response()->json([
                    'message' => 'Program deleted successfully'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Program not found'
                ], 404);
            }

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the Program.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getProgramsForToday(Request $request)
    {

        // dd($request->all());

        // if (!JWTAuth::parseToken()->authenticate()) {
        //     return response()->json(['message' => 'User not found'], 404);
        // }

        try {
            if ($request->group_name != null) {
                $group = Group::where('name', $request->group_name)->first();

                $programs = Program::select('name', 'date', 'time')
                    ->where('group_id', $group->id)
                    ->get()
                    ->map(function ($program) {
                        //
                        $programDateTime = Carbon::parse($program->date . ' ' . $program->time)
                            ->format('l dM g:ia'); // صيغة 'Monday 08Aug 3:45pm'
                        //
                        return [
                            'program_name' => $program->name,
                            'program_dateTime' => $programDateTime,
                        ];
                    });
            }
            $programs = Program::select('name', 'date', 'time')
                ->get()
                ->map(function ($program) {
                    //
                    $programDateTime = Carbon::parse($program->date . ' ' . $program->time)
                        ->format('l dM g:ia'); // صيغة 'Monday 08Aug 3:45pm'
                    //
                    return [
                        'program_name' => $program->name,
                        'program_dateTime' => $programDateTime,
                    ];
                });

            return response()->json(
                [
                    'data' => $programs,
                ],
                200
            );

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to get Program',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

}



