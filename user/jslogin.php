<?php
require_once('config.php');
session_start();
$_SESSION['phonenumber'] = $_POST['phonenumber'];

if (isset($_SESSION['waterbillingsystem'])) {    
    header("Location: index.php");

}
  $phonenumber = $_POST['phonenumber'];
  $newpassword = $_POST['newpassword'];

  $sql = "SELECT * FROM consumers C INNER JOIN monthly_bill M ON C.house_number = M.house_number WHERE phonenumber = ? AND newpassword = ? LIMIT 1 ";
  //$sql = "SELECT * FROM consumers  WHERE phonenumber = ? AND newpassword = ? LIMIT 1 ";
  $stmtselect = $db->prepare($sql);
  $result = $stmtselect->execute([$phonenumber, $newpassword]);

   if($result){
      $login_user = $stmtselect->fetch(PDO::FETCH_ASSOC);
      if($stmtselect ->rowCount() > 0){
        $_SESSION['waterbillingsystem'] = $login_user;
        echo'Log in Successfully';
      }else {
        echo 'There no user for that combination';
      }
   }else {
    echo 'There was errors while connecting to the database ';
   }

?>