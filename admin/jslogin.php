<?php
session_start();
require_once('config.php');

  $phonenumber = $_POST['phonenumber'];
  $newpassword = $_POST['newpassword'];

  
  $sql = "SELECT * FROM admins WHERE phonenumber = ? AND newpassword = ? LIMIT 1 ";
  $stmtselect = $db->prepare($sql);
  $result = $stmtselect->execute([$phonenumber, $newpassword]);

   if($result){
      $login = $stmtselect->fetch(PDO::FETCH_ASSOC);
      if($stmtselect ->rowCount() > 0){
          $_SESSION['waterbillingsystem'] = $login;
          echo'Log in Successfully';        
      }else {
          echo 'Log in Failed!';
      }
    }
    else {
      echo 'There was errors while connecting to the database ';
    }
?>