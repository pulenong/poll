USE `pollster`;

DROP TABLE IF EXISTS `polls`;
CREATE TABLE IF NOT EXISTS `polls` (
  `poll_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `question` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `category_id` int(11) unsigned NOT NULL,
  `total_votes` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `timeout` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`poll_id`),
  KEY `status` (`status`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `created` (`created`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `poll_options`;
CREATE TABLE IF NOT EXISTS `poll_options` (
  `option_id` int(11) unsigned NOT NULL auto_increment,
  `poll_id` int(11) unsigned NOT NULL,
  `option` text NOT NULL,
  `vote_tally` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`option_id`),
  KEY `poll_id` (`poll_id`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `poll_votes`;
CREATE TABLE IF NOT EXISTS `poll_votes` (
  `poll_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`poll_id`, `user_id`),
  KEY `option_id` (`option_id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (`option_id`) REFERENCES `poll_options` (`option_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `poll_categories`;
CREATE TABLE IF NOT EXISTS `poll_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `category` varchar(255) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `category_id` (`category_id`, `category`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `poll_categories` (`category_id`, `category`) VALUES
(1, 'Business'),
(2, 'Health'),
(3, 'Politics'),
(4, 'Science'),
(5, 'Sports'),
(6, 'Travel');
