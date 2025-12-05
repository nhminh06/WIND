<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

$input = json_decode(file_get_contents("php://input"), true);
$userMessage = $input["message"] ?? "";

if (!$userMessage) {
    echo json_encode(["reply" => "Tin nhắn trống"]);
    exit;
}

$apiKey = "AIzaSyDMeZQqLXIHuE-qzLmrJrNhkFpRUw6bd2c";

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$apiKey";

$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $userMessage]
            ]
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$json = json_decode($response, true);

$reply = $json["candidates"][0]["content"]["parts"][0]["text"] ?? "Bot không hiểu.";

echo json_encode(["reply" => $reply], JSON_UNESCAPED_UNICODE);
?>