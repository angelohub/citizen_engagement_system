<?php
session_start();

// No need for PHPMailer anymore, so I removed require/autoload and use statements.

// Redirect if not logged in
if (!isset($_SESSION['agency_user_id'])) {
    header("Location: agency_user_login.php");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "citizen_feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$complaint_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$agency_id = $_SESSION['agency_id'];
$errors = [];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $response = trim($_POST['response']);

    if (!in_array($status, ['Pending', 'In Progress', 'Resolved'])) {
        $errors[] = "Invalid status selected.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE complaints SET status = ?, response = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $response, $complaint_id);
        if ($stmt->execute()) {
            $success = "Complaint updated successfully.";

            // Fetch user info for notification
            $stmtUser = $conn->prepare("
                SELECT u.id, u.name, c.title
                FROM complaints c
                JOIN users u ON c.user_id = u.id
                WHERE c.id = ?
            ");
            $stmtUser->bind_param("i", $complaint_id);
            $stmtUser->execute();
            $stmtUser->bind_result($userId, $userName, $complaintTitle);
            $stmtUser->fetch();
            $stmtUser->close();

            // Prepare notification message
            $notificationMessage = "Your complaint titled \"$complaintTitle\" has been updated. Status: $status.";

            // Insert notification into notifications table
            $stmtNotif = $conn->prepare("INSERT INTO notifications (user_id, complaint_id, message) VALUES (?, ?, ?)");
            $stmtNotif->bind_param("iis", $userId, $complaint_id, $notificationMessage);
            $stmtNotif->execute();
            $stmtNotif->close();

        } else {
            $errors[] = "Failed to update complaint.";
        }
        $stmt->close();
    }
}

// Fetch complaint details
$stmt = $conn->prepare("
    SELECT c.id, c.title, c.description, c.category, c.status, c.created_at, c.response,
           u.name AS user_name, u.email AS user_email
    FROM complaints c
    JOIN users u ON c.user_id = u.id
    JOIN complaint_agencies ca ON c.id = ca.complaint_id
    WHERE c.id = ? AND ca.agency_id = ?
");
$stmt->bind_param("ii", $complaint_id, $agency_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Complaint not found or not assigned to your agency.</p>";
    exit;
}

$complaint = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Complaint</title>
<style>
    body { font-family: Arial; max-width: 800px; margin: auto; padding: 20px; }
    h2 { text-align: center; }
    .form-box { background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
    label { display: block; margin-top: 10px; }
    textarea, select { width: 100%; padding: 8px; margin-top: 4px; }
    button, .button-link { margin-top: 15px; padding: 10px 15px; background: #007BFF; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; }
    button:hover, .button-link:hover { background: #0056b3; }
    .error, .success { padding: 10px; margin-bottom: 15px; border-radius: 6px; }
    .error { background: #f8d7da; color: #721c24; }
    .success { background: #d4edda; color: #155724; }
    .back { margin-top: 20px; display: inline-block; }
</style>
</head>
<body>

<h2>Complaint Details</h2>

<?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="form-box">
    <p><strong>Complaint ID:</strong> <?= htmlspecialchars($complaint['id']) ?></p>
    <p><strong>Title:</strong> <?= htmlspecialchars($complaint['title']) ?></p>
    <p><strong>Category:</strong> <?= htmlspecialchars($complaint['category']) ?></p>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($complaint['description'])) ?></p>
    <p><strong>Submitted by:</strong> <?= htmlspecialchars($complaint['user_name']) ?> (<?= htmlspecialchars($complaint['user_email']) ?>)</p>
    <p><strong>Date Submitted:</strong> <?= htmlspecialchars($complaint['created_at']) ?></p>

    <form method="POST">
        <label for="status">Update Status:</label>
        <select name="status" id="status" required>
            <option value="Pending" <?= $complaint['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="In Progress" <?= $complaint['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="Resolved" <?= $complaint['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
        </select>

        <label for="response">Response Notes:</label>
        <textarea name="response" id="response" rows="5" required><?= htmlspecialchars($complaint['response'] ?? '') ?></textarea>

        <button type="submit">Save Updates</button>
    </form>
</div>

<a href="agency_dashboard.php" class="back">← Back to Dashboard</a>

</body>
</html>

<?php $conn->close(); ?>
