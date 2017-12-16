-- Secretary 1.1.0 (2015-07-25)

ALTER TABLE `#__secretary_messages` ADD `template` INT(11) NULL DEFAULT NULL AFTER `upload`;
ALTER TABLE `#__secretary_documents` ADD `template` INT(11) NULL DEFAULT NULL AFTER `accounting_id`;

INSERT INTO `#__secretary_templates`
	(`id`, `asset_id`, `business`, `extension`, `state`, `catid`, `title`, `text`, `css`, `fields`, `checked_out`, `checked_out_time`, `language`)
	VALUES (NULL, '0', '1', 'messages', '1', '0', 'COM_SECRETARY_CORRESPONDENCE', '<table style="height: 24px; width: 100%;">\r\n<tbody>\r\n<tr>\r\n<td> <span style="text-align: left;">{address}</span></td>\r\n<td style="text-align: right;"> {logo}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p> </p>\r\n<p> </p>\r\n<p>Anrede</p>\r\n<p>Anschrift</p>\r\n<p style="text-align: right;">{created}</p>\r\n<p style="text-align: center;"><span style="text-align: center;"><strong>{title}</strong></span></p>\r\n<p> </p>\r\n<p>Text</p>\r\n<p> </p>\r\n<p>mit freundlichen Grüßen</p>\r\n<p> </p>\r\n<p> </p>', '', NULL, '0', '0000-00-00 00:00:00', 'de-DE');