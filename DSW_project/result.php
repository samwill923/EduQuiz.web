<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'test_management');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch the number of correct answers for each student in each test
$sql = "
    SELECT 
        a.student_id,
        t.test_id,
        t.subject,
        COUNT(CASE WHEN a.is_correct = 1 THEN 1 END) AS correct_answers,
        COUNT(q.question_id) AS total_questions
    FROM answers a
    JOIN questions q ON a.question_id = q.question_id
    JOIN tests t ON q.test_id = t.test_id
    GROUP BY a.student_id, t.test_id
    ORDER BY a.student_id, t.test_id";

$result = $conn->query($sql);

// Prepare data for display
$student_tests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $student_tests[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Evaluation Results</h1>
        <?php if (!empty($student_tests)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Test ID</th>
                        <th>Subject</th>
                        <th>Correct Answers</th>
                        <th>Total Questions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($student_tests as $entry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entry['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($entry['test_id']); ?></td>
                            <td><?php echo htmlspecialchars($entry['subject']); ?></td>
                            <td><?php echo htmlspecialchars($entry['correct_answers']); ?></td>
                            <td><?php echo htmlspecialchars($entry['total_questions']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No data available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
