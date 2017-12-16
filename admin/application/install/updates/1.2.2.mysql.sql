-- Secretary 1.2.2 (2015-09-11)

ALTER TABLE `#__secretary_tasks` CHANGE `progress` `progress` FLOAT(4,1) NOT NULL DEFAULT '0.0';

INSERT INTO `#__secretary_fields` (`id`, `extension`, `title`, `description`, `hard`, `type`, `values`, `standard`, `required`) VALUES (NULL, 'newsletters', 'COM_SECRETARY_NEWSLETTER', 'COM_SECRETARY_NEWSLETTER_DESC', 'newsletter', 'sql', 'SELECT * FROM `#__secretary_folders` WHERE `extension` = ''newsletters''', '0', '0');
