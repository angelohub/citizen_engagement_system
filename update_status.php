<?php
// Enable error reporting for development (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    // Define allowed status values
    $allowed_statuses = ['Pending', 'In Progress', 'Resolved'];

    // Proceed only if inputs are valid
    if ($id > 0 && in_array($status, $allowed_statuses)) {
        // Establish database connection
        $conn = new mysqli("localhost", "root", "", "citizen_feedback");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare the SQL statement
        $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $status, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Handle statement preparation error
            die("Preparation failed: " . $conn->error);
        }

        // Close the database connection
        $conn->close();

        // Redirect back to the admin panel
        header("Location: admin_panel.php");
        exit();
    } else {
        // Invalid input handling
        echo "Invalid complaint ID or status.";
    }
} else {
    // Handle invalid request method
    echo "Invalid request method.";
}
?>
