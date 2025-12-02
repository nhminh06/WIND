<?php
$servername = "127.0.0.1"; 
$username   = "root";
$password   = "";         
$dbname     = "wind";     
$port       = 3306;       
// $port       = 3306;       

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// if ($conn->connect_error) {
//     die("❌ Kết nối thất bại: " . $conn->connect_error);
// } else {
//     echo "✅ Kết nối thành công tới database '$dbname'";
// }
?>
