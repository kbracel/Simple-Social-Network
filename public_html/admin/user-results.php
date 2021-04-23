<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
	header('Location: ../index.php');
	exit;
}
if ($_SESSION['type'] != 'admin') {
	header('Location: ../home.php');
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Simple Social Network</title>
	<meta name="description" content="Templage Page">
	<meta name="author" content="Kyle Racel">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../normal-style.css">
</head>

<body>

	<?php $page = 'manage';
	include('./topnav.php'); ?>

	<div class="content">
		<h2>User Search Results</h2>
		<p>Display search results here.</p>
	</div>

</body>

</html>