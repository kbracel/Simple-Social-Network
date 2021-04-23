<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include('config.php');

//check if current user
//if so redirect to profile.php
if ($_GET['username'] == $_SESSION['name']) {
	header('Location: profile.php');
	exit;
}

$user_info_query = 'SELECT user_id, fname, lname, phone_number, email_address, username, birthday, gender,
relationship_status, interested_in, activities, interests, about, quote, hometown, current_location, register_date 
FROM user_account WHERE username = ?';
$user_id = $fname = $lname = $phoneNumber = $email = $username = $birthday = $gender = $relationship = "";
$interested = $activies = $interests = $about = $quote = $hometown = $curLocation = $register = "";

$stmt = $con->prepare($user_info_query);
$stmt->bind_param('s', $_GET['username']);
$stmt->execute();

$stmt->store_result();
if ($stmt->num_rows == 0)	//check if user exists
{
	header('Location: home.php');	//if user doesn't exist redirect to home page
	exit;
}

$stmt->bind_result(
	$user_id,
	$fname,
	$lname,
	$phoneNumber,
	$email,
	$username,
	$birthday,
	$gender,
	$relationship,
	$interested,
	$activies,
	$interests,
	$about,
	$quote,
	$hometown,
	$curLocation,
	$register
);
$stmt->fetch();
$stmt->close();

$are_users_connected = FALSE;
$connection_query = 'SELECT user_id1, user_id2 FROM connection WHERE user_id1 = ? AND user_id2 = ? 
UNION SELECT user_id1, user_id2 FROM connection WHERE user_id1 = ? AND user_id2 = ?';

//check if users are connected
$stmt = $con->prepare($connection_query);
$stmt->bind_param('iiii', $_SESSION['id'], $user_id, $user_id, $_SESSION['id']);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
	$are_users_connected = TRUE;
}
$stmt->close();

//checks if there is an outgoing request to the user
$is_requested = FALSE;
$request_query = 'SELECT user_id_to, user_id_from FROM connection_request WHERE user_id_to = ? AND user_id_from = ?';
$stmt = $con->prepare($request_query);
$stmt->bind_param('ii', $user_id, $_SESSION['id']);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
	$is_requested = TRUE;
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
		<h2><?php echo $fname . " " . $lname; ?></h2>

		<?php
		if ($are_users_connected) {
			echo '<form action="connection-remove.php" method="post">
			<input type="hidden" id="username" name="username" value="' . $username . '">
			<input type="submit" class="profile-page-button" name="profile-page-button" value="Remove Connection" />
			</form>';
		} else {
			if ($is_requested) {
				echo '<p>A request has been sent to this user.</p>';
			} else {
				echo '<form action="connection-request.php" method="post">
				<input type="hidden" id="username" name="username" value="' . $username . '">
				<input type="submit" class="profile-page-button" name="profile-page-button" value="Request Connection" />
				</form>';
			}
		}
		?>

		<div class="tab">
			<?php
			if ($are_users_connected) {
				echo '<button class="tablinks" onclick="openTab(event, \'Posts\')" id="defaultOpen">User\'s Posts</button>';
				echo '<button class="tablinks" onclick="openTab(event, \'About\')">About User</button>';
				echo '<button class="tablinks" onclick="openTab(event, \'Connections\')">User\'s Connections</button>';
				echo '<button class="tablinks" onclick="openTab(event, \'Pages\')">User\'s Pages</button>';
				//pages
			} else {
				echo '<button class="tablinks" onclick="openTab(event, \'About\')" id="defaultOpen">About User</button>';
			}
			?>
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
		</div>

		<?php
		if ($are_users_connected) {
			echo '<div id="Posts" class="tabcontent">';
			$post_query = 'SELECT user_account.fname, user_account.lname, user_account.username, user_account.user_id, user_post.post_text, user_post.date_posted
				FROM user_account
				INNER JOIN user_post ON user_account.user_id = user_post.user_id
				WHERE user_account.username = ?
				ORDER BY user_post.date_posted DESC';

			$stmt = $con->prepare($post_query);
			$stmt->bind_param('s', $_GET['username']);
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
			echo '</div>';

			echo '<div id="Connections" class="tabcontent">';
			echo '<div class="connections-grid">';
			//change query to sory in abc order
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
				$stmt->bind_param('ii', $user_id, $user_id);
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
			echo '</div>';
			echo '</div>';

			echo '<div id="Pages" class="tabcontent">';
			echo '<div class="pages-grid">';

			$pages_query = 'SELECT user_page.page_id, user_page.title
			FROM user_joins_page
			INNER JOIN user_page
			ON user_joins_page.page_id = user_page.page_id
			WHERE user_joins_page.user_id = ?';

			$stmt = $con->prepare($pages_query);
			$stmt->bind_param('i', $user_id);
			$stmt->execute();
			$stmt->bind_result($page_id_list, $pages_list);
			while ($stmt->fetch()) {
				echo '<div class="page-item">';
				echo '<h3><a href="./page-view.php?page_id=' . $page_id_list . '">' . $pages_list . "</a></h3>";
				echo '</div>';
			}
			$stmt->close();

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