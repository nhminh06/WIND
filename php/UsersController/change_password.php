<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    include '../../db/db.php';
    $user_id = $_GET['id'];
    $current_password = $_POST['pwht']; 
    $new_password = $_POST['pwmoi'];
    $confirm_password = $_POST['xn_pwmoi'];

    $sql = "SELECT password FROM user WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashed_password = $row['password'];

        if($current_password === $hashed_password){
            if($new_password === $confirm_password){
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE user SET password = '$hash' WHERE id = $user_id";
                if(mysqli_query($conn, $update_sql)){
                    $_SESSION['thanhcong'] = 1;
                    if($_SESSION['role'] == 'admin'){
                        header("Location: ../../html/Admin/Adminacc.php");
                        exit();
                    } else {
                        header("Location: ../../html/views/user/users.php");
                        exit();

                    }
                   
                } else {
                    $_SESSION['thanhcong'] = 0;
                    if($_SESSION['role'] == 'admin'){
                        header("Location: ../../html/Admin/Adminacc.php");
                        exit();
                    } else {
                        header("Location: ../../html/views/user/users.php");
                        exit();

                    }
                }
            } else {
                $_SESSION['thanhcong'] = 0;
                if($_SESSION['role'] == 'admin'){
                    header("Location: ../../html/Admin/Adminacc.php");
                    exit();
                } else {
                    header("Location: ../../html/views/user/users.php");
                    exit();
                }
            }
        } else {
            $_SESSION['thanhcong'] = 0;
            if($_SESSION['role'] == 'admin'){
                header("Location: ../../html/Admin/Adminacc.php");
                exit();
            } else {
                header("Location: ../../html/views/user/users.php");
                exit();
        }


    }
    }
}
?>