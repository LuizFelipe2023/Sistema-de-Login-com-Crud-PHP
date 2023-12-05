<?php
require_once("models/task.php");
require_once("dao/userDao.php");

class TaskDao implements TaskDaoInterface
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function createTask(Task $task, $userEmail)
    {
        try {
            $title = $task->getTitle();
            $description = $task->getDescription();
            $status = $task->getStatus();

            $stmt = $this->conn->prepare("INSERT INTO tasks (title, description, status, userEmail) VALUES (?, ?, ?, ?)");
            $stmt->bindParam(1, $title, PDO::PARAM_STR);
            $stmt->bindParam(2, $description, PDO::PARAM_STR);
            $stmt->bindParam(3, $status, PDO::PARAM_STR);
            $stmt->bindParam(4, $userEmail, PDO::PARAM_STR);

            $stmt->execute();
            $taskId = $this->conn->lastInsertId();
            $task->setId($taskId);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }


    public function getAllTasks($userEmail)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE userEmail = ?");
            $stmt->bindParam(1, $userEmail, PDO::PARAM_STR);
            $stmt->execute();

            $tasks = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tasks[] = new Task($row['id'], $row['title'], $row['description'], $row['status']);
            }

            return $tasks;
        } catch (PDOException $e) {
            throw new Exception("Error retrieving tasks: " . $e->getMessage());
        }
    }

    public function getTaskById($id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null;
            }
            return new Task($result['id'], $result['title'], $result['description'], $result['status']);
        } catch (PDOException $e) {
            throw new Exception("Error retrieving task: " . $e->getMessage());
        }
    }

    public function updateTask(Task $task, $userEmail, $id)
    {
        try {
            $title = $task->getTitle();
            $description = $task->getDescription();
            $status = $task->getStatus();
            $taskId = $task->getId();

            $stmt = $this->conn->prepare("UPDATE tasks SET title = ?, description = ?, status = ? WHERE id = ? AND userEmail = ?");
            $stmt->bindParam(1, $title, PDO::PARAM_STR);
            $stmt->bindParam(2, $description, PDO::PARAM_STR);
            $stmt->bindParam(3, $status, PDO::PARAM_STR);
            $stmt->bindParam(4, $taskId, PDO::PARAM_INT);  // Use o ID do objeto Task
            $stmt->bindParam(5, $userEmail, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error updating task: " . $e->getMessage());
        }
    }

    public function deleteTask($id, $userEmail)
    {
        try {
            $existingTask = $this->getTaskById($id);
            if ($existingTask) {
                $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = ? AND userEmail = ?");
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $userEmail, PDO::PARAM_STR);
                return $stmt->execute();
            } else {
                throw new Exception("Task not found or you don't have permission to delete it.");
            }
        } catch (PDOException $e) {
            throw new Exception("Error deleting task: " . $e->getMessage());
        }
    }
}
