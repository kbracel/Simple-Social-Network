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

if(empty($_POST['table']))
{
	header('Location: backup.php');
	exit;
}

$backup_table = $_POST['table'];
$backup_file = 'temp/' . $backup_table . '.sql';
$download_file = $backup_table . '.sql';

header("Content-type: application/octet-stream");
header("Content-disposition: attachment;filename=$download_file");

system("/usr/bin/mysqldump --user=$DATABASE_USER --password=$DATABASE_PASS --host=$DATABASE_HOST --result-file=$backup_file $DATABASE_NAME $backup_table");

readfile($backup_file);
?>