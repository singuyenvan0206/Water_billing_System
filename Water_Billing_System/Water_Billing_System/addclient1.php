<?php
include 'db.php';
// Phần xử lý form
if (isset($_POST['add'])) {	   
    $lname = $_POST['lname'];
    $fname = $_POST['fname'];
    $mi = $_POST['mi'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $meterReader = $_POST['meterReader'];

    // Chuẩn bị các câu lệnh SQL
    $stmt1 = $conn->prepare("INSERT INTO owners (lname, fname, mi, address, contact) VALUES (?, ?, ?, ?, ?)");
    $stmt2 = $conn->prepare("INSERT INTO tempo_bill (Client, Prev) VALUES (?, ?)");

    if ($stmt1 && $stmt2) {
        // Gắn các tham số
        $stmt1->bind_param("sssss", $lname, $fname, $mi, $address, $contact);
        $stmt2->bind_param("ss", $fname, $meterReader);

        // Thực thi các câu lệnh
        $success1 = $stmt1->execute();
        $success2 = $stmt2->execute();

		$client_result = mysqli_query($conn, "SELECT * FROM owners ORDER BY id");
        $counter = 1;
        
        // Bắt đầu một giao dịch
        mysqli_begin_transaction($conn);
    
        try {
            // Update user records with sequential IDs
            while ($client = mysqli_fetch_assoc($client_result)) {
                $update_query = "UPDATE owners SET id = $counter WHERE id = {$client['id']}";
                mysqli_query($conn, $update_query);
                $counter++;
            }
    
            // Đặt lại giá trị tự động tăng (AUTO_INCREMENT)
            $reset_auto_increment_query = "ALTER TABLE owners AUTO_INCREMENT = $counter";
            mysqli_query($conn, $reset_auto_increment_query);
    
            // Hoàn thành giao dịch
            mysqli_commit($conn);
        } catch (Exception $e) {
            // Rollback giao dịch nếu có lỗi xảy ra
            mysqli_rollback($conn);
            echo '<script>alert("Error: ' . $e->getMessage() . '")</script>';
            echo '\n';
        }
        // Kiểm tra nếu cả hai lệnh chèn đều thành công
        if ($success1 && $success2) {
            header("Location: clients.php");
            exit();
        } else {
            // Nếu có lỗi, xuất thông báo lỗi
            echo "Lỗi: " . $stmt1->error . "<br>" . $stmt2->error;
        }

        // Đóng các câu lệnh
        $stmt1->close();
        $stmt2->close();
    } else {
        echo "Lỗi khi chuẩn bị các câu lệnh: " . $conn->error;
    }

    // Đóng kết nối cơ sở dữ liệu
    $conn->close();
}
?>
