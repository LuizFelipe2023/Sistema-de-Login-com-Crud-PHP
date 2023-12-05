<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

require_once("connection/connect.php");
require_once("dao/taskDao.php");

$userEmail = $_SESSION['email'];
$taskDao = new TaskDao($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $id = "";
        $title = isset($_POST["title"]) ? htmlspecialchars($_POST["title"]) : null;
        $description = isset($_POST["description"]) ? htmlspecialchars($_POST["description"]) : null;
        $status = isset($_POST["status"]) ? htmlspecialchars($_POST["status"]) : null;
        if (!empty($title) && !empty($status)) {
            $newTask = new Task($id,$title, $description, $status);
            $taskDao->createTask($newTask, $userEmail);
            header("Location: index.php?success=1");
            exit();
        } else {
            $errorMessage = "Please fill in the required fields.";
        }
    } catch (Exception $e) {
        $errorMessage = "Error creating task: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Create Task</title>
</head>
<body>
    <div class="container">
        <h2>Create Task</h2>
        <a href="index.php">Back to Tasks</a>

        <?php
        if (isset($errorMessage)) {
            echo "<p style='color: red;'>$errorMessage</p>";
        }
        ?>

        <form action="create_task.php" method="post">
            <input type="hidden" name="id" id="id">
            
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea><br>

            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select><br>

            <button type="submit">Create Task</button>
        </form>
    </div>
</body>
</html>
