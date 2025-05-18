<?php
session_start();

// Redirect logged-in user to dashboard (adjust if you have one)
if (isset($_SESSION['agency_user_id'])) {
    header("Location: agency_dashboard.php"); 
    exit;  
}

// Connect to DB
$conn = new mysqli("localhost", "root", "", "citizen_feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    if (empty($username_or_email) || empty($password)) {
        $error = "Please enter username/email and password.";
    } else {
        // Check user by username or email
        $stmt = $conn->prepare("SELECT id, password, name, agency_id FROM agency_users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashed_password, $name, $agency_id);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Password correct - create session
                $_SESSION['agency_user_id'] = $id;
                $_SESSION['agency_user_name'] = $name;
                $_SESSION['agency_id'] = $agency_id;

                header("Location: agency_dashboard.php"); // Redirect after login
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Agency User Login</title>
<style>
  body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; background: #f5f5f5; border-radius: 8px; }
  h2 { text-align: center; }
  form { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
  label { display: block; margin-top: 10px; }
  input { width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc; }
  button { margin-top: 20px; padding: 10px; width: 100%; background: #007BFF; color: white; border: none; border-radius: 6px; cursor: pointer; }
  button:hover { background: #0056b3; }
  .error { background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 6px; }
  .register-link { margin-top: 15px; text-align: center; }
  .register-link a { color: #007BFF; text-decoration: none; }
  .register-link a:hover { text-decoration: underline; }
</style>
</head>
<body>

<h2>Agency User Login</h2>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label for="username_or_email">Username or Email:</label>
    <input type="text" id="username_or_email" name="username_or_email" required />

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required />

    <button type="submit">Login</button>
</form>

<div class="register-link">
    Don't have an account? <a href="agency_user_register.php">Register here</a>.
</div>

</body>
</html>
