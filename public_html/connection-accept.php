<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include('config.php');

if(empty($_POST['username']))
{
	header('Location: home.php');
	exit;
}

$profile_redirect = 'Location: profile-view.php?username=' . $_POST['username'];

//get user_id from username
$userid_query = 'SELECT user_id FROM user_account WHERE username = ?';
$stmt = $con->prepare($userid_query);
$stmt->bind_param('s', $_POST['username']);
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows == 0)
{
	$stmt->close();
	header('Location: home.php');
	exit;
}
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

//first check if there is an incoming request
//if there is one go ahead and 'accept' it, connecting the users
$request_query = 'SELECT request_id FROM connection_request WHERE user_id_to = ? AND user_id_from = ?';
$stmt = $con->prepare($request_query);
$stmt->bind_param('ii', $_SESSION['id'], $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
	$stmt->close();

	$delete_request = 'DELETE FROM connection_request WHERE user_id_to = ? AND user_id_from = ?';
	$stmt = $con->prepare($delete_request);
	$stmt->bind_param('ii', $_SESSION['id'], $user_id);
	$stmt->execute();
	$stmt->close();

	$connect_query = 'INSERT INTO connection(user_id1, user_id2, date_connected) VALUES(?, ?, CURDATE())';
	$stmt = $con->prepare($connect_query);
	$stmt->bind_param('ii', $_SESSION['id'], $user_id);
	$stmt->execute();
	$stmt->close();

	header($profile_redirect);
	exit;
}
$stmt->close();
header('Location: home.php');
