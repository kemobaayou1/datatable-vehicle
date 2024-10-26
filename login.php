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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .car-showcase {
            position: relative;
            width: 100%;
            height: 100%;
            background: url('image/bi.jpg') center/cover no-repeat;
            filter: brightness(0.8);
        }
        .overlay-text {
            position: absolute;
            bottom: 50px;
            left: 50px;
            color: white;
            z-index: 2;
        }
        .overlay-text h1 {
            font-size: 3.5rem;
            margin: 0;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .overlay-text p {
            font-size: 1.2rem;
            opacity: 0.8;
        }
        .right-panel {
            flex: 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .login-form {
            width: 400px;
            max-width: 100%;
        }
        .login-header {
            margin-bottom: 40px;
            text-align: left;
        }
        .login-header h2 {
            font-size: 32px;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        .login-header p {
            color: #666;
            font-size: 16px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control {
            width: calc(100% - 30px);
            padding: 15px;
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            box-sizing: border-box;
        }
        .form-control:focus {
            border-color: #1a1a1a;
            background: white;
            outline: none;
        }
        .btn-login {
            width: 60%;
            padding: 15px;
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            margin: 0 auto;
        }
        .btn-login:hover {
            background: #333;
            transform: translateY(-2px);
        }
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 15px;
            text-align: center;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .left-panel {
                display: none;
            }
            .right-panel {
                padding: 20px;
            }
        }
        /* Add a container div for the button to match form-group width */
        .button-container {
            width: 100%;
            box-sizing: border-box;
            padding: 0 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <div class="car-showcase"></div>
            <div class="overlay-text">
                <h1>KHALIFA HOLDINGS GROUP</h1>
                <p>Car Management System</p>
            </div>
        </div>
        <div class="right-panel">
            <div class="login-form">
                <div class="login-header">
                    <h2>Welcome Back</h2>
                    <p>Please sign in to continue</p>
                </div>
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
                    <div class="button-container">
                        <button type="submit" class="btn-login">Sign In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
