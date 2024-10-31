<?php

namespace App\Services;

use Google_Client;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    protected $client;
    protected $fcmUrl;
    protected $credentialsFilePath;

    public function __construct()
    {
        // إعداد Google Client
        $this->client = new Google_Client();
        $this->fcmUrl = 'https://fcm.googleapis.com/v1/projects/sit-app-4902c/messages:send';
        $this->credentialsFilePath = public_path('json/sit-app-4902c-8cb4fb5f8564.json');
    }

    /**
     *
     *
     * @param string $title
     * @param string $body
     * @return array
     */
    public function sendNotification($title, $body)
    {
        $this->client->setAuthConfig($this->credentialsFilePath);
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $this->client->refreshTokenWithAssertion();
        $token = $this->client->getAccessToken();

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
                    "body" => $body,
                ],
            ]
        ];
        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        // التعامل مع الاستجابة
        if ($err) {
            Log::error("Failed to send notification: " . $err);
            return [
                'success' => false,
                'message' => 'Curl Error: ' . $err
            ];
        } else {
            return [
                'success' => true,
                'response' => json_decode($response, true)
            ];
        }
    }
}
