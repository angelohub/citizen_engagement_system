<?php
session_start();
// Optional: enforce login if needed
// if (!isset($_SESSION['user_id'])) {
//   header("Location: login.php");
//   exit;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaint Tracker</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f7f8;
      color: #333;
      margin: 0;
      padding: 0;
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

    .container {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input[type="email"] {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 16px;
      transition: border-color 0.3s;
    }

    input[type="email"]:focus {
      border-color: #007BFF;
      outline: none;
    }

    button {
      padding: 12px;
      background-color: #007BFF;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #0056b3;
    }

    .complaint {
      margin-top: 20px;
      padding: 15px;
      border-left: 4px solid #007BFF;
      background-color: #f9f9f9;
      border-radius: 4px;
    }

    .complaint h3 {
      margin: 0 0 10px;
    }

    .complaint p {
      margin: 5px 0;
    }

    .no-complaints {
      margin-top: 20px;
      text-align: center;
      color: #888;
    }
  </style>
</head>
<body>

<div class="navbar">
  <div class="title">Complaint Tracker</div>
  <div>
    <a href="user_dashboard.php">Dashboard</a>
    <a href="user_logout.php">Logout</a>
  </div>
</div>

<div class="container">
  <h2>Track Your Complaints</h2>
  <form method="GET" id="emailForm">
    <input type="email" name="email" id="emailInput" placeholder="Enter your email" required />
    <button type="submit">Track</button>
  </form>

  <?php
  if (isset($_GET['email'])) {
    $email = $_GET['email'];

    $conn = new mysqli("localhost", "root", "", "citizen_feedback");

    if ($conn->connect_error) {
      echo "<p class='no-complaints'>Connection failed: " . htmlspecialchars($conn->connect_error) . "</p>";
    } else {
      $stmt = $conn->prepare("SELECT c.title, c.status, c.created_at
                              FROM complaints c
                              JOIN users u ON c.user_id = u.id
                              WHERE u.email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<div class='complaint'>
                  <h3>" . htmlspecialchars($row['title']) . "</h3>
                  <p>Status: <strong>" . htmlspecialchars($row['status']) . "</strong></p>
                  <p>Submitted on: " . htmlspecialchars($row['created_at']) . "</p>
                </div>";
        }
      } else {
        echo "<p class='no-complaints'>No complaints found for this email.</p>";
      }

      $stmt->close();
      $conn->close();
    }
  }
  ?>
</div>

<script>
  document.getElementById('emailForm').addEventListener('submit', function(e) {
    const emailInput = document.getElementById('emailInput');
    if (!emailInput.value) {
      e.preventDefault();
      alert('Please enter your email address.');
    }
  });
</script>

</body>
</html>
