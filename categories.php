<?php
include 'include/header.php';
include 'include/category-functions.php';

if (!isset($_SESSION['id'])) {
    header('Location: ./');
}

require 'include/mysql-connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleSubmit($con);
}

list($categories, $catName, $catId) = categoryInit($con);
?>
<h1>Game Categories</h1>
<p>
    <a href="./">Home</a>
    <a href="logout.php">Log out</a>
</p>
<?php if(isset($catId)): ?>
<h5>Editing category id=<?=$catId?>, current name: <?=$catName?> <a href="categories.php">Cancel</a></h5>
<?php endif; ?>

<form action="" method="post">
    <fieldset>
        <legend>Add/edit category</legend>
        <input type="hidden" name="catid" value="<?=$catId ?? ''?>">
        <input type="text" name="catname" value="<?=$catName ?? ''?>" placeholder="Category name">
        <button type="submit">Save</button>
    </fieldset>
</form>

<ul>
<?php while($cat = $categories->fetch_object()): ?>
    <li><?=$cat->category_desc?> | 
    <a href="categories.php?edit=<?=$cat->category_id?>">Edit</a> | 
    <a href="delete-category.php?id=<?=$cat->category_id?>" onclick="return confirm('Are you sure?')">Delete</a></li>
<?php endwhile; ?>
</ul>