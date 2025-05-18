<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['agency_user_id'])) {
    header("Location: agency_user_login.php");
    exit;
}

// Connect to DB
$conn = new mysqli("localhost", "root", "", "citizen_feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$agency_id = $_SESSION['agency_id'];
$agency_user_name = $_SESSION['agency_user_name'];

// Fetch agency name
$stmtAgency = $conn->prepare("SELECT name FROM agencies WHERE id = ?");
$stmtAgency->bind_param("i", $agency_id);
$stmtAgency->execute();
$stmtAgency->bind_result($agency_name);
$stmtAgency->fetch();
$stmtAgency->close();

// Fetch complaints assigned to this agency
$stmt = $conn->prepare("
    SELECT c.id, c.title, c.description, c.category, c.status, c.created_at, u.name AS user_name, u.email AS user_email
    FROM complaints c
    JOIN complaint_agencies ca ON c.id = ca.complaint_id
    JOIN users u ON c.user_id = u.id
    WHERE ca.agency_id = ?
    ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Agency Dashboard</title>
<style>
  body { font-family: Arial, sans-serif; max-width: 960px; margin: 40px auto; background: #f9f9f9; }
  h1, h2, h3 { text-align: center; }
  table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px #ddd; }
  th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
  th { background-color: #007BFF; color: white; }
  tr:nth-child(even) { background-color: #f2f8ff; }
  .logout { float: right; margin-bottom: 20px; }
  a.button {
    background: #007BFF; 
    color: white; 
    padding: 6px 12px; 
    text-decoration: none; 
    border-radius: 5px;
    display: inline-block;
  }
  a.button:hover { background: #0056b3; }
  .action-buttons {
    display: flex;
    gap: 8px;
  }
  .resolved-label {
    color: green;
    font-weight: bold;
  }
</style>
</head>
<body>

<div>
  <a href="agency_logout.php" class="button logout">Logout</a>
</div>

<h1>Welcome, <?= htmlspecialchars($agency_user_name) ?></h1>
<h2>Agency: <?= htmlspecialchars($agency_name) ?></h2>

<h3>Complaints Assigned to Your Agency</h3>

<?php if ($result->num_rows > 0): ?>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Category</th>
      <th>Status</th>
      <th>User</th>
      <th>Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['id']) ?></td>
      <td><?= htmlspecialchars($row['title']) ?></td>
      <td><?= htmlspecialchars($row['category']) ?></td>
      <td><?= htmlspecialchars($row['status']) ?></td>
      <td><?= htmlspecialchars($row['user_name']) ?> (<?= htmlspecialchars($row['user_email']) ?>)</td>
      <td><?= htmlspecialchars($row['created_at']) ?></td>
      <td>
        <div class="action-buttons">
          <a href="agency_complaint_view.php?id=<?= urlencode($row['id']) ?>" class="button">View</a>
        </div>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
<?php else: ?>
  <p>No complaints assigned to your agency yet.</p>
<?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
