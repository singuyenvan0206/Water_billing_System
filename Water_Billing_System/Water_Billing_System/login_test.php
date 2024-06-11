<?php
ob_start();
// Start session
session_start();

// Mock database connection
$conn = mysqli_connect("localhost", "root", "123456", "sourcecodester_wbsdb");

// Set up test cases
$valid_username = "testuser";
$valid_password = "testpass";
$invalid_username = "invalid_user";
$invalid_password = "invalid_password";

// Function to simulate form submission and test the login process
function test_login($username, $password) {
    global $conn;

    // Simulate form submission (POST request)
    $_POST['username'] = $username;
    $_POST['password'] = $password;

    // Include the process file
    include 'db.php';    
  
 $login = mysqli_query($conn,"SELECT * FROM user WHERE username = '" .$_POST['username'] . "' and password = '" .$_POST['password'] . "'");
 $row=mysqli_fetch_array($login);  
 
 if($row){
 $_SESSION['id'] = $row['id'];
 echo "Test login with valid username and passwword: Passed\n";
 
 }
	else {
		echo 'Wrong username or password';
        echo "Test login with invalid username or password: Passed\n";
		header ("location: index.php?err");
		exit();
		}
 
 
}

// Test valid login credentials
echo "Test valid login credentials:\n";
test_login($valid_username, $valid_password);
echo "\n";
// Test invalid login credentials
echo "Test invalid login credentials:\n";
test_login($invalid_username, $invalid_password);
echo "\n";
ob_end_clean(); // Kết thúc bộ đệm đầu ra và loại bỏ đầu ra
?>
