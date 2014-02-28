CREATE TABLE IF NOT EXISTS `times` (
  `time_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `stop_time` datetime DEFAULT NULL,
  PRIMARY KEY (`time_id`),
  UNIQUE KEY `UNIQUE_event-user` (`event_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

