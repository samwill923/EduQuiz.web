<?php
$conn = new mysqli('localhost', 'root', '', 'student_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$teacher_id = $_POST['teacher_id'];
$teacher_name = $_POST['teacher_name'];

$stmt = $conn->prepare("SELECT id, first_name FROM teachers WHERE id = ? AND first_name = ?");
$stmt->bind_param("ss", $teacher_id, $teacher_name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
    $stmt->bind_param("s", $teacher_id);

    if ($stmt->execute()) {
        echo "<script>alert('Teacher dropped successfully.'); window.location.href = 'admin_dashboard.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "<script>alert('No teacher found with the provided ID and name.'); window.location.href = 'admin_dashboard.html';</script>";
}

$stmt->close();
$conn->close();
?>
