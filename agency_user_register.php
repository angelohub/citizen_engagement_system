<?php
session_start();

// Connect to DB
$conn = new mysqli("localhost", "root", "", "citizen_feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$name = $username = $email = $password = $confirm_password = "";
$agency_id = 0;
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $agency_id = intval($_POST['agency_id']);
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if ($agency_id <= 0) {
        $errors[] = "Please select a valid agency.";
    }
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if username or email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM agency_users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Username or email already taken.";
        }
        $stmt->close();
    }

    // If no errors, insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO agency_users (agency_id, username, email, password, name) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $agency_id, $username, $email, $hashed_password, $name);
        if ($stmt->execute()) {
            $success = "Agency user registered successfully!";
            // Clear form fields
            $name = $username = $email = "";
            $agency_id = 0;
        } else {
            $errors[] = "Error registering user. Please try again.";
        }
        $stmt->close();
    }
}

// Fetch agencies for dropdown
$agency_options = "";
$agency_result = $conn->query("SELECT id, name FROM agencies ORDER BY name ASC");
while ($agency = $agency_result->fetch_assoc()) {
    $selected = ($agency['id'] == $agency_id) ? "selected" : "";
    $agency_options .= "<option value=\"{$agency['id']}\" $selected>" . htmlspecialchars($agency['name']) . "</option>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Register Agency User</title>
<style>
  body { font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; }
  h2 { text-align: center; }
  form { background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
  label { display: block; margin-top: 10px; }
  input, select { width: 100%; padding: 8px; margin-top: 4px; border-radius: 4px; border: 1px solid #ccc; }
  button { margin-top: 20px; padding: 10px 15px; background: #007BFF; color: white; border: none; border-radius: 6px; cursor: pointer; }
  button:hover { background: #0056b3; }
  .error { background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 6px; }
  .success { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 6px; }
</style>
</head>
<body>

<h2>Register Agency User</h2>

<?php if (!empty($errors)): ?>
    <div class="error">
        <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label for="agency_id">Select Agency:</label>
    <select name="agency_id" id="agency_id" required>
        <option value="">-- Select an agency --</option>
        <?= $agency_options ?>
    </select>

    <label for="name">Full Name:</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required />

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required />

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required />

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required />

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" required />

    <button type="submit">Register</button>
</form>

<p style="text-align:center; margin-top:20px;">
  Already have an account? <a href="agency_user_login.php">Login here</a>.
</p>

</body>
</html>

<?php
$conn->close();
?>
