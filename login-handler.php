<?php
require 'include/mysql-connect.php';

function handleSubmit(&$con) {
  try {
    // get form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // validate
    if (empty($username) || empty($password)) {
      throw new Exception('All fields required');
    }

    // try to create new user record
    $q = "SELECT * FROM user WHERE username = ?";
    $stmt = $con->prepare($q); // returns mysqli_stmt object
    $stmt->bind_param('s', $username);

    // if successful route to login page
    if (!$stmt->execute()) {
      throw new Exception($stmt->error);
    }

    $result = $stmt->get_result(); // returns mysql_result object
    $user = $result->fetch_object(); // could be false

    // validate entered password with hash in db
    if (!$user || !password_verify($password, $user->password)) {
      throw new Exception('Invalid username or password');
    }

    // hooray, we made it, start a new session
    session_start();
    $_SESSION = [
      'id' => $user->id,
      'username' => $user->username,
    ];

    header('Location: ./');

    // else, display error
  } catch (Throwable $e) {
    echo '<p style="color:red">' . $e->getMessage() .
      '</p><a href="./login.php">Back</a>';
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  handleSubmit($con);
}
