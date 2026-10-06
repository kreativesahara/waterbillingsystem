<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require_once('config.php');

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
include('template/records_header.php');
?>

<div class="headerline">
    <h3>Tenancy Water Billing Records</h3>
</div>
<div class="container-fluid">
    <table class="table table-sm">
        <thead>
            <tr>
                <th id="bill_id">Bill ID</th>
                <th id="house_number">House Number</th>
                <th id="billing_month">Billing Month</th>
                <th id="date_of_reading">Date of Reading</th>
                <th id="current_reading">Current Reading</th>
                <th id="previous_reading">Previous Reading</th>
                <th id="units_consumed">Units Consumed</th>
                <th id="meter_rent">Meter Rent</th>
                <th id="current_bill">Current Billing</th>
                <th id="bbd">Balance Brought Down</th>
                <th id="total_bill">Total Bill</th>
            </tr>
        </thead>
        <tbody>
            <?php
            //Retrieving and Displaying content from the Database table.
            $sql = "SELECT * FROM master_bill ORDER BY bill_id DESc";
            $stmtselect = $db->query($sql);

            if ($stmtselect->rowCount() > 0) {
                while ($monthly_bill = $stmtselect->fetch(PDO::FETCH_ASSOC)) {
                    echo '
                                <tr>
                                    <td>' . $monthly_bill['bill_id'] . '</td>
                                    <td>' . $monthly_bill['house_number'] . '</td>
                                    <td>' . $monthly_bill['billing_month'] . '</td>
                                    <td>' . $monthly_bill['date_of_reading'] . '</td>
                                    <td>' . $monthly_bill['current_reading'] . '</td>
                                    <td>' . $monthly_bill['previous_reading'] . '</td>
                                    <td>' . $monthly_bill['units_consumed'] . '</td>
                                    <td>' . $monthly_bill['meter_rent'] . '</td>
                                    <td>' . $monthly_bill['current_bill'] . '</td>
                                    <td>' . $monthly_bill['bbd'] . '</td>
                                    <td>' . $monthly_bill['total_bill'] . '</td>
                                </tr>';
                }
            }
            ?>
        </tbody>
    </table>
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