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

	<?php $page = 'backup';
	include('./topnav.php'); ?>

	<div class="content">
		<h2>Backup Page</h2>
		<p>Click the button for the table you would like to backup.</p>
		<p>The backup file for that table will then download to your computer.</p>
		<form action="backup-table.php" method="post">
			<input type="hidden" id="table" name="table" value="user_account">
			<input type="submit" class="backup-button" name="backup-button" value="Backup user_account Table" />
		</form>

		<form action="backup-table.php" method="post">
			<input type="hidden" id="table" name="table" value="user_post">
			<input type="submit" class="backup-button" name="backup-button" value="Backup user_post Table" />
		</form>

		<form action="backup-table.php" method="post">
			<input type="hidden" id="table" name="table" value="connection">
			<input type="submit" class="backup-button" name="backup-button" value="Backup connection Table" />
		</form>

		<form action="backup-table.php" method="post">
			<input type="hidden" id="table" name="table" value="connection_request">
			<input type="submit" class="backup-button" name="backup-button" value="Backup connection_request Table" />
		</form>

		<form action="backup-table.php" method="post">
			<input type="hidden" id="table" name="table" value="user_page">
			<input type="submit" class="backup-button" name="backup-button" value="Backup user_page Table" />
		</form>

		<form action="backup-table.php" method="post">
			<input type="hidden" id="table" name="table" value="page_post">
			<input type="submit" class="backup-button" name="backup-button" value="Backup page_post Table" />
		</form>

		<form action="backup-table.php" method="post">
			<input type="hidden" id="table" name="table" value="user_joins_page">
			<input type="submit" class="backup-button" name="backup-button" value="Backup user_joins_page Table" />
		</form>
	</div>

</body>

</html>