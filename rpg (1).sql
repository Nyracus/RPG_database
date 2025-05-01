-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2025 at 10:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rpg`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `batmanprotocol`
-- (See below for the actual view)
--
CREATE TABLE `batmanprotocol` (
`user_id` int(11)
,`username` varchar(50)
,`email` varchar(100)
,`password_hash` varchar(255)
,`role` enum('Player','GuildMaster')
);

-- --------------------------------------------------------

--
-- Table structure for table `battlelogs`
--

CREATE TABLE `battlelogs` (
  `log_id` int(11) NOT NULL,
  `battle_id` int(11) DEFAULT NULL,
  `action_by` varchar(50) DEFAULT NULL,
  `action_detail` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `battles`
--

CREATE TABLE `battles` (
  `battle_id` int(11) NOT NULL,
  `char_id` int(11) DEFAULT NULL,
  `enemy_id` int(11) DEFAULT NULL,
  `status` enum('Ongoing','Won','Lost') DEFAULT NULL,
  `turn` enum('Player','Enemy') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

CREATE TABLE `characters` (
  `char_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `level` int(11) DEFAULT 1,
  `xp` int(11) DEFAULT 0,
  `power_level` int(11) DEFAULT 1,
  `coins` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `characters`
--

INSERT INTO `characters` (`char_id`, `user_id`, `name`, `level`, `xp`, `power_level`, `coins`) VALUES
(1, 2, 'Iracus2', 1, 0, 10, 0),
(2, 3, 'gm', 1, 0, 10, 100);

--
-- Triggers `characters`
--
DELIMITER $$
CREATE TRIGGER `auto_level_up` AFTER UPDATE ON `characters` FOR EACH ROW BEGIN
  IF NEW.xp >= 100 THEN
    UPDATE Characters SET level = level + 1, xp = 0 WHERE char_id = NEW.char_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `chatmessages`
--

