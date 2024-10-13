<?php
session_start();
require_once 'connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $con->prepare("SELECT id, username, password, role FROM auth_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    // Bind the result variables
    $stmt->bind_result($id, $db_username, $db_password, $role);
    $stmt->store_result(); // Store the result to check the number of rows

    if ($stmt->num_rows === 1) {
        $stmt->fetch(); // Fetch the result into the bound variables
        
        // Debug: Print user data
        error_log("User data: " . print_r(['id' => $id, 'username' => $db_username, 'role' => $role], true));
        
        if (password_verify($password, $db_password)) {
            // Debug: Password verified
            error_log("Password verified for user: " . $db_username);
            
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = $role;
            
            // Debug: Print session data
            error_log("Session data: " . print_r($_SESSION, true));
            
            // Check for output before header
            if (!headers_sent()) {
                header("Location: index.php"); // Adjust path if necessary
                exit();
            } else {
                error_log("Headers already sent, cannot redirect.");
            }
        } else {
            // Debug: Password verification failed
            error_log("Password verification failed for user: " . $db_username);
            $error = "Invalid username or password";
        }
    } else {
        // Debug: User not found
        error_log("User not found: " . $username);
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ABER-AL-ALAM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
        }
        .background {
            background-image: url('image/login_bg.jpg');
            height: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background-color: #ffffff;
            padding: 60px;
            border-radius: 20px;
            box-shadow: 
                0 10px 25px rgba(0, 0, 0, 0.1),
                0 20px 48px rgba(0, 0, 0, 0.1),
                0 30px 66px rgba(0, 0, 0, 0.06);
            width: 500px;
            max-width: 90%;
            position: relative;
            overflow: hidden;
            transform: translateY(-20px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .login-container:hover {
            transform: translateY(-25px);
            box-shadow: 
                0 14px 30px rgba(0, 0, 0, 0.15),
                0 24px 60px rgba(0, 0, 0, 0.15),
                0 34px 80px rgba(0, 0, 0, 0.1);
        }
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 150px;
            background: linear-gradient(to bottom, #bcdaf5, #ffffff);
            z-index: 0;
        }
        .login-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 40px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .wave-icon {
            font-size: 46px;
            margin-left: 10px;
            vertical-align: middle;
            display: inline-block;
            animation: wave 2s infinite;
            transform-origin: 70% 70%;
            position: relative;
            top: -8px; /* This moves the icon up */
        }
        @keyframes wave {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(14deg); }
            20% { transform: rotate(-8deg); }
            30% { transform: rotate(14deg); }
            40% { transform: rotate(-4deg); }
            50% { transform: rotate(10deg); }
            60% { transform: rotate(0deg); }
            100% { transform: rotate(0deg); }
        }
        .form-group {
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }
        .form-label {
            display: block;
            font-size: 16px;
            font-weight: 600;
            color: #555;
            margin-bottom: 10px;
        }
        .form-control {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #67b2ff;
            box-shadow: 0 0 0 4px rgba(103, 178, 255, 0.1);
        }
        .btn-login {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            background-color: #67b2ff;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.1s ease;
            position: relative;
            z-index: 1;
        }
        .btn-login:hover {
            background-color: #4a9eff;
        }
        .btn-login:active {
            transform: scale(0.98);
        }
        .error-message {
            color: #dc3545;
            font-size: 16px;
            margin-top: 15px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="background">
        <div class="login-container">
            <h2 class="login-title">hey, hello <span class="wave-icon">👋</span></h2>
            <h3 class="login-title">WELCOME TO ABER-AL-ALAM DASHBOARD</h3>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>