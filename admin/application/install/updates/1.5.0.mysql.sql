-- Secretary 1.5.0 (2015-12-25)

INSERT INTO `#__secretary_fields` (`id`, `extension`, `title`, `description`, `hard`, `type`, `values`, `standard`, `required`) 
	VALUES (NULL, 'documents', 'COM_SECRETARY_EMAIL_TEMPLATE', 'COM_SECRETARY_EMAIL_TEMPLATE_DESC', 'emailtemplate', 'sql', 'SELECT * FROM `#__secretary_templates`', '1', '1');
	
INSERT INTO `#__secretary_templates` (`id`, `asset_id`, `business`, `extension`, `state`, `catid`, `title`, `text`, `css`, `fields`, `checked_out`, `checked_out_time`, `language`)
	VALUES (NULL, 0, 1, 'documents', 19, 0, 'Ihre {document-title} vom {created}', 'Guten Tag {contact-gender} {contact-lastname},<br><br>anbei übersende ich Ihnen Ihre {document-title} vom {created}.<br><br>Ihre {document-title} liegt im PDF-Format vor. Um die {document-title} zu lesen oder auszudrucken, benötigen Sie das Programm Acrobat Reader von Adobe, welches Sie kostenlos über diesen Link herunterladen können: https://get.adobe.com/de/reader/<br><br>mit freundlichen Grüßen<br><br>{user-name}<br>{address}','','',0,'0000-00-00 00:00:00','de-DE');
	
INSERT INTO `#__secretary_templates` (`id`, `asset_id`, `business`, `extension`, `state`, `catid`, `title`, `text`, `css`, `fields`, `checked_out`, `checked_out_time`, `language`)
	VALUES (NULL, 0, 1, 'documents', 19, 0, '{document-title} vom {created}', 'Guten Tag {contact-gender} {contact-lastname},<br><br>anbei übersende ich Ihnen ein {document-title} für Ihren Auftrag: {title}.<br><br>Das {document-title} liegt im PDF-Format vor. Um es zu lesen oder auszudrucken, benötigen Sie das Programm Acrobat Reader von Adobe, welches Sie kostenlos über diesen Link herunterladen können: https://get.adobe.com/de/reader/<br><br>mit freundlichen Grüßen<br><br>{user-name}<br>{address}','','',0,'0000-00-00 00:00:00','de-DE');

