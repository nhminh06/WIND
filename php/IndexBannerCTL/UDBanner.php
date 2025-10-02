<?php include '../../db/db.php'; ?>
<?php 
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $mota = $_POST['mota'];
    $anh1 = $_POST['anh1'];
    $anh2 = $_POST['anh2'];
  
  
    
    header("Location: ../../html/Admin/IndexController.php");
    exit();
}
?>