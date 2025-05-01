<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM Users WHERE username = '$username' AND password_hash = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['user_id'];

        // ✅ Redirect all to menu — role-based view handled there
        header("Location: menu.php");
    } else {
        $error = "❌ Invalid username or password.";
    }
}
?>


<!DOCTYPE html>
<html>
<head><title>Adventurer Login</title></head>
<body>
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" name="login" value="Login">
    </form>
    <p>New here? <a href="signup.php">Create an account</a></p>
    <p><em>One Guild Master to control them all: <strong>ID and Password appointed</strong></em></p>



</body>
</html>
