<?php
require 'include/mysql-connect.php';

function handleSubmit(&$con) {
  try {
    // get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // validate
    if (empty($username) || empty($password) ||
      empty($confirm)) {
      throw new Exception('All fields required');
    }

    if ($password !== $confirm) {
      throw new Exception('Passwords do not match');
    }

    // hash password
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // try to create new user record
    $q = "INSERT INTO user(username, password) VALUES (?, ?)";
    $stmt = $con->prepare($q);
    $stmt->bind_param('ss', $username, $hash);

    // if successful route to login page
    if (!$stmt->execute()) {
      throw new Exception($stmt->error);
    }

    header('Location: login.php');

    // else, display error
  } catch (Throwable $e) {
    echo '<p style="color:red">' . $e->getMessage() .
      '</p><a href="./register.php">Back</a>';
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  handleSubmit($con);
}
