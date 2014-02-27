CREATE TABLE `addresses_events` (
  `address_id` int(11) NOT NULL COMMENT 'fk: address',
  `event_id` int(11) NOT NULL COMMENT 'fk: event',
  PRIMARY KEY (`event_id`,`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;