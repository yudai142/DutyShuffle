-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost:8889
-- 生成日時: 2022 年 11 月 12 日 23:20
-- サーバのバージョン： 5.7.26
-- PHP のバージョン: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `duty_shuffle`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `work_id` int(11) DEFAULT NULL,
  `menber_id` int(11) NOT NULL,
  `day` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `menber`
--

CREATE TABLE `menber` (
  `id` int(11) NOT NULL,
  `last_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kana_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `archive` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `menber_option`
--

CREATE TABLE `menber_option` (
  `id` int(11) NOT NULL,
  `work_id` int(11) NOT NULL,
  `menber_id` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `shuffle_option`
--

CREATE TABLE `shuffle_option` (
  `id` int(11) NOT NULL,
  `interval` int(11) DEFAULT '7'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `work`
--

CREATE TABLE `work` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `multiple` int(10) UNSIGNED DEFAULT NULL,
  `archive` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `_prisma_migrations`
--

CREATE TABLE `_prisma_migrations` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `checksum` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `finished_at` datetime(3) DEFAULT NULL,
  `migration_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logs` text COLLATE utf8mb4_unicode_ci,
  `rolled_back_at` datetime(3) DEFAULT NULL,
  `started_at` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `applied_steps_count` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `_prisma_migrations`
--

INSERT INTO `_prisma_migrations` (`id`, `checksum`, `finished_at`, `migration_name`, `logs`, `rolled_back_at`, `started_at`, `applied_steps_count`) VALUES
('1a55dc16-51e4-429e-aea5-63d82dd42bfe', '8c2a30f0e43c75a60963e647359c35b7f4c861b0594cf21363e8a85d6572f8dd', '2022-11-09 10:22:15.413', '20221108021909_init', NULL, NULL, '2022-11-09 10:22:15.103', 1),
('c66f77bc-29c1-49a0-9120-14e30ab42d96', '28244d4519d3b8827345e74995b17f19036adfeb80c15d3e9cfa06743a8c9eb5', '2022-11-09 10:22:15.470', '20221109015544_add_status_to_work', NULL, NULL, '2022-11-09 10:22:15.414', 1);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `history_work_id_fkey` (`work_id`),
  ADD KEY `history_menber_id_fkey` (`menber_id`);

--
-- テーブルのインデックス `menber`
--
ALTER TABLE `menber`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `menber_option`
--
ALTER TABLE `menber_option`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menber_option_work_id_fkey` (`work_id`),
  ADD KEY `menber_option_menber_id_fkey` (`menber_id`);

--
-- テーブルのインデックス `shuffle_option`
--
ALTER TABLE `shuffle_option`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `work`
--
ALTER TABLE `work`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `_prisma_migrations`
--
ALTER TABLE `_prisma_migrations`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルのAUTO_INCREMENT
--

--
-- テーブルのAUTO_INCREMENT `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `menber`
--
ALTER TABLE `menber`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `menber_option`
--
ALTER TABLE `menber_option`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `shuffle_option`
--
ALTER TABLE `shuffle_option`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `work`
--
ALTER TABLE `work`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_menber_id_fkey` FOREIGN KEY (`menber_id`) REFERENCES `menber` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `history_work_id_fkey` FOREIGN KEY (`work_id`) REFERENCES `work` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- テーブルの制約 `menber_option`
--
ALTER TABLE `menber_option`
  ADD CONSTRAINT `menber_option_menber_id_fkey` FOREIGN KEY (`menber_id`) REFERENCES `menber` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `menber_option_work_id_fkey` FOREIGN KEY (`work_id`) REFERENCES `work` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
