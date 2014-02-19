CREATE TABLE `events_skills` (
  `event_id` int(11) NOT NULL COMMENT 'fk ;event',
  `skill_id` int(11) NOT NULL COMMENT 'fk: skill',
  PRIMARY KEY (`event_id`,`skill_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;