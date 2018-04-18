<?php include 'include/header.php' ?>
<form action="register-handler.php" method="post">
<fieldset>
  <legend>Create new user</legend>
  <div>
    <label>Username:
      <input name="username" type="text" />
    </label>
  </div>
  <div>
    <label>Password:
      <input name="password" type="password" />
    </label>
  </div>
  <div>
    <label>Confirm Password:
      <input name="confirm" type="password" />
    </label>
  </div>
  <div>
    <button type="submit">Save</button>
    <a href="./">Cancel</a>
  </div>
</fieldset>
</form>
<?php include 'include/footer.php' ?>
