-- Secretary 1.4.2 (2015-12-15)

ALTER TABLE `#__secretary_businesses` 
	ADD `created_by` INT(11) NOT NULL DEFAULT '0' AFTER `createdEntry`, 
	ADD `checked_out` INT(11) NOT NULL DEFAULT '0' AFTER `created_by`, 
	ADD `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `checked_out`;
	
ALTER TABLE `#__secretary_tasks` ADD `level` SMALLINT NOT NULL DEFAULT '0' AFTER `parentID`;