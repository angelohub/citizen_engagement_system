<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "citizen_feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];

// Fetch latest 5 notifications for the user
$stmt = $conn->prepare("SELECT id, message, created_at, is_read FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();

// Fetch latest 5 complaints with their status and response for this user
$stmtComplaints = $conn->prepare("SELECT id, title, status, response, created_at FROM complaints WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmtComplaints->bind_param("i", $userId);
$stmtComplaints->execute();
$resultComplaints = $stmtComplaints->get_result();
$complaints = [];
while ($row = $resultComplaints->fetch_assoc()) {
    $complaints[] = $row;
}
$stmtComplaints->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Dashboard</title>
    <style>
        :root {
            --primary-color: #007bff;
            --primary-hover: #0056b3;
            --background: #f4f6f8;
            --card-bg: #ffffff;
            --text-color: #333;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background);
            margin: 0;
            padding: 0;
            color: var(--text-color);
        }
        .navbar {
            background-color: var(--primary-color);
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .title {
            font-size: 20px;
            font-weight: bold;
        }
        .navbar a.button {
            background-color: white;
            color: var(--primary-color);
            padding: 8px 16px;
            border-radius: 5px;
            font-weight: 500;
            text-decoration: none;
            transition: 0.3s;
        }
        .navbar a.button:hover {
            background-color: #e6e6e6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: var(--card-bg);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        h2 {
            margin-bottom: 25px;
        }
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 40px;
        }
        .action-buttons a {
            background-color: var(--primary-color);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .action-buttons a:hover {
            background-color: var(--primary-hover);
        }
        @media (min-width: 500px) {
            .action-buttons {
                flex-direction: row;
                justify-content: center;
            }
        }
        /* Notifications styles */
        ul.notifications, ul.complaints {
            list-style: none;
            padding: 0;
            max-width: 600px;
            margin: 0 auto 50px;
            text-align: left;
        }
        ul.notifications li, ul.complaints li {
            background-color: #d9edf7;
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 6px;
            color: #31708f;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        ul.notifications li.read {
            background-color: #f0f0f0;
            color: #666;
        }
        ul.notifications li small, ul.complaints li small {
            display: block;
            margin-top: 5px;
            font-size: 0.85em;
            color: #555;
        }
        ul.complaints li strong {
            display: block;
            margin-bottom: 5px;
        }
        .response-note {
            margin-top: 8px;
            font-style: italic;
            color: #444;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="title">User Dashboard</div>
        <a href="user_logout.php" class="button">Logout</a>
    </div>
    <div class="container">
        <h2>Manage Your Complaints</h2>
        <div class="action-buttons">
            <a href="submit_complaint.php">Submit a Complaint</a>
            <a href="track_status.php">Track Complaint Status</a>
        </div>

        <h2>Your Notifications</h2>
        <?php if (empty($notifications)): ?>
            <p>No new notifications.</p>
        <?php else: ?>
            <ul class="notifications">
                <?php foreach ($notifications as $note): ?>
                    <li class="<?= $note['is_read'] ? 'read' : '' ?>">
                        <?= htmlspecialchars($note['message']) ?>
                        <small><?= htmlspecialchars($note['created_at']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h2>Your Recent Complaints</h2>
        <?php if (empty($complaints)): ?>
            <p>You have not submitted any complaints yet.</p>
        <?php else: ?>
            <ul class="complaints">
                <?php foreach ($complaints as $comp): ?>
                    <li>
                        <strong><?= htmlspecialchars($comp['title']) ?></strong>
                        Status: <strong><?= htmlspecialchars($comp['status']) ?></strong>
                        <div class="response-note">
                            Response: <?= nl2br(htmlspecialchars($comp['response'] ?: 'No response yet.')) ?>
                        </div>
                        <small>Submitted on: <?= htmlspecialchars($comp['created_at']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>

