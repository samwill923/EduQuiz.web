<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.html"); // Redirect to login if not logged in
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'student_registration');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = $_SESSION['student_id'];

$sql = "SELECT id, first_name, last_name, email, dob, gender, phone, address FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "<script>alert('Student profile not found.'); window.location.href = 'student_dashboard.html';</script>";
    exit();
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        .profile-info {
            margin-top: 20px;
        }

        .profile-info div {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .value {
            color: #333;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Profile</h1>
        <div class="profile-info">
            <div><span class="label">Student ID:</span> <span class="value"><?php echo htmlspecialchars($student['id']); ?></span></div>
            <div><span class="label">First Name:</span> <span class="value"><?php echo htmlspecialchars($student['first_name']); ?></span></div>
            <div><span class="label">Last Name:</span> <span class="value"><?php echo htmlspecialchars($student['last_name']); ?></span></div>
            <div><span class="label">Email:</span> <span class="value"><?php echo htmlspecialchars($student['email']); ?></span></div>
            <div><span class="label">Date of Birth:</span> <span class="value"><?php echo htmlspecialchars($student['dob']); ?></span></div>
            <div><span class="label">Gender:</span> <span class="value"><?php echo htmlspecialchars($student['gender']); ?></span></div>
            <div><span class="label">Phone:</span> <span class="value"><?php echo htmlspecialchars($student['phone']); ?></span></div>
            <div><span class="label">Address:</span> <span class="value"><?php echo nl2br(htmlspecialchars($student['address'])); ?></span></div>
        </div>
        <div class="button-container">
            <button onclick="window.location.href='student_dashboard.html'">Back to Dashboard</button>
        </div>
    </div>
</body>
</html>
