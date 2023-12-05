<?php
session_start();
require_once("connection/connect.php");
require_once("dao/taskDao.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['email'];
$taskDao = new TaskDao($conn);

$tasks = $taskDao->getAllTasks($userEmail);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Home</title>
</head>

<body>
    <div class="container">
        <h2>Welcome, <?php echo $userEmail; ?>!</h2>
        <br>
        <h3>Your Tasks</h3>
        <br>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php

            foreach ($tasks as $task) {
                echo "<tr>";
                echo "<td>" . $task->getId() . "</td>";
                echo "<td>" . $task->getTitle() . "</td>";
                echo "<td>" . $task->getDescription() . "</td>";
                echo "<td>" . $task->getStatus() . "</td>";
                echo "<td class='action-links'>";
                echo "<a href='update_task.php?id=" . $task->getId() . "'>Update</a>";
                echo "<a href='delete_task.php?id=" . $task->getId() . "'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }


            ?>

        </table>
        <br>

        <a href="create_task.php">Create Task</a>
        <a href="logout.php">Logout</a>

    </div>
</body>

</html>