<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $test_id = $_POST['test_id'];

    $conn = new mysqli("localhost", "root", "", "test_management");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql='CREATE TABLE IF NOT EXISTS answers (
    answer_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    question_id INT NOT NULL,
    test_id INT NOT NULL,
    selected_option TEXT NOT NULL,
    is_correct BOOLEAN NOT NULL,
    FOREIGN KEY (question_id) REFERENCES questions(question_id) ON DELETE CASCADE,
    FOREIGN KEY (test_id) REFERENCES tests(test_id) ON DELETE CASCADE
)';

if(!mysqli_query($conn,$sql)){
    echo 'error in creating table';
}

    $answers = $_POST['answers'];
    foreach ($answers as $question_id => $selected_option) {
        $is_correct = 0;

        $stmt = $conn->prepare("SELECT is_correct FROM choices WHERE question_id = ? AND choice_text = ?");
        $stmt->bind_param("is", $question_id, $selected_option);
        $stmt->execute();
        $stmt->bind_result($is_correct_choice);
        if ($stmt->fetch()) {
            $is_correct = $is_correct_choice ? 1 : 0;
        }
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO answers (student_id, question_id, test_id, selected_option, is_correct) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siisi", $student_id, $question_id, $test_id, $selected_option, $is_correct);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
    echo "<script>alert('Test submitted successfully');window.location.href = 'student_dashboard.html';</script>";
    exit;
}

if (!isset($_GET['test_id'])) {
    die("Test ID is required.");
}

$test_id = $_GET['test_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }

        .container {
            width: 80%;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        .question {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .answers {
            margin-top: 10px;
        }

        .answer-option {
            margin-bottom: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Attempt Test</h1>
        <form method="POST" action="">
            <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">

            <div>
                <label for="student_id">Enter Student ID:</label>
                <input type="text" id="student_id" name="student_id" required>
            </div>

            <?php
            $conn = new mysqli("localhost", "root", "", "test_management");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT q.question_id, q.question_text, c.choice_text 
                    FROM questions q
                    LEFT JOIN choices c ON q.question_id = c.question_id
                    WHERE q.test_id = ?
                    ORDER BY q.question_id ASC";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $test_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $questions = [];
            while ($row = $result->fetch_assoc()) {
                $questions[$row['question_id']]['question_text'] = $row['question_text'];
                $questions[$row['question_id']]['choices'][] = $row['choice_text'];
            }
            $stmt->close();
            $conn->close();

            foreach ($questions as $question_id => $question) {
                echo "<div class='question'>";
                echo "<h3>" . htmlspecialchars($question['question_text']) . "</h3>";
                echo "<div class='answers'>";
                foreach ($question['choices'] as $choice) {
                    echo "<div class='answer-option'>";
                    echo "<label><input type='radio' name='answers[$question_id]' value='" . htmlspecialchars($choice) . "' required> " . htmlspecialchars($choice) . "</label>";
                    echo "</div>";
                }
                echo "</div>";
                echo "</div>";
            }
            ?>
            <button type="submit">Submit Test</button>
        </form>
    </div>
</body>
</html>
