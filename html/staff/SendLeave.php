<?php
session_start();
include('../../db/db.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $_SESSION['id'];
    $leave_type = trim($_POST['leave_type']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = trim($_POST['reason']);
    
    // Validation
    if (empty($leave_type) || empty($start_date) || empty($end_date) || empty($reason)) {
        $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
        header("Location: LeaveRequest.php");
        exit();
    }
    
    // Check dates
    if (strtotime($end_date) < strtotime($start_date)) {
        $_SESSION['error'] = 'Ngày kết thúc phải sau ngày bắt đầu!';
        header("Location: LeaveRequest.php");
        exit();
    }
    
    // Insert into database
    $sql = "INSERT INTO leave_requests (staff_id, leave_type, start_date, end_date, reason, status, request_date) 
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $staff_id, $leave_type, $start_date, $end_date, $reason);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = '✅ Gửi đơn xin nghỉ thành công! Đang chờ phê duyệt.';
    } else {
        $_SESSION['error'] = '❌ Có lỗi xảy ra. Vui lòng thử lại!';
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: LeaveRequest.php");
    exit();
} else {
    header("Location: LeaveRequest.php");
    exit();
}
?>