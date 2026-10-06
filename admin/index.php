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
    include('template/header.php');
?>
<div class="headerline">
    <h3>Tenancy Water Monthly Biling Form</h3>
</div>
</div>
<div>
    <?php
    if (!isset($_POST["populate_bill"])) {
        $current_reading = 0;
        $previous_reading = 0;
        $meter_rent = 0;
        $units_consumed = 0;
        $current_bill = 0;
        $bbd = 0;
        $total_bill = 0;
    } else if (isset($_POST["populate_bill"])) {
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
    }
    ?>
</div>
<form action="index.php" method="post">
    <div class="container-fluid">
        <div class="form-grid">
            <label for="house">Choose House Number:</label>
            <select id="house_number" name="house_number" class="form" required>
                <option value="1">House Number: 1</option>
                <option value="2">House Number: 2</option>
                <option value="3">House Number: 3</option>
                <option value="4">House Number: 4</option>
                <option value="5">House Number: 5</option>
                <option value="6">House Number: 6</option>
                <option value="7">House Number: 7</option>
                <option value="8">House Number: 8</option>
                <option value="9">House Number: 9</option>
                <option value="10">House Number: 10</option>
                <option value="11">House Number: 11</option>
                <option value="12">House Number: 12</option>
                <option value="13">House Number: 13</option>
                <option value="14">House Number: 14</option>
                <option value="15">House Number: 15</option>
                <option value="16">House Number: 16</option>
                <option value="17">House Number: 17</option>
                <option value="18">House Number: 18</option>
            </select>
            <!-- <label for="Month">consumer phone number:</label>
            <input type="month" name="billing_month" id="billing_month" class="form" required> -->
            <label for="Month">Select Billing Month:</label>
            <input type="month" name="billing_month" id="billing_month" class="form" required>
            <label for="date_of_reading">Select Date of Reading:</label>
            <input type="date" name="date_of_reading" id="date_of_reading" class="form" required>
            <label for="current_reading">Enter Current Meter Reading:</label>
            <input type="number" name="current_reading" id="current_reading" class="form" required>
            <input type="submit" value="Populate Bill" class="form btn btn-primary" name="populate_bill" id="populate_bill">
            <input type="submit" value="Send Bill" class="form btn btn-danger" name="send" id="send">
        </div>
</form>
<?php
//Retrieving and Displaying content from the Database table.
$sql = "SELECT * FROM monthly_bill ORDER BY timestamp DESC limit 1";
$stmtselect = $db->query($sql);

if ($stmtselect->rowCount = 1) {
    while ($monthly_bill = $stmtselect->fetch(PDO::FETCH_ASSOC)) {
        echo '                                
                    <div class="aside-grid">
                    <div class="aside" name="total_bill">
                        <h4>Total Cost</h4>
                        <h1 id="total_bill">
                        ' . $monthly_bill['total_bill'] . '
                        </h1>
                    </div>
                    <div class="aside" name="current_reading">
                        <h4>Current Reading</h4>
                        <h1 id="current_reading">
                            ' . $monthly_bill['current_reading'] . '
                        </h1>
                    </div>
                    <div class="aside" name="balance_brought_down">
                        <h4>Balance From Previous Billing</h4>
                        <h1 id="balance_brought_down">
                            ' . $monthly_bill['bbd'] . '
                        </h1>
                    </div>
                    <div class="aside" name="previous_reading">
                        <h4>Previous Reading</h4>
                        <h1 id="previous_reading">
                            ' . $monthly_bill['previous_reading'] . '
                        </h1>
                    </div>
                    <div class="aside" name="meter_rent">
                        <h4>Meter Rent</h4>
                        <h1 id="meter_rent">Ksh:
                            ' . $monthly_bill['meter_rent'] . '
                        </h1>
                    </div>
                    <div class="aside" name="units_consumed">
                        <h4>Units Consumed</h4>
                        <h1 id="units_consumed">
                            ' . $monthly_bill['units_consumed'] . '
                        </h1>
                    </div>
                ';
    }
}
?>
</div>
</div>
<div class="footer">
    <h4 onclick="window.location.href='index.php'">CLASSIC HEIGHTS APARTMENTS</h4>
</div>
<div class="copyright">Lensmen Technologies @2022 </div>
<!-- Jquery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- sweetalert2 library -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
    $(function() {
        $('#send').click(function() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Send Bill',
                text: "You won't be able to revert this!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, send it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    swalWithBootstrapButtons.fire(
                        'Successfully.',
                        'bill sent'
                    )
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled!',
                        'bill terminated'
                    )
                }
            })
        });
    });
</script>
<script type="text/javascript">
    $(function() {
        $('#populate_bill').click(function(e) {

            var valid = this.form.checkValidity();

            if (valid) {
                var house_number = $('#house_number').val();
                var billing_month = $('#billing_month').val();
                var date_of_reading = $('#date_of_reading').val();
                var current_reading = $('#current_reading').val();

                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'jsindex.php',
                    data: {
                        house_number: house_number,
                        billing_month: billing_month,
                        date_of_reading: date_of_reading,
                        current_reading: current_reading,
                    },
                    success: function(data) {
                        //add registration - login process.
                        // alert(data);
                        if ($.trim(data) === "Bill Processed Successfully") {
                            setTimeout('window.location.href="index.php"', 5000);
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: data,
                                showConfirmButton: false,
                                timer: 3000
                            })
                        } else {
                            error: {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'error',
                                    title: 'Error: Data Duplication Detected.',
                                    showConfirmButton: false,
                                    timer: 5000
                                })
                            }
                        }
                    }
                });
            }
        });
    });
</script>
</body>
</html>