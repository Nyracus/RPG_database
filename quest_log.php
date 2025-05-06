<?php
include 'db.php';
session_start();


$completed = mysqli_query($conn,
    "SELECT QA.accept_id, Q.name AS quest_name, C.name AS char_name, U.username,
            QA.accepted_at, QA.completion_status
     FROM QuestAcceptance QA
     JOIN Quests Q ON QA.quest_id = Q.quest_id
     JOIN Characters C ON QA.char_id = C.char_id
     JOIN Users U ON C.user_id = U.user_id
     WHERE QA.completion_status = 'Completed'
     ORDER BY QA.accepted_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quest Log</title>
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

        .quest-log-wrapper {
            max-width: 900px;
            margin: 80px auto;
            background-color: rgba(30, 30, 30, 0.94);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 25px #000;
            color: #f0e6d2;
        }

        .quest-log-wrapper h2 {
            text-align: center;
            color: #ffd700;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid #555;
            text-align: center;
        }

        th {
            background-color: #333;
            color: #ffd700;
        }

        td strong {
            color: #9acd32;
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

<!-- 🎥 Background Video -->
<video autoplay muted loop id="bg-video">
    <source src="assets/background.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="quest-log-wrapper">
    <h2>Quest Completion Log (GM View)</h2>

    <table>
        <tr>
            <th>Player</th>
            <th>Character</th>
            <th>Quest Name</th>
            <th>Accepted At</th>
            <th>Status</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($completed)): ?>
            <tr>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['char_name']) ?></td>
                <td><?= htmlspecialchars($row['quest_name']) ?></td>
                <td><?= $row['accepted_at'] ?></td>
                <td style="color:lightgreen; font-weight:bold;"><?= $row['completion_status'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div class="back-link">
        <a href="menu.php">← Back to Menu</a>
    </div>
</div>

</body>
</html>

