<div class="topnav">
	<a <?php echo ($page == 'home') ? "class='active' " : ""; ?>href="home.php">Home</a>
	<a <?php echo ($page == 'manage') ? "class='active' " : ""; ?>href="user-manage.php">Manage Users</a>
	<a <?php echo ($page == 'backup') ? "class='active' " : ""; ?>href="backup.php">Backup Tables</a>
	<a <?php echo ($page == 'settings') ? "class='active' " : ""; ?>href="settings.php">Settings</a>
	<a href="../home.php">User Portal</a>
	<div class="logout-container">
		<a href="../logout.php">Logout</a>
	</div>
</div>