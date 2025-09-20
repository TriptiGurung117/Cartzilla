<?php
include 'dbconnection.php';
session_start();

// Redirect if not logged in
if(!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$username = $user['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Profile</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
.navbar-custom { background-color: #1a1f2b; }
.navbar-custom .navbar-brand { 
    color: #dc3545; 
    font-weight: bold; 
    font-size: 2rem; /* make it bigger */
}

.navbar-custom .btn-outline-light { color: #fff; }
.navbar-custom .btn-outline-light:hover { color: #dc3545; }
.dropdown-menu { min-width: 200px; }

.profile-container {
    max-width: 700px;
    margin: 40px auto;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.profile-container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #dc3545;
}
.profile-form label { font-weight: 600; }
.profile-form input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
.profile-form button {
    width: 100%;
    padding: 10px;
    background-color: #dc3545;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.profile-form button:hover { background-color: #b71c1c; }

footer { background: #1a1f2b; color: #fff; text-align: center; padding: 15px; margin-top: 30px; }
</style>
</head>
<body>

<!-- Navbar with Profile Dropdown -->
<nav class="navbar navbar-expand-lg navbar-custom py-3">
  <div class="container">
    <a class="navbar-brand" href="index.php">Cartzilla</a>
    <div class="ms-auto">
      <div class="dropdown">
        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
          <?= htmlspecialchars($username); ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="index.php">Home</a></li>
          <li><a class="dropdown-item" href="profile.php">View / Edit Profile</a></li>
          <li><a class="dropdown-item" href="my_orders.php">My Orders</a></li>
          <li><a class="dropdown-item" href="payments.php">Payments</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>


<!-- Profile Form -->
<div class="profile-container">
    <h2>My Profile</h2>
    <form class="profile-form" action="update_profile.php" method="post">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        <button type="submit">Update Profile</button>

    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
