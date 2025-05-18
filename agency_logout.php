<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to agency login page
header("Location: agency_user_login.php");
exit;
