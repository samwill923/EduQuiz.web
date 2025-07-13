<?php
$conn = new mysqli('localhost', 'root', '', 'student_registration');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = $_POST['id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];

$stmt = $conn->prepare("SELECT id FROM students WHERE id = ? AND first_name = ? AND last_name = ?");
$stmt->bind_param("sss", $student_id, $first_name, $last_name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("s", $student_id);

    if ($stmt->execute()) {
        echo "<script>alert('Student dropped successfully.'); window.location.href = 'admin_dashboard.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "<script>alert('No student found with the provided ID and name.'); window.location.href = 'admin_dashboard.html';</script>";
}

$stmt->close();
$conn->close();
?>
