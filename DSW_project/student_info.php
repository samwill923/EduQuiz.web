<?php

$conn = new mysqli('localhost', 'root', '');

if ($conn->connect_error) {
    echo "not connected";
    die("Connection failed: " . $conn->connect_error);
}
$dbname="student_registration";
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
} else {
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

$id=$_POST['id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$phone = $_POST['phone'];
$address = $_POST['address'];

// Check if ID already exists
$stmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "<script>alert('ID already exists. Please login.'); window.location.href = 'student_login.html';</script>";
    exit();  
}
$stmt->close();
if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO students (id, first_name, last_name, email, password, dob, gender, phone, address) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $id, $first_name, $last_name, $email, $hashed_password, $dob, $gender, $phone, $address);

if ($stmt->execute()) {
    echo "<script>alert('Registration successful! You will be redirected to the login page.'); window.location.href = 'student_login.html';</script>";  
    exit();
} else {
    echo "Error: ". $stmt->error;
}
$stmt->close();
$conn->close();
?>