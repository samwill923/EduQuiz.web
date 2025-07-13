<?php
$conn = new mysqli('localhost', 'root', '', 'student_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_type = $_POST['user_type'];
$user_id = $_POST['id'];
$name = $_POST['name'];
$new_password = $_POST['new_password'];

$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$table = ($user_type === 'teacher') ? 'teachers' : 'students';

$stmt = $conn->prepare("SELECT id FROM $table WHERE id = ? AND first_name = ?");
$stmt->bind_param("ss", $user_id, $name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE id = ?");
    $stmt->bind_param("ss", $hashed_password, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Password changed successfully.'); window.location.href = 'admin_dashboard.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "<script>alert('No user found with the provided ID and name.'); window.location.href = 'admin_dashboard.html';</script>";
}

$stmt->close();
$conn->close();
?>
