<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

// Đọc file .env
$env = parse_ini_file(__DIR__ . "/../../../key/.env");
$apiKey = $env["GEMINI_KEY"] ?? "";

// Đọc message từ phía client
$input = json_decode(file_get_contents("php://input"), true);
$userMessage = $input["message"] ?? "";

if (!$userMessage) {
    echo json_encode(["reply" => "Tin nhắn trống"]);
    exit;
}

if (!$apiKey) {
    echo json_encode(["reply" => "Lỗi: Không tìm thấy API KEY"]);
    exit;
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=$apiKey";


$data = [
    "contents" => [
        [
            "parts" => [
                [
                    "text" => "Bạn là chuyên gia du lịch. Trả lời tự nhiên, thân thiện. Câu hỏi: $userMessage"
                ]
            ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);
curl_close($ch);

$res = json_decode($response, true);
$reply = $res["candidates"][0]["content"]["parts"][0]["text"] 
    ?? "Xin lỗi, mình chưa hiểu. Bạn hỏi lại nhé!";

echo json_encode(["reply" => $reply], JSON_UNESCAPED_UNICODE);