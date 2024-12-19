<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $repeat_password = trim($_POST['repeat_password']);

    if ($password !== $repeat_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'lalamons_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the email already exists
        $check_sql = "SELECT * FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            // Insert new user
            $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $full_name, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = "Account created successfully. You can now log in.";
            } else {
                $error = "Error creating account. Please try again.";
            }
            $stmt->close();
        }

        $check_stmt->close();
        $conn->close();
    }
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

        .register-container {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #333;
        }

        .register-container h1 {
            font-size: 24px;
            color: #d32f2f;
            margin-bottom: 10px;
        }

        .register-container p {
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

        .register-button {
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

        .register-button:hover {
            background: #b71c1c;
        }

        .login-section {
            margin-top: 15px;
            font-size: 14px;
        }

        .login-section a {
            color: #d32f2f;
            font-weight: bold;
            text-decoration: none;
        }

        .error-message {
            margin-top: 10px;
            color: #d32f2f;
            font-size: 14px;
        }

        .success-message {
            margin-top: 10px;
            color: #388e3c;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>LALAMONS</h1>
        <p>Food Delivery App</p>
        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="full_name" placeholder="Full Name" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="password" name="repeat_password" placeholder="Repeat Password" required>
            </div>
            <button type="submit" class="register-button">Register</button>
            <?php if (!empty($error)) : ?>
                <div class="error-message"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)) : ?>
                <div class="success-message"><?= htmlspecialchars($success); ?></div>
            <?php endif; ?>
        </form>
        <div class="login-section">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
