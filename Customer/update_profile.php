<?php
include 'dbconnection.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get submitted data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');

if($name === '' || $email === ''){
    echo "Name and Email cannot be empty.";
    exit();
}

// Update user info
$stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
$stmt->bind_param("ssi", $name, $email, $user_id);

if($stmt->execute()){
    // Success - redirect back to profile
    $_SESSION['user_name'] = $name; // Update session for navbar display
    header("Location: profile.php?success=1");
    exit();
} else {
    echo "Failed to update profile. Please try again.";
}
?>
