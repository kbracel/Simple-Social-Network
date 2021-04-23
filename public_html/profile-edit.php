<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include("config.php");

$gender = $relationship = $interested = $activies = $interests = $about = $quote = $hometown = $currentlocation = "";
$gender_error = $relationship_error = $interested_error = $activies_error = $interests_error = $about_error = $quote_error = $hometown_error = $currentlocation_error = "";

$profile_query = 'SELECT gender, relationship_status, interested_in, activities, interests, about, quote, hometown, current_location FROM user_account WHERE user_id = ?';
$profile_update = 'UPDATE user_account SET gender=?, relationship_status=?, interested_in=?, activities=?, interests=?, about=?, quote=?, hometown=?, current_location=? WHERE user_id=?';

$stmt = $con->prepare($profile_query);
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($gender, $relationship, $interested, $activies, $interests, $about, $quote, $hometown, $currentlocation);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($stmt = $con->prepare($profile_update)) {
		$stmt->bind_param('ssssssssss', $_POST['gender'], $_POST['relationship-status'], $_POST['interested-in'], $_POST['activities'], $_POST['interests'], $_POST['about'], $_POST['quote'], $_POST['hometown'], $_POST['current-location'], $_SESSION['id']);
		if ($stmt->execute()) {
			$stmt->close();
			header('Location: profile.php');
			exit;
		} else {
			echo 'Could not execute profile edits.';
		}
	} else {
		echo 'Could not prepare profile edit statement.';
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

	<?php $page = 'profile';
	include('topnav.php'); ?>

	<div class="content">
		<h2>Edit Your Profile</h2>
		<p>This information will be public.</p>
		<p>You may leave any field blank, if you wish.</p>

		<div class="form-border">
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-container">
					<div class="error"><?= $gender_error ?></div>
					<label for="gender"><b>Gender</b></label>
					<input type="text" placeholder="Enter Your Gender" name="gender" id="gender" value="<?= $gender ?>">

					<div class="error"><?= $relationship_error ?></div>
					<label for="relationship-status"><b>Relationship Status</b></label>
					<input type="text" placeholder="Enter Your Relationship Status" name="relationship-status" id="relationship-status" value="<?= $relationship ?>">

					<div class="error"><?= $interested_error ?></div>
					<label for="interested-in"><b>Interested In</b></label>
					<input type="text" placeholder="Enter Who You Are Interested In" name="interested-in" id="interested-in" value="<?= $interested ?>">

					<div class="error"><?= $activies_error ?></div>
					<label for="activities"><b>Activities</b></label>
					<textarea placeholder="Enter Your Activities" name="activities" id="activities" style="height: 100px;"><?php echo $activies; ?></textarea>

					<div class="error"><?= $interests_error ?></div>
					<label for="interests"><b>Interests</b></label>
					<textarea placeholder="Enter Your Interests" name="interests" id="interests" style="height: 100px;"><?php echo $interests; ?></textarea>

					<div class="error"><?= $about_error ?></div>
					<label for="about"><b>About You</b></label>
					<textarea placeholder="Enter About You" name="about" id="about" style="height: 100px;"><?php echo $about; ?></textarea>

					<div class="error"><?= $quote_error ?></div>
					<label for="quote"><b>Favorite Quote</b></label>
					<textarea placeholder="Enter Your Favorite Quote" name="quote" id="quote" style="height: 100px;"><?php echo $quote; ?></textarea>

					<div class="error"><?= $hometown_error ?></div>
					<label for="hometown"><b>Hometown</b></label>
					<input type="text" placeholder="Enter Your Hometown" name="hometown" id="hometown" value="<?= $hometown ?>">

					<div class="error"><?= $currentlocation_error ?></div>
					<label for="current-location"><b>Current Location</b></label>
					<input type="text" placeholder="Enter Your Current Location" name="current-location" id="current-location" value="<?= $currentlocation ?>">

					<div></div>
					<button type="submit" class="submit-button">Submit</button>
				</div>
			</form>
		</div>
	</div>

</body>

</html>