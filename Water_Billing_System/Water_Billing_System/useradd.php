<?php
include 'db.php';
                   
					$username= $_POST['username'] ;					
					$password=$_POST['password'] ;
					$name=$_POST['name'] ;

// Check if all fields are provided and not empty
$check_query = "SELECT COUNT(*) AS num_rows FROM user WHERE username = '$username'";
$check_result = mysqli_query($conn, $check_query);
$check_row = mysqli_fetch_assoc($check_result);
if ($check_row['num_rows'] > 0) {
    // Nếu tên người dùng đã tồn tại, in thông báo lỗi và dừng thực thi tiếp
    echo '<script>alert("Username already exists. Please choose a different username.")</script>';
    echo "<script>windows: location='user.php'</script>";

} else {
    $sql = "INSERT INTO user (username, password, name) 
            VALUES ('$username', '$password', '$name')";
    if (mysqli_query($conn, $sql)) {
        // Success message
        echo '<script>alert("User added successfully")</script>';
    }
        $user_result = mysqli_query($conn, "SELECT * FROM user ORDER BY id");
        $counter = 1;
        
        // Bắt đầu một giao dịch
        mysqli_begin_transaction($conn);
    
        try {
            // Update user records with sequential IDs
            while ($user = mysqli_fetch_assoc($user_result)) {
                $update_query = "UPDATE user SET id = $counter WHERE id = {$user['id']}";
                mysqli_query($conn, $update_query);
                $counter++;
            }
    
            // Đặt lại giá trị tự động tăng (AUTO_INCREMENT)
            $reset_auto_increment_query = "ALTER TABLE user AUTO_INCREMENT = $counter";
            mysqli_query($conn, $reset_auto_increment_query);
    
            // Hoàn thành giao dịch
            mysqli_commit($conn);
        } catch (Exception $e) {
            // Rollback giao dịch nếu có lỗi xảy ra
            mysqli_rollback($conn);
            echo '<script>alert("Error: ' . $e->getMessage() . '")</script>';
        }
    echo "<script>windows: location='user.php'</script>";
    }// Fetch updated list of users


?>