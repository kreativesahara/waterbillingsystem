<!DOCTYPE html>
<html lang="en">
<?php
session_start();

if (isset($_SESSION['waterbillingsystem'])) {
    header("Location: index.php");
}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="admin_css/log_styles.css">

    <title>Water Biling System | Login</title>
</head>

<body>
    <div class="header">
        <h3> Administrator</h3>
    </div>
    <div class="container-fluid">
        <form action="login.php" method="POST">
            <div class="form-grid" id="form-register">
                <label for="phonenumber" class="form-item">Phone Number:</label>
                <input type="number" name="phonenumber" id="phonenumber" class="form-item" required>
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
            Swal.fire(
                'Welcome',
                'Please Enter YOur LOgin Details.',
                'info'
            )
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
                            // alert(data);
                            if ($.trim(data) === "Log in Successfully") {
                                setTimeout('window.location.href="index.php"', 2000);
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: data,
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                            } else {
                                error: {
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'error',
                                        title: data,
                                        showConfirmButton: false,
                                        timer: 2000
                                    })
                                }
                            }
                        }
                    });
                } else {
                    alert('Failed: Please enter values');
                    Swal.fire(
                        'The Form is Blank!',
                        'Please enter values',
                        'error'
                    )
                }

            });
        });
    </script>
</body>

</html>