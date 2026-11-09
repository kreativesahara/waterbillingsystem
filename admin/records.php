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
    <h3>Tenancy Water Monthly Billing Records</h3>
</div>
<div class="container-fluid">
    <table class="table table-sm" id="table-records">
        <thead>
            <tr>
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
                <th id="dispatch_action">Dispatch</th>
            </tr>
        </thead>
        <tbody>
            <?php
            //Retrieving and Displaying content from the Database table.
            $sql = "SELECT * FROM monthly_bill ORDER BY house_number ASC";
            $stmtselect = $db->query($sql);

            if ($stmtselect->rowCount() > 0) {
                while ($monthly_bill = $stmtselect->fetch(PDO::FETCH_ASSOC)) {
                    echo '
                                <tr>
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
                                    <td><button type="button" class="btn btn-sm btn-success btn-dispatch-wa" data-bill-id="' . $monthly_bill['bill_id'] . '" data-house="' . $monthly_bill['house_number'] . '">Send WhatsApp</button></td>
                                </tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>
<div>
    <br>
    <input type="submit" value="Print Table" class="form btn btn-primary" name="print" id="print">

    <input type="submit" value="Clear Table" class="form btn btn-danger" name="delete" id="delete">
    <?php
    if (isset($_POST['delete'])) {
        $sql = "DELETE FROM `monthly_bill` WHERE 0";
        $stmtselect = $db->query($sql);
    }
    ?>
</div>
<div class="footer">
    <h4 onclick="window.location.href='index.php'">CLASSIC HEIGHTS APARTMENTS</h4>
</div>
<div class="copyright">Lensmen Technologies @2022 </div>
<!-- Jquery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- sweetalert2 library -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<!-- <script src="https://unpkg.com/tabulator-tables@4.5.3/dist/js/tabulator.min.js"></script> -->
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
        $("#print").on("click", function() {
            window.print();
        });

        $('.btn-dispatch-wa').on('click', function() {
            var billId = $(this).data('bill-id');
            var houseNo = $(this).data('house');

            $.ajax({
                type: 'POST',
                url: 'dispatch_bill.php',
                dataType: 'json',
                data: {
                    bill_id: billId,
                    house_number: houseNo
                },
                success: function(res) {
                    if (!res || res.status !== 'success') {
                        Swal.fire('Error', (res && res.message) ? res.message : 'Unable to prepare WhatsApp dispatch.', 'error');
                        return;
                    }

                    Swal.fire({
                        title: 'Dispatch Bill via WhatsApp',
                        html: `
                            <div style="text-align:left; font-size:14px;">
                                <p style="margin-bottom:8px;"><strong>Tenant:</strong> ${res.tenant_name} (House No. ${res.house_number})</p>
                                <p style="margin-bottom:8px;"><strong>Billing Month:</strong> ${res.billing_month} &bull; <strong>Total Due:</strong> KSh ${res.total_bill}</p>
                                <label style="font-weight:600; display:block; margin-top:10px; margin-bottom:4px;">Tenant WhatsApp Number:</label>
                                <input type="text" id="swal_wa_phone" class="swal2-input" value="${res.tenant_phone || ''}" placeholder="e.g. 0798765432" style="margin:0 0 12px 0; width:100%; height:40px; font-size:14px;">
                                <label style="font-weight:600; display:block; margin-bottom:4px;">Message &amp; Redirect Link Preview:</label>
                                <textarea id="swal_wa_message" class="swal2-textarea" rows="6" style="margin:0; width:100%; font-size:12px; font-family:monospace;">${res.message}</textarea>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Send to WhatsApp',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        preConfirm: function() {
                            var phoneVal = $('#swal_wa_phone').val().trim();
                            var msgVal = $('#swal_wa_message').val().trim();
                            if (!phoneVal) {
                                Swal.showValidationMessage('Please enter the tenant WhatsApp phone number');
                                return false;
                            }
                            return {
                                phone: formatWhatsAppDigits(phoneVal),
                                message: msgVal
                            };
                        }
                    }).then(function(result) {
                        if (result.isConfirmed && result.value) {
                            var waUrl = 'https://wa.me/' + result.value.phone + '?text=' + encodeURIComponent(result.value.message);
                            window.open(waUrl, '_blank');
                        }
                    });
                }
            });
        });
    });
</script>
</body>

</html>