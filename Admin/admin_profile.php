<?php
include 'dbconnection.php';
session_start();

// Ensure only admin can access
if (!isset($_SESSION['userId']) || $_SESSION['userrole'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Get admin info
$user_id = $_SESSION['userId'];
$select = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'") or die('Query failed');

if (mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
} else {
    $fetch = null;
}

// Handle profile update
$success = $error = '';
if(isset($_POST['update_profile'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $update = mysqli_query($conn, "UPDATE users SET name='$name', email='$email', password='$password' WHERE id='$user_id'");
    if($update){
        $success = "Profile updated successfully!";
        // Refresh fetch data
        $select = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
        $fetch = mysqli_fetch_assoc($select);
    } else {
        $error = "Update failed: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Profile - CartZilla</title>
<link rel="stylesheet" type="text/css" href="dashboardstyle.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
<style>
/* Simple CSS similar to view users */
.main-content { padding: 10px; max-width: 800px; margin: 0 auto; }
h1 { color: #127b8e; margin-bottom: 20px; }
.profile-box { background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px; }
.profile-box img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; display: block; margin-bottom: 15px; }
.profile-box h2 { margin: 0 0 10px 0; color: #333; }
.profile-box p { margin: 5px 0; color: #555; }
.profile-details { margin-top: 20px; }
.profile-details table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.profile-details th, .profile-details td { text-align: left; padding: 5px; border-bottom: 1px solid #ddd; }
.profile-details th { background: #127b8e; color: #fff; }
.success { color: green; font-weight: bold; margin-bottom: 10px; }
.error { color: red; font-weight: bold; margin-bottom: 10px; }
form input[type="text"], form input[type="email"], form input[type="password"] {
    width: 100%; padding: 8px; margin: 5px 0 10px 0; border: 1px solid #ccc; border-radius: 5px;
}
form button {
    padding: 10px 20px; background: #127b8e; color: #fff; border: none; border-radius: 5px; cursor: pointer;
}
form button:hover { background: #0f6673; }
</style>
</head>
<body>

<?php include 'sidebar.php'; ?> <!-- Sidebar remains -->

<section class="main-content">
    <h1>Admin Profile</h1>

    <?php if($success) echo "<p class='success'>$success</p>"; ?>
    <?php if($error) echo "<p class='error'>$error</p>"; ?>

    <?php if($fetch): ?>
    <div class="profile-box">
        <img src="images/bed.jpg" alt="Profile Picture">
        <h2><?php echo htmlspecialchars($fetch['name']); ?></h2>
        <p>User Role: <?php echo htmlspecialchars($fetch['userrole']); ?></p>
        <p>Email: <?php echo htmlspecialchars($fetch['email']); ?></p>
    </div>

    <div class="profile-details">
        <h2>Edit Profile</h2>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($fetch['name']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($fetch['email']); ?>" required>

            <label>Password:</label>
            <input type="text" name="password" value="<?php echo htmlspecialchars($fetch['password']); ?>" required>

            <button type="submit" name="update_profile">Update Profile</button>
        </form>
    </div>
    <?php else: ?>
        <p>User not found!</p>
    <?php endif; ?>
</section>

</body>
</html>
