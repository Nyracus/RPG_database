-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 05:30 PM
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
-- Table structure for table `characters`
--

CREATE TABLE `characters` (
  `char_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `level` int(11) DEFAULT 1,
  `xp` int(11) DEFAULT 0,
  `power` int(11) DEFAULT 0,
  `coins` int(11) DEFAULT 100,
  `rank` enum('F','E','D','C','B','A','S','SS','SSS') DEFAULT 'F'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `characters`
--

INSERT INTO `characters` (`char_id`, `user_id`, `name`, `level`, `xp`, `power`, `coins`, `rank`) VALUES
(1, 2, 'Iracus2', 4, 157, 33, 146, 'F'),
(2, 3, 'gm', 1, 0, 0, 100, 'F'),
(3, 6, 'Iracus4', 1, 0, 10, 100, 'F'),
(5, 8, 't', 1, 0, 10, 36, 'F');

-- --------------------------------------------------------

--
-- Table structure for table `characterskills`
--

CREATE TABLE `characterskills` (
  `id` int(11) NOT NULL,
  `char_id` int(11) DEFAULT NULL,
  `skill_id` int(11) DEFAULT NULL,
  `equipped` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `characterskills`
--

INSERT INTO `characterskills` (`id`, `char_id`, `skill_id`, `equipped`) VALUES
(1, 1, 8, 1),
(2, 1, 7, 0),
(3, 1, 15, 0),
(5, 5, 15, 0);

-- --------------------------------------------------------

--
-- Table structure for table `chatmessages`
--

CREATE TABLE `chatmessages` (
  `msg_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chatmessages`
--

INSERT INTO `chatmessages` (`msg_id`, `sender_id`, `receiver_id`, `message`, `sent_at`) VALUES
(2, 2, 8, 'Hello', '2025-05-10 17:27:37'),
(3, 2, 8, 'Hi', '2025-05-10 17:27:39'),
(4, 2, 8, 'Hello', '2025-05-10 21:20:28');

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
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equip_id` int(11) NOT NULL,
  `char_id` int(11) DEFAULT NULL,
  `slot` enum('MainHand','OffHand','Head','Torso','Legs','Arms','Hands','Feet','Finger1','Finger2') DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equip_id`, `char_id`, `slot`, `item_id`) VALUES
(3, 1, 'MainHand', 5),
(4, 1, 'Arms', 27),
(6, 5, 'MainHand', 6),
(7, 1, 'Feet', 33);

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
(3, 8, 2, 'Accepted'),
(5, 2, 6, 'Pending');

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
(2, 1, 2, 2),
(3, 1, 27, 1),
(4, 1, 5, 1),
(7, 5, 6, 1),
(8, 1, 33, 1);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `type` enum('Weapon','Armor','Consumable','Misc') DEFAULT NULL,
  `effect` text DEFAULT NULL,
  `slot` enum('MainHand','OffHand','Head','Torso','Legs','Arms','Hands','Feet','Finger1','Finger2') DEFAULT 'MainHand',
  `power_value` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `name`, `type`, `effect`, `slot`, `power_value`) VALUES
(1, 'Iron Sword', 'Weapon', 'Attack +5', 'MainHand', 0),
(2, 'Healing Potion', 'Consumable', 'Restore 20 HP', 'MainHand', 0),
(3, 'Leather Armor', 'Armor', 'Defense +3', 'MainHand', 0),
(4, 'Iron Sword', NULL, NULL, 'MainHand', 20),
(5, 'Steel Sword', NULL, NULL, 'MainHand', 30),
(6, 'Flame Dagger', NULL, NULL, 'MainHand', 25),
(7, 'Warhammer', NULL, NULL, 'MainHand', 40),
(8, 'Wooden Shield', NULL, NULL, 'OffHand', 10),
(9, 'Iron Shield', NULL, NULL, 'OffHand', 20),
(10, 'Magic Orb', NULL, NULL, 'OffHand', 15),
(11, 'Buckler Shield', NULL, NULL, 'OffHand', 18),
(12, 'Leather Cap', NULL, NULL, 'Head', 8),
(13, 'Iron Helm', NULL, NULL, 'Head', 15),
(14, 'Wizard Hat', NULL, NULL, 'Head', 12),
(15, 'Crown of Insight', NULL, NULL, 'Head', 25),
(16, 'Leather Armor', NULL, NULL, 'Torso', 18),
(17, 'Chainmail Armor', NULL, NULL, 'Torso', 28),
(18, 'Robes of Wisdom', NULL, NULL, 'Torso', 22),
(19, 'Dragon Scale Armor', NULL, NULL, 'Torso', 45),
(20, 'Padded Pants', NULL, NULL, 'Legs', 10),
(21, 'Iron Greaves', NULL, NULL, 'Legs', 18),
(22, 'Shadowwalk Leggings', NULL, NULL, 'Legs', 20),
(23, 'Knight Legguards', NULL, NULL, 'Legs', 25),
(24, 'Bronze Bracers', NULL, NULL, 'Arms', 7),
(25, 'Steel Bracers', NULL, NULL, 'Arms', 15),
(26, 'Vambraces of Valor', NULL, NULL, 'Arms', 18),
(27, 'Arcane Bindings', NULL, NULL, 'Arms', 22),
(28, 'Cloth Gloves', NULL, NULL, 'Hands', 5),
(29, 'Spiked Gloves', NULL, NULL, 'Hands', 14),
(30, 'Thief Grips', NULL, NULL, 'Hands', 10),
(31, 'Infernal Gauntlets', NULL, NULL, 'Hands', 20),
(32, 'Worn Boots', NULL, NULL, 'Feet', 5),
(33, 'Steel Toe Boots', NULL, NULL, 'Feet', 12),
(34, 'Shoes of Swiftness', NULL, NULL, 'Feet', 15),
(35, 'Titan Greaves', NULL, NULL, 'Feet', 25),
(36, 'Silver Ring', NULL, NULL, 'Finger1', 10),
(37, 'Ring of Strength', NULL, NULL, 'Finger1', 20),
(38, 'Golden Band', NULL, NULL, 'Finger2', 12),
(39, 'Ring of Fire', NULL, NULL, 'Finger2', 18);

-- --------------------------------------------------------

--
-- Stand-in structure for view `leaderboard`
-- (See below for the actual view)
--
CREATE TABLE `leaderboard` (
);

-- --------------------------------------------------------

--
-- Table structure for table `levelxp`
--

CREATE TABLE `levelxp` (
  `level` int(11) NOT NULL,
  `xp_required` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questacceptance`
--

CREATE TABLE `questacceptance` (
  `accept_id` int(11) NOT NULL,
  `quest_id` int(11) DEFAULT NULL,
  `char_id` int(11) DEFAULT NULL,
  `status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completion_status` enum('Pending','Completed','Rejected') DEFAULT NULL,
  `accepted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questacceptance`
--

INSERT INTO `questacceptance` (`accept_id`, `quest_id`, `char_id`, `status`, `requested_at`, `completion_status`, `accepted_at`) VALUES
(1, 1, 1, 'Accepted', '2025-05-01 16:29:47', 'Completed', '2025-05-02 19:06:48'),
(2, 2, 1, 'Accepted', '2025-05-01 17:33:09', 'Completed', '2025-05-02 19:06:48'),
(3, 5, 1, 'Accepted', '2025-05-01 20:01:34', 'Completed', '2025-05-02 19:06:48'),
(4, 6, 1, 'Accepted', '2025-05-03 08:32:51', 'Completed', '2025-05-03 14:32:51'),
(5, 4, 1, 'Accepted', '2025-05-03 08:50:52', 'Rejected', '2025-05-03 14:50:52'),
(6, 7, 1, 'Accepted', '2025-05-03 08:52:26', 'Completed', '2025-05-03 14:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `questrewards`
--

CREATE TABLE `questrewards` (
  `reward_id` int(11) NOT NULL,
  `quest_id` int(11) DEFAULT NULL,
  `reward_type` enum('coin','xp','item') DEFAULT NULL,
  `reward_value` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questrewards`
--

INSERT INTO `questrewards` (`reward_id`, `quest_id`, `reward_type`, `reward_value`, `item_id`) VALUES
(1, 3, 'coin', 1000, NULL),
(2, 4, 'item', 1, 2),
(3, 5, 'xp', 500, NULL),
(4, 7, 'coin', 100, NULL),
(5, 7, 'xp', 100, NULL),
(6, 8, 'coin', 100, NULL),
(7, 8, 'xp', 100, NULL);

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
(1, 'Bhagoooo', 'Badda', '100x Coins', 'F', 'Agun lagse bhagoooo', '2025-05-01', 'Doura halaaa', '2025-05-01 08:46:19'),
(2, 'Slay elden beast', 'world', NULL, 'SSS', 'maar halare', '2025-05-01', '', '2025-05-01 17:31:39'),
(3, 'Kando', 'dou', NULL, 'S', 'Its 2 am cry cry time', '2025-05-08', '', '2025-05-01 18:03:13'),
(4, 'Heal', 'basha', NULL, 'F', 'Gese ga bottle khao', '2025-05-03', '', '2025-05-01 18:03:53'),
(5, 'shokti barao', 'mon', NULL, 'D', 'powerrrr', '2025-05-06', '', '2025-05-01 20:01:25'),
(6, 'Emergency Bounty: Iracus', 'Unknown', NULL, 'S', 'A rogue adventurer named Iracus has vanished. Hunt down their legacy and restore balance.', '2025-05-09', NULL, '2025-05-02 13:24:14'),
(7, 'Sample', 'unknown', NULL, 'SSS', 'koro', '2025-05-03', '', '2025-05-03 08:52:16'),
(8, 'Sample', 'unknown', NULL, 'SSS', 'koro', '2025-05-03', '', '2025-05-03 08:52:29'),
(9, 'Emergency Bounty: aqqib', 'Unknown', NULL, 'S', 'A rogue adventurer named aqqib has vanished. Hunt down their legacy and restore balance.', '2025-05-11', NULL, '2025-05-04 12:37:59'),
(10, 'Emergency Bounty: jodu', 'Unknown', NULL, 'S', 'A rogue adventurer named jodu has vanished. Hunt down their legacy and restore balance.', '2025-05-16', NULL, '2025-05-08 18:16:53'),
(11, 'Emergency Bounty: iracus3', 'Unknown', NULL, 'S', 'A rogue adventurer named iracus3 has vanished. Hunt down their legacy and restore balance.', '2025-05-16', NULL, '2025-05-09 06:15:57');

-- --------------------------------------------------------

--
-- Stand-in structure for view `rankdistribution`
-- (See below for the actual view)
--
CREATE TABLE `rankdistribution` (
);

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

CREATE TABLE `shop` (
  `shop_id` int(11) NOT NULL,
  `item_or_skill` enum('Item','Skill') DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `cost` int(11) DEFAULT NULL,
  `type` enum('item','skill') DEFAULT 'item',
  `skill_id` int(11) DEFAULT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `slot` enum('MainHand','OffHand','Head','Torso','Legs','Arms','Hands','Feet','Finger1','Finger2','None') DEFAULT 'None',
  `power_value` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop`
--

INSERT INTO `shop` (`shop_id`, `item_or_skill`, `item_id`, `cost`, `type`, `skill_id`, `display_name`, `slot`, `power_value`) VALUES
(74, NULL, 1, 45, 'item', NULL, 'Iron Sword', 'MainHand', 0),
(75, NULL, 2, 40, 'item', NULL, 'Healing Potion', 'MainHand', 0),
(76, NULL, 3, 41, 'item', NULL, 'Leather Armor', 'MainHand', 0),
(77, NULL, 4, 61, 'item', NULL, 'Iron Sword', 'MainHand', 20),
(78, NULL, 5, 58, 'item', NULL, 'Steel Sword', 'MainHand', 30),
(79, NULL, 6, 29, 'item', NULL, 'Flame Dagger', 'MainHand', 25),
(80, NULL, 7, 50, 'item', NULL, 'Warhammer', 'MainHand', 40),
(81, NULL, 8, 38, 'item', NULL, 'Wooden Shield', 'OffHand', 10),
(82, NULL, 9, 65, 'item', NULL, 'Iron Shield', 'OffHand', 20),
(83, NULL, 10, 37, 'item', NULL, 'Magic Orb', 'OffHand', 15),
(84, NULL, 11, 63, 'item', NULL, 'Buckler Shield', 'OffHand', 18),
(85, NULL, 12, 31, 'item', NULL, 'Leather Cap', 'Head', 8),
(86, NULL, 13, 42, 'item', NULL, 'Iron Helm', 'Head', 15),
(87, NULL, 14, 43, 'item', NULL, 'Wizard Hat', 'Head', 12),
(88, NULL, 15, 62, 'item', NULL, 'Crown of Insight', 'Head', 25),
(89, NULL, 16, 59, 'item', NULL, 'Leather Armor', 'Torso', 18),
(90, NULL, 17, 34, 'item', NULL, 'Chainmail Armor', 'Torso', 28),
(91, NULL, 18, 67, 'item', NULL, 'Robes of Wisdom', 'Torso', 22),
(92, NULL, 19, 59, 'item', NULL, 'Dragon Scale Armor', 'Torso', 45),
(93, NULL, 20, 68, 'item', NULL, 'Padded Pants', 'Legs', 10),
(94, NULL, 21, 41, 'item', NULL, 'Iron Greaves', 'Legs', 18),
(95, NULL, 22, 26, 'item', NULL, 'Shadowwalk Leggings', 'Legs', 20),
(96, NULL, 23, 32, 'item', NULL, 'Knight Legguards', 'Legs', 25),
(97, NULL, 24, 56, 'item', NULL, 'Bronze Bracers', 'Arms', 7),
(98, NULL, 25, 61, 'item', NULL, 'Steel Bracers', 'Arms', 15),
(99, NULL, 26, 62, 'item', NULL, 'Vambraces of Valor', 'Arms', 18),
(100, NULL, 27, 52, 'item', NULL, 'Arcane Bindings', 'Arms', 22),
(101, NULL, 28, 49, 'item', NULL, 'Cloth Gloves', 'Hands', 5),
(102, NULL, 29, 64, 'item', NULL, 'Spiked Gloves', 'Hands', 14),
(103, NULL, 30, 51, 'item', NULL, 'Thief Grips', 'Hands', 10),
(104, NULL, 31, 37, 'item', NULL, 'Infernal Gauntlets', 'Hands', 20),
(105, NULL, 32, 58, 'item', NULL, 'Worn Boots', 'Feet', 5),
(106, NULL, 33, 54, 'item', NULL, 'Steel Toe Boots', 'Feet', 12),
(107, NULL, 34, 72, 'item', NULL, 'Shoes of Swiftness', 'Feet', 15),
(108, NULL, 35, 72, 'item', NULL, 'Titan Greaves', 'Feet', 25),
(109, NULL, 36, 70, 'item', NULL, 'Silver Ring', 'Finger1', 10),
(110, NULL, 37, 60, 'item', NULL, 'Ring of Strength', 'Finger1', 20),
(111, NULL, 38, 67, 'item', NULL, 'Golden Band', 'Finger2', 12),
(112, NULL, 39, 27, 'item', NULL, 'Ring of Fire', 'Finger2', 18),
(137, NULL, NULL, 68, 'skill', 1, 'Firebolt', 'None', 10),
(138, NULL, NULL, 62, 'skill', 2, 'Shield Wall', 'None', 15),
(139, NULL, NULL, 77, 'skill', 3, 'Ice Lance', 'None', 12),
(140, NULL, NULL, 72, 'skill', 4, 'War Cry', 'None', 20),
(141, NULL, NULL, 47, 'skill', 5, 'Quickstep', 'None', 8),
(142, NULL, NULL, 42, 'skill', 6, 'Firebolt', 'None', 10),
(143, NULL, NULL, 36, 'skill', 7, 'Shield Wall', 'None', 15),
(144, NULL, NULL, 77, 'skill', 8, 'Ice Lance', 'None', 12),
(145, NULL, NULL, 45, 'skill', 9, 'War Cry', 'None', 20),
(146, NULL, NULL, 67, 'skill', 10, 'Quickstep', 'None', 8),
(147, NULL, NULL, 69, 'skill', 11, 'Heal Pulse', 'None', 10),
(148, NULL, NULL, 64, 'skill', 12, 'Thundercrack', 'None', 18),
(149, NULL, NULL, 33, 'skill', 13, 'Barrier', 'None', 14),
(150, NULL, NULL, 42, 'skill', 14, 'Focus Shot', 'None', 9),
(151, NULL, NULL, 35, 'skill', 15, 'Backstab', 'None', 16);

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `skill_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `power_value` int(11) DEFAULT 0,
  `required_level` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`skill_id`, `name`, `description`, `power_value`, `required_level`) VALUES
(1, 'Firebolt', 'Deals basic fire damage', 10, 2),
(2, 'Shield Wall', 'Raises defense significantly', 15, 4),
(3, 'Ice Lance', 'Freezes target and slows them', 12, 3),
(4, 'War Cry', 'Temporarily boosts morale', 20, 6),
(5, 'Quickstep', 'Increases speed and agility', 8, 1),
(6, 'Firebolt', 'Deals fire damage', 10, 2),
(7, 'Shield Wall', 'Raises defense', 15, 4),
(8, 'Ice Lance', 'Freezes the enemy', 12, 3),
(9, 'War Cry', 'Boosts attack', 20, 6),
(10, 'Quickstep', 'Increases speed', 8, 1),
(11, 'Heal Pulse', 'Restores HP', 10, 3),
(12, 'Thundercrack', 'Lightning damage', 18, 5),
(13, 'Barrier', 'Magic shield', 14, 2),
(14, 'Focus Shot', 'Boosts next attack', 9, 2),
(15, 'Backstab', 'Extra damage from behind', 16, 4);

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
(2, 'Iracus2', 'iracus02@gmail.com', '1', 'Player'),
(3, 'gm', 'iracus02@gmail.com', '1', 'GuildMaster'),
(6, 'Iracus4', 'iracus02@gmail.com', '1', 'Player'),
(8, 't', 't@gmail.com', '1', 'Player');

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
-- Indexes for table `characters`
--
ALTER TABLE `characters`
  ADD PRIMARY KEY (`char_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `characterskills`
--
ALTER TABLE `characterskills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `char_id` (`char_id`),
  ADD KEY `skill_id` (`skill_id`);

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
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equip_id`),
  ADD KEY `char_id` (`char_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `friendrequests`
--
ALTER TABLE `friendrequests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `friendrequests_ibfk_1` (`sender_id`),
  ADD KEY `friendrequests_ibfk_2` (`receiver_id`);

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
-- Indexes for table `levelxp`
--
ALTER TABLE `levelxp`
  ADD PRIMARY KEY (`level`);

--
-- Indexes for table `questacceptance`
--
ALTER TABLE `questacceptance`
  ADD PRIMARY KEY (`accept_id`),
  ADD KEY `quest_id` (`quest_id`),
  ADD KEY `char_id` (`char_id`);

--
-- Indexes for table `questrewards`
--
ALTER TABLE `questrewards`
  ADD PRIMARY KEY (`reward_id`),
  ADD KEY `quest_id` (`quest_id`),
  ADD KEY `item_id` (`item_id`);

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
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`skill_id`);

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
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
  MODIFY `char_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `characterskills`
--
ALTER TABLE `characterskills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `friendrequests`
--
ALTER TABLE `friendrequests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `questacceptance`
--
ALTER TABLE `questacceptance`
  MODIFY `accept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `questrewards`
--
ALTER TABLE `questrewards`
  MODIFY `reward_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `quests`
--
ALTER TABLE `quests`
  MODIFY `quest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `shop`
--
ALTER TABLE `shop`
  MODIFY `shop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `characters`
--
ALTER TABLE `characters`
  ADD CONSTRAINT `characters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `characterskills`
--
ALTER TABLE `characterskills`
  ADD CONSTRAINT `characterskills_ibfk_1` FOREIGN KEY (`char_id`) REFERENCES `characters` (`char_id`),
  ADD CONSTRAINT `characterskills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`skill_id`);

--
-- Constraints for table `chatmessages`
--
ALTER TABLE `chatmessages`
  ADD CONSTRAINT `chatmessages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chatmessages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`char_id`) REFERENCES `characters` (`char_id`),
  ADD CONSTRAINT `equipment_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `friendrequests`
--
ALTER TABLE `friendrequests`
  ADD CONSTRAINT `friendrequests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friendrequests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`char_id`) REFERENCES `characters` (`char_id`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `questacceptance`
--
ALTER TABLE `questacceptance`
  ADD CONSTRAINT `questacceptance_ibfk_1` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`quest_id`),
  ADD CONSTRAINT `questacceptance_ibfk_2` FOREIGN KEY (`char_id`) REFERENCES `characters` (`char_id`);

--
-- Constraints for table `questrewards`
--
ALTER TABLE `questrewards`
  ADD CONSTRAINT `questrewards_ibfk_1` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`quest_id`),
  ADD CONSTRAINT `questrewards_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);

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
