<?php
session_start();
include '../../db/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Thư mục upload
    $uploadDir = "../../uploads/";
    
    // Tạo thư mục nếu chưa tồn tại
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $tieu_de = mysqli_real_escape_string($conn, $_POST['tieu_de']);
    $mo_ta = mysqli_real_escape_string($conn, $_POST['mo_ta']);
    $thu_tu = mysqli_real_escape_string($conn, $_POST['thu_tu']);
    
    $hinh_anh = "";
    
    // Lấy ảnh cũ nếu có ID
    if (!empty($id)) {
        $result = mysqli_query($conn, "SELECT hinh_anh FROM banner_slider WHERE id='$id'");
        if ($row = mysqli_fetch_assoc($result)) {
            $hinh_anh = $row['hinh_anh'];
        }
    }
    
    // Upload ảnh mới nếu có
    if (isset($_FILES['hinh_anh']) && !empty($_FILES['hinh_anh']['name']) && $_FILES['hinh_anh']['error'] == 0) {
        
        $file = $_FILES['hinh_anh'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $new_filename = "banner_slide_" . $thu_tu . "_" . time() . "." . $file_extension;
        
        // Đường dẫn vật lý để lưu file
        $target_path = $uploadDir . $new_filename;
        
        // Đường dẫn tương đối để lưu vào database
        $db_path = "uploads/" . $new_filename;
        
        // Kiểm tra định dạng file
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        
        if (in_array($file_extension, $allowed_types)) {
            
            // Kiểm tra kích thước (max 5MB)
            if ($file['size'] <= 5000000) {
                
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    // Upload thành công - xóa ảnh cũ
                    if (!empty($hinh_anh) && file_exists("../../" . $hinh_anh)) {
                        unlink("../../" . $hinh_anh);
                    }
                    $hinh_anh = $db_path;
                    
                } else {
                    echo "<script>
                        window.history.back();
                    </script>";
                    exit;
                }
                
            } else {
                echo "<script>
                    window.history.back();
                </script>";
                exit;
            }
            
        } else {
            echo "<script>
                window.history.back();
            </script>";
            exit;
        }
    }
    
    // Nếu có ID → UPDATE
    if (!empty($id)) {
        
        $sql = "UPDATE banner_slider SET 
                    tieu_de = '$tieu_de',
                    mo_ta = '$mo_ta',
                    hinh_anh = '$hinh_anh',
                    thu_tu = '$thu_tu'
                WHERE id = '$id'";
        
    } else {
        // INSERT (tạo mới)
        if (empty($hinh_anh)) {
            echo "<script>
                window.history.back();
            </script>";
            exit;
        }
        
        $sql = "INSERT INTO banner_slider (tieu_de, mo_ta, hinh_anh, thu_tu) 
                VALUES ('$tieu_de', '$mo_ta', '$hinh_anh', '$thu_tu')";
    }
    
    // Chạy SQL
    if (mysqli_query($conn, $sql)) {
        echo "<script>
            window.location.href = '../../html/Admin/IndexController.php';
        </script>";
        exit();
    } else {
        echo "<script>
            window.history.back();
        </script>";
        exit();
    }
    
} else {
    header("Location: ../../html/Admin/IndexController.php");
    exit;
}

mysqli_close($conn);
?>