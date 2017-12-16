-- Secretary 2.1.3 (2016-07-27)

ALTER TABLE `#__secretary_subjects` DROP `type`;
ALTER TABLE `#__secretary_subjects` CHANGE `knr` `number` VARCHAR(31) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `#__secretary_subjects` ADD `template` INT(10) NOT NULL DEFAULT '0' AFTER `fields`;
ALTER TABLE `#__secretary_products` ADD `template` INT(10) NOT NULL DEFAULT '0' AFTER `fields`;


CREATE TABLE `#__secretary_markets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `symbol` varchar(12) NOT NULL,
  `name` varchar(255) NOT NULL,
  `exch` varchar(20) NOT NULL,
  `exchType` char(1) NOT NULL,
  `quantity` int(11) NOT NULL,
  `ek_price` decimal(10,4) NOT NULL,
  `created` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
