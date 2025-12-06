<?php
// File: google-api/autoload.php

if(!isset($_SESSION)) {
    session_start();
}

// Đọc file .env
$envPath = __DIR__ . '/../key/.env';
if (!file_exists($envPath)) {
    die("Không tìm thấy file .env");
}

$env = parse_ini_file($envPath);

// Tạo hằng số
define("GOOGLE_CLIENT_ID", $env["GOOGLE_CLIENT_ID"]);
define("GOOGLE_CLIENT_SECRET", $env["GOOGLE_CLIENT_SECRET"]);
define("GOOGLE_REDIRECT_URI", $env["GOOGLE_REDIRECT_URI"]);

/**
 * Lấy token từ Google bằng code
 */
function getGoogleToken($code) {
    $url = "https://oauth2.googleapis.com/token";

    $data = [
        "code" => $code,
        "client_id" => GOOGLE_CLIENT_ID,
        "client_secret" => GOOGLE_CLIENT_SECRET,
        "redirect_uri" => GOOGLE_REDIRECT_URI,
        "grant_type" => "authorization_code"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if ($response === false) {
        die("Lỗi CURL: " . curl_error($ch));
    }
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * Lấy thông tin user Google từ access_token
 */
function getGoogleUser($access_token) {
    $ch = curl_init("https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $access_token);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if ($response === false) {
        die("Lỗi CURL khi lấy user: " . curl_error($ch));
    }
    curl_close($ch);

    return json_decode($response, true);
}
