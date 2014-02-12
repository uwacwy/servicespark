CREATE TABLE IF NOT EXISTS `skills_users` (
  `skill_id` int(11) NOT NULL COMMENT 'fk ;skill',
  `user_id` int(11) NOT NULL COMMENT 'fk: user',
  PRIMARY KEY (`skill_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;