<?php
session_start();

$loginSuccess = false;
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $conn = new mysqli("localhost", "root", "", "citizen_feedback");
    if ($conn->connect_error) {
        $errorMessage = "Connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($admin_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['admin_id'] = $admin_id;
                $loginSuccess = true;
            } else {
                $errorMessage = "Invalid password.";
            }
        } else {
            $errorMessage = "Admin not found.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0069d9;
        }
        p.register-link {
            margin-top: 15px;
        }
        p.register-link a {
            color: #007bff;
            text-decoration: none;
        }
        p.register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="POST" id="loginForm">
            <input type="text" name="username" placeholder="Enter your username" required />
            <input type="password" name="password" placeholder="Enter your password" required />
            <button type="submit">Login</button>
        </form>

        <p class="register-link">
            Don't have an account? <a href="admin_register.php">Register here</a>
        </p>
    </div>

    <?php if ($loginSuccess): ?>
        <script>
            alert("✅ Login successful!");
            window.location.href = "admin_dashboard.php";
        </script>
    <?php elseif (!empty($errorMessage)): ?>
        <script>
            alert("❌ <?php echo $errorMessage; ?>");
        </script>
    <?php endif; ?>

</body>
</html>
