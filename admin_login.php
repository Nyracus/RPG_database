<?php
session_start();
include 'db.php';

if (isset($_POST['gm_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username !== 'gm') {
        $error = "Only the 'gm' account may access this portal.";
    } else {
        $query = "SELECT * FROM Users WHERE username = 'gm' AND password_hash = '$password' AND role = 'GuildMaster'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $user['user_id'];
            header("Location: menu.php");
        } else {
            $error = "Invalid credentials.";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head><title>🦇 Guild Master Access</title></head>
<body>
<h2>🦇 Welcome to Batman Protocol Access</h2>

<p style="color:blue;">
    Your username is <strong>gm</strong>.<br>
    The password was the secret code sent to you by the guild.
</p>

<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="post">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit" name="gm_login">Enter Guild</button>
</form>

<p><a href="index.php">← Back to Player Login</a></p>
</body>
</html>
