<?php

$pixel_id = "829693206440069";
$access_token = "EAAQqOuf3DIsBQDT4CAj4bhUqwvmpYlevy5OASZBWA8uS9M9uuGLXbldvB7nyggBwNPHhifgARjS3AT5ebQDP4do7VPH4t31GK8lF..."; // FULL token

function sendFacebookEvent($event_name, $event_data = []) {
    global $pixel_id, $access_token;

    $url = "https://graph.facebook.com/v18.0/$pixel_id/events?access_token=$access_token";

    $payload = [
        "data" => [[
            "event_name" => $event_name,
            "event_time" => time(),
            "action_source" => "website",
            "event_source_url" => "https://yourwebsite.com",
            "user_data" => [
                "client_ip_address" => $_SERVER['REMOTE_ADDR'],
                "client_user_agent" => $_SERVER['HTTP_USER_AGENT']
            ],
            "custom_data" => $event_data
        ]]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
