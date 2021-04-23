<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: ../index.php');
	exit;
}
if ($_SESSION['type'] != 'admin') {
	header('Location: ../home.php');
	exit;
}
include("../config.php");

$email = $fname = $lname = $phone = $address = $birthday = $username = "";
$email_error = $fname_error = $lname_error = $phone_error = $address_error = $birthday_error = $username_error = "";
$password1_error = $password2_error = $password3_error = "";

$get_info = 'SELECT email_address, fname, lname, phone_number, mailing_address, birthday, username FROM user_account WHERE user_id = ?';
$stmt = $con->prepare($get_info);
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($email, $fname, $lname, $phone, $address, $birthday, $username);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST['form'] == 'account') {
		$data_check = TRUE;

		//email validation
		if (empty($_POST["email"]) || !isset($_POST['email'])) {
			$email_error = "* Email is required.";
			$data_check = FALSE;
		} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$email_error = "* Email is not valid!";
			$data_check = FALSE;
		}
		//check email is not taken
		if ($email != $_POST['email']) {
			$email_query = 'SELECT * FROM user_account WHERE email_address = ?';
			$stmt = $con->prepare($email_query);
			$stmt->bind_param('s', $_POST['email']);
			$stmt->execute();
			$stmt->store_result();
			if ($stmt->num_rows > 0) {
				$email_error = "* Email is already taken!";
				$data_check = FALSE;
			}
			$stmt->close();
		}

		//first name validation
		if (empty($_POST["fname"]) || !isset($_POST['fname'])) {
			$fname_error = "* First Name is required.";
			$data_check = FALSE;
		}

		//last name validation
		if (empty($_POST["lname"]) || !isset($_POST['lname'])) {
			$lname_error = "* Last Name is required.";
			$data_check = FALSE;
		}

		//phone number validation
		if (empty($_POST["phone_number"]) || !isset($_POST['phone_number'])) {
			$phone_error = "* Phone Number is required.";
			$data_check = FALSE;
		}

		//mailing address validation
		if (empty($_POST["mailing_address"]) || !isset($_POST['mailing_address'])) {
			$address_error = "* Mailing Address is required.";
			$data_check = FALSE;
		}

		//birthday validation
		if (empty($_POST["birthday"]) || !isset($_POST['birthday'])) {
			$birthday_error = "* Birthday is required.";
			$data_check = FALSE;
		}

		//username validation
		if (empty($_POST["username"]) || !isset($_POST['username'])) {
			$username_error = "* Username is required.";
			$data_check = FALSE;
		} elseif (preg_match('/[A-Za-z0-9]+/', $_POST['username']) == 0) {
			$username_error = "* Username is not valid!";
			$data_check = FALSE;
		}

		//check username is not taken
		if ($username != $_POST['username']) {
			$username_query = 'SELECT * FROM user_account WHERE username = ?';
			$stmt = $con->prepare($username_query);
			$stmt->bind_param('s', $_POST['username']);
			$stmt->execute();
			$stmt->store_result();
			if ($stmt->num_rows > 0) {
				$username_error = "* Username is already taken!";
				$data_check = FALSE;
			}
			$stmt->close();
		}

		if ($data_check) {
			$account_update = "UPDATE user_account SET email_address=?, fname=?, lname=?, phone_number=?, mailing_address=?, birthday=?, username=? WHERE user_id=?";
			$stmt = $con->prepare($account_update);
			$stmt->bind_param('sssssssi', $_POST['email'], $_POST['fname'], $_POST['lname'], $_POST['phone_number'], $_POST['mailing_address'], $_POST['birthday'], $_POST['username'], $_SESSION['id']);
			$stmt->execute();
			$stmt->close();
			header('Location: settings.php');
			exit;
		}
	} elseif ($_POST['form'] == 'password') {
		$data_check = TRUE;

		//get current password for use below
		$password_query = 'SELECT user_password FROM user_account WHERE user_id = ?';
		$stmt = $con->prepare($password_query);
		$stmt->bind_param('i', $_SESSION['id']);
		$stmt->execute();
		$stmt->bind_result($user_password);
		$stmt->fetch();
		$stmt->close();

		if (empty($_POST["new_password"]) || !isset($_POST['new_password'])) {
			$password1_error = "* New password is required.";
			$data_check = FALSE;
		} elseif (strlen($_POST['new_password']) > 20 || strlen($_POST['new_password']) < 5) {
			$password1_error = "* New password must be between 5 and 20 characters long!";
			$data_check = FALSE;
		}

		if (empty($_POST["repeat_password"]) || !isset($_POST['repeat_password'])) {
			$password2_error = "* Please re-enter new password.";
			$data_check = FALSE;
		} elseif ($_POST['new_password'] != $_POST['repeat_password']) {
			$password2_error = "* Passwords do not match.";
			$data_check = FALSE;
		} elseif (password_verify($_POST['repeat_password'], $user_password)) {
			$password2_error = "* Password can not be the same as current password.";
			$data_check = FALSE;
		}

		if (empty($_POST["old_password"]) || !isset($_POST['old_password'])) {
			$password3_error = "* Old password is required.";
			$data_check = FALSE;
		} elseif (!password_verify($_POST['old_password'], $user_password)) {
			$password3_error = "* Password is wrong.";
			$data_check = FALSE;
		}

		if ($data_check) {
			$password_update = 'UPDATE user_account SET user_password=? WHERE user_id=?';
			$hashed_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

			$stmt = $con->prepare($password_update);
			$stmt->bind_param('si', $hashed_password, $_SESSION['id']);
			$stmt->execute();
			$stmt->close();

			header('Location: settings.php');
			exit;
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Simple Social Network</title>
	<meta name="description" content="User's Setting Page">
	<meta name="author" content="Kyle Racel">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../normal-style.css">
</head>

<body>

	<?php $page = 'settings';
	include('topnav.php'); ?>

	<div class="content">
		<h2>Edit Account Info</h2>

		<div class="form-border">
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-container">
					<input type="hidden" id="form" name="form" value="account">

					<div class="error"><?php echo $email_error; ?></div>
					<label for="email"><b>Email</b></label>
					<input type="text" placeholder="Enter Email..." name="email" id="email" value="<?= isset($_POST['email']) ? $_POST['email'] : $email; ?>">

					<div class="error"><?php echo $fname_error; ?></div>
					<label for="fname"><b>First Name</b></label>
					<input type="text" placeholder="Enter First Name..." name="fname" id="fname" value="<?= isset($_POST['fname']) ? $_POST['fname'] : $fname; ?>">

					<div class="error"><?php echo $lname_error; ?></div>
					<label for="lname"><b>Last Name</b></label>
					<input type="text" placeholder="Enter Last Name..." name="lname" id="lname" value="<?= isset($_POST['lname']) ? $_POST['lname'] : $lname; ?>">

					<div class="error"><?php echo $phone_error; ?></div>
					<label for="phone_number"><b>Phone Number</b></label>
					<input type="text" placeholder="Enter Phone Number..." name="phone_number" id="phone_number" value="<?= isset($_POST['phone_number']) ? $_POST['phone_number'] : $phone; ?>">

					<div class="error"><?php echo $address_error; ?></div>
					<label for="mailing_address"><b>Mailing Address</b></label>
					<input type="text" placeholder="Enter Mailing Address..." name="mailing_address" id="mailing_address" value="<?= isset($_POST['mailing_address']) ? $_POST['mailing_address'] : $address; ?>">

					<div class="error"><?php echo $birthday_error; ?></div>
					<label for="birthday"><b>Birthday</b></label>
					<input type="date" name="birthday" id="birthday" value="<?= isset($_POST['birthday']) ? $_POST['birthday'] : $birthday; ?>">

					<div class="error"><?php echo $username_error; ?></div>
					<label for="username"><b>Username</b></label>
					<input type="text" placeholder="Enter Username..." name="username" id="username" value="<?= isset($_POST['username']) ? $_POST['username'] : $username; ?>">

					<div></div>
					<button type="submit" class="submit-button">Submit</button>
				</div>
			</form>

			<hr>

			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-container">
					<input type="hidden" id="form" name="form" value="password">

					<div class="error"><?php echo $password1_error ?></div>
					<label for="new_password"><b>New Password</b></label>
					<input type="password" placeholder="Enter New Password..." name="new_password" id="new_password" value="">

					<div class="error"><?php echo $password2_error ?></div>
					<label for="repeat_password"><b>Repeat Password</b></label>
					<input type="password" placeholder="Repeat New Password..." name="repeat_password" id="repeat_password" value="">

					<div class="error"><?php echo $password3_error ?></div>
					<label for="old_password"><b>Old Password</b></label>
					<input type="password" placeholder="Enter Old Password..." name="old_password" id="old_password" value="">

					<div></div>
					<button type="submit" class="submit-button">Submit</button>
				</div>
			</form>
		</div>
	</div>

</body>

</html>