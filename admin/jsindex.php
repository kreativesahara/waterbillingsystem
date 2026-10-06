<?php
session_start();
    require_once('config.php');
            $house_number = $_POST['house_number'];
            $billing_month = $_POST['billing_month'];
            $date_of_reading = $_POST['date_of_reading'];
            $current_reading = $_POST['current_reading'];
            $previous_reading = 100;
            $meter_rent = 50;
            $units_consumed = $current_reading - $previous_reading;
            $cost_per_unit = 150;
            $current_bill = ($units_consumed * $cost_per_unit) + $meter_rent;
            $bbd = 1000;
            $total_bill = $current_bill + $bbd;

            $sql = "INSERT INTO monthly_bill(house_number,billing_month,date_of_reading,current_reading,previous_reading,meter_rent,units_consumed,current_bill,bbd,total_bill) VALUES(?,?,?,?,?,?,?,?,?,?)";
            $sql2 = "INSERT INTO master_bill(house_number,billing_month,date_of_reading,current_reading,previous_reading,meter_rent,units_consumed,current_bill,bbd,total_bill) VALUES(?,?,?,?,?,?,?,?,?,?)";
            $stmtinsert = $db->prepare($sql);
            $stmtinsert2 = $db->prepare($sql2);
            $result = $stmtinsert->execute([$house_number, $billing_month, $date_of_reading, $current_reading, $previous_reading, $meter_rent, $units_consumed, $current_bill, $bbd, $total_bill]);
            $result2 = $stmtinsert2->execute([$house_number, $billing_month, $date_of_reading, $current_reading, $previous_reading, $meter_rent, $units_consumed, $current_bill, $bbd, $total_bill]);

            if ($result) {
                $index= $stmtinsert->fetch(PDO::FETCH_ASSOC);
                if ($stmtinsert->rowCount() > 0) {
                    $_SESSION['waterbillingsystem'] = $index;
                    echo 'Bill Processed Successfully';
                } else {
                    echo 'Error: Data Duplication Detected';
                }
            }        
            else {
                echo 'There was errors while connecting to the database ';
            }
?>