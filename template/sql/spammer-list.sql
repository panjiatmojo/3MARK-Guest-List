CREATE TABLE IF NOT EXISTS `%s` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`ip_address`),
  UNIQUE KEY `ip_address` (`ip_address`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;