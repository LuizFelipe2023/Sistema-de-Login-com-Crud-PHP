<?php
include_once("models/user.php");

class userDao implements UserDaoInterface
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function createUser(User $user)
    {
        try {
            $email = $user->getEmail();
            $password = $user->getPassword();
            $options = ['cost' => 12];
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options);

            $stmt = $this->conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->bindParam(2, $hashedPassword, PDO::PARAM_STR);

            $result = $stmt->execute();

            if ($result) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new Exception("ERROR AT CREATING USER: " . $e->getMessage());
        }
    }
    public function getUserByEmail($email)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null;
            }

            return new User($result['id'], $result['email'], $result['password']);
        } catch (PDOException $e) {
            throw new Exception("Error retrieving user by email: " . $e->getMessage());
        }
    }

    public function userExists($email)
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (PDOException $e) {
            throw new Exception("Error checking if user exists: " . $e->getMessage());
        }
    }

    public function login($email, $password)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                if (password_verify($password, $user['password'])) {
                    return new User($user['id'], $user['email'], $user['password']);
                }
            }
            return null;
        } catch (PDOException $e) {
            throw new Exception("Error at login: " . $e->getMessage());
        }
    }
    public function redefinePassword($email, $newPassword)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $options = ['cost' => 12];
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, $options);
                $updateSTMT = $this->conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $updateSTMT->bindParam(1, $hashedPassword, PDO::PARAM_STR);
                $updateSTMT->bindParam(2, $email, PDO::PARAM_STR);
                return $updateSTMT->execute();
            }
        } catch (PDOException $e) {
            throw new Exception("Error at redefine password: " . $e->getMessage());
        }
    }
}
