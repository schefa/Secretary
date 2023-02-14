-- Secretary 2.0.0 (2016-02-25)

ALTER TABLE `#__secretary_documents` CHANGE `rabatt` `rabatt` DECIMAL(10,4) NOT NULL;
ALTER TABLE `#__secretary_documents` CHANGE `paid` `paid` DECIMAL(15,4) NOT NULL;
ALTER TABLE `#__secretary_documents` CHANGE `total` `total` DECIMAL(15,4) NOT NULL;
ALTER TABLE `#__secretary_documents` CHANGE `subtotal` `subtotal` DECIMAL(15,4) NOT NULL;

ALTER TABLE `#__secretary_products` CHANGE `priceCost` `priceCost` DECIMAL(15,4) NOT NULL;
ALTER TABLE `#__secretary_products` CHANGE `priceSale` `priceSale` DECIMAL(15,4) NOT NULL;
ALTER TABLE `#__secretary_products` CHANGE `quantityBought` `quantityBought` DECIMAL(15,4) NOT NULL;
ALTER TABLE `#__secretary_products` CHANGE `quantityMax` `quantityMax` DECIMAL(15,4) NOT NULL;
ALTER TABLE `#__secretary_products` CHANGE `quantityMin` `quantityMin` DECIMAL(15,4) NOT NULL;
ALTER TABLE `#__secretary_products` CHANGE `quantity` `quantity` DECIMAL(15,4) NOT NULL;
ALTER TABLE `#__secretary_products` CHANGE `totalBought` `totalBought` DECIMAL(15,4) NOT NULL;
ALTER TABLE `#__secretary_products` CHANGE `total` `total` DECIMAL(15,4) NOT NULL;

ALTER TABLE `#__secretary_templates` ADD `dpi` SMALLINT(4) NOT NULL DEFAULT '96' AFTER `css`, ADD `format` VARCHAR(16) NOT NULL DEFAULT '210mm;297mm' AFTER `dpi`;

DROP TABLE `#__secretary_accounting`;
CREATE TABLE IF NOT EXISTS `#__secretary_accounting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL,
  `business` int(11) NOT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `title` varchar(123) NOT NULL DEFAULT '',
  `state` int(11) NOT NULL DEFAULT '26',
  `soll` varchar(255) NOT NULL COMMENT 'JSON of accounts : sum',
  `haben` varchar(255) NOT NULL COMMENT 'JSON of accounts : sum',
  `total` decimal(15,4) NOT NULL,
  `upload` varchar(30) NOT NULL,
  `fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_business_state` (`business`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE `#__secretary_accounts`;
CREATE TABLE `#__secretary_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  `kid` int(11) NOT NULL,
  `budget` decimal(15,4) NOT NULL,
  `soll` decimal(15,4) NOT NULL,
  `haben` decimal(15,4) NOT NULL,
  `history` text NOT NULL,
  `fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_business_year` (`business`,`year`),
  KEY `idx_kid` (`kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE `#__secretary_accounts_system`;
CREATE TABLE `#__secretary_accounts_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `ordering` int(10) unsigned NOT NULL DEFAULT '0',
  `nr` varchar(30) NOT NULL,
  `title` varchar(500) NOT NULL,
  `description` text,
  `type` varchar(32) NOT NULL DEFAULT '0',
  `locked` tinyint(1) NOT NULL,
  `fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_state` (`type`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__secretary_newsletter` (
  `listID` int(10) NOT NULL,
  `contactID` int(10) NOT NULL,
  PRIMARY KEY (`listID`,`contactID`),
  KEY `idx_list` (`listID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
