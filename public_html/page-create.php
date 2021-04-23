<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include("config.php");

$title_error = $about_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//check data
	$data_check = TRUE;

	if (empty($_POST['title'])) {
		$title_error = "* Title is required.";
		$data_check = FALSE;
	}

	if (empty($_POST['about'])) {
		$about_error = "* About is required.";
		$data_check = FALSE;
	}

	if ($data_check) {
		//create page
		$create_page = 'INSERT INTO user_page( user_id, title, about, date_created) VALUES( ?, ?, ?, CURDATE())';
		$stmt = $con->prepare($create_page);
		$stmt->bind_param('iss', $_SESSION['id'], $_POST['title'], $_POST['about']);
		$stmt->execute();
		$stmt->close();

		//find page id
		$page_title = $_POST['title'];
		$page_title = "%$page_title%";
		$find_page_id = 'SELECT page_id FROM user_page WHERE user_id = ? AND title LIKE ?';
		$stmt = $con->prepare($find_page_id);
		$stmt->bind_param('is', $_SESSION['id'], $page_title);
		$stmt->execute();
		$stmt->bind_result($page_id);
		$stmt->fetch();
		$stmt->close();

		//join creator to page
		$add_creator = 'INSERT INTO user_joins_page( user_id, page_id, date_joined) VALUES( ?, ?, CURDATE())';
		$stmt = $con->prepare($add_creator);
		$stmt->bind_param('ii', $_SESSION['id'], $page_id);
		$stmt->execute();
		$stmt->close();
		header('Location: page-view.php?page_id=' . $page_id);
		exit;
	}
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
	<link rel="stylesheet" type="text/css" href="normal-style.css">
</head>

<body>

	<?php $page = 'page';
	include('topnav.php'); ?>

	<div class="content">
		<h2>Create Page</h2>

		<div class="form-border">
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-container">
					<div class="error"><?= $title_error ?></div>
					<label for="title"><b>Page Title</b></label>
					<input type="text" placeholder="Enter a title for your page..." name="title" id="title" value="<?= isset($_POST['title']) ? $_POST['title'] : ''; ?>">

					<div class="error"><?= $about_error ?></div>
					<label for="about"><b>About the Page</b></label>
					<textarea placeholder="Enter information about the page..." name="about" id="about" style="height: 150px;"><?= isset($_POST['about']) ? $_POST['about'] : ''; ?></textarea>

					<div></div>
					<button type="submit" class="submit-button">Submit</button>
				</div>
			</form>
		</div>

	</div>

</body>

</html>