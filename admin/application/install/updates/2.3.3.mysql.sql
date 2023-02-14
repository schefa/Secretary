-- Secretary 2.3.3 (2016-09-)

ALTER TABLE `#__secretary_templates` 
	ADD `header` TEXT NULL DEFAULT NULL AFTER `text`,
	ADD `footer` TEXT NULL DEFAULT NULL AFTER `header`,
	ADD `margins` VARCHAR(256) NOT NULL DEFAULT '15;15;10;10' AFTER `format`;