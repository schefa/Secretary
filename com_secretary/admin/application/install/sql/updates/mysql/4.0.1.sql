-- Secretary 4.0.1

ALTER TABLE `#__secretary_businesses` MODIFY `asset_id` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_documents` MODIFY `asset_id` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_products` MODIFY `asset_id` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_subjects` MODIFY `asset_id` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_tasks` MODIFY `asset_id` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_templates` MODIFY `asset_id` int(11) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_times` MODIFY `asset_id` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `#__secretary_subjects` MODIFY `catid` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_subjects` MODIFY `country` varchar(64) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_subjects` MODIFY `upload` varchar(30) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_subjects` MODIFY `checked_out` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_subjects` MODIFY `modified` datetime NULL DEFAULT NULL;

ALTER TABLE `#__secretary_documents` MODIFY `deadline` date NULL DEFAULT NULL;

ALTER TABLE `#__secretary_documents` MODIFY `checked_out` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_products` MODIFY `checked_out` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_templates` MODIFY `checked_out` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_times` MODIFY `checked_out` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `#__secretary_businesses` MODIFY `upload` varchar(30) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_documents` MODIFY `upload` varchar(30) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_products` MODIFY `upload` varchar(30) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_tasks` MODIFY `upload` varchar(255) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_times` MODIFY `upload` varchar(30) NULL DEFAULT NULL;

ALTER TABLE `#__secretary_documents` MODIFY `taxtotal` varchar(255) NULL DEFAULT NULL;

ALTER TABLE `#__secretary_subjects` MODIFY `lat` float(10,6) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_subjects` MODIFY `lng` float(10,6) NULL DEFAULT NULL;

ALTER TABLE `#__secretary_products` MODIFY `catid` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_products` MODIFY `priceCost` decimal(15,4) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_products` MODIFY `priceSale` decimal(15,4) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_products` MODIFY `quantityBought` decimal(15,4) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_products` MODIFY `quantityMax` decimal(15,4) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_products` MODIFY `quantityMin` decimal(15,4) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_products` MODIFY `quantity` decimal(15,4) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_products` MODIFY `totalBought` decimal(15,4) NULL DEFAULT NULL;
ALTER TABLE `#__secretary_products` MODIFY `total` decimal(15,4) NULL DEFAULT NULL;
