CREATE TABLE `addresses_users` (
  `address_id` int(11) NOT NULL COMMENT 'fk: address',
  `user_id` int(11) NOT NULL COMMENT 'fk: user',
  PRIMARY KEY (`user_id`,`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;