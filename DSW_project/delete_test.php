<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_id = $_POST['test_id'];

    $conn = new mysqli('localhost', 'root', '', 'test_management');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "DELETE FROM tests WHERE test_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $test_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Test with Test ID: $test_id deleted successfully.'); window.location.href = 'admin_dashboard.html';</script>";
    } else {
        echo "<script>alert('Test ID: $test_id not found. Please check and try again.'); window.location.href = 'delete_test.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
