<?php
include 'db.php';
	$id = $_POST['id'];
	$delete_query = "DELETE FROM user WHERE id = $id";
	if (mysqli_query($conn, $delete_query)) {
		// Success message
		echo '<script>alert("User deleted successfully")</script>';
		
		// Fetch all users
		$users_result = mysqli_query($conn, "SELECT * FROM user");
		
		// Counter for sequential IDs
		$counter = 1;
		
		// Update user records with sequential IDs
		while ($user = mysqli_fetch_assoc($users_result)) {
			$update_query = "UPDATE user SET id = $counter WHERE id = {$user['id']}";
			mysqli_query($conn, $update_query);
			$counter++;
		}
	} else {
		// Error message
		echo '<script>alert("Error: ' . mysqli_error($conn) . '")</script>';
	}
	
	// Fetch updated list of users
	$user_result = mysqli_query($conn, "SELECT * FROM user");
		 echo "<script>windows: location='user.php'</script>";				
			