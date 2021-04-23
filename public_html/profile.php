<?php
/*
 * Current users profile page
 */
session_start();

if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}

include('config.php');

$error = "";

//query to get all the profile data to display
if ($stmt = $con->prepare('SELECT fname, lname, phone_number, email_address, username, birthday, gender, relationship_status, interested_in, activities, interests, about, quote, hometown, current_location, register_date FROM user_account WHERE user_id = ?')) {
	$stmt->bind_param('i', $_SESSION['id']);
	$stmt->execute();
	$stmt->bind_result($fname, $lname, $phoneNumber, $email, $username, $birthday, $gender, $relationship, $interested, $activies, $interests, $about, $quote, $hometown, $curLocation, $register);
	$stmt->fetch();
	$stmt->close();
} else {
	$error = "statement not prepared";
}

//if the post form on the page is submit
//this section creates the user's post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$post_query = 'INSERT INTO user_post(user_id, date_posted, post_text) VALUES(?, NOW(), ?)';
	$data_check = TRUE;

	//check if post blank
	if (empty($_POST["user-post"]) || !isset($_POST['user-post'])) {
		$data_check = FALSE;
	}

	if ($data_check) {
		if ($stmt = $con->prepare($post_query)) {
			$stmt->bind_param('ss', $_SESSION['id'], $_POST['user-post']);
			if ($stmt->execute()) {
				$stmt->close();
				header('Location: profile.php');
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
	<meta name="description" content="User's Profile Page">
	<meta name="author" content="Kyle Racel">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="normal-style.css">
</head>

<body>

	<?php $page = 'profile';
	include('topnav.php'); ?>

	<div class="content">
		<p><?php echo $error; ?></p>
		<h2><?php echo $fname . " " . $lname; ?></h2>

		<div class="tab">
			<button class="tablinks" onclick="openTab(event, 'Posts')" id="defaultOpen">Your Posts</button>
			<button class="tablinks" onclick="openTab(event, 'About')">About You</button>
			<button class="tablinks" onclick="openTab(event, 'Connections')">Your Connections</button>
			<button class="tablinks" onclick="openTab(event, 'Pages')">Your Pages</button>
		</div>

		<div id="Posts" class="tabcontent">
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
				$post_query = 'SELECT user_account.fname, user_account.lname, user_account.username, user_account.user_id, user_post.post_text, user_post.date_posted
				FROM user_account
				INNER JOIN user_post ON user_account.user_id = user_post.user_id
				WHERE user_account.user_id = ?
				ORDER BY user_post.date_posted DESC';

				$stmt = $con->prepare($post_query);
				$stmt->bind_param('i', $_SESSION['id']);
				$stmt->execute();
				$stmt->bind_result($fname_list, $lname_list, $username_list, $user_id_list, $post_text_list, $date_posted_list);
				while ($stmt->fetch()) {
					echo '<div class="post-item">';
					echo '<h3><a href="./profile-view.php?username=' . $username_list . '">' . $fname_list . " " . $lname_list . "</a></h3>";
					echo "<p>" . $post_text_list . "</p>";
					echo "<p>" . $date_posted_list . "</p>";
					echo '</div>';
				}
				$stmt->close();
				?>
			</div>
		</div>

		<div id="About" class="tabcontent">
			<p>Phone Number: <?php echo $phoneNumber; ?></p>
			<p>Email Address: <?php echo $email; ?></p>
			<p>Username: <?php echo $username; ?></p>
			<p>Birthday: <?php echo $birthday; ?></p>
			<p>Gender: <?php echo $gender; ?></p>
			<p>Relationship Status: <?php echo $relationship; ?></p>
			<p>Interested In: <?php echo $interested; ?></p>
			<p>Activities: </p>
			<p><?php echo $activies; ?></p>
			<p>Interests: </p>
			<p><?php echo $interests; ?></p>
			<p>About: </p>
			<p><?php echo $about; ?></p>
			<p>Quote: </p>
			<p><?php echo $quote; ?></p>
			<p>Hometown: <?php echo $hometown; ?></p>
			<p>Current Location: <?php echo $curLocation; ?></p>
			<p>Date Registered: <?php echo $register; ?></p>
			<div class="link-button"><a href="profile-edit.php">Edit Profile</a></div>
		</div>

		<div id="Connections" class="tabcontent">
			<div class="connections-grid">
				<?php
				$connections_query = 'SELECT user_account.fname, user_account.lname, user_account.username, connection.user_id2 AS cur_id
				FROM connection
				INNER JOIN user_account ON connection.user_id2 = user_account.user_id
				WHERE connection.user_id1 = ?
				UNION
				SELECT user_account.fname, user_account.lname, user_account.username, connection.user_id1 AS cur_id
				FROM connection
				INNER JOIN user_account ON connection.user_id1 = user_account.user_id
				WHERE connection.user_id2 = ?
				ORDER BY lname ASC,
				fname ASC';

				if ($stmt = $con->prepare($connections_query)) {
					$stmt->bind_param('ii', $_SESSION['id'], $_SESSION['id']);
					if ($stmt->execute()) {
						$stmt->bind_result($fname_list, $lname_list, $username_list, $user_id_list);
						while ($stmt->fetch()) {
							echo '<div class="connection-item">';
							echo '<h3><a href="./profile-view.php?username=' . $username_list . '">' . $fname_list . " " . $lname_list . "</a></h3>";
							echo '</div>';
						}
						$stmt->close();
					}
				}
				?>
			</div>
		</div>

		<div id="Pages" class="tabcontent">
			<div class="pages-grid">
				<?php
				$pages_query = 'SELECT user_page.page_id, user_page.title
				FROM user_joins_page
				INNER JOIN user_page
				ON user_joins_page.page_id = user_page.page_id
				WHERE user_joins_page.user_id = ?';

				$stmt = $con->prepare($pages_query);
				$stmt->bind_param('i', $_SESSION['id']);
				$stmt->execute();
				$stmt->bind_result($page_id_list, $pages_list);
				while ($stmt->fetch()) {
					echo '<div class="page-item">';
					echo '<h3><a href="./page-view.php?page_id=' . $page_id_list . '">' . $pages_list . "</a></h3>";
					echo '</div>';
				}
				$stmt->close();
				?>
			</div>
			<div class="create-page-button"><a href="page-create.php">Create New Page</a></div>
		</div>

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

			// Get the element with id="defaultOpen" and click on it
			document.getElementById("defaultOpen").click();
		</script>
	</div>

</body>

</html>