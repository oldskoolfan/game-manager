<?php
session_start();

if (!isset($_SESSION['id'])) {
  header('Location: ./');
  return;
}
/**
 * We come to this page when the user clicks delete on a game row.
 * The id is part of the url, e.g. delete.php?id=5.
 * All we want to do here is a DELETE query and return to the index page.
 */
require 'include/mysql-connect.php';

$id = $_GET['id'];

$q = 'delete from game where game_id = ?';
$stmt = $con->prepare($q);
$stmt->bind_param('i', $id);

if (!$stmt->execute()) {
  header('Location: index.php');
} else {
  echo $stmt->error;
}
