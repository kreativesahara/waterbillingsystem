<!DOCTYPE html>
<html lang="en">
<?php
require_once('config.php');
session_start();

if (!isset($_SESSION['waterbillingsystem'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION);
    header("Location: login.php");
    exit;
}

$sessionUser = is_array($_SESSION['waterbillingsystem']) ? $_SESSION['waterbillingsystem'] : [];
$tenantPhone = isset($_SESSION['phonenumber']) ? $_SESSION['phonenumber'] : (isset($sessionUser['phonenumber']) ? $sessionUser['phonenumber'] : '');
$requestedBillId = isset($_GET['bill_id']) ? (int)$_GET['bill_id'] : 0;

$shouldPromptPayment = !empty($_SESSION['prompt_payment_on_login']) || isset($_GET['prompt_payment']);
unset($_SESSION['prompt_payment_on_login']);

if ($requestedBillId > 0) {
    $sql = "SELECT C.firstname, C.secondname, C.surname, C.phonenumber, C.house_number,
                   M.bill_id, M.billing_month, M.date_of_reading, M.current_reading, M.previous_reading,
                   M.units_consumed, M.cost_per_unit, M.current_bill, M.meter_rent, M.bbd, M.total_bill
            FROM consumers C
            LEFT JOIN monthly_bill M ON C.house_number = M.house_number AND M.bill_id = ?
            WHERE C.phonenumber = ?
            LIMIT 1";
    $stmtselect = $db->prepare($sql);
    $stmtselect->execute([$requestedBillId, $tenantPhone]);
} else {
    $sql = "SELECT C.firstname, C.secondname, C.surname, C.phonenumber, C.house_number,
                   M.bill_id, M.billing_month, M.date_of_reading, M.current_reading, M.previous_reading,
                   M.units_consumed, M.cost_per_unit, M.current_bill, M.meter_rent, M.bbd, M.total_bill
            FROM consumers C
            LEFT JOIN monthly_bill M ON C.house_number = M.house_number
            WHERE C.phonenumber = ?
            ORDER BY M.bill_id DESC
            LIMIT 1";
    $stmtselect = $db->prepare($sql);
    $stmtselect->execute([$tenantPhone]);
}

$monthly_bill = $stmtselect->fetch(PDO::FETCH_ASSOC);
if (!$monthly_bill) {
    $monthly_bill = [
        'firstname'        => isset($sessionUser['firstname']) ? $sessionUser['firstname'] : 'Tenant',
        'secondname'       => isset($sessionUser['secondname']) ? $sessionUser['secondname'] : '',
        'surname'          => isset($sessionUser['surname']) ? $sessionUser['surname'] : '',
        'phonenumber'      => $tenantPhone,
        'house_number'     => isset($sessionUser['house_number']) ? $sessionUser['house_number'] : 'N/A',
        'bill_id'          => 0,
        'billing_month'    => 'N/A',
        'date_of_reading'  => 'N/A',
        'current_reading'  => 0,
        'previous_reading' => 0,
        'units_consumed'   => 0,
        'cost_per_unit'    => 150,
        'current_bill'     => 0,
        'meter_rent'       => 50,
        'bbd'              => 0,
        'total_bill'       => 0
    ];
}
?>
<?php
include('template/header.php');
?>

<div class="headerline">
    <h3>Tenancy Water Monthly Bill Statement (House <?= htmlspecialchars((string)$monthly_bill['house_number']) ?>)</h3>
</div>
<form action="index.php" method="post">
    <div class="container-fluid">
        <div class="aside-grid">
            <div class="aside" id="total_cost">
                <h4>Total Cost</h4>
                <h1 name="total_cost">
                    <?= htmlspecialchars((string)($monthly_bill['total_bill'] ?? 0)) ?>
                </h1>
            </div>
            <div class="aside" id="current_bill">
                <h4>Current Bill</h4>
                <h1 name="current_bill">
                    <?= htmlspecialchars((string)($monthly_bill['current_bill'] ?? 0)) ?>
                </h1>
            </div>
            <div class="aside" id="bbd">
                <h4>Balance From Previous Billing</h4>
                <h1 name="bbd">
                    <?= htmlspecialchars((string)($monthly_bill['bbd'] ?? 0)) ?>
                </h1>
            </div>
            <div class="aside" id="current_reading">
                <h4>Current Reading</h4>
                <h1 name="current_reading">
                    <?= htmlspecialchars((string)($monthly_bill['current_reading'] ?? 0)) ?>
                </h1>
            </div>
            <div class="aside" id="previous_reading">
                <h4>Previous Reading</h4>
                <h1 name="previous_reading">
                    <?= htmlspecialchars((string)($monthly_bill['previous_reading'] ?? 0)) ?>
                </h1>
            </div>
            <div class="aside" id="units_consumed">
                <h4>Units Consumed</h4>
                <h1 name="units_consumed">
                    <?= htmlspecialchars((string)($monthly_bill['units_consumed'] ?? 0)) ?>
                </h1>
            </div>
            <div class="aside" id="date_of_reading">
                <h4>Date Of Reading</h4>
                <h1 name="date_of_reading">
                    <?= htmlspecialchars((string)($monthly_bill['date_of_reading'] ?? 'N/A')) ?>
                </h1>
            </div>
            <div class="aside" id="billing_month">
                <h4>Billing Month</h4>
                <h1 name="billing_month">
                    <?= htmlspecialchars((string)($monthly_bill['billing_month'] ?? 'N/A')) ?>
                </h1>
            </div>
            <div class="aside" id="meter_rent">
                <h4>Meter Rent</h4>
                <h1 name="meter_rent">Ksh:
                    <?= htmlspecialchars((string)($monthly_bill['meter_rent'] ?? 50)) ?>
                </h1>
            </div>
        </div>
    </div>
</form>
<div style="text-align: center; margin: 20px 0;">
    <button id="send_bill" type="button" class="btn btn-info" style="font-weight: 600; padding: 10px 28px;">Pay Bill (Prompt Payment)</button>
</div>
<div class="footer">
    <h4 onclick="window.location.href='index.php'">CLASSIC HEIGHTS APARTMENTS</h4>
</div>
<div class="copyright">Lensmen Technologies @2022 </div>
<!-- Jquery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- sweetalert2 library -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(function() {
        var billData = <?= json_encode([
            'bill_id'       => (int)($monthly_bill['bill_id'] ?? 0),
            'house_number'  => (string)($monthly_bill['house_number'] ?? ''),
            'tenant_name'   => trim(($monthly_bill['firstname'] ?? '') . ' ' . ($monthly_bill['secondname'] ?? '') . ' ' . ($monthly_bill['surname'] ?? '')),
            'phone_number'  => (string)($monthly_bill['phonenumber'] ?? $tenantPhone),
            'billing_month' => (string)($monthly_bill['billing_month'] ?? 'N/A'),
            'current_bill'  => (float)($monthly_bill['current_bill'] ?? 0),
            'bbd'           => (float)($monthly_bill['bbd'] ?? 0),
            'total_bill'    => (float)($monthly_bill['total_bill'] ?? 0)
        ]) ?>;

        var shouldPromptOnLogin = <?= json_encode($shouldPromptPayment) ?>;

        function openPaymentPrompt() {
            var defaultPhone = billData.phone_number || '';
            if (defaultPhone.length === 9 && (defaultPhone.charAt(0) === '7' || defaultPhone.charAt(0) === '1')) {
                defaultPhone = '0' + defaultPhone;
            }
            var defaultAmount = billData.total_bill > 0 ? billData.total_bill : billData.current_bill;

            Swal.fire({
                title: 'Prompt for Bill Payment',
                icon: 'info',
                html:
                    '<div style="text-align:left; font-size:0.95rem; line-height:1.5;">' +
                        '<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:14px;">' +
                            '<div><strong>Tenant:</strong> ' + (billData.tenant_name || 'Tenant') + ' (House ' + billData.house_number + ')</div>' +
                            '<div><strong>Billing Period:</strong> ' + billData.billing_month + '</div>' +
                            '<div><strong>Current Charge:</strong> KSh ' + billData.current_bill.toLocaleString() + '</div>' +
                            '<div><strong>Previous Balance:</strong> KSh ' + billData.bbd.toLocaleString() + '</div>' +
                            '<div style="margin-top:6px; font-size:1.1rem; color:#0f766e;"><strong>Total Amount Due: KSh ' + billData.total_bill.toLocaleString() + '</strong></div>' +
                        '</div>' +
                        '<label style="display:block; font-weight:600; margin-bottom:4px;">M-Pesa Phone Number to Prompt:</label>' +
                        '<input id="swal_pay_phone" class="swal2-input" style="margin:0 0 12px 0; width:100%;" value="' + defaultPhone + '" placeholder="e.g. 0798765432">' +
                        '<label style="display:block; font-weight:600; margin-bottom:4px;">Amount to Pay (KSh):</label>' +
                        '<input id="swal_pay_amount" type="number" step="0.01" min="1" class="swal2-input" style="margin:0; width:100%;" value="' + defaultAmount + '">' +
                    '</div>',
                showCancelButton: true,
                confirmButtonText: 'Send Payment Prompt',
                cancelButtonText: 'Pay Later',
                confirmButtonColor: '#10b981',
                preConfirm: function() {
                    var phone = $('#swal_pay_phone').val().trim();
                    var amount = parseFloat($('#swal_pay_amount').val());
                    if (!phone) {
                        Swal.showValidationMessage('Please enter a valid M-Pesa phone number');
                        return false;
                    }
                    if (isNaN(amount) || amount <= 0) {
                        Swal.showValidationMessage('Please enter a valid payment amount');
                        return false;
                    }
                    return {
                        phone_number: phone,
                        amount: amount
                    };
                }
            }).then(function(result) {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        title: 'Sending Payment Prompt...',
                        text: 'Initiating STK push prompt to ' + result.value.phone_number + '...',
                        allowOutsideClick: false,
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        type: 'POST',
                        url: 'pay_bill.php',
                        dataType: 'json',
                        data: {
                            bill_id: billData.bill_id,
                            house_number: billData.house_number,
                            billing_month: billData.billing_month,
                            phone_number: result.value.phone_number,
                            amount: result.value.amount
                        },
                        success: function(res) {
                            if (res && res.ok) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Payment Prompt Sent!',
                                    html:
                                        '<div style="text-align:left; font-size:0.95rem;">' +
                                            '<p>' + res.message + '</p>' +
                                            '<div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:8px; padding:10px; margin-top:10px;">' +
                                                '<div><strong>Reference:</strong> ' + res.reference_code + '</div>' +
                                                '<div><strong>Phone:</strong> ' + res.phone_number + '</div>' +
                                                '<div><strong>Amount:</strong> KSh ' + res.amount + '</div>' +
                                                '<div><strong>House:</strong> ' + res.house_number + ' (' + res.billing_month + ')</div>' +
                                            '</div>' +
                                        '</div>',
                                    confirmButtonColor: '#10b981'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Payment Prompt Failed',
                                    text: (res && res.message) ? res.message : 'Unable to send payment prompt.'
                                });
                            }
                        },
                        error: function(xhr) {
                            var msg = 'Unable to connect to payment service.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: msg
                            });
                        }
                    });
                }
            });
        }

        $('#send_bill').on('click', function(e) {
            e.preventDefault();
            openPaymentPrompt();
        });

        if (shouldPromptOnLogin) {
            openPaymentPrompt();
        }
    });
</script>
</body>
</html>