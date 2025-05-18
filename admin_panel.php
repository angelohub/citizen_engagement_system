<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit;
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "citizen_feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If you want to handle status updates on this page, you can do it here (optional)
// Or keep update_status.php separate for updates (recommended).

echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Panel - Complaints</title>
<style>
  body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: #f5f7fa;
    margin: 20px;
    color: #333;
  }
  .logout-form {
    text-align: right;
    margin-bottom: 20px;
  }
  .logout-form button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 14px;
    font-size: 14px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.25s ease;
  }
  .logout-form button:hover {
    background-color: #0056b3;
  }
  .complaint-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgb(0 0 0 / 0.1);
    padding: 20px 25px;
    margin-bottom: 20px;
    transition: transform 0.2s ease;
  }
  .complaint-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgb(0 0 0 / 0.15);
  }
  .complaint-card h3 {
    color: #0056b3;
    margin-bottom: 8px;
  }
  .complaint-card p {
    font-size: 16px;
    margin: 6px 0;
  }
  .complaint-card strong {
    color: #222;
  }
  form.update-form {
    margin-top: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  select {
    padding: 7px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    min-width: 140px;
    cursor: pointer;
    transition: border-color 0.2s ease;
  }
  select:hover, select:focus {
    border-color: #0056b3;
    outline: none;
  }
  button.update-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 14px;
    font-size: 14px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.25s ease;
  }
  button.update-button:hover {
    background-color: #0056b3;
  }
  .message {
    text-align: center;
    margin-top: 40px;
    font-size: 18px;
    color: #888;
  }
</style>
</head>
<body>

<div class="logout-form">
  <form method="POST">
    <button type="submit" name="logout">Logout</button>
  </form>
</div>';

$result = $conn->query("SELECT c.id, u.name, c.title, c.description, c.status 
                        FROM complaints c 
                        JOIN users u ON c.user_id = u.id");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='complaint-card'>
            <h3>" . htmlspecialchars($row['title']) . "</h3>
            <p><strong>From:</strong> " . htmlspecialchars($row['name']) . "</p>
            <p><strong>Status:</strong> " . htmlspecialchars($row['status']) . "</p>
            <form class='update-form' action='update_status.php' method='POST' onsubmit='return confirmUpdate(this);'>
              <input type='hidden' name='id' value='" . (int)$row['id'] . "'>
              <select name='status'>
                <option value='Pending'" . ($row['status'] == 'Pending' ? ' selected' : '') . ">Pending</option>
                <option value='In Progress'" . ($row['status'] == 'In Progress' ? ' selected' : '') . ">In Progress</option>
                <option value='Resolved'" . ($row['status'] == 'Resolved' ? ' selected' : '') . ">Resolved</option>
              </select>
              <button class='update-button' type='submit'>Update</button>
            </form>
        </div>";
    }
} else {
    echo "<p class='message'>No complaints found.</p>";
}

echo "<script>
function confirmUpdate(form) {
  const status = form.status.value;
  return confirm('Are you sure you want to update the status to \"' + status + '\"?');
}
</script>";

echo '</body></html>';

// Close connection only once here
$conn->close();
?>
