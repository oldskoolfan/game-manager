<?php
/**
 * This page contains our html form for adding to/editing games in our
 * collection. If we are editing, we need to get the game we are editing
 * from the database using the primary key we pass as a GET parameter in
 * the URL.
 */
include 'include/header.php';
require 'include/mysql-connect.php';
require 'include/game.php';

if (!isset($_SESSION['id'])) {
  header('Location: ./');
}

list($developers, $systems, $categories, $game) = Game::initGameForm($con);

?>
<p><a href="./">Back</a></p>
<form action="form-handler.php" method="post">
  <fieldset>
    <legend>Game Form</legend>
    <input type="hidden" value="<?=$game->id?>" name="id">
    <label>
      Title:
      <input type="text" name="title" value="<?=$game->title?>">
    </label>
    <label>
      Release Year:
      <input type="text" name="year" value="<?=$game->year?>">
    </label>
    <label>
      <input type="checkbox" name="beaten" <?=$game->beaten ? 'checked' : ''?>>Beaten?
    </label>
    <label>Developer:
      <select name="developer">
        <option></option>
        <!--
        To build our dropdowns we loop over the result rows and compare each
        row's developer_id with the developerId of the game we are editing.
        If there's a match, we add the word "selected" to the option tag so
        it will be selected in the dropdown.
        -->
        <?php foreach($developers as $row): ?>
          <option value="<?=$row['developer_id']?>"
            <?=$game->developer == $row['developer_id'] ? 'selected' : ''?>>
            <?=$row['developer_name']?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>System:
      <select name="system">
        <option></option>
        <!--
        Same idea here as with the developer dropdown.
        -->
        <?php foreach($systems as $row): ?>
          <option value="<?=$row['system_id']?>"
            <?=$game->system == $row['system_id'] ? 'selected' : ''?>>
            <?=$row['system_name']?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <ul>Categories:
    <?php while($category = $categories->fetch_object()): ?>
      <li>
        <input type="checkbox" name="categories[]" 
          value="<?=$category->category_id?>"
          <?=in_array($category->category_id, $game->categories) ? 'checked' : ''?>>
          <?=$category->category_desc?>
      </li>
    <?php endwhile; ?>
    </ul>
    <button type="submit">Save</button>
  </fieldset>
</form>
<?php include 'include/footer.php' ?>
