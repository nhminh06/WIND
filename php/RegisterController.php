<?php session_start() ?>
<?php include '../db/db.php'; ?>
<?php
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $USERNAME = $_POST['username'];
    $EMAIL = $_POST['email'];
    $PASSWORD = $_POST['password'];
    $role = 'user';

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$USERNAME', '$EMAIL', '$PASSWORD', '$role')";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['username'] = $USERNAME;
        echo "<script>window.location.href = '../html/views/WebIndex.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>