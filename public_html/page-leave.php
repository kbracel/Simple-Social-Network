<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include('config.php');

//check user is in page
$page_query = 'SELECT * FROM user_joins_page WHERE user_id = ? AND page_id = ?';
$stmt = $con->prepare($page_query);
$stmt->bind_param('ii', $_SESSION['id'], $_POST['page_id']);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
	$stmt->close();

	$page_leave = 'DELETE FROM user_joins_page WHERE user_id = ? AND page_id = ?';
	$stmt = $con->prepare($page_leave);
	$stmt->bind_param('ii', $_SESSION['id'], $_POST['page_id']);
	$stmt->execute();
	$stmt->close();

	header('Location: home.php');
	exit;
}
$stmt->close();
header("Location: home.php");
exit;
