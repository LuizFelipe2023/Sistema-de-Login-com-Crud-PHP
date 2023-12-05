<?php
session_start();
require_once("connection/connect.php");
require_once("dao/userDao.php");

$userDao = new UserDao($conn);
$redefineMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST["email"]) || !isset($_POST["new_password"]) || !isset($_POST["confirm_password"])) {
        $redefineMessage = "Fill in both the email, new password, and confirm password fields.";
    } else {
        $email = $_POST["email"];
        $newPassword = $_POST["new_password"];
        $confirmPassword = $_POST["confirm_password"];
        if ($newPassword != $confirmPassword) {
            $redefineMessage = "New password and confirm password do not match.";
        } else {
            try {
                $userExists = $userDao->userExists($email);
                if ($userExists) {
                    $redefineResult = $userDao->redefinePassword($email, $newPassword);

                    if ($redefineResult) {
                        $redefineMessage = "Password redefined successfully. You can now log in with your new password.";
                    } else {
                        $redefineMessage = "Password redefinition failed. Please try again.";
                    }
                } else {
                    $redefineMessage = "User with the provided email does not exist.";
                }
            } catch (PDOException $e) {
                $redefineMessage = "Error during password redefinition: " . $e->getMessage();
                error_log("Database Error: " . $e->getMessage());
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
    <title>Password Redefinition</title>
</head>

<body>
    <div class="container">

        <h2>Password Redefinition</h2>

        <?php
        if (!empty($redefineMessage)) {
            echo "<p style='color: red;'>$redefineMessage</p>";
        }
        ?>

        <form action="redefinePassword.php" method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required><br>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required><br>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br>

            <button type="submit">Redefine Password</button>
        </form>

        <br>

        <a href="login.php">Login</a>

    </div>
</body>

</html>