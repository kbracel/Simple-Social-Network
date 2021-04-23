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

	<?php $page = 'home';
	include('./topnav.php'); ?>

	<div class="content">
		<h2>Admin Home Page</h2>

		<h3>Manage Users</h3>
		<p>The manage users tab is where you can search through the user_account table.
			After searching you can select a user and then edit any of the user's information.</p>

		<h3>Backup Tables</h3>
		<p>The backup tables tab is where you can download a backup of any of the tables.
			There is a list of buttons and you can click on the one for the table you would like to backup, and it will download to your computer.</p>
	</div>

</body>

</html>