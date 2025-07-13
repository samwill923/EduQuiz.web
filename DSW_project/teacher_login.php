<?php
session_start();

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

$sql = "CREATE TABLE IF NOT EXISTS teachers (
    id varchar(100) NOT NULL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    subject ENUM('Physics', 'Maths', 'Biology', 'Chemistry', 'Literature') NOT NULL,
    phone_no VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    hire_date DATE NOT NULL
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

$id = $_POST['id'];
$password = $_POST['password'];

$sql = "SELECT * FROM teachers WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['id'] = $user['id'];
        header("Location: teacher_dashboard.html");
        exit();
    } else {
        echo "<script>alert('Incorrect password. Please try again.'); window.location.href = 'teacher_login.html';</script>";
    }
} else {
    echo "<script>alert('Teacher ID not found. Please register or try again.'); window.location.href = 'teacher_login.html';</script>";
}
$stmt->close();
$conn->close();
?>
