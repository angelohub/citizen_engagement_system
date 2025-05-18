<?php
$conn = new mysqli("localhost", "root", "", "citizen_feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$title = $_POST['title'] ?? '';
$desc = $_POST['description'] ?? '';
$category = $_POST['category'] ?? '';

// Output header + style + navbar
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Complaint Confirmation</title>
  <style>
    body {
      background: linear-gradient(to right, #f0f4f8, #e0efff);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      color: #333;
    }
    .navbar {
      background-color: #007BFF;
      padding: 15px 20px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .navbar .title {
      font-weight: bold;
      font-size: 18px;
    }
    .navbar a {
      color: white;
      text-decoration: none;
      margin-left: 15px;
      font-size: 14px;
    }
    .navbar a:hover {
      text-decoration: underline;
    }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    h2 {
      color: #28a745;
      margin-bottom: 20px;
    }
    p {
      font-size: 16px;
      margin-bottom: 10px;
    }
    strong {
      color: #444;
    }
    .back-button, .status-button {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      transition: background-color 0.3s ease;
    }
    .back-button:hover, .status-button:hover {
      background-color: #0056b3;
    }
    .status-button {
      margin-left: 10px;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <div class="title">Complaint System</div>
    <div>
      <a href="user_dashboard.php">Dashboard</a>
      <a href="user_logout.php">Logout</a>
    </div>
  </div>
  <div class="container">
HTML;

// Insert or get user
$stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
$stmt->bind_param("ss", $name, $email);
$stmt->execute();

$user_id = $conn->insert_id;
if ($user_id === 0) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_id = $user['id'] ?? null;
    if (!$user_id) {
        die("<p style='color:red;'>❌ Failed to retrieve user ID.</p></div></body></html>");
    }
}

// Get agency_id by category
$stmt = $conn->prepare("SELECT id FROM agencies WHERE category = ? LIMIT 1");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
$agency = $result->fetch_assoc();

$agency_id = $agency['id'] ?? null;
if ($agency_id === null) {
    echo "<p style='color:red;'>❌ No matching agency found for category '<strong>" . htmlspecialchars($category) . "</strong>'.</p>";
    echo '<a href="submit_complaint.php" class="back-button">⬅ Go Back</a>';
    exit("</div></body></html>");
}

// Insert complaint
$stmt = $conn->prepare("INSERT INTO complaints (user_id, id, title, description, category) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $user_id, $agency_id, $title, $desc, $category);
$stmt->execute();

$complaint_id = $conn->insert_id;  // get inserted complaint id

// Show confirmation
echo "<h2>✅ Complaint submitted successfully!</h2>";
echo "<p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>";
echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
echo "<p><strong>Title:</strong> " . htmlspecialchars($title) . "</p>";
echo "<p><strong>Description:</strong> " . nl2br(htmlspecialchars($desc)) . "</p>";
echo "<p><strong>Category:</strong> " . htmlspecialchars($category) . "</p>";

// Buttons
echo '<a href="submit_complaint.php" class="back-button">⬅ Submit Another Complaint</a>';
echo '<a href="track_status.php?id=' . urlencode($complaint_id) . '" class="status-button">🔍 Check Complaint Status</a>';

echo "</div></body></html>";
?>
