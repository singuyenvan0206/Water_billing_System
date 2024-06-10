<?php
session_start();
if (!isset($_SESSION['id'])) {
    echo '<script>window.location="index.php"</script>';
    exit; // Exit to prevent further execution
}

$session = $_SESSION['id'];
include 'db.php';
$result = mysqli_query($conn, "SELECT * FROM user where id= '$session'");
while ($row = mysqli_fetch_array($result)) {
    $sessionname = $row['name'];
}

$q = "DELETE FROM user WHERE id ='$session'";
mysqli_query($conn, $q); // Pass the database connection as the first argument
header("Location: user.php");
?>
