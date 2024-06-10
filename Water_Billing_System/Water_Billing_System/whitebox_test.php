<?php
include 'db.php';

// Hàm để kiểm tra kết quả của một truy vấn và in thông báo
function check_query($result, $success_message, $failure_message) {
    if ($result) {
        echo $success_message . "\n";
    } else {
        echo $failure_message . ": " . mysqli_error($GLOBALS['conn']) . "\n";
    }
}

// Hàm để reset lại bảng user cho kiểm thử
function reset_user_table() {
    global $conn;
    mysqli_query($conn, "DROP TABLE IF EXISTS user");
    $create_table_query = "
        CREATE TABLE user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(50) NOT NULL,
            name VARCHAR(100) NOT NULL
        )
    ";
    check_query(mysqli_query($conn, $create_table_query), "Table reset successfully", "Table reset failed");
}

// Kiểm tra 1: Thêm người dùng thành công
function test_useradd_success() {
    global $conn;
    $_POST['username'] = 'testuser';
    $_POST['password'] = 'testpass';
    $_POST['name'] = 'Test User';
    include 'useradd.php'; // Tệp chứa mã chính
    $result = mysqli_query($conn, "SELECT * FROM user WHERE username='testuser'");
    $user = mysqli_fetch_assoc($result);
    if ($user) {
        echo "Test add user success: Passed\n";
    } else {
        echo "Test add user success: Failed\n";
    }
}

// Kiểm tra 2: Thiếu trường dữ liệu
function test_missing_fields() {
    global $conn;
    $_POST['username'] = '';
    $_POST['password'] = 'testpass';
    $_POST['name'] = 'Test User';
    ob_start();
    include 'useradd.php'; // Tệp chứa mã chính
    $output = ob_get_clean();
    if (strpos($output, "Tất cả các trường đều bắt buộc") !== false) {
        echo "Test missing fields: Passed\n";
    } else {
        echo "Test missing fields: Failed\n";
    }
}

// Kiểm tra 3: Lỗi chèn cơ sở dữ liệu (Vi phạm ràng buộc duy nhất)
function test_unique_constraint_violation() {
    global $conn;
    $_POST['username'] = 'duplicateuser';
    $_POST['password'] = 'testpass';
    $_POST['name'] = 'Duplicate User';
    include 'useradd.php'; // Tệp chứa mã chính
    ob_start();
    include 'useradd.php'; // Tệp chứa mã chính
    $output = ob_get_clean();
    if (strpos($output, "Lỗi: Duplicate entry") !== false) {
        echo "Test unique constraint violation: Passed\n";
    } else {
        echo "Test unique constraint violation: Failed\n";
    }
}

// Kiểm tra 4: Lỗi cập nhật ID tuần tự
function test_sequential_id_update_failure() {
    global $conn;
    reset_user_table();
    mysqli_query($conn, "INSERT INTO user (username, password, name) VALUES ('user1', 'pass1', 'User One')");
    mysqli_query($conn, "INSERT INTO user (username, password, name) VALUES ('user2', 'pass2', 'User Two')");
    $conn->close(); // Đóng kết nối để mô phỏng lỗi
    ob_start();
    include 'useradd.php'; // Tệp chứa mã chính
    $output = ob_get_clean();
    if (strpos($output, "Lỗi:") !== false) {
        echo "Test sequential ID update failure: Passed\n";
    } else {
        echo "Test sequential ID update failure: Failed\n";
    }
    // Mở lại kết nối
    include 'db.php';
}

// Kiểm tra 5: Lỗi đặt lại AUTO_INCREMENT
function test_auto_increment_reset_failure() {
    global $conn;
    reset_user_table();
    mysqli_query($conn, "INSERT INTO user (username, password, name) VALUES ('user1', 'pass1', 'User One')");
    mysqli_query($conn, "INSERT INTO user (username, password, name) VALUES ('user2', 'pass2', 'User Two')");
    // Sửa hàm chính để gây lỗi tại bước đặt lại AUTO_INCREMENT (Ví dụ: bằng cách xóa dòng đó đi)
    ob_start();
    include 'useradd.php'; // Tệp chứa mã chính đã sửa đổi
    $output = ob_get_clean();
    if (strpos($output, "Lỗi:") !== false) {
        echo "Test auto increment reset failure: Passed\n";
    } else {
        echo "Test auto increment reset failure: Failed\n";
    }
}

// Thiết lập lại bảng user cho kiểm thử
reset_user_table();

// Thực hiện các kiểm thử
test_useradd_success();
echo "\n";
test_missing_fields();
echo "\n";
test_unique_constraint_violation();
echo "\n";
test_sequential_id_update_failure();
echo "\n";
test_auto_increment_reset_failure();
echo "\n";

?>
