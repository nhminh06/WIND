<?php
include '../../db/db.php';
$comment_id = $_GET['id'];
$khampha_id = $_GET['khampha_id'];

$sql = "DELETE FROM binh_luan WHERE id = ? AND khampha_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $comment_id, $khampha_id);
$stmt->execute();

header("Location: ../../../html/views/index/detailed_explore.php?id=" . $khampha_id);
exit();
?>