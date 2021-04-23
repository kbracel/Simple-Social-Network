<?php
session_start();

if (isset($_SESSION['loggedin'])) {
	header('Location: home.php');
	exit;
}

include("config.php");

$username_error = $password_error = "";
$data_check = TRUE;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$data_check = TRUE;

	//username validation
	if (empty($_POST["username"]) || !isset($_POST['username'])) {
		$username_error = "* Username is required";
		$data_check = FALSE;
	}

	//password validation
	if (empty($_POST["password"]) || !isset($_POST['password'])) {
		$password_error = "* Password is required";
		$data_check = FALSE;
	}

	if ($data_check) {
		$login_query = 'SELECT user_id, user_password, user_type FROM user_account WHERE username = ?';
		$stmt = $con->prepare($login_query);
		$stmt->bind_param('s', $_POST['username']);
		$stmt->execute();
		$stmt->store_result();

		if ($stmt->num_rows > 0) {
			$stmt->bind_result($id, $password, $type);
			$stmt->fetch();
			
			if (password_verify($_POST['password'], $password)) {
				session_regenerate_id();
				$_SESSION['loggedin'] = TRUE;
				$_SESSION['name'] = $_POST['username'];
				$_SESSION['id'] = $id;
				$_SESSION['type'] = $type;
				if ($_SESSION['type'] == 'admin') {
					header('Location: ./admin/home.php');
					exit;
				} else {
					header('Location: ./home.php');
					exit;
				}
			} else {
				// Incorrect password
				$password_error = "* Incorrect Password";
			}
		} else {
			// Incorrect username
			$username_error = "* User does not exist";
		}

		$stmt->close();
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Simple Social Network</title>
	<meta name="description" content="Login Page">
	<meta name="author" content="Kyle Racel">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="login-style.css">
</head>

<body>
	<div class="title-container">
		<h1>Simple Social Network</h1>
		<h2>a project by Kyle Racel</h2>
	</div>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="container">
			<div class="error"><?php echo $username_error; ?></div>
			<label for="username"><b>Username</b></label>
			<input type="text" placeholder="Enter Username" name="username" id="username" value="<?= isset($_POST['username']) ? $_POST['username'] : ''; ?>">

			<div class="error"><?php echo $password_error; ?></div>
			<label for="password"><b>Password</b></label>
			<input type="password" placeholder="Enter Password" name="password" id="password" value="<?= isset($_POST['password']) ? $_POST['password'] : ''; ?>">
			<button type="submit">Log In</button>
			<hr>
			<p style="text-align: center;">Don't have an account?</p>
			<p style="text-align: center; font-weight: bold;"><a href="registration.php">Sign Up Here</a></p>

		</div>
	</form>
</body>

</html>