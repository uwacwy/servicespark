CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `mailing_address` varchar(120) COLLATE utf8_bin,
  `mailing_city` varchar(100) COLLATE utf8_bin,
  `mailing_state` char(2) COLLATE utf8_bin,
  `mailing_zip` varchar(16) COLLATE utf8_bin,
  
  `physical_address` varchar(120) COLLATE utf8_bin,
  `physical_city` varchar(100) COLLATE utf8_bin,
  `physical_state` char(2) COLLATE utf8_bin,
  `physical_zip` varchar(16) COLLATE utf8_bin,
  
  PRIMARY KEY (`address_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;