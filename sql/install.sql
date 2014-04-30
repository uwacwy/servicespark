--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `address1` varchar(120) COLLATE utf8_bin DEFAULT NULL,
  `address2` varchar(120) COLLATE utf8_bin DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `state` char(2) COLLATE utf8_bin DEFAULT NULL,
  `zip` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `type` enum('mailing','physical','both') COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `addresses_events`
--

DROP TABLE IF EXISTS `addresses_events`;
CREATE TABLE `addresses_events` (
  `address_id` int(11) NOT NULL COMMENT 'fk: address',
  `event_id` int(11) NOT NULL COMMENT 'fk: event',
  PRIMARY KEY (`event_id`,`address_id`),
  KEY `FK_addresses_events_TO_addresses` (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `addresses_organizations`
--

DROP TABLE IF EXISTS `addresses_organizations`;
CREATE TABLE `addresses_organizations` (
  `address_id` int(11) NOT NULL COMMENT 'fk: address',
  `organization_id` int(11) NOT NULL COMMENT 'fk: organization',
  PRIMARY KEY (`organization_id`,`address_id`),
  KEY `FK_addresses_organizations_TO_addresses` (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `addresses_users`
--

DROP TABLE IF EXISTS `addresses_users`;
CREATE TABLE `addresses_users` (
  `address_id` int(11) NOT NULL COMMENT 'fk: address',
  `user_id` int(11) NOT NULL COMMENT 'fk: user',
  PRIMARY KEY (`user_id`,`address_id`),
  KEY `FK_addresses_users_TO_addresses` (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL COMMENT 'fk: organization',
  `title` varchar(100) COLLATE utf8_bin NOT NULL,
  `description` varchar(512) COLLATE utf8_bin NOT NULL,
  `start_time` datetime NOT NULL,
  `stop_time` datetime NOT NULL,
  `start_token` varchar(9) COLLATE utf8_bin NOT NULL,
  `stop_token` varchar(9) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`event_id`),
  KEY `FK_events_TO_organizations` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `events_skills`
--

DROP TABLE IF EXISTS `events_skills`;
CREATE TABLE `events_skills` (
  `event_id` int(11) NOT NULL COMMENT 'fk ;event',
  `skill_id` int(11) NOT NULL COMMENT 'fk: skill',
  PRIMARY KEY (`event_id`,`skill_id`),
  KEY `FK_events_skills_TO_skills` (`skill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

DROP TABLE IF EXISTS `organizations`;
CREATE TABLE `organizations` (
  `organization_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'pk',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`organization_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `publish` tinyint(1) NOT NULL,
  `read` tinyint(1) NOT NULL,
  `write` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `USER_ORG_UNIQUE` (`organization_id`,`user_id`),
  KEY `FK_permissions_TO_users` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `recoveries`
--

DROP TABLE IF EXISTS `recoveries`;
CREATE TABLE `recoveries` (
  `user_id` int(11) NOT NULL,
  `expiration` datetime NOT NULL,
  `token` varchar(40) COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

DROP TABLE IF EXISTS `skills`;
CREATE TABLE `skills` (
  `skill_id` int(11) NOT NULL AUTO_INCREMENT,
  `skill` varchar(100) CHARACTER SET utf8 NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`skill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `skills_users`
--

DROP TABLE IF EXISTS `skills_users`;
CREATE TABLE `skills_users` (
  `skill_id` int(11) NOT NULL COMMENT 'fk: skills',
  `user_id` int(11) NOT NULL COMMENT 'fk: users',
  PRIMARY KEY (`skill_id`,`user_id`),
  KEY `FK_skills_users_TO_users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `times`
--

DROP TABLE IF EXISTS `times`;
CREATE TABLE `times` (
  `time_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `stop_time` datetime DEFAULT NULL,
  PRIMARY KEY (`time_id`),
  UNIQUE KEY `UNIQUE_event-user` (`event_id`,`user_id`),
  KEY `FK_times_TO_users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `super_admin` tinyint(1) NOT NULL,
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  `password` varchar(40) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `first_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `last_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `missed_punches` int(11) NOT NULL DEFAULT '0' COMMENT 'this field will be maintained by CakePHP''s counter cache',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses_events`
--
ALTER TABLE `addresses_events`
  ADD CONSTRAINT `FK_addresses_events_TO_addresses` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_addresses_events_TO_events` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `addresses_organizations`
--
ALTER TABLE `addresses_organizations`
  ADD CONSTRAINT `FK_addresses_organizations_TO_addresses` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_addresses_organizations_TO_organizations` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `addresses_users`
--
ALTER TABLE `addresses_users`
  ADD CONSTRAINT `FK_addresses_users_TO_addresses` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_addresses_users_TO_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `FK_events_TO_organizations` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `events_skills`
--
ALTER TABLE `events_skills`
  ADD CONSTRAINT `FK_events_skills_TO_events` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_events_skills_TO_skills` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`skill_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `FK_permissions_TO_organizations` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_permissions_TO_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recoveries`
--
ALTER TABLE `recoveries`
  ADD CONSTRAINT `FK_recoveries_TO_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `skills_users`
--
ALTER TABLE `skills_users`
  ADD CONSTRAINT `FK_skills_users_TO_skills` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`skill_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_skills_users_TO_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `times`
--
ALTER TABLE `times`
  ADD CONSTRAINT `FK_times_TO_events` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_times_TO_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
