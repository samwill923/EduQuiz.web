<?php
$conn = new mysqli('localhost', 'root', '');

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
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$password = $_POST['password'];
$subject = $_POST['subject'];
$phone_no = $_POST['phone_no'];
$address = $_POST['address'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$hire_date = $_POST['hire_date'];

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT id FROM teachers WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('Teacher ID already exists! Please use a unique ID.'); window.location.href = 'admin_dashboard.html';</script>";
    exit();
}

$stmt->close();

$stmt = $conn->prepare("INSERT INTO teachers (id, first_name, last_name, email, password, subject, phone_no, address, dob, gender, hire_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->bind_param("sssssssssss", $id, $first_name, $last_name, $email, $hashed_password, $subject, $phone_no, $address, $dob, $gender, $hire_date);

if ($stmt->execute()) {
    echo "<script>alert('Teacher added successfully.'); window.location.href = 'admin_dashboard.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
