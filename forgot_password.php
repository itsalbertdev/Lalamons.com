<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'lalamons_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the email exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate reset token
        $token = bin2hex(random_bytes(50));
        $update_sql = "UPDATE users SET reset_token = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $token, $email);
        $update_stmt->execute();

        // Send reset email
        $reset_link = "http://localhost/lalamons/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n$reset_link";

        // SMTP Configuration
        require_once '/path/to/vendor/autoload.php'; // Adjust this path according to your Composer installation

        $transport = (new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
            ->setUsername('your_email@gmail.com') // Your email address
            ->setPassword('your_email_password'); // Your email password

        $mailer = new Swift_Mailer($transport);
        $message = (new Swift_Message($subject))
            ->setFrom(['your_email@gmail.com' => 'Lalamons'])
            ->setTo([$email])
            ->setBody($message);

        try {
            $result = $mailer->send($message);
            if ($result) {
                $success = "A password reset link has been sent to your email.";
            } else {
                $error = "Failed to send email.";
            }
        } catch (Exception $e) {
            $error = "Error sending email: " . $e->getMessage();
        }
    } else {
        $error = "No account found with this email.";
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

        .forgot-password-container {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #333;
        }

        .forgot-password-container h1 {
            font-size: 24px;
            color: #d32f2f;
            margin-bottom: 10px;
        }

        .forgot-password-container p {
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

        .reset-button {
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

        .reset-button:hover {
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
    <div class="forgot-password-container">
        <h1>LALAMONS</h1>
        <p>Food Delivery App</p>
        <form method="POST" action="">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <button type="submit" class="reset-button">Send Reset Link</button>
            <?php if (!empty($error)) : ?>
                <div class="error-message"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)) : ?>
                <div class="success-message"><?= htmlspecialchars($success); ?></div>
            <?php endif; ?>
        </form>
        <div class="login-section">
            <p>Remember your password? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
