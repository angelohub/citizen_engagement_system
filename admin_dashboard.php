<?php
session_start();

// Redirect to login if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Connect to DB
$conn = new mysqli("localhost", "root", "", "citizen_feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit;
}

// Handle deletion (only if admin logged in)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_complaint_id'])) {
    $delete_id = intval($_POST['delete_complaint_id']);

    $stmt = $conn->prepare("DELETE FROM complaints WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Complaint #$delete_id deleted successfully.";
    } else {
        $error = "Failed to delete complaint #$delete_id.";
    }
    $stmt->close();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaint_id'], $_POST['status']) && !isset($_POST['logout'], $_POST['delete_complaint_id'], $_POST['route_complaint_id'])) {
    $complaint_id = intval($_POST['complaint_id']);
    $new_status = $_POST['status'];
    $allowed_statuses = ['Pending', 'In Progress', 'Resolved'];

    if (in_array($new_status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $complaint_id);
        $stmt->execute();
        $stmt->close();
        $message = "Complaint #$complaint_id status updated to '$new_status'.";
    } else {
        $error = "Invalid status selected.";
    }
}

// Handle routing to agency
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['route_complaint_id'], $_POST['agency_id'])) {
    $complaint_id = intval($_POST['route_complaint_id']);
    $agency_id = intval($_POST['agency_id']);

    $check_stmt = $conn->prepare("SELECT * FROM complaint_agencies WHERE complaint_id = ? AND agency_id = ?");
    $check_stmt->bind_param("ii", $complaint_id, $agency_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error = "Complaint is already assigned to the selected agency.";
    } else {
        $stmt = $conn->prepare("INSERT INTO complaint_agencies (complaint_id, agency_id, assigned_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $complaint_id, $agency_id);
        if ($stmt->execute()) {
            $_SESSION['complaint_routed'] = true;
            $stmt->close();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = "Failed to route complaint.";
            $stmt->close();
        }
    }
    $check_stmt->close();
}

// Fetch complaints
$sql = "SELECT c.id, c.title, c.description, c.category, c.status, c.created_at, u.name AS user_name, u.email AS user_email
        FROM complaints c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC";
$result = $conn->query($sql);

// Agencies
$agency_options = "";
$agency_result = $conn->query("SELECT id, name FROM agencies");
while ($agency = $agency_result->fetch_assoc()) {
    $agency_id = $agency['id'];
    $agency_name = htmlspecialchars($agency['name']);
    $agency_options .= "<option value=\"$agency_id\">$agency_name</option>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard - Complaint Management</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 20px;
            background: #f4f6f9;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .action-group form {
            margin-bottom: 5px;
        }
        .action-group button {
            padding: 5px 10px;
            border: none;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
        .action-group button:hover {
            opacity: 0.9;
        }
        select {
            padding: 5px;
        }
        .logout-container {
            text-align: right;
        }
        .logout-container button {
            background-color: #007bff;
            padding: 6px 12px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .message, .error {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }
        .message { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .delete-btn {
            background-color: #007bff;
        }
        .delete-btn:hover {
            opacity: 0.9;
        }
    </style>
    <script>
        function confirmLogout(e) {
            if (!confirm("Are you sure you want to log out?")) {
                e.preventDefault();
            }
        }
        function confirmDelete() {
            return confirm('Are you sure you want to delete this complaint?');
        }
    </script>
</head>
<body>

<?php if (isset($_SESSION['complaint_routed'])): ?>
<script>
    alert("Complaint assigned successfully to the agency.");
</script>
<?php unset($_SESSION['complaint_routed']); endif; ?>

<div class="logout-container">
    <form method="POST" onsubmit="confirmLogout(event)">
        <button type="submit" name="logout">Logout</button>
    </form>
</div>

<h1>Admin Dashboard - Manage Complaints</h1>

<?php if (isset($message)): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ID</th><th>User</th><th>Email</th><th>Title</th><th>Category</th><th>Description</th><th>Status</th><th>Submitted</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td><?= htmlspecialchars($row['user_email']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td>
                <div class="action-group">
                    <form method="POST">
                        <input type="hidden" name="complaint_id" value="<?= $row['id'] ?>" />
                        <select name="status">
                            <?php foreach (['Pending', 'In Progress', 'Resolved'] as $status): ?>
                                <option value="<?= $status ?>" <?= $row['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit">Update</button>
                    </form>

                    <form method="POST">
                        <input type="hidden" name="route_complaint_id" value="<?= $row['id'] ?>" />
                        <select name="agency_id" required>
                            <option value="">Assign to agency</option>
                            <?= $agency_options ?>
                        </select>
                        <button type="submit">Route</button>
                    </form>

                    <form method="POST" onsubmit="return confirmDelete();" style="display:inline;">
                        <input type="hidden" name="delete_complaint_id" value="<?= $row['id'] ?>" />
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="9" style="text-align:center;">No complaints found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>

<?php $conn->close(); ?>
