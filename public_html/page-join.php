<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include('config.php');

//check user is not already in page...
$page_query = 'SELECT * FROM user_joins_page WHERE user_id = ? AND page_id = ?';
$stmt = $con->prepare($page_query);
$stmt->bind_param('ii', $_SESSION['id'], $_POST['page_id']);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
	$stmt->close();
	header('Location: home.php');
	exit;
}
$stmt->close();

//add user to page
$join_page_insert = 'INSERT INTO user_joins_page(user_id, page_id, date_joined) 
VALUES(?, ?, CURDATE())';
$redirect_page = 'Location: page-view.php?page_id=' . htmlspecialchars($_POST['page_id']);

if (isset($_POST['page_id']) && !empty($_POST['page_id'])) {
	if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
		$stmt = $con->prepare($join_page_insert);
		$stmt->bind_param('ii', $_SESSION['id'], $_POST['page_id']);
		$stmt->execute();
		$stmt->close();
	} else {
		header("Location: home.php");
		exit;
	}
} else {
	header("Location: home.php");
	exit;
}

header($redirect_page);
