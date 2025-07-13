<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'admin');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_id = $_POST['admin_id'];
$password = $_POST['password'];

$sql = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_id); 
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    if ($password===$row['password']) {
        $_SESSION['admin_id'] = $row['id'];
        header("Location: admin_dashboard.html");
        exit();
    } else {
        echo "<script>alert('Invalid credentialsssss!'); window.location.href = 'admin_login.html';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid credentials!'); window.location.href = 'admin_login.html';</script>";
    exit();
}
$conn->close();
?>