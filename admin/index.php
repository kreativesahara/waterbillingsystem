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
$sql = "SELECT * FROM monthly_bill ORDER BY timestamp DESC, bill_id DESC limit 1";
$stmtselect = $db->query($sql);

if ($stmtselect && $stmtselect->rowCount() > 0) {
    while ($monthly_bill = $stmtselect->fetch(PDO::FETCH_ASSOC)) {
        echo '                                
                    <div class="aside-grid" data-latest-house="' . htmlspecialchars($monthly_bill['house_number']) . '" data-latest-bill-id="' . htmlspecialchars($monthly_bill['bill_id']) . '">
                    <div class="aside" name="total_bill">
                        <h4>Total Cost (House ' . htmlspecialchars($monthly_bill['house_number']) . ')</h4>
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
    function formatWhatsAppDigits(phone) {
        var digits = (phone || '').replace(/\D+/g, '');
        if (!digits) return '';
        if (digits.indexOf('0') === 0 && digits.length === 10) {
            return '254' + digits.substring(1);
        }
        if (digits.length === 9 && (digits.indexOf('7') === 0 || digits.indexOf('1') === 0)) {
            return '254' + digits;
        }
        return digits;
    }

    $(function() {
        $('#send').click(function(e) {
            e.preventDefault();

            var selectedHouse = $('#house_number').val() || $('.aside-grid').attr('data-latest-house') || '';
            var billingMonth = $('#billing_month').val() || '';
            var dateOfReading = $('#date_of_reading').val() || '';
            var currentReading = $('#current_reading').val() || '';

            $.ajax({
                type: 'POST',
                url: 'dispatch_bill.php',
                dataType: 'json',
                data: {
                    house_number: selectedHouse,
                    billing_month: billingMonth,
                    date_of_reading: dateOfReading,
                    current_reading: currentReading
                },
                success: function(res) {
                    if (!res || res.status !== 'success') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Unable to Dispatch Bill',
                            text: (res && res.message) ? res.message : 'Please populate a bill first before dispatching.'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Dispatch Bill via WhatsApp',
                        html: `
                            <div style="text-align:left; font-size:14px;">
                                <p style="margin-bottom:8px;"><strong>Tenant:</strong> ${res.tenant_name} (House No. ${res.house_number})</p>
                                <p style="margin-bottom:8px;"><strong>Billing Month:</strong> ${res.billing_month} &bull; <strong>Total Due:</strong> KSh ${res.total_bill}</p>
                                <label style="font-weight:600; display:block; margin-top:10px; margin-bottom:4px;">Tenant WhatsApp Number:</label>
                                <input type="text" id="swal_wa_phone" class="swal2-input" value="${res.tenant_phone || ''}" placeholder="e.g. 0798765432 or 254798765432" style="margin:0 0 12px 0; width:100%; height:40px; font-size:14px;">
                                <label style="font-weight:600; display:block; margin-bottom:4px;">Message &amp; Redirect Link Preview:</label>
                                <textarea id="swal_wa_message" class="swal2-textarea" rows="6" style="margin:0; width:100%; font-size:12px; font-family:monospace;">${res.message}</textarea>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Send to WhatsApp',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-success ms-2',
                            cancelButton: 'btn btn-danger'
                        },
                        buttonsStyling: false,
                        preConfirm: function() {
                            var phoneVal = $('#swal_wa_phone').val().trim();
                            var msgVal = $('#swal_wa_message').val().trim();
                            if (!phoneVal) {
                                Swal.showValidationMessage('Please enter the tenant WhatsApp phone number');
                                return false;
                            }
                            return {
                                phone: formatWhatsAppDigits(phoneVal),
                                rawPhone: phoneVal,
                                message: msgVal
                            };
                        }
                    }).then(function(result) {
                        if (result.isConfirmed && result.value) {
                            var waPhone = result.value.phone;
                            var updatedMsg = result.value.message;
                            var waUrl = 'https://wa.me/' + waPhone + '?text=' + encodeURIComponent(updatedMsg);

                            // Open WhatsApp directly inside the synchronous user confirmation click
                            window.open(waUrl, '_blank');

                            Swal.fire({
                                icon: 'success',
                                title: 'WhatsApp Dispatch Initiated',
                                html: `
                                    <p>Redirecting to WhatsApp for <strong>${res.tenant_name}</strong> (${result.value.rawPhone}).</p>
                                    <p style="font-size:13px; margin-top:8px;">
                                        <a href="${waUrl}" target="_blank" class="btn btn-sm btn-success">Open WhatsApp Again</a>
                                    </p>
                                `,
                                confirmButtonText: 'Done'
                            });
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            Swal.fire(
                                'Cancelled!',
                                'Bill dispatch cancelled.',
                                'info'
                            );
                        }
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'Could not reach the server to prepare the WhatsApp dispatch.'
                    });
                }
            });
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