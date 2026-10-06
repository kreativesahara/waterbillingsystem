<?php
    $number = $_SESSION['phonenumber'];
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <title>Water Biling System | Home</title>
</head>

<body>
    <div class="header">
        <h3>Water Bill Records</h3>
        <div class="avatar">
            <h6>
                <?php echo $number; ?>
            </h6>
        </div>
    </div>
    <div class="navbar2">
        <a href="index.php">
            <button class="btn btn-outline-success">Home</button>
        </a>
        <a href="records.php">
            <button class="btn btn-outline-success">View Billing Records</button>
        </a>
        <a href="c_records.php">
            <button class="btn btn-outline-success">View Payment Records</button>
        </a>
        <a href="index.php?logout= true" id="logout" name="logout">
            <button class="btn btn-outline-success">Logout</button>
        </a>
    </div>