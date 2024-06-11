<?php
include 'db.php';

// Hàm kiểm tra và thực thi truy vấn
function execute_query($query) {
    global $conn;
    if (mysqli_query($conn, $query)) {
        return true;
    } else {
        return false;
    }
}

// Hàm thiết lập lại bảng user
function reset_user_table() {
    execute_query("DROP TABLE IF EXISTS user");
    $create_table_query = "
        CREATE TABLE user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(50) NOT NULL,
            name VARCHAR(100) NOT NULL
        )
    ";
    return execute_query($create_table_query);
}

// Hàm kiểm thử thêm người dùng
function test_useradd($username, $password, $name) {
    $_POST['username'] = $username;
    $_POST['password'] = $password;
    $_POST['name'] = $name;

    ob_start();
    include 'useradd.php';
    $output = ob_get_clean();

    $query = "SELECT * FROM user WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && strpos($output, "User added successfully") !== false) {
        echo "Test add user ($username): Passed\n";
    } else {
        echo "Test add user ($username): Failed\n";
    }
}
// Hàm kiểm thử thêm người dùng khi một hoặc nhiều trường bỏ trống
function test_useradd_missing_fields($username, $password, $name) {
    $_POST['username'] = $username;
    $_POST['password'] = $password;
    $_POST['name'] = $name;
    

    ob_start();
    include 'useradd.php';
    $output = ob_get_clean();

    if (strpos($output, "Tất cả các trường đều bắt buộc") !== false) {
        echo "Test add user missing fields: Passed\n";
    } else {
        echo "Test add user missing fields: Failed\n";
    }
}

test_useradd_missing_fields('', 'testpass', 'Test User'); // Bỏ trống trường username
test_useradd_missing_fields('testuser', '', 'Test User'); // Bỏ trống trường password
test_useradd_missing_fields('testuser', 'testpass', ''); // Bỏ trống trường name

// Thiết lập lại bảng user trước khi chạy các kiểm thử
reset_user_table();

// Chạy các kiểm thử
test_useradd('testuser', 'testpass', 'Test User');
// Các kiểm thử khác có thể được thêm ở đây nếu cần

?>
