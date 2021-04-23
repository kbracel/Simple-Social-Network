<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include('config.php');

//get page information to display
$page_info_query = 'SELECT user_page.title, user_page.about, user_page.date_created, user_account.fname, user_account.lname, user_account.username
FROM user_page
INNER JOIN user_account
ON user_page.user_id = user_account.user_id
WHERE user_page.page_id = ?';

$stmt = $con->prepare($page_info_query);
$stmt->bind_param('i', $_GET['page_id']);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) //check if page exists
{
	$stmt->bind_result($page_title, $page_about, $page_date, $creator_fname, $creator_lname, $creator_username);
	$stmt->fetch();
	$stmt->close();
} else {
	$stmt->close();
	header('Location: home.php');
	exit;
}

$is_user_member = FALSE;
$is_user_member_query = 'SELECT user_id FROM user_joins_page WHERE user_id = ? AND page_id = ?';

$stmt = $con->prepare($is_user_member_query);
$stmt->bind_param('ii', $_SESSION['id'], $_GET['page_id']);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
	$is_user_member = TRUE;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Simple Social Network</title>
	<meta name="description" content="Profile View Page">
	<meta name="author" content="Kyle Racel">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="normal-style.css">
</head>

<body>

	<?php include('topnav.php'); ?>

	<div class="content">
		<h2 style="text-align: center"><?php echo $page_title; ?></h2>

		<?php
		if ($is_user_member) {
			echo '<form action="page-leave.php" method="post">
			<input type="hidden" id="page_id" name="page_id" value="' . htmlspecialchars($_GET['page_id']) . '">
			<input type="submit" class="profile-page-button" name="profile-page-button" value="Leave Page" />
			</form>';
		} else {
			echo '<form action="page-join.php" method="post">
			<input type="hidden" id="page_id" name="page_id" value="' . htmlspecialchars($_GET['page_id']) . '">
			<input type="submit" class="profile-page-button" name="profile-page-button" value="Join Page" />
			</form>';
		}
		?>

		<div class="tab">
			<?php
			if ($is_user_member) {
				echo '<button class="tablinks" onclick="openTab(event, \'Posts\')" id="defaultOpen">Pages\'s Posts</button>';
				echo '<button class="tablinks" onclick="openTab(event, \'About\')">About this Page</button>';
				echo '<button class="tablinks" onclick="openTab(event, \'Users\')">Page\'s Users</button>';
			} else {
				echo '<button class="tablinks" onclick="openTab(event, \'About\')" id="defaultOpen">About this Page</button>';
			}
			?>
		</div>

		<div id="About" class="tabcontent">
			<p>About:</p>
			<p><?php echo $page_about; ?></p>
			<p>Date Created: <?php echo $page_date; ?></p>
			<div class="creator-container">
				<p>Created by: <a href="./profile-view.php?username=<?php echo $creator_username; ?>"><?php echo $creator_fname . " " . $creator_lname; ?></a></p>
			</div>
			<?php
			//display and edit page button if user is the creator
			if ($creator_username == $_SESSION['name']) {
				$edit_link = 'page-edit.php?page_id=' . $_GET['page_id'];
				echo '<div class="link-button"><a href="' . $edit_link . '">Edit Page</a></div>';
			}
			?>
		</div>

		<?php

		if ($is_user_member) {
			echo '<div id="Posts" class="tabcontent">';
			echo '<div class="form-border">
			<form action="page-post.php" method="post">
				<div class="form-container">
					<input type="hidden" id="page_id" name="page_id" value="' . htmlspecialchars($_GET['page_id']) . '">
					<label for="user_post">
						<h3>Write Post</h3>
					</label>
					<textarea placeholder="Write your post here..." name="user_post" id="user_post" style="height: 100px;"></textarea>
					<div></div>
					<button type="submit" class="post-button">Post</button>
				</div>
			</form>
		</div>';

			echo '<div class="posts-container">';
			$page_posts_query = 'SELECT user_account.fname, user_account.lname, user_account.username, page_post.post_text, page_post.date_posted
			FROM page_post
			INNER JOIN user_account
			ON page_post.user_id = user_account.user_id
			WHERE page_post.page_id = ?
			ORDER BY page_post.date_posted DESC';

			$stmt = $con->prepare($page_posts_query);
			$stmt->bind_param('i', $_GET['page_id']);
			$stmt->execute();
			$stmt->bind_result($fname_list, $lname_list, $username_list, $post_text_list, $date_posted_list);
			while ($stmt->fetch()) {
				echo '<div class="post-item">';
				echo '<h3><a href="./profile-view.php?username=' . $username_list . '">' . $fname_list . " " . $lname_list . "</a></h3>";
				echo "<p>" . $post_text_list . "</p>";
				echo "<p>" . $date_posted_list . "</p>";
				echo '</div>';
			}
			$stmt->close();
			echo '</div>';
			echo '</div>';

			echo '<div id="Users" class="tabcontent">';
			echo '<div class="connections-grid">';

			$page_users_query = 'SELECT user_account.fname, user_account.lname, user_account.username
			FROM user_account
			INNER JOIN user_joins_page ON user_joins_page.user_id = user_account.user_id
			WHERE user_joins_page.page_id = ?';

			if ($stmt = $con->prepare($page_users_query)) {
				$stmt->bind_param('i', $_GET['page_id']);
				if ($stmt->execute()) {
					$stmt->bind_result($fname_list, $lname_list, $username_list);
					while ($stmt->fetch()) {
						echo '<div class="connection-item">';
						echo '<h3><a href="./profile-view.php?username=' . $username_list . '">' . $fname_list . " " . $lname_list . "</a></h3>";
						echo '</div>';
					}
					$stmt->close();
				}
			}
			echo '</div>';
			echo '</div>';
		}
		?>

		<script>
			function openTab(evt, tabName) {
				var i, tabcontent, tablinks;
				tabcontent = document.getElementsByClassName("tabcontent");
				for (i = 0; i < tabcontent.length; i++) {
					tabcontent[i].style.display = "none";
				}
				tablinks = document.getElementsByClassName("tablinks");
				for (i = 0; i < tablinks.length; i++) {
					tablinks[i].className = tablinks[i].className.replace(" active", "");
				}
				document.getElementById(tabName).style.display = "block";
				evt.currentTarget.className += " active";
			}

			document.getElementById("defaultOpen").click();
		</script>
	</div>

</body>

</html>