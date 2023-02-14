-- Secretary 1.4.1 (2015-12-15)

ALTER TABLE `#__secretary_messages` ADD `contact_to_alias` VARCHAR(255) NULL DEFAULT NULL AFTER `contact_to`;
ALTER TABLE `#__secretary_messages` ADD `refer_to` INT(11) NOT NULL DEFAULT '0' AFTER `catid`;

ALTER TABLE `#__secretary_times` ADD `document_id` INT(11) NOT NULL DEFAULT '0' AFTER `location_id`;
ALTER TABLE `#__secretary_times` DROP `priceTotal`;
ALTER TABLE `#__secretary_times` DROP `priceSubtotal`;
ALTER TABLE `#__secretary_times` DROP `priceTaxtype`;
