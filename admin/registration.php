<?php
require_once('config.php');
session_start();

if (!isset($_SESSION['waterbillingsystem'])) {
    header("Location: login.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="admin_css/reg_styles.css">

    <title>Water Biling System | Register</title>
</head>

<body>
    <div class="headerline">
        <h3>Tenancy Water Registration Form</h3>
    </div>
    <div class="navbar">
        <ul>
            <!-- <a href="#"><li id="home">Home</li></a> -->
            <!-- <a href="#"><li id="billing_form">Billing Form</li></a>   -->
            <!-- <a href="index_list.html"><li id="billing_table">Billing Table</li></a>              -->
        </ul>
    </div>

    <!-- Registration process -->
    <div>
        <!-- Button register intializes the registeration php process.  -->
        <?php

        ?>
    </div>
    <form action="registration.php" method="post">
        <div class="container-fluid">
            <div class="form-grid" id="form-register">
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
                <label for="firstname">First Name:</label>
                <input type="text" name="firstname" id="firstname" class="form" required>
                <label for="lastname">lastname:</label>
                <input type="text" name="lastname" id="lastname" class="form" required>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form" placeholder="johndoe@domain.com" required>
                <label for="phonenumber">Phone Number:</label>
                <input type="number" name="phonenumber" id="phonenumber" class="form" required>
                <label for="national_id">ID Number:</label>
                <input type="number" name="national_id" id="national_id" class="form" required>
                <label for="newpassword">New Password:</label>
                <input type="Password" name="newpassword" id="newpassword" class="form" required>
                <label for="confirmpassword">Confirm Password:</label>
                <input type="Password" name="confirmpassword" id="confirmpassword" class="form" required>
                <input type="submit" value="Register" class="form btn btn-primary" name="register" id="register">
                <input type="button" value="Home" onclick="window.location.href='index.php'" class="form btn btn-primary" name="login" id="login" />

            </div>
        </div>
    </form>
    <div class="footer">
        <h4 onclick="window.location.href='index.php'">CLASSIC HEIGHTS APARTMENTS</h4>
    </div>
    <div class="copyright">Lensmen Technologies @2022 </div>
    </script>
    <!-- Jquery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- sweetalert2 library -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom javascript -->
    <script type="text/javascript">
        $(function() {
            Swal.fire(
                'Register new Tenant',
                'Please fill the form with the correct information :)',
                'info'
            )
            $('#register').click(function(e) {

                var valid = this.form.checkValidity();

                if (valid) {
                    var house_number = $('#house_number').val();
                    var firstname = $('#firstname').val();
                    var lastname = $('#lastname').val();
                    var email = $('#email').val();
                    var phonenumber = $('#phonenumber').val();
                    var national_id = $('#national_id').val();
                    var newpassword = $('#newpassword').val();
                    var confirmpassword = $('#confirmpassword').val();
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'process.php',
                        data: {
                            house_number: house_number,
                            firstname: firstname,
                            lastname: lastname,
                            email: email,
                            phonenumber: phonenumber,
                            national_id: national_id,
                            newpassword: newpassword,
                            confirmPassword: confirmpassword
                        },
                        success: function(data) {
                            //add registration - login process.
                            // alert(data);
                            if ($.trim(data) === "Registration Successfully") {
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
                                        title: 'Invalid Input',
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
                        'Registration Failed!',
                        'Please enter values',
                        'error'
                    )
                }
            });
        });
    </script>
</body>

</html>