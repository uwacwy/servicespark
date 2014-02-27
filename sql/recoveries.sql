CREATE TABLE IF NOT EXISTS `recoveries` (
  `user_id` int(11) NOT NULL,
  `expiration` datetime NOT NULL,
  `token` varchar(40) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;