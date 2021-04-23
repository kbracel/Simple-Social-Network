<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
include('config.php');

$post_page_insert = 'INSERT INTO page_post(page_id, user_id, date_posted, post_text)
VALUES(?, ?, NOW(), ?)';
$redirect_page = 'Location: page-view.php?page_id=' . htmlspecialchars($_POST['page_id']);

if (isset($_POST['page_id']) && !empty($_POST['page_id'])) {
	if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
		if (isset($_POST['user_post']) && !empty($_POST['user_post'])) {
			//check that user is part of page...
			$stmt = $con->prepare($post_page_insert);
			$stmt->bind_param('iis', $_POST['page_id'], $_SESSION['id'], $_POST['user_post']);
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
} else {
	header("Location: home.php");
	exit;
}

header($redirect_page);
