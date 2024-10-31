<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProgramController;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;


// Authentication routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify_email', [AuthController::class, 'verifyEmail']);
Route::post('/reset_password', [AuthController::class, 'resetPassword']);

// Group routes
Route::post('/add_group', [GroupController::class, 'addGroup']);
Route::get('/get_group', [GroupController::class, 'getGroup']);

// Staff routes
Route::get('/get_staff', [StaffController::class, 'getStaff']);

// Program routes
Route::post('/add_program', [ProgramController::class, 'addProgram']);
Route::get('/get_program', [ProgramController::class, 'getProgram']);
Route::get('/get-all-programs', [ProgramController::class, 'getAllPrograms']);
Route::post('/update-program', [ProgramController::class, 'updateProgram']);
Route::delete('/delete-program', [ProgramController::class, 'deleteProgram']);

// Update and delete group name
Route::post('/update-group-name', [GroupController::class, 'updateGroupName']);
Route::delete('/delete-group-name', [GroupController::class, 'deleteGroupName']);
Route::get('/get-programs-for-today', [ProgramController::class, 'getProgramsForToday']);

// Routes protected by 'auth:api' middleware
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route::get('/get-programs-for-today', [ProgramController::class, 'getProgramsForToday']);
    Route::post('/update_user_group', [GroupController::class, 'updateUserGroup']);
});

// Test route (not protected)
Route::get('/test', [AuthController::class, 'test']);

//
Route::get('/testnotification', function () {

    // $fcm = "DEVICE_FCM_TOKEN";

    $title = "اشعار جديد";
    $description = "تيست تيست تيست";
    // $credentialsFilePath = public_path('json/file.json');
       $credentialsFilePath = "json/sit-app-4902c-8cb4fb5f8564.json";  // local
    // $credentialsFilePath = Http::get(asset('json/file.json')); // in server
    $client = new GoogleClient();
    $client->setAuthConfig($credentialsFilePath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $client->refreshTokenWithAssertion();
    $token = $client->getAccessToken();

    $access_token = $token['access_token'];

    $headers = [
        "Authorization: Bearer $access_token",
        'Content-Type: application/json'
    ];

    $data = [
        "message" => [
            "topic" => "allUsers",
            "notification" => [
                "title" => $title,
                "body" => $description,
            ],
        ]
    ];
    $payload = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/sit-app-4902c/messages:send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return response()->json([
            'message' => 'Curl Error: ' . $err
        ], 500);
    } else {
        return response()->json([
            'message' => 'Notification has been sent',
            'response' => json_decode($response, true)
        ]);
    }
});


