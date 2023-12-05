<?php
session_start();
require_once("connection/connect.php");
require_once("models/user.php");
require_once("dao/userDao.php");

$userDao = new UserDao($conn);
$loginResultMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST["email"]) ? $_POST["email"] : '';
    $password = isset($_POST["password"]) ? $_POST["password"] : '';

    if (empty($email) || empty($password)) {
        $loginResultMessage = "Please fill in both the email and password fields.";
    } else {
        try {
            $loggedUser = $userDao->login($email, $password);

            if ($loggedUser) {
                $_SESSION['email'] = $email;
                header("Location: index.php");
                exit();
            } else {
                $loginResultMessage = "Invalid email or password. Please try again.";
            }
        } catch (PDOException $e) {
            $loginResultMessage = "Error during login. Please try again later.";
            error_log("Database Error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <?php
        if (!empty($loginResultMessage)) {
            echo "<p style='color: red;'>$loginResultMessage</p>";
        }
        ?>

        <form action="login.php" method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit">Login</button>
        </form>

        <br>

        <a href="register.php">Register</a>
        <a href="redefinePassword.php">Forgot your password?</a>

    </div>
</body>
</html>
