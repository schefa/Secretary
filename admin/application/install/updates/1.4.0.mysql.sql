-- Secretary 1.4 (2015-11-08)


CREATE TABLE IF NOT EXISTS `#__secretary_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `business` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `extension` char(16) NOT NULL DEFAULT 'system',
  `catid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `street` varchar(255) DEFAULT NULL,
  `zip` varchar(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `lat` float(10,6) NOT NULL,
  `lng` float(10,6) NOT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `upload` varchar(30) DEFAULT NULL,
  `fields` text,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_business_ext_catid` (`business`,`extension`,`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__secretary_currencies` (
  `id` tinyint(3) AUTO_INCREMENT,
  `currency` char(3) NOT NULL DEFAULT 'EUR',
  `title` varchar(255) DEFAULT 'Euro',
  `symbol` varchar(10) DEFAULT '€',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `#__secretary_currencies` (`currency`, `title`, `symbol`) VALUES
('AUD', 'Australian dollar', 'A$'),
('BGN', 'Lev', 'лв.'),
('BRL', 'Real', 'R$'),
('CAD', 'Canadian dollar', 'C$'),
('CHF', 'Swiss franc', 'CHF'),
('CNY', 'Yuan renminbi', '¥'),
('CZK', 'Koruna', 'Kč'),
('DKK', 'Krone', 'kr'),
('EUR', 'Euro', '€'),
('GBP', 'Pound sterling', '£'),
('HKD', 'Hong Kong dollar', 'HK$'),
('HRK', 'Kuna', 'kn'),
('HUF', 'Forint', 'Ft'),
('IDR', 'Rupiah', 'Rp'),
('ILS', 'Shekel', '₪'),
('INR', 'Rupee', '₹'),
('JPY', 'Yen', '¥'),
('KRW', 'Won', '₩'),
('MXN', 'Peso', 'MX$'),
('MYR', 'Ringgit', 'RM'),
('NZD', 'New Zealand dollar', 'NZ$'),
('PHP', 'Peso', '₱'),
('PLN', 'Zloty', 'zł'),
('RON', 'Leu', 'lei'),
('RUB', 'Rouble', 'руб'),
('SEK', 'Krona', 'kr'),
('SGD', 'Singapore dollar', 'SG$'),
('THB', 'Baht', '฿'),
('TRY', 'Lira', '₺'),
('USD', 'US dollar', '$'),
('ZAR', 'Rand', 'R');

ALTER TABLE `#__secretary_products` ADD `location` INT(11) NOT NULL DEFAULT '0' AFTER `business`;
ALTER TABLE `#__secretary_products` ADD `created_by` INT(11) NOT NULL DEFAULT '0' AFTER `fields`;
ALTER TABLE `#__secretary_times` CHANGE `address` `location_id` INT(11) NOT NULL;

ALTER TABLE `#__secretary_businesses` CHANGE `taxvalue` `taxvalue` DECIMAL(4,2) NOT NULL;
ALTER TABLE `#__secretary_documents` ADD `office` INT NOT NULL DEFAULT '0' AFTER `business`;
ALTER TABLE `#__secretary_folders` CHANGE `created_user_id` `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0';

--
-- Update Currencies
--

ALTER TABLE `#__secretary_documents` ADD `currency` VARCHAR(10) DEFAULT NULL AFTER `items`;
UPDATE #__secretary_businesses as b set currency = ( SELECT currency FROM #__secretary_currencies as c WHERE c.symbol = b.currency );
UPDATE #__secretary_documents as d set d.currency = ( SELECT currency FROM #__secretary_businesses as b where b.id = d.business );