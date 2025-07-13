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

        .take-test-button {
            display: inline-block;
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .take-test-button:hover {
            background-color: #218838;
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
    <button class="back-button" onclick="window.location.href='student_dashboard.html'">Back to Dashboard</button>

    <div class="container">
        <h1>All Tests</h1>
        <table>
            <thead>
                <tr>
                    <th>Test ID</th>
                    <th>Teacher</th>
                    <th>Subject</th>
                    <th>Timestamp</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conn = new mysqli("localhost", "root", "", "test_management");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT test_id, teacher_id, subject, created_at FROM tests where subject='Biology' ORDER BY created_at DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['test_id'] . "</td>
                                <td>" . $row['teacher_id'] . "</td>
                                <td>" . $row['subject'] . "</td>
                                <td>" . $row['created_at'] . "</td>
                                <td><a href='take_test.php?test_id=" . $row['test_id'] . "' class='take-test-button'>Take Test</a></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No tests found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
