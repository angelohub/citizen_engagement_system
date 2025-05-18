<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Complaint</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #f0f4f7, #e8eff5);
      margin: 0;
      padding: 0;
    }

    .navbar {
      background-color: #4a90e2;
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

    .form-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      height: calc(100vh - 70px); /* Adjust for navbar height */
    }

    .form-container {
      background-color: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 400px;
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
    }

    .form-container input,
    .form-container textarea,
    .form-container select,
    .form-container button {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      transition: 0.3s;
    }

    .form-container input:focus,
    .form-container textarea:focus,
    .form-container select:focus {
      border-color: #4a90e2;
      box-shadow: 0 0 5px rgba(74, 144, 226, 0.5);
      outline: none;
    }

    .form-container button {
      background-color: #4a90e2;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .form-container button:hover {
      background-color: #357ab8;
    }

    .form-container .error {
      color: red;
      font-size: 13px;
      display: none;
      margin-top: -10px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

<div class="navbar">
  <div class="title">Submit a Complaint</div>
  <div>
    <a href="user_dashboard.php">Dashboard</a>
    <a href="user_logout.php">Logout</a>
  </div>
</div>

<div class="form-wrapper">
  <div class="form-container">
    <h2>Submit Your Complaint</h2>
    <form id="complaintForm" action="handle_submit.php" method="POST">
      <input type="text" name="name" placeholder="Your Name" required>
      <div class="error" id="nameError">Please enter your name.</div>

      <input type="email" name="email" placeholder="Your Email" required>
      <div class="error" id="emailError">Please enter a valid email.</div>

      <input type="text" name="title" placeholder="Complaint Title" required>
      <div class="error" id="titleError">Please enter a complaint title.</div>

      <textarea name="description" placeholder="Describe your issue" required></textarea>
      <div class="error" id="descError">Please provide a description.</div>

      <select name="category" required>
        <option value="">Select Category</option>
        <option value="public safety">Public Safety</option>
        <option value="Health Services">Health Services</option>
        <option value="Urban planning">Urban Planning</option>
        <option value="Utilities and Energy">Utilities and Energy</option>
        <option value="Water and Sanitation">Water and Sanitation</option>
      </select>
      <div class="error" id="categoryError">Please select a category.</div>

      <button type="submit">Submit</button>
    </form>
  </div>
</div>

<script>
  // Optional JS validation
  document.getElementById('complaintForm').addEventListener('submit', function (e) {
    let valid = true;

    const name = this.name.value.trim();
    const email = this.email.value.trim();
    const title = this.title.value.trim();
    const description = this.description.value.trim();
    const category = this.category.value;

    document.querySelectorAll('.error').forEach(el => el.style.display = 'none');

    if (!name) {
      document.getElementById('nameError').style.display = 'block';
      valid = false;
    }
    if (!email || !email.includes('@')) {
      document.getElementById('emailError').style.display = 'block';
      valid = false;
    }
    if (!title) {
      document.getElementById('titleError').style.display = 'block';
      valid = false;
    }
    if (!description) {
      document.getElementById('descError').style.display = 'block';
      valid = false;
    }
    if (!category) {
      document.getElementById('categoryError').style.display = 'block';
      valid = false;
    }

    if (!valid) e.preventDefault(); // Stop form from submitting if invalid
  });
</script>

</body>
</html>
