CREATE TABLE IF NOT EXISTS `organizations` (
  `organization_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'pk',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`organization_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;