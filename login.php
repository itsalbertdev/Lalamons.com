<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'lalamons_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to check user credentials
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];

            // Log session variables for debugging
            error_log("Session variables: " . print_r($_SESSION, true));
            error_log("User logged in: " . $user['id']);
            header("Location: main.php");
            exit();
        } else {
            $error = 'Incorrect password.';
        }
    } else {
        $error = 'No account found with this email.';
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lalamons</title>
    <style>
        /* Global Styles */
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom right, #f44336, #d32f2f);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .login-container {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #333;
        }

        .login-container h1 {
            font-size: 24px;
            color: #d32f2f;
            margin-bottom: 10px;
        }

        .login-container p {
            color: #555;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .form-options a {
            text-decoration: none;
            color: #d32f2f;
        }

        .login-button {
            width: 100%;
            background: #d32f2f;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-button:hover {
            background: #b71c1c;
        }

        .register-section {
            margin-top: 15px;
            font-size: 14px;
        }

        .register-section a {
            color: #d32f2f;
            font-weight: bold;
            text-decoration: none;
        }

        .error-message {
            margin-top: 10px;
            color: #d32f2f;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>LALAMONS</h1>
        <p>Food Delivery App</p>
        <form method="POST" action="">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-options">
                <label>
                    <input type="checkbox" name="remember_me"> Remember Me
                </label>
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
            <button type="submit" class="login-button">Login</button>
            <?php if (!empty($error)) : ?>
                <div class="error-message"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </form>
        <div class="register-section">
            <p>Don't have an account? <a href="register.php">Create an account</a></p>
        </div>
    </div>
</body>
</html>