CREATE TABLE `chatmessages` (
  `msg_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chatmessages`
--

INSERT INTO `chatmessages` (`msg_id`, `sender_id`, `receiver_id`, `message`, `sent_at`) VALUES
(1, 2, 1, '\'sup', '2025-04-30 16:49:23'),
(2, 2, 1, 'niga', '2025-04-30 16:49:55'),
(3, 1, 2, 'you nigger', '2025-04-30 16:50:47'),
(4, 2, 1, 'Nah you niggest', '2025-04-30 16:59:30');

-- --------------------------------------------------------

--
-- Table structure for table `enemies`
--

CREATE TABLE `enemies` (
  `enemy_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `health` int(11) DEFAULT NULL,
  `attack` int(11) DEFAULT NULL,
  `power_level` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friendrequests`
--

CREATE TABLE `friendrequests` (
  `request_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friendrequests`
--

INSERT INTO `friendrequests` (`request_id`, `sender_id`, `receiver_id`, `status`) VALUES
(1, 2, 1, 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inv_id` int(11) NOT NULL,
  `char_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inv_id`, `char_id`, `item_id`, `quantity`) VALUES
(1, 1, 1, 2),
(2, 1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `type` enum('Weapon','Armor','Consumable','Misc') DEFAULT NULL,
  `effect` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `name`, `type`, `effect`) VALUES
(1, 'Iron Sword', 'Weapon', 'Attack +5'),
(2, 'Healing Potion', 'Consumable', 'Restore 20 HP'),
(3, 'Leather Armor', 'Armor', 'Defense +3');

-- --------------------------------------------------------

--
-- Stand-in structure for view `leaderboard`
-- (See below for the actual view)
--
CREATE TABLE `leaderboard` (
`username` varchar(50)
,`level` int(11)
,`xp` int(11)
,`power_level` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `quests`
--

CREATE TABLE `quests` (
  `quest_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `rewards` text DEFAULT NULL,
  `suggested_rank` enum('SSS','SS','S','A','B','C','D','E','F') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quests`
--

INSERT INTO `quests` (`quest_id`, `name`, `area`, `rewards`, `suggested_rank`, `description`, `deadline`, `special_requests`, `created_at`) VALUES
(1, 'Bhagoooo', 'Badda', '100x Coins', 'F', 'Agun lagse bhagoooo', '2025-05-01', 'Doura halaaa', '2025-05-01 08:46:19');

-- --------------------------------------------------------

--
-- Stand-in structure for view `rankdistribution`
-- (See below for the actual view)
--
CREATE TABLE `rankdistribution` (
`char_id` int(11)
,`name` varchar(50)
,`rank` varchar(3)
);

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

CREATE TABLE `shop` (
  `shop_id` int(11) NOT NULL,
  `item_or_skill` enum('Item','Skill') DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `cost` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop`
--

INSERT INTO `shop` (`shop_id`, `item_or_skill`, `item_id`, `cost`) VALUES
(1, NULL, 1, 50),
(2, NULL, 2, 25),
(3, NULL, 3, 40);

-- --------------------------------------------------------

--
-- Table structure for table `spells`
--

CREATE TABLE `spells` (
  `spell_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `mana_cost` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spellslots`
--

CREATE TABLE `spellslots` (
  `slot_id` int(11) NOT NULL,
  `char_id` int(11) DEFAULT NULL,
  `spell_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('Player','GuildMaster') DEFAULT 'Player'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `role`) VALUES
(1, 'Iracus', 'iracus02@gmail.com', '313', 'Player'),
(2, 'Iracus2', 'iracus02@gmail.com', '1', 'Player'),
(3, 'gm', 'iracus02@gmail.com', '1', 'GuildMaster');

-- --------------------------------------------------------

--
-- Structure for view `batmanprotocol`
--
DROP TABLE IF EXISTS `batmanprotocol`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `batmanprotocol`  AS SELECT `users`.`user_id` AS `user_id`, `users`.`username` AS `username`, `users`.`email` AS `email`, `users`.`password_hash` AS `password_hash`, `users`.`role` AS `role` FROM `users` WHERE `users`.`role` = 'Admin' ;

-- --------------------------------------------------------

--
-- Structure for view `leaderboard`
--
DROP TABLE IF EXISTS `leaderboard`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `leaderboard`  AS SELECT `users`.`username` AS `username`, `characters`.`level` AS `level`, `characters`.`xp` AS `xp`, `characters`.`power_level` AS `power_level` FROM (`characters` join `users` on(`characters`.`user_id` = `users`.`user_id`)) ORDER BY `characters`.`power_level` DESC LIMIT 0, 10 ;

-- --------------------------------------------------------

--
-- Structure for view `rankdistribution`
--
DROP TABLE IF EXISTS `rankdistribution`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `rankdistribution`  AS SELECT `characters`.`char_id` AS `char_id`, `characters`.`name` AS `name`, CASE WHEN `characters`.`power_level` >= 95 THEN 'SSS' WHEN `characters`.`power_level` >= 85 THEN 'SS' WHEN `characters`.`power_level` >= 70 THEN 'S' WHEN `characters`.`power_level` >= 50 THEN 'A' WHEN `characters`.`power_level` >= 35 THEN 'B' WHEN `characters`.`power_level` >= 20 THEN 'C' WHEN `characters`.`power_level` >= 10 THEN 'D' ELSE 'F' END AS `rank` FROM `characters` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `battlelogs`
--
ALTER TABLE `battlelogs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `battles`
--
ALTER TABLE `battles`
  ADD PRIMARY KEY (`battle_id`),
  ADD KEY `char_id` (`char_id`),
  ADD KEY `enemy_id` (`enemy_id`);

--
-- Indexes for table `characters`
--
ALTER TABLE `characters`
  ADD PRIMARY KEY (`char_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chatmessages`
--
ALTER TABLE `chatmessages`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `enemies`
--
ALTER TABLE `enemies`
  ADD PRIMARY KEY (`enemy_id`);

--
-- Indexes for table `friendrequests`
--
ALTER TABLE `friendrequests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inv_id`),
  ADD KEY `char_id` (`char_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `quests`
--
ALTER TABLE `quests`
  ADD PRIMARY KEY (`quest_id`);

--
-- Indexes for table `shop`
--
ALTER TABLE `shop`
  ADD PRIMARY KEY (`shop_id`);

--
-- Indexes for table `spells`
--
ALTER TABLE `spells`
  ADD PRIMARY KEY (`spell_id`);

--
-- Indexes for table `spellslots`
--
ALTER TABLE `spellslots`
  ADD PRIMARY KEY (`slot_id`),
  ADD KEY `char_id` (`char_id`),
  ADD KEY `spell_id` (`spell_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `battlelogs`
--
ALTER TABLE `battlelogs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `battles`
--
ALTER TABLE `battles`
  MODIFY `battle_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
  MODIFY `char_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chatmessages`
--
ALTER TABLE `chatmessages`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enemies`
--
ALTER TABLE `enemies`
  MODIFY `enemy_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friendrequests`
--
ALTER TABLE `friendrequests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quests`
--
ALTER TABLE `quests`
  MODIFY `quest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shop`
--
ALTER TABLE `shop`
  MODIFY `shop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `spells`
--
ALTER TABLE `spells`
  MODIFY `spell_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spellslots`
--
ALTER TABLE `spellslots`
  MODIFY `slot_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `battles`
--
ALTER TABLE `battles`
  ADD CONSTRAINT `battles_ibfk_1` FOREIGN KEY (`char_id`) REFERENCES `characters` (`char_id`),
  ADD CONSTRAINT `battles_ibfk_2` FOREIGN KEY (`enemy_id`) REFERENCES `enemies` (`enemy_id`);

--
-- Constraints for table `characters`
--
ALTER TABLE `characters`
  ADD CONSTRAINT `characters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `chatmessages`
--
ALTER TABLE `chatmessages`
  ADD CONSTRAINT `chatmessages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `chatmessages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `friendrequests`
--
ALTER TABLE `friendrequests`
  ADD CONSTRAINT `friendrequests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `friendrequests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`char_id`) REFERENCES `characters` (`char_id`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `spellslots`
--
ALTER TABLE `spellslots`
  ADD CONSTRAINT `spellslots_ibfk_1` FOREIGN KEY (`char_id`) REFERENCES `characters` (`char_id`),
  ADD CONSTRAINT `spellslots_ibfk_2` FOREIGN KEY (`spell_id`) REFERENCES `spells` (`spell_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
