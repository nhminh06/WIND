<?php
include '../../db/db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $khampha_id = intval($_POST['khampha_id']);
    $user_id = intval($_POST['user_id']);
    $comment = $_POST['comment'];
    $ngay_tao = date('Y-m-d H:i:s');
    if ($user_id == 0) {
        header("Location: ../../html/views/index/note.php");
        exit();
    }

    $sql = "INSERT INTO binh_luan (khampha_id, user_id, noi_dung, ngay_tao) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $khampha_id, $user_id, $comment, $ngay_tao);

    if ($stmt->execute() === TRUE) {
        header("Location: ../../html/views/index/detailed_explore.php?id=" . $khampha_id);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>