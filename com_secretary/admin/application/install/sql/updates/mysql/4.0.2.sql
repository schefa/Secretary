-- Secretary 4.0.2

ALTER TABLE `#__secretary_businesses` ADD `vatid` varchar(50) NULL DEFAULT NULL AFTER `address` /** CAN FAIL **/;
