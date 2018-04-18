<?php
session_start();

if (!isset($_SESSION['id'])) {
  header('Location: ./');
  return;
}
/**
 * We come to this page when the user clicks delete on a category row.
 * The id is part of the url, e.g. delete-category.php?id=5.
 * All we want to do here is a DELETE query and return to the category page.
 */
require 'include/mysql-connect.php';

$id = $_GET['id'];

$q = 'delete from category where category_id = ?';
$stmt = $con->prepare($q);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
  header('Location: categories.php');
} else {
  echo $stmt->error;
}
