<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include("config.php");
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
		<h2 style="text-align: center">Search Results</h2>
		<?php
		if ($_POST['form_type'] == 'user_search') {
			if (!empty($_POST['fname']) && !empty($_POST['lname'])) {
				$user_search_query = "SELECT fname, lname, username FROM user_account WHERE fname LIKE ? AND lname LIKE ?";
				if ($stmt = $con->prepare($user_search_query)) {
					$fname = $_POST['fname'];
					$fname = "%$fname%";
					$lname = $_POST['lname'];
					$lname = "%$lname%";
					$stmt->bind_param('ss', $fname, $lname);
					if ($stmt->execute()) {
						$stmt->bind_result($fname_list, $lname_list, $username_list);
						while ($stmt->fetch()) {
							echo '<h3><a href="./profile-view.php?username=' . $username_list . '">' . $fname_list . " " . $lname_list . "</a></h3>";
						}
						$stmt->close();
					}
				}
			} elseif (!empty($_POST['fname']) && empty($_POST['lname'])) {
				$user_search_query = "SELECT fname, lname, username FROM user_account WHERE fname LIKE ?";
				if ($stmt = $con->prepare($user_search_query)) {
					$fname = $_POST['fname'];
					$fname = "%$fname%";
					$stmt->bind_param('s', $fname);
					if ($stmt->execute()) {
						$stmt->bind_result($fname_list, $lname_list, $username_list);
						while ($stmt->fetch()) {
							echo '<h3><a href="./profile-view.php?username=' . $username_list . '">' . $fname_list . " " . $lname_list . "</a></h3>";
						}
						$stmt->close();
					}
				}
			} elseif (empty($_POST['fname']) && !empty($_POST['lname'])) {
				$user_search_query = "SELECT fname, lname, username FROM user_account WHERE lname LIKE ?";
				if ($stmt = $con->prepare($user_search_query)) {
					$lname = $_POST['lname'];
					$lname = "%$lname%";
					$stmt->bind_param('s', $lname);
					if ($stmt->execute()) {
						$stmt->bind_result($fname_list, $lname_list, $username_list);
						while ($stmt->fetch()) {
							echo '<h3><a href="./profile-view.php?username=' . $username_list . '">' . $fname_list . " " . $lname_list . "</a></h3>";
						}
						$stmt->close();
					}
				}
			} else {
				header('Location: search.php');
				exit;
			}
		} else if ($_POST['form_type'] == 'page_search') {
			if (!empty($_POST['title'])) {
				$page_search_query = "SELECT page_id, title FROM user_page WHERE title LIKE ?";
				$stmt = $con->prepare($page_search_query);
				$title = $_POST['title'];
				$title = "%$title%";
				$stmt->bind_param('s', $title);
				$stmt->execute();
				$stmt->bind_result($page_id_list, $pages_list);
				while ($stmt->fetch()) {
					echo '<h3><a href="./page-view.php?page_id=' . $page_id_list . '">' . $pages_list . "</a></h3>";
				}
				$stmt->close();
			} else {
				header('Location: search.php');
				exit;
			}
		}
		?>
	</div>

</body>

</html>