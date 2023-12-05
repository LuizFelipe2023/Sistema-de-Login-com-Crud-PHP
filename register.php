<?php
require_once("connection/connect.php");
require_once("models/user.php");
require_once("dao/userDao.php");

$userDao = new userDao($conn);

$registrationResultMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["email"]) || !isset($_POST["password"])) {
        $registrationResultMessage = "Please fill in both the email and password fields.";
    } else {
        $email = $_POST["email"];
        $password = $_POST["password"];

        if (strlen($password) < 6) {
            $registrationResultMessage = "Password must be at least 6 characters long.";
        } else {
            try {
                $user = new User($email, $password);
                $registrationResult = $userDao->createUser($user);

                if ($registrationResult) {
                    header("location: login.php");
                    exit();
                } else {
                    $registrationResultMessage = "Registration failed. Try again.";
                }
            } catch (PDOException $e) {
                $registrationResultMessage = "Error during registration: " . $e->getMessage();
            }
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
    <title>Registration</title>
</head>

<body>
    <div class="container">

        <h2>Registration</h2>

        <?php
        if (!empty($registrationResultMessage)) {
            echo "<p style='color: red;'>$registrationResultMessage</p>";
        }
        ?>

        <form action="register.php" method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit">Register</button>
        </form>

        <br>

        <a href="login.php">Login</a>

    </div>
</body>

</html>
