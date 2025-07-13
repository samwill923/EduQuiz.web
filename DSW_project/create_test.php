<?php
$conn = new mysqli('localhost', 'root', '');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$dbname = "test_management";
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";

if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

$conn->select_db($dbname);

// Ensure required tables are created
$sql = "CREATE TABLE IF NOT EXISTS tests (
    test_id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id VARCHAR(50) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) AUTO_INCREMENT=100";

if ($conn->query($sql) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

$sql = "CREATE TABLE IF NOT EXISTS questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT NOT NULL,
    question_text TEXT NOT NULL,
    FOREIGN KEY (test_id) REFERENCES tests(test_id) ON DELETE CASCADE
) AUTO_INCREMENT=1000";

if ($conn->query($sql) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

$sql = "CREATE TABLE IF NOT EXISTS choices (
    choice_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    choice_text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(question_id) ON DELETE CASCADE
) AUTO_INCREMENT=10000";

if ($conn->query($sql) !== TRUE) {
    die("Error creating table: " . $conn->error);

    
}

$sql = "CREATE TABLE IF NOT EXISTS student_attempts (
    attempt_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    test_id INT NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(student_id, test_id),
    FOREIGN KEY (test_id) REFERENCES tests(test_id) ON DELETE CASCADE
);";

if ($conn->query($sql) !== TRUE) {
    die("Error creating table: " . $conn->error);

    
}

// Retrieve data from the form
$teacherId = $_POST['teacherId'];
$subject = $_POST['subject'];

$stmt = $conn->prepare("INSERT INTO tests (teacher_id, subject) VALUES (?, ?)");
$stmt->bind_param("ss", $teacherId, $subject);

if ($stmt->execute()) {
    $testId = $stmt->insert_id; // Get the ID of the newly created test

    if (isset($_POST['questions'])) {
        foreach ($_POST['questions'] as $index => $question) {
            $questionText = $question['questionText'];

            // Insert question into the `questions` table
            $stmt = $conn->prepare("INSERT INTO questions (test_id, question_text) VALUES (?, ?)");
            $stmt->bind_param("is", $testId, $questionText);

            if ($stmt->execute()) {
                $questionId = $stmt->insert_id; // Get the ID of the newly created question

                if (isset($question['choices'])) {
                    foreach ($question['choices'] as $choiceIndex => $choice) {
                        $choiceText = $choice['text'];

                        // Check if the current choice is marked as correct
                        $isCorrect = (isset($question['correct']) && $question['correct'] == $choiceIndex) ? 1 : 0;

                        // Insert choice into the `choices` table
                        $stmt = $conn->prepare("INSERT INTO choices (question_id, choice_text, is_correct) VALUES (?, ?, ?)");
                        $stmt->bind_param("isi", $questionId, $choiceText, $isCorrect);
                        $stmt->execute();
                    }
                }
            }
        }
    }

    echo "<script>alert('Test created successfully.'); window.location.href = 'teacher_dashboard.html';</script>";
} else {
    echo "Error creating test: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
