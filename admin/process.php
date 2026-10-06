<?php
    require_once('config.php');
        //Declare and input data.
        $house_number = $_POST['house_number'];
        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];
        $email = $_POST["email"];
        $phonenumber = $_POST["phonenumber"];
        $national_id = $_POST["national_id"];
        $newpassword = $_POST["newpassword"];
        $confirmpassword = $newpassword;

        //push data in a mysql table database.
        $sql= "INSERT INTO consumers(house_number,firstname,lastname,email,phonenumber,national_id,newpassword,confirmpassword) VALUES(?,?,?,?,?,?,?,?)";
        $stmtinsert = $db->prepare($sql);
        $result = $stmtinsert->execute([$house_number,$firstname,$lastname,$email,$phonenumber, $national_id,$newpassword,$confirmpassword]);

        //callout process to the viewer.
        if ($result) {
            $registration = $stmtinsert->fetch(PDO::FETCH_ASSOC);
            if ($stmtinsert->rowCount() > 0) {
                $_SESSION['waterbillingsystem'] = $registration;
                echo 'Registration Successfully';
            } else {
                echo 'The Form is Blank!';
            }          
        } 
        else{
            echo 'There was errors while connecting to the database ';
        }
?>