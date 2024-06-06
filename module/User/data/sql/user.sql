USE `pollster`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users`(
  `user_id` int(11) unsigned NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL DEFAULT 'avatar.png',
  `birthdate` date NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  KEY `enabled` (`enabled`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `role_id` int(11) unsigned NOT NULL auto_increment,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `user_roles` (`role_id`, `role`) VALUES
(1, 'admin'),
(2, 'member'),
(3, 'guest');

DROP TABLE IF EXISTS `user_forgot`;
CREATE TABLE IF NOT EXISTS `user_forgot` (
  `user_id` int(11) unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY(`user_id`),
  KEY `token` (`token`),
  KEY `created` (`created`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
