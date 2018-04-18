<!doctype html>
<html>
<head>
	<title>xss example</title>
</head>
<body>
	<h1>XSS Example</h1>
	<form action="" method="post">
		<label>
			Enter your name:
			<input name="firstname" type="text">
		</label>
		<button type="submit">Submit</button>
	</form>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = $_POST['firstname'];

	if (!empty($name)) {

		// htmlentities() does the same thing (more chars included)
		$safeName = htmlspecialchars($name);

		$stripName = strip_tags($name);

		echo '<p>Hello, ' . $safeName . '!</p>';

		echo "<p>Hello, $stripName!</p>";

		$filterName = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
		echo "<p>Hello, $filterName!</p>";
	}
}
?>
</body>
</html>