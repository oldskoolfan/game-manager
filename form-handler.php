<?php
/**
 * We land here when the user clicks submit on the form, which POSTs the form
 * data, which PHP collects for us in $_POST.
 *  1. Initialize a Game object with the form data
 *  2. Verify that we have what we need
 *  3. Get our SQL query (could be INSERT or UPDATE)
 *  4. Run the query
 *  5. Return to the index page
 */
require 'include/mysql-connect.php';
require 'include/game.php';

/**
 * we can use var_dump to check the value of $_POST if form submission doesn't
 * seem to be working correctly (commented out for now)
 */
// var_dump($_POST);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  handleForm($con);
}

function handleForm($con) {
  try {
    // step 1
    $game = new Game(
      $_POST['id'],
      $_POST['title'],
      $_POST['year'],
      isset($_POST['beaten']),
      $_POST['system'],
      $_POST['developer'],
      $_POST['categories'] ?? []
    );

    // step 2
    if (!$game->hasAllValues()) {
      throw new \Exception('please enter all fields');
    }

    // step 3
    $stmt = $game->getStatement($con);

    // step 4
    if (!$stmt->execute()) {
      throw new \Exception($stmt->error);
    }

    $game->saveCategories($con);

    // step 5
    header('Location: ./');
  } catch (\Throwable $e) {
    displayMessage($e->getMessage());
  }
}

function displayMessage($msg) {
  echo '<p>' . $msg . '</p><a href="game-form.php">Back</a>';
}
