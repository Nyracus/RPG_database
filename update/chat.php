<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Determine who you're chatting with
$chat_with = $_POST['friend_id'] ?? $_GET['chat_with'] ?? null;

// ✅ Send message
if (isset($_POST['send']) && $chat_with) {
    $msg = trim($_POST['message']);
    if ($msg !== '') {
        $msg_safe = mysqli_real_escape_string($conn, $msg);
        mysqli_query($conn, "INSERT INTO ChatMessages (sender_id, receiver_id, message, sent_at) 
                             VALUES ($user_id, $chat_with, '$msg_safe', NOW())");

        // Redirect to avoid resubmission and keep chat context
        header("Location: chat.php?chat_with=$chat_with");
        exit();
    }
}

// ✅ Get friends (Accepted)
$friends_query = mysqli_query($conn,
    "SELECT U.user_id, U.username
     FROM FriendRequests F
     JOIN Users U ON (
         (U.user_id = F.sender_id AND F.receiver_id = $user_id)
         OR (U.user_id = F.receiver_id AND F.sender_id = $user_id)
     )
     WHERE F.status = 'Accepted' AND U.user_id != $user_id"
);
?>


<!DOCTYPE html>
<html>
<head>
    <title>RPG Chat</title>
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

        .chat-container {
            max-width: 700px;
            margin: 80px auto;
            background-color: rgba(25, 25, 25, 0.9);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 20px #000;
            color: #f0e6d2;
        }

        .chat-container h2, .chat-container h3 {
            text-align: center;
            color: #ffd700;
        }

        select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 10px;
            background-color: #333;
            color: #fff;
            border: 1px solid #777;
            border-radius: 5px;
        }

        .chat-box {
            border: 1px solid #777;
            padding: 10px;
            height: 220px;
            overflow-y: scroll;
            background-color: #1b1b1b;
            margin: 15px 0;
            border-radius: 6px;
        }

        .chat-box p {
            margin-bottom: 6px;
        }

        button {
            margin-top: 8px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #9acd32;
        }
    </style>
</head>
<body>

<!--  Background Video -->
<video autoplay muted loop id="bg-video">
    <source src="assets/background.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="chat-container">
    <h2> Chat with a Friend</h2>

    <!-- Friend selection -->
    <form method="post">
        <label>Choose friend: </label>
        <select name="friend_id" required onchange="this.form.submit()">
            <option value="">--Select--</option>
            <?php while ($row = mysqli_fetch_assoc($friends_query)) {
                $selected = ($chat_with == $row['user_id']) ? 'selected' : '';
                echo "<option value='{$row['user_id']}' $selected>{$row['username']}</option>";
            } ?>
        </select>
    </form>

    <?php if ($chat_with): ?>
        <h3>Chatting with 
            <?php
            $res = mysqli_query($conn, "SELECT username FROM Users WHERE user_id = $chat_with");
            $name = mysqli_fetch_assoc($res)['username'];
            echo $name;
            ?>
        </h3>

        <!-- Display chat history -->
        <div class="chat-box">
            <?php
            $chat_query = mysqli_query($conn,
                "SELECT sender_id, message, sent_at FROM ChatMessages 
                 WHERE (sender_id = $user_id AND receiver_id = $chat_with)
                    OR (sender_id = $chat_with AND receiver_id = $user_id)
                 ORDER BY sent_at ASC");

            while ($row = mysqli_fetch_assoc($chat_query)) {
                $sender = ($row['sender_id'] == $user_id) ? "🧍‍♂️ You" : $name;
                echo "<p><strong>$sender:</strong> {$row['message']} <small style='color:gray'>[{$row['sent_at']}]</small></p>";
            }
            ?>
        </div>

        <!-- Send message -->
        <form method="post">
            <input type="hidden" name="friend_id" value="<?php echo $chat_with; ?>">
            <textarea name="message" rows="2" placeholder="Type your message..." required></textarea>
            <button type="submit" name="send">Send</button>
        </form>
    <?php endif; ?>

    <div class="back-link">
        <a href="menu.php">← Back to Menu</a>
    </div>
</div>

</body>
</html>
