<!DOCTYPE html>
<html lang="en">
<?php
session_start();

$prefillPhone = isset($_GET['phone']) ? trim($_GET['phone']) : '';
$targetHouse  = isset($_GET['house']) ? trim($_GET['house']) : '';
$targetBillId = isset($_GET['bill_id']) ? (int)$_GET['bill_id'] : 0;
$isPayRedirect = (isset($_GET['action']) && $_GET['action'] === 'pay');

$redirectParams = ['prompt_payment' => 1];
if ($targetBillId > 0) {
    $redirectParams['bill_id'] = $targetBillId;
}
if ($targetHouse !== '') {
    $redirectParams['house'] = $targetHouse;
}
$redirectUrl = 'index.php?' . http_build_query($redirectParams);

if (isset($_SESSION['waterbillingsystem'])) {
    $_SESSION['prompt_payment_on_login'] = true;
    header("Location: " . $redirectUrl);
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/log_styles.css">

    <title>Water Biling System | Login</title>
</head>

<body>
    <div class="header">
        <h3>Tenant</h3>
    </div>
    <div style="text-align: center; margin: 15px 0;">
        <a href="../home.php" style="text-decoration: none; font-weight: 600; color: #0d6efd;">&larr; Back to Portal Selection</a>
    </div>
    <?php if ($isPayRedirect): ?>
    <div style="max-width: 520px; margin: 0 auto 15px; padding: 12px 16px; background: #ecfdf5; border: 1px solid #10b981; border-radius: 8px; color: #065f46; text-align: center; font-size: 0.95rem;">
        <strong>WhatsApp Bill Dispatch Link</strong><br>
        <?php if ($targetHouse !== ''): ?>
            House <strong><?= htmlspecialchars($targetHouse) ?></strong> &mdash;
        <?php endif; ?>
        Log in below to view your statement and trigger your payment prompt.
    </div>
    <?php endif; ?>
    <div class="container-fluid">
        <form action="login.php" method="POST">
            <div class="form-grid" id="form-register">
                <label for="phonenumber" class="form-item">Phone Number:</label>
                <input type="tel" name="phonenumber" id="phonenumber" class="form-item" value="<?= htmlspecialchars($prefillPhone) ?>" placeholder="e.g. 0798765432" required>
                <label for="newpassword" class="form-item">Password:</label>
                <input type="Password" name="newpassword" id="newpassword" class="form-item" required>
                <input type="submit" value="login" class="form-item btn btn-primary" name="login" id="login">
            </div>
        </form>
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
            var isPayRedirect = <?= json_encode($isPayRedirect) ?>;
            var redirectUrl = <?= json_encode($redirectUrl) ?>;

            if (isPayRedirect) {
                Swal.fire({
                    icon: 'info',
                    title: 'Water Bill Payment',
                    text: 'Please sign in with your registered phone number and password to receive your payment prompt.',
                    confirmButtonColor: '#10b981'
                });
            } else {
                Swal.fire(
                    'Welcome',
                    'Please Enter Your Login Details.',
                    'info'
                );
            }

            $('#login').click(function(e) {
                var valid = this.form.checkValidity();

                if (valid) {
                    var phonenumber = $('#phonenumber').val();
                    var newpassword = $('#newpassword').val();

                    e.preventDefault();

                    $.ajax({
                        type: 'POST',
                        url: 'jslogin.php',
                        data: {
                            phonenumber: phonenumber,
                            newpassword: newpassword
                        },
                        success: function(data) {
                            if ($.trim(data) === "Log in Successfully") {
                                setTimeout(function() {
                                    window.location.href = redirectUrl;
                                }, 1500);
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: 'Login Successful! Preparing Payment Prompt...',
                                    showConfirmButton: false,
                                    timer: 1400
                                });
                            } else {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'error',
                                    title: data,
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }
                        }
                    });
                } else {
                    Swal.fire(
                        'The Form is Blank!',
                        'Please enter values',
                        'error'
                    );
                }

            });
        });
    </script>
</body>

</html>