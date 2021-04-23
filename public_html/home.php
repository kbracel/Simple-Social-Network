<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}

include("config.php");

$post_query = 'INSERT INTO user_post(user_id, date_posted, post_text) VALUES(?, NOW(), ?)';
$data_check = TRUE;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//check if post blank
	if (empty($_POST["user-post"]) || !isset($_POST['user-post'])) {
		$password2_error = "* Post can not be blank.";
		$data_check = FALSE;
	}

	if ($data_check) {
		if ($stmt = $con->prepare($post_query)) {
			$stmt->bind_param('ss', $_SESSION['id'], $_POST['user-post']);
			if ($stmt->execute()) {
				$stmt->close();
				header('Location: home.php');
				exit;
			} else {
				echo 'Could not execute statement.';
			}
		} else {
			echo 'Could not prepare post insert statement.';
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Simple Social Network</title>
	<meta name="description" content="Home Page">
	<meta name="author" content="Kyle Racel">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="normal-style.css">
</head>

<body>
	<?php $page = 'home';
	include('topnav.php'); ?>

	<div class="content">
		<h2 style="text-align: center">Home Page</h2>

		<div class="form-border">
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-container">
					<label for="user-post">
						<h3>Write Post</h3>
					</label>
					<textarea placeholder="Write your post here..." name="user-post" id="user-post" style="height: 100px;"></textarea>
					<div></div>
					<button type="submit" class="post-button">Post</button>
				</div>
			</form>
		</div>

		<div class="posts-container">
			<?php
			$home_query = 'SELECT table1.fname, table1.lname, table1.username, table1.cur_id, user_post.post_text, user_post.date_posted
			FROM (
			SELECT fname, lname, username, user_id AS cur_id
			FROM user_account
			WHERE user_id = ?
			UNION
			SELECT user_account.fname, user_account.lname, user_account.username, connection.user_id2 AS cur_id
			FROM connection
			INNER JOIN user_account ON connection.user_id2 = user_account.user_id
			WHERE connection.user_id1 = ?
			UNION
			SELECT user_account.fname, user_account.lname, user_account.username, connection.user_id1 AS cur_id
			FROM connection
			INNER JOIN user_account ON connection.user_id1 = user_account.user_id
			WHERE connection.user_id2 = ?
			)table1
			INNER JOIN user_post ON table1.cur_id = user_post.user_id
			ORDER BY user_post.date_posted DESC';

			if ($stmt = $con->prepare($home_query)) {
				$stmt->bind_param('iii', $_SESSION['id'], $_SESSION['id'], $_SESSION['id']);
				if ($stmt->execute()) {
					$stmt->bind_result($fname_list, $lname_list, $username_list, $user_id_list, $post_text_list, $date_posted_list);
					while ($stmt->fetch()) {
						echo '<div class="post-item">';
						echo '<h3><a href="./profile-view.php?username=' . $username_list . '">' . $fname_list . " " . $lname_list . "</a></h3>";
						echo "<p>" . $post_text_list . "</p>";
						echo "<p>" . $date_posted_list . "</p>";
						echo '</div>';
					}
					$stmt->close();
				}
			}
			?>
		</div>
	</div>
</body>

</html>