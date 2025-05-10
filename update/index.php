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
        $error = "Invalid username or password.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Adventurer Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        #bg-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: -1;
            object-fit: cover;
            opacity: 0.75;
            filter: brightness(50%);
        }

        .login-container {
            max-width: 400px;
            margin: 80px auto;
            background-color: rgba(30, 30, 30, 0.92);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 20px #000;
            text-align: center;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 90%;
            margin-bottom: 10px;
        }

        .login-container input[type="submit"] {
            margin-top: 10px;
        }

        .login-container p a {
            color: #9acd32;
        }
    </style>
</head>
<body>

<!-- 🎥 Background Video -->
<video autoplay muted loop id="bg-video">
    <source src="assets/index_bg.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="login-container">
    <h2> Adventurer Login</h2>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" name="login" value="Login">
    </form>

    <p>New here? <a href="signup.php">Create an account</a></p>
    <p style="color: #aaa;"><em>One Guild Master to control them all.<br><strong>ID and Password appointed</strong></em></p>
</div>

</body>
</html>
