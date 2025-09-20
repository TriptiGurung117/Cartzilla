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
    <title>View Users - Cartzilla Admin</title>
    <link rel="stylesheet" type="text/css" href="dashboardstyle.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <style>
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .user-table th, .user-table td {
            padding: 12px;
            text-align: left;
        }
        .user-table th {
            background-color: #1abc9c;
            color: white;
        }
        .user-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .user-table tr:hover {
            background-color: #d1f0eb;
        }
        h1 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header class="header">
        <h2>Cartzilla Admin</h2>
    </header>

    <div class="body">
        <?php include 'sidebar.php'; ?>

        <section class="main-content">
            <h1>Customers List</h1>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM users WHERE userrole = 'customer' ORDER BY id DESC";
                    $result = mysqli_query($conn, $query);

                    if(mysqli_num_rows($result) > 0){
                        while($row = mysqli_fetch_assoc($result)){
                            echo "<tr>";
                            echo "<td>".$row['id']."</td>";
                            echo "<td>".$row['name']."</td>";
                            echo "<td>".$row['email']."</td>";
                            echo "<td>".$row['userrole']."</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center;'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
