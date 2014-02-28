CREATE TABLE IF NOT EXISTS `skills_users` (
  `skill_id` int(11) NOT NULL COMMENT 'fk: skills',
  `user_id` int(11) NOT NULL COMMENT 'fk: users',
  PRIMARY KEY (`skill_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

