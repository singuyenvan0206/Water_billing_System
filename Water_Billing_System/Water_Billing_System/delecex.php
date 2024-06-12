<?php
include 'db.php';
	$id = $_POST['id'];
	$deletes_query = "DELETE FROM owners WHERE id = $id";
	if (mysqli_query($conn, $deletes_query)) {
		// Success message
		echo '<script>alert("Client deleted successfully")</script>';
		
		// Fetch all users
		$clients_result = mysqli_query($conn, "SELECT * FROM owners");
		
		// Counter for sequential IDs
		$counter = 1;
		
		// Update user records with sequential IDs
		while ($clients = mysqli_fetch_assoc($clients_result)) {
			$update_query = "UPDATE owners SET id = $counter WHERE id = {$clients['id']}";
			mysqli_query($conn, $update_query);
			$counter++;
		}
	} else {
		// Error message
		echo '<script>alert("Error: ' . mysqli_error($conn) . '")</script>';
	}

		 echo "<script>windows: location='clients.php'</script>";				
			
