<?php
session_start();
require_once "../google-api/autoload.php";

// Tạo URL đăng nhập Google
$params = [
    "client_id" => GOOGLE_CLIENT_ID,
    "redirect_uri" => GOOGLE_REDIRECT_URI,
    "response_type" => "code",
    "scope" => "email profile",
    "access_type" => "offline",
    "prompt" => "consent"
];

$googleAuthUrl = "https://accounts.google.com/o/oauth2/auth?" . http_build_query($params);

// Chuyển hướng đến Google Login
header("Location: " . $googleAuthUrl);
exit();
