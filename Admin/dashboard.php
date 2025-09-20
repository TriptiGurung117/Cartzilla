<?php 
include 'dbconnection.php';
session_start();

// Ensure only admin can access
if(!isset($_SESSION['userrole']) || $_SESSION['userrole'] != 'admin'){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - cartzilla</title>
    <link rel="stylesheet" href="dashboardstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body>
    <header class="header">
        <h2>Cart<b>zilla</b></h2>
        <span class="material-symbols-outlined">account_circle</span>
    </header>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <h1>Welcome Admin</h1>

        <div class="stats">
            <!-- Total Users -->
            <div class="stat">
                <div>
                    <h2>Total Users</h2>
                    <?php
                    $query="SELECT id FROM users";
                    $query_run=mysqli_query($conn,$query);
                    $row=mysqli_num_rows($query_run);
                    echo '<div class="number">'.$row.'</div>';
                    ?>
                </div>
                <div class="icon"><i class="fa-solid fa-users"></i></div>
            </div>

            <!-- Total Products -->
            <div class="stat">
                <div>
                    <h2>Total Products</h2>
                    <?php
                    $query="SELECT id FROM products";
                    $query_run=mysqli_query($conn,$query);
                    $row=mysqli_num_rows($query_run);
                    echo '<div class="number">'.$row.'</div>';
                    ?>
                </div>
                <div class="icon"><i class="fa-solid fa-box-open"></i></div>
            </div>

            <!-- Total Categories -->
            <div class="stat">
                <div>
                    <h2>Total Categories</h2>
                    <?php
                    $query="SELECT id FROM categories";
                    $query_run=mysqli_query($conn,$query);
                    $row=mysqli_num_rows($query_run);
                    echo '<div class="number">'.$row.'</div>';
                    ?>
                </div>
                <div class="icon"><i class="fa-solid fa-tags"></i></div>
            </div>

            <!-- Total Orders -->
            <div class="stat">
                <div>
                    <h2>Total Orders</h2>
                    <?php
                    $query="SELECT id FROM orders";
                    $query_run=mysqli_query($conn,$query);
                    $row=mysqli_num_rows($query_run);
                    echo '<div class="number">'.$row.'</div>';
                    ?>
                </div>
                <div class="icon"><i class="fa-solid fa-shopping-cart"></i></div>
            </div>
        </div>
    </main>

</body>
</html>
