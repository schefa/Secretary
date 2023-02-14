
--
-- Daten für Tabelle `#__secretary_businesses`
--

INSERT IGNORE INTO `#__secretary_businesses` (`id`, `asset_id`, `state`, `home`, `title`, `slogan`, `address`, `upload`, `access`, `defaultNote`, `currency`, `taxvalue`, `taxPrepo`, `guv1`, `guv2`, `selectedFolders`, `fields`, `createdEntry`) VALUES
(1, 0, 1, 1, 'Mein Business', '', 'Street 123\r\n12345 Stadt\r\nDeutschland', '', 1, '', 'EUR', 19, 2, '["2"]', '', '{"documents":[2,3],"subjects":[4,5,6]}', '[[3,"Bank account","1234567891234567","text"]]', 1441029141);

--
-- Daten für Tabelle `#__secretary_folders`
--

INSERT IGNORE INTO `#__secretary_folders` (`id`, `asset_id`, `business`, `extension`, `parent_id`, `ordering`, `state`, `level`, `title`, `alias`, `description`, `fields`, `number`, `checked_out`, `checked_out_time`, `created_by`, `created_time`, `access`) VALUES
(1, 0, 0, 'system', 0, 1, 0, 0, 'ROOT', '', '', NULL, '', 0, '0000-00-00 00:00:00', 601, '2011-01-01 00:00:01', 1),
(2, 0, 1, 'documents', 0, 1, 24, 1, 'COM_SECRETARY_INVOICES', 'COM_SECRETARY_INVOICE', '', '[[13,"COM_SECRETARY_EMAIL_TEMPLATE","6","emailtemplate"],[2,"COM_SECRETARY_TEMPLATE","1","template"],[1,"COM_SECRETARY_PRODUCT_USAGE","1","pUsage"]]', '{CNT start=0}', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 1),
(3, 0, 1, 'documents', 0, 2, 24, 1, 'COM_SECRETARY_QUOTES', 'COM_SECRETARY_QUOTE', '', '[[13,"COM_SECRETARY_EMAIL_TEMPLATE","7","emailtemplate"]]', '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 1),
(4, 0, 1, 'subjects', 0, 1, 24, 1, 'COM_SECRETARY_CUSTOMERS', 'COM_SECRETARY_CUSTOMER', '', '[]', '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 1),
(5, 0, 1, 'subjects', 0, 2, 24, 1, 'COM_SECRETARY_EMPLOYEES', 'COM_SECRETARY_EMPLOYEE', '', '[[6,"COM_SECRETARY_BIRTHDAY","1999-01-01","date"]]', '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 1),
(6, 0, 1, 'subjects', 0, 3, 24, 1, 'COM_SECRETARY_SUPPLIERS', 'COM_SECRETARY_SUPPLIER', '', '[]', '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 1),
(7, 0, 1, 'documents', 0, 3, 24, 1, 'COM_SECRETARY_REMINDERS', 'COM_SECRETARY_REMINDER', '', '[]', '', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 1);

--
-- Daten für Tabelle `#__secretary_subjects`
--

INSERT IGNORE INTO `#__secretary_subjects` (`id`, `asset_id`, `business`, `state`, `catid`, `number`, `gender`, `firstname`, `lastname`, `street`, `zip`, `location`, `country`, `phone`, `email`, `lat`, `lng`, `upload`, `created_by`, `created`, `checked_out`, `checked_out_time`, `modified`, `connections`, `fields`) VALUES
(1, 0, 1, 13, 4, NULL, 0, 'Custom', 'Customer', 'Street', '', 'New York', '', 0, 'cc@example.com', 34.989235, -81.249924, '', 0, CURDATE(), 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '[]'),
(2, 0, 1, 13, 5, NULL, 1, 'Co', 'Worker', 'Street', '', 'Miami', 'USA', 0, '', 25.928465, -80.171288, '', 0, CURDATE(), 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '[[6,"COM_SECRETARY_BIRTHDAY","1969-11-31","date"]]');
