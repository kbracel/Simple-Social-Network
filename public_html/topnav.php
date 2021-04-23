<?php
if (!isset($page)) {
	$page = "";
}
?>

<div class="topnav">
	<a <?php echo ($page == 'home') ? "class='active' " : ""; ?>href="home.php">Home</a>
	<a <?php echo ($page == 'profile') ? "class='active' " : ""; ?>href="profile.php">Profile</a>
	<a <?php echo ($page == 'requests') ? "class='active' " : ""; ?>href="requests.php">Requests</a>
	<a <?php echo ($page == 'search') ? "class='active' " : ""; ?>href="search.php">Search</a>
	<a <?php echo ($page == 'settings') ? "class='active' " : ""; ?>href="settings.php">Settings</a>
	<?php
	if ($_SESSION['type'] == 'admin') {
		echo '<a href="admin/home.php">Admin Portal</a>';
	}
	?>
	<div class="logout-container">
		<a href="logout.php">Logout</a>
	</div>
</div>