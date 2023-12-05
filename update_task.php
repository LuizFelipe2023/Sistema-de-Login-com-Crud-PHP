<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET["id"])) {
        header("Location: index.php");
        exit();
    }

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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once("connection/connect.php");
    require_once("dao/taskDao.php");

    $taskIdFromUser = $_POST["id"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $status = $_POST["status"];
    
    $taskDao = new TaskDao($conn);
    $updatedTask = new Task($taskIdFromUser, $title, $description, $status);  
    $updatedTask->setId($taskIdFromUser);  
    
    try {
        $taskDao->updateTask($updatedTask, $userEmail, $taskIdFromUser);
    
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $errorMessage = "Error updating task: " . $e->getMessage();
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Update Task</title>
</head>
<body>
    <div class="container">
        <h2>Update Task</h2>
        <a href="index.php">Back to Tasks</a>

        <?php
        if (isset($errorMessage)) {
            echo "<p style='color: red;'>$errorMessage</p>";
        }
        ?>

        <form action="update_task.php" method="post">
            <label for="id">Id:</label>
            <input type="number" name="id" id="id">

            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo $task->getTitle(); ?>" required><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo $task->getDescription(); ?></textarea><br>

            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Pending" <?php echo ($task->getStatus() === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="In Progress" <?php echo ($task->getStatus() === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                <option value="Completed" <?php echo ($task->getStatus() === 'Completed') ? 'selected' : ''; ?>>Completed</option>
            </select><br>

            <button type="submit">Update Task</button>
        </form>
    </div>
</body>
</html>
