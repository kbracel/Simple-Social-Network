<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include('config.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Simple Social Network</title>
	<meta name="description" content="User's Connection Requests Page">
	<meta name="author" content="Kyle Racel">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="normal-style.css">
</head>

<body>

	<?php $page = 'requests';
	include('topnav.php'); ?>

	<div class="content">
		<h2 style="text-align: center">Your Requests</h2>

		<?php
		$request_query = 'SELECT user_account.fname, user_account.lname, user_account.username, connection_request.date_sent
			FROM connection_request
			INNER JOIN user_account ON connection_request.user_id_from = user_account.user_id
			WHERE connection_request.user_id_to = ?
			ORDER BY connection_request.date_sent DESC';

		if ($stmt = $con->prepare($request_query)) {
			$stmt->bind_param('i', $_SESSION['id']);
			if ($stmt->execute()) {
				$stmt->bind_result($fname_list, $lname_list, $username_list, $date_sent_list);
				while ($stmt->fetch()) {
					echo '<div class="request-border">
						<div class="request-container">
							<form action="connection-accept.php" method="post">
								<h3 style="display:inline-block"><a href="./profile-view.php?username=' . $username_list . '">' . $fname_list . " " . $lname_list . '</a></h3>
								<input type="hidden" id="username" name="username" value="' . $username_list . '">
								<input type="submit" class="accept-button" name="accept-button" value="Accept Request" />
							</form>
						</div>
					</div>';
				}
				$stmt->close();
			}
		}
		?>
	</div>

</body>

</html>