<?php
session_start();
include 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Handle sending friend request
if (isset($_POST['send_request'])) {
    $friend_username = $_POST['friend_username'];

    $receiver_query = mysqli_query($conn, "SELECT user_id FROM Users WHERE username = '$friend_username'");
    if ($receiver = mysqli_fetch_assoc($receiver_query)) {
        $receiver_id = $receiver['user_id'];

        if ($receiver_id == $user_id) {
            $message = "❌ You cannot add yourself!";
        } else {
            // Check for existing request
            $check_query = mysqli_query($conn,
                "SELECT * FROM FriendRequests 
                 WHERE (sender_id = $user_id AND receiver_id = $receiver_id)
                    OR (sender_id = $receiver_id AND receiver_id = $user_id)");

            if (mysqli_num_rows($check_query) > 0) {
                $message = "⚠️ Friend request already exists.";
            } else {
                mysqli_query($conn,
                    "INSERT INTO FriendRequests (sender_id, receiver_id, status) 
                     VALUES ($user_id, $receiver_id, 'Pending')");
                $message = "✅ Friend request sent to $friend_username.";
            }
        }
    } else {
        $message = "❌ User not found.";
    }
}

// ✅ Handle accept/reject
if (isset($_POST['action']) && isset($_POST['request_id'])) {
    $req_id = $_POST['request_id'];
    $action = $_POST['action'];

    $new_status = ($action === 'accept') ? 'Accepted' : 'Rejected';
    mysqli_query($conn, "UPDATE FriendRequests SET status = '$new_status' WHERE request_id = $req_id");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Friends</title>
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

        .friends-container {
            max-width: 600px;
            margin: 80px auto;
            background-color: rgba(25, 25, 25, 0.92);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 20px #000;
            color: #f0e6d2;
        }

        .friends-container h2, .friends-container h3 {
            text-align: center;
            color: #ffd700;
        }

        .friends-container input[type="text"] {
            width: 60%;
            margin-top: 10px;
        }

        .friends-container button {
            margin-top: 10px;
        }

        .friend-block {
            border: 1px solid #444;
            background-color: #1e1e1e;
            padding: 10px;
            margin: 10px 0;
            border-radius: 6px;
        }

        .friend-block strong {
            color: #9acd32;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #9acd32;
        }

        form {
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

<div class="friends-container">
    <h2> Friend System</h2>
    <?php if (isset($message)) echo "<p style='color:lightgreen;'>$message</p>"; ?>

    <h3>Send a Friend Request</h3>
    <form method="post">
        <input type="text" name="friend_username" placeholder="Enter username..." required>
        <button type="submit" name="send_request">Send Request</button>
    </form>

    <h3>Your Friends</h3>
    <?php
    $friends_query = mysqli_query($conn,
        "SELECT U.username 
         FROM FriendRequests F
         JOIN Users U ON (
             (U.user_id = F.sender_id AND F.receiver_id = $user_id)
             OR (U.user_id = F.receiver_id AND F.sender_id = $user_id)
         )
         WHERE F.status = 'Accepted' AND U.user_id != $user_id");

    if (mysqli_num_rows($friends_query) > 0) {
        while ($row = mysqli_fetch_assoc($friends_query)) {
            echo "<div class='friend-block'>👤 <strong>{$row['username']}</strong></div>";
        }
    } else {
        echo "<p>No friends yet. Go touch some grass. </p>";
    }
    ?>

    <h3>Incoming Friend Requests</h3>
    <?php
    $incoming = mysqli_query($conn,
        "SELECT F.request_id, U.username 
         FROM FriendRequests F
         JOIN Users U ON F.sender_id = U.user_id
         WHERE F.receiver_id = $user_id AND F.status = 'Pending'");

    if (mysqli_num_rows($incoming) > 0) {
        while ($row = mysqli_fetch_assoc($incoming)) {
            echo "<div class='friend-block'>";
            echo "<strong>{$row['username']}</strong> wants to be friends.";
            echo "<form method='post'>";
            echo "<input type='hidden' name='request_id' value='{$row['request_id']}'>";
            echo " <button type='submit' name='action' value='accept'>Accept</button>";
            echo " <button type='submit' name='action' value='reject'>Reject</button>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "<p>No incoming requests. Keu tore chay na. </p>";
    }
    ?>

    <div class="back-link">
        <a href='menu.php'>← Back to Menu</a>
    </div>
</div>

</body>
</html>

