<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include('config.php');

//get user_id from username
$userid_query = 'SELECT user_id FROM user_account WHERE username = ?';
$stmt = $con->prepare($userid_query);
$stmt->bind_param('s', $_POST['username']);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
	$stmt->close();
	header('Location: home.php');
	exit;
}
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

$connection_query = 'SELECT connection_id FROM connection WHERE user_id1 = ? AND user_id2 = ?';
$stmt = $con->prepare($connection_query);
$stmt->bind_param('ii', $_SESSION['id'], $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
	$stmt->close();

	$delete_connection = 'DELETE FROM connection WHERE user_id1 = ? AND user_id2 = ?';

	$stmt = $con->prepare($delete_connection);
	$stmt->bind_param('ii', $_SESSION['id'], $user_id);
	$stmt->execute();
	$stmt->close();

	header('Location: home.php');
	exit;
}
$stmt->close();

$connection_query = 'SELECT connection_id FROM connection WHERE user_id1 = ? AND user_id2 = ?';
$stmt = $con->prepare($connection_query);
$stmt->bind_param('ii', $user_id, $_SESSION['id']);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
	$stmt->close();
	$delete_connection = 'DELETE FROM connection WHERE user_id1 = ? AND user_id2 = ?';

	$stmt = $con->prepare($delete_connection);
	$stmt->bind_param('ii', $user_id, $_SESSION['id']);
	$stmt->execute();
	$stmt->close();

	header('Location: home.php');
	exit;
}
$stmt->close();
header('Location: home.php');
