<!DOCTYPE html>
<html lang="en">
<?php
require_once('config.php');
session_start();

if (!isset($_SESSION['waterbillingsystem'])) {
    header("Location: login.php");
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION);
    header("Location: login.php");
}
?>
<?php
include('template/header.php');
?>

<div class="headerline">
    <h3>Tenancy Water Monthly Bill Statement</h3>
</div>
<div>
    <?php
    if (!isset($_POST)) {
        $billing_month = 0;
        $date_of_reading = 0;
        $current_reading = 0;
        $previous_reading = 100;
        $meter_rent = 50;
        $units_consumed = $current_reading - $previous_reading;
        $cost_per_unit = 150;
        $current_bill = ($units_consumed * $cost_per_unit) + $meter_rent;
        $bbd = 1000;
        $total_bill = $current_bill + $bbd;
    }
    ?>
</div>
<form action="index.php" method="post">
    <div class="container-fluid">
        <div class="aside-grid">
            <?php
            //$sql = "SELECT * FROM monthly_bill Limit 1 ";

            $sql = "SELECT * FROM consumers C INNER JOIN monthly_bill M ON C.house_number = M.house_number WHERE phonenumber = $_SESSION[phonenumber]";
            $stmtselect = $db->query($sql);

            if ($stmtselect->rowCount() > 0) {
                while ($monthly_bill = $stmtselect->fetch(PDO::FETCH_ASSOC)) {
                    echo '
                                <div class="aside" id="total_cost">
                                    <h4>Total Cost</h4>
                                    <h1 name="total_cost">
                                        ' . $monthly_bill['total_bill'] . '
                                    </h1>
                                </div>                                                
                                <div class="aside" id="current_bill">
                                    <h4>Current Bill</h4>
                                    <h1 name="current_bill">
                                        ' . $monthly_bill['current_bill'] . '
                                    </h1> 
                                </div>                             
                                <div class="aside" id="bbd">
                                    <h4>Balance From Previous Billing</h4>
                                    <h1 name="bbd">
                                    ' . $monthly_bill['bbd'] . '  
                                    </h1>
                                </div>
                                <div class="aside" id="current_reading">
                                    <h4>Current Reading</h4>
                                    <h1 name="current_reading">
                                        ' . $monthly_bill['current_reading'] . '
                                    </h1>
                                </div>
                                <div class="aside" id="previous_reading">
                                    <h4>Previous Reading</h4>
                                    <h1 name="previous_reading">
                                        ' . $monthly_bill['previous_reading'] . '
                                    </h1>
                                </div>
                                
                                <div class="aside" id="units_consumed">
                                    <h4>Units Consumed</h4>
                                    <h1 name="units_consumed">
                                        ' . $monthly_bill['units_consumed'] . '
                                    </h1>
                                </div>                                
                                 <div class="aside" id="date_of_reading">
                                    <h4>Date Of Reading</h4>
                                    <h1 name="date_of_reading">
                                        ' . $monthly_bill['date_of_reading'] . '
                                    </h1>
                                </div>
                                <div class="aside" id="billing_month">
                                    <h4>Billing Month</h4>
                                    <h1 name="billing_month">
                                        ' . $monthly_bill['billing_month'] . '
                                    </h1> 
                                </div>    
                                <div class="aside"  id="meter_rent">
                                    <h4>Meter Rent</h4>
                                    <h1 name="meter_rent">Ksh:
                                        ' . $monthly_bill['meter_rent'] . '
                                    </h1>
                                </div>
                                ';
                }
            }
            ?>
        </div>
    </div>
</form>
<div>
    <button id="send_bill" type="button" class="btn btn-info">Pay Bill</button>
</div>
<div class="footer">
    <h4 onclick="window.location.href='index.php'">CLASSIC HEIGHTS APARTMENTS</h4>
</div>
<div class="copyright">Lensmen Technologies @2022 </div>
<!-- Jquery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- sweetalert2 library -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</body>
</html>