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
<head><title>Sign Up</title></head>
<body>
<h2>Register as an Adventurer</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post">
    Username: <input type="text" name="username" required><br>
    Email:    <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" name="register" value="Sign Up">
</form>

<p>Already registered? <a href="index.php">Login here</a>.</p>
</body>
</html>
