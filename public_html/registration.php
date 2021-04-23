<?php

include("config.php");

$email_error = $fname_error = $lname_error = $phone_error = $address_error = $birthday_error = $username_error = $password_error = $password2_error = "";
$usertype = "user";
$data_check = TRUE;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$data_check = TRUE;

	//email validation
	if (empty($_POST["email"]) || !isset($_POST['email'])) {
		$email_error = "* Email is required.";
		$data_check = FALSE;
	} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$email_error = "* Email is not valid!";
		$data_check = FALSE;
	} else {
		//check email is not taken
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
	} else {
		//check username is not taken
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

	//password validation
	if (empty($_POST["password"]) || !isset($_POST['password'])) {
		$password_error = "* Password is required.";
		$data_check = FALSE;
	} elseif (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
		$password_error = "* Password must be between 5 and 20 characters long!";
		$data_check = FALSE;
	}

	if (empty($_POST["password2"]) || !isset($_POST['password2'])) {
		$password2_error = "* Please re-enter password.";
		$data_check = FALSE;
	} elseif ($_POST['password'] != $_POST['password2']) {
		$password2_error = "* Passwords do not match.";
		$data_check = FALSE;
	}

	if ($data_check) {
		$insert_user = 'INSERT INTO user_account( username, user_password, user_type, fname, lname, phone_number, mailing_address, email_address, birthday, register_date ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE( ))';
		$stmt = $con->prepare($insert_user);
		$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
		$stmt->bind_param('sssssssss', $_POST['username'], $password, $usertype, $_POST['fname'], $_POST['lname'], $_POST['phone_number'], $_POST['mailing_address'], $_POST['email'], $_POST['birthday']);
		$stmt->execute();
		$stmt->close();

		$stmt = $con->prepare('SELECT user_id FROM user_account WHERE username = ?');
		$stmt->bind_param('s', $_POST['username']);
		$stmt->execute();
		$stmt->bind_result($id);
		$stmt->fetch();
		$stmt->close();

		session_start();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['name'] = $_POST['username'];
		$_SESSION['id'] = $id;
		$_SESSION['type'] = $usertype;
		header('Location: profile-edit.php');
		exit;
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="registration-style.css">
</head>

<body>
	<div class="title-container">
		<h1>Simple Social Network</h1>
		<h2>a project by Kyle Racel</h2>
	</div>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="container">
			<h1>Register</h1>
			<p>Please fill in this form to create an account.</p>
			<hr>

			<div class="error"><?php echo $email_error; ?></div>
			<label for="email"><b>Email</b></label>
			<input type="text" placeholder="Enter Email" name="email" id="email" value="<?= isset($_POST['email']) ? $_POST['email'] : ''; ?>">

			<div class="error"><?php echo $fname_error; ?></div>
			<label for="fname"><b>First Name</b></label>
			<input type="text" placeholder="Enter First Name" name="fname" id="fname" value="<?= isset($_POST['fname']) ? $_POST['fname'] : ''; ?>">

			<div class="error"><?php echo $lname_error; ?></div>
			<label for="lname"><b>Last Name</b></label>
			<input type="text" placeholder="Enter Last Name" name="lname" id="lname" value="<?= isset($_POST['lname']) ? $_POST['lname'] : ''; ?>">

			<div class="error"><?php echo $phone_error; ?></div>
			<label for="phone_number"><b>Phone Number</b></label>
			<input type="text" placeholder="Enter Phone Number" name="phone_number" id="phone_number" value="<?= isset($_POST['phone_number']) ? $_POST['phone_number'] : ''; ?>">

			<div class="error"><?php echo $address_error; ?></div>
			<label for="mailing_address"><b>Mailing Address</b></label>
			<input type="text" placeholder="Enter Mailing Address" name="mailing_address" id="mailing_address" value="<?= isset($_POST['mailing_address']) ? $_POST['mailing_address'] : ''; ?>">

			<div class="error"><?php echo $birthday_error; ?></div>
			<label for="birthday"><b>Birthday</b></label>
			<input type="date" name="birthday" id="birthday" value="<?= isset($_POST['birthday']) ? $_POST['birthday'] : ''; ?>">

			<div class="error"><?php echo $username_error; ?></div>
			<label for="username"><b>Username</b></label>
			<input type="text" placeholder="Enter Username" name="username" id="username" value="<?= isset($_POST['username']) ? $_POST['username'] : ''; ?>">

			<div class="error"><?php echo $password_error; ?></div>
			<label for="password"><b>Password</b></label>
			<input type="password" placeholder="Enter Password" name="password" id="password" value="<?= isset($_POST['password']) ? $_POST['password'] : ''; ?>">

			<div class="error"><?php echo $password2_error; ?></div>
			<label for="password2"><b>Repeat Password</b></label>
			<input type="password" placeholder="Repeat Password" name="password2" id="password2" value="<?= isset($_POST['password2']) ? $_POST['password2'] : ''; ?>">

			<hr>
			<button type="submit" class="registerbtn">Register</button>
		</div>

		<div class="container signin">
			<p>Already have an account?</p>
			<p><a href="index.php">Sign In</a></p>
		</div>
	</form>

</body>

</html>