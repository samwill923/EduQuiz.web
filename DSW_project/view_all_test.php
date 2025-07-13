<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .container {
            width: 80%;
            margin: auto;
            padding-top: 20px;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f9;
        }

        .questions-container {
            margin-top: 20px;
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
        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<button class="back-button" onclick="window.location.href='teacher_dashboard.html'">Back to Dashboard</button>

    <div class="container">
        <h1>All Tests</h1>
        <table>
            <thead>
                <tr>
                    <th>Test ID</th>
                    <th>Teacher</th>
                    <th>Subject</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                    $conn = new mysqli("localhost", "root", "", "test_management");
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $sql = "SELECT test_id, teacher_id, subject, created_at FROM tests ORDER BY created_at DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr onclick='window.location.href=\"?test_id=" . $row['test_id'] . "\"'>
                                    <td>" . $row['test_id'] . "</td>
                                    <td>" . $row['teacher_id'] . "</td>
                                    <td>" . $row['subject'] . "</td>
                                    <td>" . $row['created_at'] . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No tests found</td></tr>";
                    }

                    $conn->close();
                ?>
            </tbody>
        </table>
Double click Test ID to view it's questions.
        <?php
            if (isset($_GET['test_id'])) {
                $test_id = $_GET['test_id'];

                $conn = new mysqli("localhost", "root", "", "test_management");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT q.question_text, c.choice_text 
                        FROM questions q
                        LEFT JOIN choices c ON q.question_id = c.question_id
                        WHERE q.test_id = $test_id
                        ORDER BY q.question_id ASC";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<div class='questions-container'>";
                    $current_question = null;
                    while ($row = $result->fetch_assoc()) {
                        if ($current_question && $current_question['questionText'] !== $row['question_text']) {
                            echo "<h3>Question: " . $current_question['questionText'] . "</h3>";
                            echo "<div class='answers'>";
                            foreach ($current_question['choices'] as $choice) {
                                echo "<div class='answer-option'>" . $choice . "</div>";
                            }
                            echo "</div>";

                            $current_question = null;
                        }
                        
                        if (!$current_question) {
                            $current_question = [
                                'questionText' => $row['question_text'],
                                'choices' => []
                            ];
                        }
                        
                        $current_question['choices'][] = $row['choice_text'];
                    }

                    if ($current_question) {
                        echo "<h3>Question: " . $current_question['questionText'] . "</h3>";
                        echo "<div class='answers'>";
                        foreach ($current_question['choices'] as $choice) {
                            echo "<div class='answer-option'>" . $choice . "</div>";
                        }
                        echo "</div>";
                    }

                    echo "</div>";
                } else {
                    echo "<p>No questions found for this test.</p>";
                }

                $conn->close();
            }
        ?>

    </div>

</body>
</html>
