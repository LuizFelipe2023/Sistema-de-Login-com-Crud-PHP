<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $taskId = $_GET["id"];
    
    require_once("connection/connect.php");
    require_once("dao/taskDao.php");

    $taskDao = new TaskDao($conn);

    try {
        $task = $taskDao->getTaskById($taskId);

        if (!$task) {
            header("Location: index.php");
            exit();
        }
    } catch (Exception $e) {
        $errorMessage = "Error retrieving task: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["confirm"])) {
    $taskId = $_POST["id"];
    $confirmation = $_POST["confirm"];

    require_once("connection/connect.php");
    require_once("dao/taskDao.php");

    $taskDao = new TaskDao($conn);

    try {
        if ($confirmation === 'DELETE') {
            $taskDao->deleteTask($taskId, $userEmail);

            header("Location: index.php");
            exit();
        } else {
            $errorMessage = "Invalid confirmation. Please type 'DELETE' to confirm.";
        }
    } catch (Exception $e) {
        $errorMessage = "Error deleting task: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Delete Task</title>
</head>
<body>
    <div class="container">
        <h2>Delete Task</h2>
        <a href="index.php">Back to Tasks</a>

        <?php
        if (isset($errorMessage)) {
            echo "<p style='color: red;'>$errorMessage</p>";
        }
        ?>

        <form action="delete_task.php" method="post">
            <label for="id">Id:</label> 
            <input type="number" name="id" id="id">
            
            <p>Are you sure you want to delete the task?</p>

            <label for="confirm">Type 'DELETE' to confirm:</label>
            <input type="text" name="confirm" id = "confirm">

            <button type="submit">Delete Task</button>
        </form>
    </div>
</body>
</html>
