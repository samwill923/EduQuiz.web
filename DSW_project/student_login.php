<?php
$conn = new mysqli('localhost','root','');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$dbname = "student_registration";
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";

if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

$conn->select_db($dbname);
$table_sql = "
CREATE TABLE IF NOT EXISTS students (
    id varchar(100) PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL
)";
if ($conn->query($table_sql) === TRUE) {
    // Table created or already exists
} else {
    die("Error creating table: " . $conn->error);
}
$student_id = $_POST['student-id'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password FROM students WHERE id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($db_id, $db_password);

    $stmt->fetch();

    if (password_verify($password, $db_password)) {
        session_start();
        $_SESSION['student_id'] = $db_id;
        header("Location: student_dashboard.html");  
        exit();
    } else {
        echo "<script>alert('Incorrect password. Please try again.'); window.location.href = 'student_login.html';</script>";
    }
} else {
    echo "<script>alert('Student ID not found. Please register or try again.'); window.location.href = 'student_login.html';</script>";
}
$stmt->close();
$conn->close();
?>