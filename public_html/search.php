<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Simple Social Network</title>
	<meta name="description" content="Search Results Page">
	<meta name="author" content="Kyle Racel">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="normal-style.css">
</head>

<body>

	<?php $page = 'search';
	include('topnav.php'); ?>

	<div class="content">
		<h2 style="text-align: center">Search Page</h2>
		<div class="form-border">
			<form action="search-results.php" method="post">
				<div class="form-container">
					<h3>Search for a user by first and/or last name.</h3>
					<input type="hidden" id="form_type" name="form_type" value="user_search">

					<label for="fname"><b>First Name</b></label>
					<input type="text" placeholder="Enter first name..." name="fname" id="fname" value="">

					<label for="lname"><b>Last Name</b></label>
					<input type="text" placeholder="Enter last name..." name="lname" id="lname" value="">

					<div></div>
					<button type="submit" class="submit-button">Submit</button>
				</div>
			</form>

			<hr>

			<form action="search-results.php" method="post">
				<div class="form-container">
					<h3>Search for a page by name.</h3>
					<input type="hidden" id="form_type" name="form_type" value="page_search">

					<label for="title"><b>Page Name</b></label>
					<input type="text" placeholder="Enter page name..." name="title" id="title" value="">

					<div></div>
					<button type="submit" class="submit-button">Submit</button>
				</div>
			</form>
		</div>
	</div>

</body>

</html>