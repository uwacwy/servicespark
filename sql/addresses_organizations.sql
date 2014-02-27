CREATE TABLE `addresses_organizations` (
  `address_id` int(11) NOT NULL COMMENT 'fk: address',
  `organization_id` int(11) NOT NULL COMMENT 'fk: organization',
  PRIMARY KEY (`organization_id`,`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;