<?php
session_start();
include 'db.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = $password; // Use password_hash($password, PASSWORD_DEFAULT) in real app

    // Check if username/email already exists
    $check_query = "SELECT * FROM Users WHERE username='$username' ";
    //OR email='$email'
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error = "Username or Email already exists!";
    } else {
        // Insert user into database
        $query = "INSERT INTO Users (username, email, password_hash) 
                  VALUES ('$username', '$email', '$hashed_password')";
        if (mysqli_query($conn, $query)) {
            $user_id = mysqli_insert_id($conn);
            $create_char = "INSERT INTO Characters (user_id, name, level, xp, power_level, coins)
                            VALUES ($user_id, '$username', 1, 0, 10, 100)";
            mysqli_query($conn, $create_char);

            $success = "Registration successful! You can now <a href='index.php'>login</a>.";
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
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
            opacity: 0.72;
            filter: brightness(75%);
        }

        .signup-container {
            max-width: 450px;
            margin: 80px auto;
            background-color: rgba(30, 30, 30, 0.94);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px #000;
            color: #f0e6d2;
            text-align: center;
        }

        h2 {
            color: #ffd700;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            background-color: #333;
            color: #fff;
            border: 1px solid #777;
            border-radius: 6px;
        }

        input[type="submit"] {
            background-color: #5a2a27;
            color: white;
            padding: 10px 20px;
            border: none;
            margin-top: 15px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #882e2e;
        }

        a {
            color: #9acd32;
        }

        .message {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- 🎥 Background Video -->
<video autoplay muted loop id="bg-video">
    <source src="assets/background.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="signup-container">
    <h2> Register as an Adventurer</h2>

    <?php if (isset($error)) echo "<p class='message' style='color:red;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='message' style='color:lightgreen;'>$success</p>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" name="register" value="Sign Up">
    </form>

    <p class="message">Already registered? <a href="index.php">Login here</a>.</p>
</div>

</body>
</html>
