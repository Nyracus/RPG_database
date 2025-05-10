<?php
include 'db.php';
session_start();

$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');

$leaders = mysqli_query($conn,
    "SELECT U.username, C.level, C.rank,
        C.power AS base_power,
        COALESCE((
            SELECT SUM(I.power_value)
            FROM Equipment E
            JOIN Items I ON E.item_id = I.item_id
            WHERE E.char_id = C.char_id
        ), 0) AS equip_power,
        COALESCE((
            SELECT SUM(S.power_value)
            FROM CharacterSkills CS
            JOIN Skills S ON CS.skill_id = S.skill_id
            WHERE CS.char_id = C.char_id AND CS.equipped = 1
        ), 0) AS skill_power
    FROM Users U
    JOIN Characters C ON U.user_id = C.user_id
    WHERE U.role = 'Player' " .
    ($search !== '' ? "AND U.username LIKE '%$search%'" : "") . "
    ORDER BY (C.power +
              COALESCE((
                  SELECT SUM(I.power_value)
                  FROM Equipment E
                  JOIN Items I ON E.item_id = I.item_id
                  WHERE E.char_id = C.char_id
              ), 0) +
              COALESCE((
                  SELECT SUM(S.power_value)
                  FROM CharacterSkills CS
                  JOIN Skills S ON CS.skill_id = S.skill_id
                  WHERE CS.char_id = C.char_id AND CS.equipped = 1
              ), 0)
    ) DESC");

?>


<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard</title>
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

        .leaderboard-wrapper {
            max-width: 1000px;
            margin: 80px auto;
            background-color: rgba(25, 25, 25, 0.93);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 25px #000;
            color: #f0e6d2;
        }

        .leaderboard-wrapper h2 {
            text-align: center;
            color: #ffd700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #555;
        }

        th {
            background-color: #333;
            color: #ffd700;
        }

        td strong {
            color: #ffcc00;
        }

        form {
            text-align: center;
            margin-top: 20px;
        }

        input[type="text"] {
            width: 250px;
            padding: 8px;
            background-color: #333;
            color: #fff;
            border: 1px solid #777;
            border-radius: 6px;
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

<div class="leaderboard-wrapper">
    <h2>RPG Leaderboard — Sorted by Total Power</h2>

    <form method="get">
        <label>Search by Username:</label>
        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>Rank</th>
            <th>Username</th>
            <th>Level</th>
            <th>Rank Title</th>
            <th>Base Power</th>
            <th>Equip Power</th>
            <th>Skill Power</th>
            <th>Total Power</th>
        </tr>

        <?php
        $rank = 1;
        while ($row = mysqli_fetch_assoc($leaders)):
            $total = $row['base_power'] + $row['equip_power'] + $row['skill_power'];
        ?>
            <tr>
                <td><?= $rank++ ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= $row['level'] ?></td>
                <td><?= $row['rank'] ?></td>
                <td><?= $row['base_power'] ?></td>
                <td><?= $row['equip_power'] ?></td>
                <td><?= $row['skill_power'] ?></td>
                <td><strong><?= $total ?></strong></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div class="back-link">
        <a href="menu.php">← Back to Menu</a>
    </div>
</div>

</body>
</html>

