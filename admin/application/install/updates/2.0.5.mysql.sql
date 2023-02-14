-- Secretary 2.0.5 (2016-05-05)

CREATE TABLE IF NOT EXISTS `#__secretary_connections` (
  `extension` varchar(16) NOT NULL DEFAULT 'system',
  `one` int(11) NOT NULL,
  `two` int(11) NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`extension`,`one`,`two`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;