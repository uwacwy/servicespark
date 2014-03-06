CREATE TABLE `addresses_events` (
  `address_id` int(11) NOT NULL COMMENT 'fk: address',
  `event_id` int(11) NOT NULL COMMENT 'fk: event',
  PRIMARY KEY (`event_id`,`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `addresses_organizations` (
  `address_id` int(11) NOT NULL COMMENT 'fk: address',
  `organization_id` int(11) NOT NULL COMMENT 'fk: organization',
  PRIMARY KEY (`organization_id`,`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `address1` varchar(120) COLLATE utf8_bin DEFAULT NULL,
  `address2` varchar(120) COLLATE utf8_bin DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `state` char(2) COLLATE utf8_bin DEFAULT NULL,
  `zip` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `type` enum('mailing','physical','both') COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `addresses_users` (
  `address_id` int(11) NOT NULL COMMENT 'fk: address',
  `user_id` int(11) NOT NULL COMMENT 'fk: user',
  PRIMARY KEY (`user_id`,`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `events_skills` (
  `event_id` int(11) NOT NULL COMMENT 'fk ;event',
  `skill_id` int(11) NOT NULL COMMENT 'fk: skill',
  PRIMARY KEY (`event_id`,`skill_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_bin NOT NULL,
  `description` varchar(512) COLLATE utf8_bin NOT NULL,
  `start_time` datetime NOT NULL,
  `stop_time` datetime NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE IF NOT EXISTS `organizations` (
  `organization_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'pk',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`organization_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `publish` tinyint(1) NOT NULL,
  `read` tinyint(1) NOT NULL,
  `write` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `USER_ORG_UNIQUE` (`organization_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `recoveries` (
  `user_id` int(11) NOT NULL,
  `expiration` datetime NOT NULL,
  `token` varchar(40) NOT NULL,

  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `skills` (
  `skill_id` int(11) NOT NULL AUTO_INCREMENT,
  `skill` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`skill_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `skills_users` (
  `skill_id` int(11) NOT NULL COMMENT 'fk: skills',
  `user_id` int(11) NOT NULL COMMENT 'fk: users',
  PRIMARY KEY (`skill_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `times` (
  `time_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `stop_time` datetime DEFAULT NULL,
  PRIMARY KEY (`time_id`),
  UNIQUE KEY `UNIQUE_event-user` (`event_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `super_admin` tinyint(1) NOT NULL,
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  `password` varchar(40) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `first_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `last_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

