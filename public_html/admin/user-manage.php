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
		<h2>Manage Users</h2>
		<div class="form-border">
			<form action="user-results.php" method="post">
				<div class="form-container">
					<h3>Search for a user by first and/or last name.</h3>
					<input type="hidden" id="form_type" name="form_type" value="name_search">

					<label for="fname"><b>First Name</b></label>
					<input type="text" placeholder="Enter first name..." name="fname" id="fname" value="">

					<label for="lname"><b>Last Name</b></label>
					<input type="text" placeholder="Enter last name..." name="lname" id="lname" value="">

					<div></div>
					<button type="submit" class="submit-button">Submit</button>
				</div>
			</form>

			<hr>

			<form action="user-results.php" method="post">
				<div class="form-container">
					<h3>Search for a user by username and/or email.</h3>
					<input type="hidden" id="form_type" name="form_type" value="username_search">

					<label for="username"><b>Username</b></label>
					<input type="text" placeholder="Enter username..." name="username" id="username" value="">

					<label for="email"><b>Email</b></label>
					<input type="text" placeholder="Enter email address..." name="email" id="email" value="">

					<div></div>
					<button type="submit" class="submit-button">Submit</button>
				</div>
			</form>
		</div>
	</div>

</body>

</html>