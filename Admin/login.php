<?php
include 'dbconnection.php';
session_start();

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare query for admin login using email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND userRole = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify password (plain text for now)
        if ($row['password'] === $password) {
            // Set session variables
            $_SESSION['email'] = $row['email'];
            $_SESSION['userId'] = $row['id'];  // consistent with your DB
            $_SESSION['userrole'] = $row['userrole'];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "Admin not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furniture Shop - Admin Login</title>
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
        .login-box input[type="text"],
        .login-box input[type="password"] {
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
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <p>Enter your credentials to access the dashboard</p>

        <form method="post">
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <?php if (!empty($error_message)) : ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
