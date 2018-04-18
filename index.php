<?php
include 'include/header.php';
/**
 * Our main page to display the games in an HTML table.
 * We need a MySQL connection so we can get the games to display.
 * We join to system and developer tables so we can get that information too.
 */
require 'include/mysql-connect.php';
require 'include/game.php';
// $q = 'select * from game
//   left join developer using(developer_id)
//   left join system using(system_id)
//   order by title';
// $result = $con->query($q);
$result = Game::initIndex($con);
?>
<h1>My Video Games</h1>
<p>
  <?php if(isset($_SESSION['id'])): ?>
    <span>Greetings, <?=$_SESSION['username']?>!</span>
    <a href="game-form.php">Add new</a>
    <a href="categories.php">View/edit categories</a>
    <a href="logout.php">Log out</a>
  <?php else: ?>
    <a href="login.php">Log in</a>
    <a href="register.php">Create account</a>
  <?php endif; ?>
</p>
<table>
  <tr>
    <th>Title</th>
    <th>Year</th>
    <th>Categories</th>
    <th>System</th>
    <th>Developer</th>
    <th>Beaten?</th>
    <?php if(isset($_SESSION['id'])): ?>
      <th>Action</th>
    <?php endif; ?>
  </tr>
<!--
here we loop over the game results and create a table row for each game
-->
<?php foreach($result as $row): ?>
  <tr>
    <td><?=$row['title']?></td>
    <td><?=$row['release_year']?></td>
    <td><?=$row['categories']?></td>
    <td><?=$row['system_name']?></td>
    <td><?=$row['developer_name']?></td>
    <td><?=$row['beaten'] ? 'Yes' : 'No'?></td>
    <?php if(isset($_SESSION['id'])): ?>
      <td>
        <!--
        Note for these edit and delete links, we add an "id" GET parameter to
        the url, so we can retrieve the game record to edit or delete by PK.
        It is up to us to give the GET parameter a key name, and here we choose
        "id".
        -->
        <a href="game-form.php?id=<?=$row['game_id']?>">Edit</a>
        &nbsp;
        <!--
        We can add a little javascript on the delete link to make sure the user
        really wants to delete the record before we actually do it.
        Returning false (if user chose NO) causes the browser to not follow the link.
        -->
        <a href="delete.php?id=<?=$row['game_id']?>" onclick="return confirm('Are you sure?')">Delete</a>
      </td>
    <?php endif; ?>
  </tr>
<?php endforeach; ?>
</table>
<?php include 'include/footer.php' ?>
