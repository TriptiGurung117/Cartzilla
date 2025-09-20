<?php
include 'dbconnection.php';
session_start();

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Name Validation
    if (empty($name)) {
        $error_message = "Name is required.";
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        $error_message = "Name can only contain letters, spaces, apostrophes, and hyphens.";
    } elseif (strlen($name) < 2 || strlen($name) > 50) {
        $error_message = "Name must be between 2 and 50 characters.";
    }
    // Email Validation

    elseif (empty($email)) {
        $error_message = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (strlen($email) > 100) {
        $error_message = "Email must not exceed 100 characters.";
    }
    //Pssword Validation
  elseif (empty($password)) {
        $error_message = "Password is required.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 6 characters.";
    }

    // Proceed only if no error from name validation
    if (empty($error_message)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email already registered. Please login.";
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, userRole) VALUES (?, ?, ?, 'customer')");
            $stmt->bind_param("sss", $name, $email, $password);

            if ($stmt->execute()) {
                $success_message = "Registration successful. You can now login.";
                header("Location: login.php"); // Redirect to customer login
                exit();
            } else {
                $error_message = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Register</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #23242b, #485461);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        color: #fff;
    }
    .login-box {
        background-color: #1c1c1c;
        padding: 40px 50px;
        border-radius: 12px;
        box-shadow: 0px 10px 25px rgba(0,0,0,0.5);
        width: 100%;
        max-width: 400px;
        text-align: center;
    }
    .login-box h2 {
        margin-bottom: 10px;
        color: #ff6f61;
    }
    .login-box p {
        font-size: 14px;
        color: #ccc;
        margin-bottom: 20px;
    }
    /* Style for all text, password, and email inputs */
    .login-box input[type="text"],
    .login-box input[type="password"],
    .login-box input[type="email"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #555;
        border-radius: 6px;
        background-color: #333;
        color: #fff;
        box-sizing: border-box;
    }
    .login-box input::placeholder {
        color: #aaa;
    }
    .login-box button {
        width: 100%;
        padding: 12px;
        background-color: #ff6f61;
        border: none;
        color: white;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
    }
    .login-box button:hover {
        background-color: #ff3b2e;
    }
    .error-message {
        color: #ff4d4d;
        margin-top: 10px;
        font-size: 14px;
    }
    .register-text {
        margin-top: 15px;
        font-size: 14px;
    }
    .register-text a {
        color: #ff6f61;
        text-decoration: none;
    }
    .register-text a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="login-box">
    <h2>Furniture Shop</h2>
    <p>Create your account</p>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <?php if(!empty($error_message)) : ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <p class="register-text">Already have an account? <a href="login.php">Login Here</a></p>
</div>
</body>
</html>
