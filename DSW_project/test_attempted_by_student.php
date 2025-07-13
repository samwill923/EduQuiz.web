<?php
// Check if student ID is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'test_management');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch all tests attempted by the student
    $sql = "
        SELECT t.test_id, t.subject, t.created_at, q.question_text, a.selected_option, c.choice_text AS correct_option
        FROM answers a
        JOIN questions q ON a.question_id = q.question_id
        JOIN tests t ON q.test_id = t.test_id
        LEFT JOIN choices c ON q.question_id = c.question_id AND c.choice_text = a.selected_option
        WHERE a.student_id = ?
        ORDER BY t.test_id, q.question_id";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $attempted_tests = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $attempted_tests[$row['test_id']]['subject'] = $row['subject'];
            $attempted_tests[$row['test_id']]['created_at'] = $row['created_at'];
            $attempted_tests[$row['test_id']]['questions'][] = [
                'question_text' => $row['question_text'],
                'selected_option' => $row['selected_option'],
                'correct_option' => $row['correct_option']
            ];
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attempted Tests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .test {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .test h3 {
            margin-top: 0;
        }
        .question {
            margin-bottom: 10px;
        }
        .correct {
            color: green;
            font-weight: bold;
        }
        .incorrect {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>View Attempted Tests</h1>
        <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="student_id">Enter Student ID:</label>
                    <input type="text" id="student_id" name="student_id" required>
                </div>
                <div class="form-group">
                    <button type="submit">View Tests</button>
                </div>
            </form>
        <?php else: ?>
            <?php if (!empty($attempted_tests)): ?>
                <h2>Tests Attempted by Student ID: <?php echo htmlspecialchars($student_id); ?></h2>
                <?php foreach ($attempted_tests as $test_id => $test_data): ?>
                    <div class="test">
                        <h3>Test ID: <?php echo $test_id; ?> | Subject: <?php echo htmlspecialchars($test_data['subject']); ?> | Date: <?php echo htmlspecialchars($test_data['created_at']); ?></h3>
                        <?php foreach ($test_data['questions'] as $question): ?>
                            <div class="question">
                                <strong>Question:</strong> <?php echo htmlspecialchars($question['question_text']); ?><br>
                                <strong>Your Answer:</strong> <?php echo htmlspecialchars($question['selected_option']); ?> 
                                <?php if ($question['selected_option'] === $question['correct_option']): ?>
                                    <span class="correct">(Correct)</span>
                                <?php else: ?>
                                    <span class="incorrect">(Incorrect)</span>
                                <?php endif; ?>
                                <br>
                                <strong>Correct Answer:</strong> <?php echo htmlspecialchars($question['correct_option']); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tests found for the given Student ID.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
