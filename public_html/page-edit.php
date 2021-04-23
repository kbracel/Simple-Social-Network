<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//check user is creator
	$page_info_query = 'SELECT user_id FROM user_page WHERE page_id = ?';

	$stmt = $con->prepare($page_info_query);
	$stmt->bind_param('i', $_POST['page_id']);
	$stmt->execute();
	$stmt->bind_result($creator_id);
	$stmt->fetch();
	$stmt->close();

	if ($creator_id != $_SESSION['id']) {
		header('Location: home.php');
		exit;
	}

	//update page about
	$page_update = 'UPDATE user_page SET about=? WHERE page_id=?';
	$redirect_location = "page-view.php?page_id=" . $_POST['page_id'];

	$stmt = $con->prepare($page_update);
	$stmt->bind_param('si', $_POST['about'], $_POST['page_id']);
	$stmt->execute();
	$stmt->close();
	header('Location: ' . $redirect_location);
	exit;
}

$page_title = $page_about = $creator_id = "";
$about_error = "";

//check user is creator to view this page and
//get page information to display
$page_info_query = 'SELECT title, about, user_id FROM user_page WHERE page_id = ?';

$stmt = $con->prepare($page_info_query);
$stmt->bind_param('i', $_GET['page_id']);
$stmt->execute();
$stmt->bind_result($page_title, $page_about, $creator_id);
$stmt->fetch();
$stmt->close();

if ($creator_id != $_SESSION['id']) {
	header('Location: home.php');
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
	<link rel="stylesheet" type="text/css" href="normal-style.css">
</head>

<body>
	<?php $page = 'page';
	include('topnav.php'); ?>

	<div class="content">
		<h2 style="text-align: center">Edit Page About</h2>
		<h2 style="margin-top: 10px;"><?php echo $page_title; ?></h2>

		<div class="form-border">
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-container">
					<input type="hidden" id="page_id" name="page_id" value="<?php echo htmlspecialchars($_GET['page_id']); ?>">

					<div class="error"><?= $about_error ?></div>
					<label for="about"><b>About the Page</b></label>
					<textarea placeholder="Enter information about the page..." name="about" id="about" style="height: 150px;"><?= $page_about ?></textarea>

					<div></div>
					<button type="submit" class="submit-button">Submit</button>
				</div>
			</form>
		</div>
	</div>
</body>

</html>