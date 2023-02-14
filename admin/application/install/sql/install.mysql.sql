
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_activities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `extension` char(16) NOT NULL DEFAULT 'system',
  `business` int(11) unsigned NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
  `itemID` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_business_ext` (`business`,`extension`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_businesses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL,
  `state` int(11) NOT NULL DEFAULT '1',
  `home` int(1) unsigned NOT NULL,
  `title` text NOT NULL,
  `slogan` varchar(255) DEFAULT NULL,
  `address` mediumtext,
  `upload` varchar(30) NOT NULL,
  `access` int(10) unsigned NOT NULL,
  `defaultNote` text NOT NULL,
  `currency` varchar(50) NOT NULL,
  `taxvalue` decimal(4,2) NOT NULL,
  `taxPrepo` int(1) NOT NULL,
  `guv1` varchar(255) NOT NULL,
  `guv2` varchar(255) NOT NULL,
  `selectedFolders` varchar(255) NOT NULL,
  `fields` text,
  `createdEntry` int(20) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_home` (`home`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_connections` (
  `extension` varchar(16) NOT NULL DEFAULT 'system',
  `one` int(11) NOT NULL,
  `two` int(11) NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`extension`,`one`,`two`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_currencies` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `currency` char(3) NOT NULL DEFAULT 'EUR',
  `title` varchar(255) DEFAULT 'Euro',
  `symbol` varchar(10) DEFAULT '€',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__secretary_currencies` (`id`, `currency`, `title`, `symbol`) VALUES
(1, 'AUD', 'Australian dollar', 'A$'),
(2, 'BGN', 'Lev', 'лв.'),
(3, 'BRL', 'Real', 'R$'),
(4, 'CAD', 'Canadian dollar', 'C$'),
(5, 'CHF', 'Swiss franc', 'CHF'),
(6, 'CNY', 'Yuan renminbi', '¥'),
(7, 'CZK', 'Koruna', 'Kč'),
(8, 'DKK', 'Krone', 'kr'),
(9, 'EUR', 'Euro', '€'),
(10, 'GBP', 'Pound sterling', '£'),
(11, 'HKD', 'Hong Kong dollar', 'HK$'),
(12, 'HRK', 'Kuna', 'kn'),
(13, 'HUF', 'Forint', 'Ft'),
(14, 'IDR', 'Rupiah', 'Rp'),
(15, 'ILS', 'Shekel', '₪'),
(16, 'INR', 'Rupee', '₹'),
(17, 'JPY', 'Yen', '¥'),
(18, 'KRW', 'Won', '₩'),
(19, 'MXN', 'Peso', 'MX$'),
(20, 'MYR', 'Ringgit', 'RM'),
(21, 'NZD', 'New Zealand dollar', 'NZ$'),
(22, 'PHP', 'Peso', '₱'),
(23, 'PLN', 'Zloty', 'zł'),
(24, 'RON', 'Leu', 'lei'),
(25, 'RUB', 'Rouble', 'руб'),
(26, 'SEK', 'Krona', 'kr'),
(27, 'SGD', 'Singapore dollar', 'SG$'),
(28, 'THB', 'Baht', '฿'),
(29, 'TRY', 'Lira', '₺'),
(30, 'USD', 'US dollar', '$'),
(31, 'ZAR', 'Rand', 'R');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_documents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `business` int(11) unsigned NOT NULL,
  `office` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '1',
  `nr` char(30) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `subjectid` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `createdEntry` int(16) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `created` date NOT NULL,
  `deadline` date NOT NULL,
  `paid` decimal(15,4) DEFAULT NULL,
  `items` text,
  `currency` varchar(10) DEFAULT NULL,
  `upload` varchar(30) NOT NULL,
  `text` text NOT NULL,
  `total` decimal(15,4) NOT NULL,
  `subtotal` decimal(15,4) NOT NULL,
  `taxtotal` varchar(255) NOT NULL,
  `taxtype` int(11) NOT NULL DEFAULT '1',
  `rabatt` decimal(10,4) NOT NULL,
  `accounting_id` int(11) NOT NULL DEFAULT '0',
  `template` int(11) DEFAULT NULL,
  `fields` text,
  PRIMARY KEY (`id`),
  KEY `idx_created_catid` (`business`,`created`,`catid`),
  KEY `idx_catid_state` (`business`,`catid`,`state`),
  KEY `idx_subjectid` (`business`,`subjectid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT IGNORE INTO `#__secretary_entities` (`id`, `title`, `description`) VALUES
(1, 'COM_SECRETARY_STK', 'COM_SECRETARY_STUECK');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` char(16) NOT NULL DEFAULT 'system',
  `title` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `hard` varchar(128) NOT NULL,
  `type` char(32) NOT NULL,
  `values` text NOT NULL,
  `standard` text,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_ext` (`extension`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO  `#__secretary_fields` (`id`, `extension`, `title`, `description`, `hard`, `type`, `values`, `standard`, `required`) VALUES
(1, 'documents', 'COM_SECRETARY_PRODUCT_USAGE', 'COM_SECRETARY_PRODUCT_USAGE_DESC', 'pUsage', 'list', '["JNO","COM_SECRETARY_PRODUCTS_USAGE_VERBRAUCH","COM_SECRETARY_PRODUCTS_USAGE_EINKAUF"]', '1', 1),
(2, 'documents', 'COM_SECRETARY_TEMPLATE', 'COM_SECRETARY_TEMPLATE_DESC', 'template', 'sql', 'SELECT * FROM `#__secretary_templates`', '1', 1),
(3, 'system', 'COM_SECRETARY_FIELD_TEXT', 'COM_SECRETARY_FIELD_TEXT_DESC', 'text', 'text', '', '', 0),
(4, 'system', 'COM_SECRETARY_FIELD_NUMBER', 'COM_SECRETARY_FIELD_NUMBER_DESC', 'number', 'number', '', '0', 0),
(5, 'system', 'COM_SECRETARY_FIELD_LIST', 'COM_SECRETARY_FIELD_LIST_DESC', 'list', 'list', '', '0', 0),
(6, 'system', 'COM_SECRETARY_FIELD_DATE', 'COM_SECRETARY_FIELD_DATE_DESC', 'date', 'date', '0000-00-00', '2010-06-01', 0),
(7, 'system', 'COM_SECRETARY_FIELD_COLOR', 'COM_SECRETARY_FIELD_COLOR_DESC', 'color', 'color', '', '', 0),
(8, 'system', 'COM_SECRETARY_FIELD_URL', 'COM_SECRETARY_FIELD_URL_DESC', 'url', 'url', '', 'http://secretary.schefa.com', 0),
(9, 'system', 'COM_SECRETARY_FIELD_TEXTAREA', 'COM_SECRETARY_FIELD_TEXTAREA_DESC', 'textarea', 'textarea', '', '', 0),
(10, 'times', 'COM_SECRETARY_FIELD_TIMECOLOR', 'COM_SECRETARY_FIELD_TIMECOLOR_DESC', 'timeColor', 'color', '', '', 0),
(11, 'newsletters', 'COM_SECRETARY_NEWSLETTER', 'COM_SECRETARY_NEWSLETTER_DESC', 'newsletter', 'sql', 'SELECT * FROM `#__secretary_folders` WHERE `extension` = ''newsletters''', '0', 0),
(12, 'system', 'COM_SECRETARY_GENDER', '', 'anrede', 'list', '["COM_SECRETARY_GENDER_MR","COM_SECRETARY_GENDER_MRS",""]', '2', 0),
(13, 'documents', 'COM_SECRETARY_EMAIL_TEMPLATE', 'COM_SECRETARY_EMAIL_TEMPLATE_DESC', 'emailtemplate', 'sql', 'SELECT * FROM `#__secretary_templates`', '1', 0);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_folders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) unsigned NOT NULL DEFAULT '0',
  `business` int(11) NOT NULL,
  `extension` char(16) NOT NULL DEFAULT 'system',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '1',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `fields` text,
  `number` varchar(32) NOT NULL DEFAULT '',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cat_idx` (`business`,`extension`,`state`),
  KEY `idx_ordering` (`parent_id`,`ordering`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_alias` (`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT IGNORE INTO `#__secretary_folders` (`id`, `asset_id`, `business`, `extension`, `parent_id`, `ordering`, `state`, `level`, `title`, `alias`, `description`, `fields`, `number`, `checked_out`, `checked_out_time`, `created_by`, `created_time`, `access`) VALUES
(1, 0, 0, 'system', 0, 1, 0, 0, 'ROOT', '', '', NULL, '', 0, '0000-00-00 00:00:00', 601, '2011-01-01 00:00:01', 1);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL DEFAULT '0',
  `business` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `extension` char(16) NOT NULL DEFAULT 'system',
  `catid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `street` varchar(255) DEFAULT NULL,
  `zip` varchar(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `lat` float(10,6) NOT NULL,
  `lng` float(10,6) NOT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `upload` varchar(30) DEFAULT NULL,
  `fields` text,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_business_ext_catid` (`business`,`extension`,`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_markets` (
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

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business` int(11) NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
  `refer_to` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `created_by` varchar(255) NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) DEFAULT NULL,
  `contact_to` int(11) unsigned NOT NULL DEFAULT '0',
  `contact_to_alias` varchar(255) DEFAULT NULL,
  `priority` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `upload` varchar(30) NOT NULL,
  `template` int(11) DEFAULT NULL,
  `fields` text,
  PRIMARY KEY (`id`),
  KEY `idx_business_catid` (`business`, `catid`, `state`),
  KEY `idx_contactto_state` (`business`, `contact_to`, `state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_newsletter` (
  `listID` int(10) NOT NULL,
  `contactID` int(10) NOT NULL,
  PRIMARY KEY (`listID`,`contactID`),
  KEY `idx_list` (`listID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `business` int(10) unsigned NOT NULL,
  `location` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '1',
  `year` int(11) NOT NULL,
  `nr` varchar(63) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `upload` varchar(30) NOT NULL,
  `entity` char(30) NOT NULL,
  `items` text NOT NULL,
  `history` text,
  `contacts` text COMMENT 'Lieferanten',
  `taxRate` float NOT NULL,
  `priceCost` decimal(15,4) NOT NULL,
  `priceSale` decimal(15,4) NOT NULL,
  `quantityBought` decimal(15,4) NOT NULL,
  `quantityMax` decimal(15,4) NOT NULL,
  `quantityMin` decimal(15,4) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `totalBought` decimal(15,4) NOT NULL,
  `total` decimal(15,4) NOT NULL,
  `fields` text,
  `template` int(10) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cat` (`business`,`year`,`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_repetition` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `extension` char(16) NOT NULL DEFAULT 'system',
  `time_id` int(11) NOT NULL,
  `startTime` int(11) NOT NULL DEFAULT '0',
  `nextTime` int(11) NOT NULL DEFAULT '0',
  `endTime` int(11) NOT NULL COMMENT 'nextTime + intervall < endTime',
  `intervall` int(11) NOT NULL DEFAULT '0',
  `int_in_words` char(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ext` (`extension`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `params` text NOT NULL,
  `rules` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__secretary_settings` (`id`, `params`, `rules`) VALUES
(1, '{"templateColor":"white","pdf":"0","entitySelect":"0","numberformat":"0","currencyformat":"0","accessMissingNote":"0","cache":"1","gMapsAPI":"","gMapsContacts":"1","gMapsLocations":"1","activityCreated":"1","activityEdited":"0","activityDeleted":"1","documentExt":"jpeg,jpg,png,gif,pdf,doc,docx,odt","documentSize":"2500000","products_columns":["priceCost","quantityBought","totalBought","priceSale","quantity","total"],"contacts_columns":["category","street","zip","location"],"documents_frontend":"0","filterList":"1","messages_unread":"9","messages_chat":"1","messages_waitMsg":"3000","messages_waitPing":"10000"}', '');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` char(16) NOT NULL DEFAULT 'system',
  `ordering` int(3) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `closeTask` int(4) NOT NULL,
  `class` char(16) NOT NULL,
  `icon` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ext` (`extension`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__secretary_status` (`id`, `extension`, `ordering`, `title`, `description`, `closeTask`, `class`, `icon`) VALUES
(1, 'system', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 2, 'unpublish', 'circle-thin'),
(2, 'system', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 1, 'publish', 'check-circle'),
(3, 'system', 0, 'COM_SECRETARY_STATUS_ARCHIVED', 'COM_SECRETARY_STATUS_ARCHIVED_DESC', 1, '', 'archive'),
(4, 'system', 0, 'COM_SECRETARY_STATUS_TRASH', 'COM_SECRETARY_STATUS_TRASH_DESC', 1, 'trash', 'trash'),
(5, 'documents', 0, 'COM_SECRETARY_STATUS_OPEN', 'COM_SECRETARY_STATUS_DONE_ITEM', 6, 'unpaid', 'check-circle'),
(6, 'documents', 0, 'COM_SECRETARY_STATUS_DONE', 'COM_SECRETARY_STATUS_OPEN_ITEM', 5, 'paid', 'circle-thin'),
(7, 'documents', 0, 'COM_SECRETARY_STATUS_PENDING', 'COM_SECRETARY_STATUS_PENDING_ITEM', 5, 'paidso', 'coffee'),
(8, 'documents', 0, 'COM_SECRETARY_STATUS_STORNO', 'COM_SECRETARY_STATUS_STORNO_ITEM', 6, 'canceled', 'trash'),
(9, 'messages', 0, 'COM_SECRETARY_MESSAGES_OPTION_UNREAD', 'COM_SECRETARY_MESSAGES_MARK_AS_READ',  11, 'unpublish', 'circle-thin'),
(10, 'messages', 0, 'JTRASHED', 'COM_SECRETARY_MESSAGES_MARK_AS_UNREAD', 9, 'unpublish', 'trash'),
(11, 'messages', 0, 'COM_SECRETARY_MESSAGES_OPTION_READ', 'COM_SECRETARY_MESSAGES_MARK_AS_UNREAD', 9, 'publish', 'check-circle'),
(12, 'subjects', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 13, 'unpublish', 'circle-thin'),
(13, 'subjects', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 12, 'publish', 'check-circle'),
(14, 'products', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 15, 'unpublish', 'circle-thin'),
(15, 'products', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 14, 'publish', 'check-circle'),
(16, 'businesses', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 17, 'unpublish', 'circle-thin'),
(17, 'businesses', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 16, 'publish', 'check-circle'),
(18, 'templates', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 19, 'unpublish', 'circle-thin'),
(19, 'templates', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 18, 'publish', 'check-circle'),
(20, 'times', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 21, 'unpublish', 'circle-thin'),
(21, 'times', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 20, 'publish', 'check-circle'),
(22, 'times', 0, 'COM_SECRETARY_STATUS_ARCHIVED', 'COM_SECRETARY_STATUS_ARCHIVED_DESC', 20, '', 'archive'),
(23, 'folders', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 24, 'unpublish', 'circle-thin'),
(24, 'folders', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 23, 'publish', 'check-circle');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `business` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '1',
  `catid` int(11) NOT NULL,
  `number` varchar(31) DEFAULT NULL,
  `gender` int(4) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `zip` varchar(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `country` varchar(64) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `lat` float(10,6) NOT NULL,
  `lng` float(10,6) NOT NULL,
  `upload` varchar(30) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` date NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `connections` text,
  `fields` text,
  `template` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_name` (`lastname`, `firstname`),
  KEY `idx_catid_state` (`business`, `catid`, `state`),
  KEY `idx_zip` (`business`, `zip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `business` int(11) unsigned NOT NULL,
  `projectID` int(11) NOT NULL,
  `parentID` int(11) NOT NULL DEFAULT '0',
  `level` smallint(6) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `progress` float(4,1) NOT NULL DEFAULT '0.0',
  `contacts` text NOT NULL,
  `startDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `calctime` int(11) NOT NULL DEFAULT '0',
  `totaltime` int(11) NOT NULL,
  `upload` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `fields` text,
  PRIMARY KEY (`id`),
  KEY `idx_project` (`business`,`projectID`,`parentID`,`state`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) unsigned NOT NULL,
  `business` int(11) unsigned NOT NULL,
  `extension` char(16) NOT NULL DEFAULT 'system',
  `state` int(11) NOT NULL DEFAULT '1',
  `catid` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `header` text,
  `footer` text,
  `css` text NOT NULL,
  `dpi` SMALLINT(4) NOT NULL DEFAULT '96', 
  `format` VARCHAR(16) NOT NULL DEFAULT '210mm;297mm',
  `margins` varchar(256) NOT NULL DEFAULT '15;15;10;10',
  `fields` text,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `language` char(7) NOT NULL DEFAULT '*',
  PRIMARY KEY (`id`),
  KEY `idx_ext` (`business`,`extension`,`state`,`catid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

INSERT IGNORE INTO `#__secretary_templates` (`id`, `asset_id`, `business`, `extension`, `state`, `catid`, `title`, `text`, `header`, `footer`, `css`, `dpi`, `format`, `margins`, `fields`, `checked_out`, `checked_out_time`, `language`) VALUES
(1, 0, 1, 'documents', 19, 0, 'COM_SECRETARY_INVOICE', '<div id="table-print">\r\n\r\n<table class="table no-border">\r\n  <tbody>\r\n  	<tr>\r\n      <td>{address}</td>\r\n      <td class="text-right">{logo}</td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n\r\n<div class="slogan text-center">{slogan}</div>\r\n<h3 class="title">{document-title}</h3>\r\n  \r\n<table class="table no-border">\r\n  <tbody>\r\n  	<tr>\r\n      <td rowspan="3">\r\n        <div>{contact-gender} {contact-firstname} {contact-lastname}</div>\r\n        <div>{contact-street}</div>\r\n        <div>{contact-zip} {contact-location}</div>\r\n      </td>\r\n      <td class="text-right" height="15" width="60">Nr:</td>\r\n      <td class="text-right" height="15" width="80">{nr}</td>\r\n    </tr>\r\n    \r\n    <tr>\r\n      <td class="text-right" height="15" width="60">Datum:</td>\r\n      <td class="text-right" height="15" width="80">{created}</td>\r\n    </tr>\r\n    \r\n    <tr>\r\n      <td colspan="2"> </td>\r\n    </tr>\r\n    \r\n    <tr>\r\n      <td colspan="2">  <div class="auftrag">Ihr Auftrag: {title}</div> </td>\r\n    </tr>\r\n    \r\n  </tbody>\r\n</table>\r\n  \r\n<table class="table table-items">\r\n  <thead>\r\n    <tr>\r\n      <th colspan="2">Anzahl</th>\r\n      <th>Bezeichnung</th>\r\n      <th>Einzelpreis</th>\r\n      <th>Gesamtpreis</th>\r\n    </tr>\r\n  </thead>\r\n  \r\n  <tbody>\r\n    {item_start}\r\n    <tr>\r\n      <td class="item-quantity">{item_quantity}</td>\r\n      <td class="item-entity">{item_entity}</td>\r\n      <td class="item-title">{item_title}<br />{item_desc}</td>\r\n      <td class="item-price">{item_price}</td>\r\n      <td class="item-total">{item_total}</td>\r\n   </tr>\r\n    {item_end}\r\n  </tbody>\r\n  \r\n</table>\r\n\r\n<table class="table no-border">\r\n  <tbody>\r\n  <tr>\r\n	<td>{note}</td>\r\n\r\n	<td class="text-right">\r\n	<table class="table no-border summary">\r\n		<tbody>\r\n          \r\n          <tr class="border-bottom">\r\n            <td height="20" class="border-bottom">Gesamt</td>\r\n            <td height="20" width="100" class="border-bottom text-right">{total} {currency}</td>\r\n          </tr>\r\n\r\n          <tr>\r\n            <td>Netto</td>\r\n            <td class="text-right">{subtotal} {currency}</td>\r\n          </tr>\r\n\r\n         {taxtotal_start}\r\n         <tr>\r\n            <td>{taxtotal_percent}% Mwst</td>\r\n            <td class="text-right">{taxtotal_value}</td>\r\n         </tr>\r\n        {taxtotal_end}\r\n\r\n        </tbody>\r\n	</table>\r\n	</td>\r\n  </tr>\r\n  </tbody>\r\n</table>\r\n\r\n<h4 class="footer-title"></h4>\r\n<table class="table no-border">\r\n  <tbody>\r\n    <tr>\r\n      <td style="width: 33%;" valign="top"></td>\r\n      <td style="width: 33%;" valign="top"></td>\r\n      <td style="width: 33%;" valign="top"></td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n</div>', NULL, NULL, 'body{ font-family:Arial,Helvetica,sans-serif;font-size:12px; }\r\n#table-print .pull-right{ float:right !important; }\r\n#table-print .pull-left{ float:left !important; }\r\n\r\n#table-print{}\r\ntable { background-color: transparent; border-collapse: collapse;  border-spacing: 0;width: 100%;margin-bottom:14px; }\r\ntable tr th, table tr td {padding:8px; border-top: 1px solid #ddd; line-height: 1.42857; vertical-align:top;}\r\n\r\n#table-print .table,#table-print .row-fluid{ width:100%; }\r\n\r\n#table-print th,#table-print td{ padding:0;margin:0;vertical-align:top;border:1px solid #ccc;}\r\ntable.no-border,table.no-border td,table.no-border th,table.no-border tr{border:0 none !important;}\r\n\r\n.table-items thead th{background-color:#eee;font-weight:normal;}\r\n.table-items .item-quantity{width:8%;}\r\n.table-items .item-entity{width:6%;}\r\n.table-items .item-title{width:60%;}\r\n.table-items .item-price{width:13%;}\r\n.table-items .item-total{width:13%;}\r\n.slogan { width:100%;text-align:center;padding:10px 0; }\r\nh3.title { padding:10px 0 20px 0;text-align: center;width:100%;margin:0; }\r\nh4.footer-title {margin:20px 0 0 0;padding:0;font-weight:bold;padding:8px;}\r\n\r\n#table-print .summary {margin:0;width:100%;}\r\n#table-print table.summary tr td,#table-print table.summary tr th{padding:7px 6px 0 6px;}\r\ntable.no-border .border-bottom {border-bottom:1px solid #ccc !important;padding:8px 6px;}\r\n.summary-total-entry {font-size:120%;}\r\n\r\n.row-fluid{width:100%;}\r\n.row-fluid > div{width:auto;}\r\n\r\nimg{max-width:260px;}\r\n\r\n.text-right, .item-total,.item-quantity,.item-price{ text-align:right; }\r\n#table-print td.item-quantity{border-right:0 none !important; }\r\n#table-print td.item-entity{border-left:0 none !important;}\r\n.text-center{ text-align:center !important; }\r\n\r\n.clear{clear:both;}', 96, '210mm;297mm', '15;15;10;10', NULL, 0, '0000-00-00 00:00:00', 'de-DE'),
(2, 0, 1, 'documents', 19, 0, 'COM_SECRETARY_QUOTE', '<div id="table-print">\r\n<table class="table no-border">\r\n  <tbody>\r\n  	<tr>\r\n      <td>{address}</td>\r\n      <td class="text-right">{logo}</td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n\r\n  <div class="slogan text-center">{slogan}</div>\r\n  \r\n  <h3 class="title">{document-title}</h3>\r\n  \r\n<table class="table no-border">\r\n  <tbody>\r\n  	<tr>\r\n      <td width="100">Angebot für</td>\r\n      <td>\r\n        {contact-gender} {contact-firstname} {contact-lastname}, {contact-street}, {contact-zip} {contact-location}\r\n      </td>\r\n      <td class="text-right" height="15" width="60">Datum:</td>\r\n      <td class="text-right" height="15" width="80">{created}</td>\r\n    </tr>\r\n    {title}\r\n    \r\n    <tr>\r\n      <td>Bauvorhaben</td>\r\n      <td>{title}</td>\r\n      <td> </td>\r\n      <td> </td>\r\n    </tr>\r\n    \r\n  </tbody>\r\n</table>\r\n\r\n<table class="table table-items">\r\n  \r\n  <thead>\r\n    <tr>\r\n      <th colspan="2">Anzahl</th>\r\n      <th>Bezeichnung</th>\r\n      <th>Einzelpreis</th>\r\n      <th>Gesamtpreis</th>\r\n    </tr>\r\n  </thead>\r\n  \r\n  <tbody>\r\n    {item_start}\r\n    <tr>\r\n      <td class="item-quantity">{item_quantity}</td>\r\n      <td class="item-entity">{item_entity}</td>\r\n      <td class="item-title">{item_title}<br />{item_desc}</td>\r\n      <td class="item-price">{item_price}</td>\r\n      <td class="item-total">{item_total}</td>\r\n   </tr>\r\n    {item_end}\r\n  </tbody>\r\n  \r\n</table>\r\n\r\n<table class="table no-border summary">\r\n  <tbody>\r\n  <tr>\r\n	<td>Vielen Dank für Ihre Anfrage.\r\n        <br />Für Rückfragen stehen wir Ihnen gerne zur Verfügung\r\n        <br /><br />Mit freundlichen Grüßen\r\n        <br /><br /><br /><br />\r\n    </td>\r\n\r\n	<td class="text-right">\r\n	<table class="table no-border">\r\n		<tbody>\r\n          \r\n          <tr class="border-bottom">\r\n            <td height="20" class="border-bottom">Gesamt</td>\r\n            <td height="20" width="100" class="border-bottom text-right">{total} {currency}</td>\r\n          </tr>\r\n\r\n          <tr>\r\n            <td>Netto</td>\r\n            <td class="text-right">{subtotal} {currency}</td>\r\n          </tr>\r\n\r\n         {taxtotal_start}\r\n         <tr>\r\n            <td>{taxtotal_percent}% Mwst</td>\r\n            <td class="text-right">{taxtotal_value}</td>\r\n         </tr>\r\n        {taxtotal_end}\r\n\r\n        </tbody>\r\n	</table>\r\n	</td>\r\n  \r\n  </tr>\r\n  </tbody>\r\n</table>\r\n  \r\n  \r\n  \r\n<h4 class="footer-title"></h4>\r\n\r\n<table class="table no-border">\r\n  <tbody>\r\n    <tr>\r\n      <td style="width: 33%;"></td>\r\n      <td style="width: 33%;"></td>\r\n      <td style="width: 33%;" valign="top"></td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n</div>', NULL, NULL, 'body{\r\n	font-family:Arial,Helvetica,sans-serif;font-size:12px;\r\n}\r\n#table-print{}\r\ntable { background-color: transparent; border-collapse: collapse;  border-spacing: 0;width: 100%;margin-bottom:14px; }\r\n.table tr th, .table tr td { border-top: 1px solid #ddd; line-height: 1.42857;padding: 8px;  vertical-align:top;}\r\n\r\n#table-print .table,#table-print .row-fluid{ width:100%; }\r\n\r\n#table-print th,#table-print td{ padding:0;margin:0;vertical-align:top;border:1px solid #ccc;}\r\ntable.no-border,table.no-border td,table.no-border th,table.no-border tr{border:0 none !important;}\r\n\r\n.table-items thead th{background-color:#eee;font-weight:normal;}\r\n.table-items .item-quantity{width:8%;}\r\n.table-items .item-entity{width:6%;}\r\n.table-items .item-title{width:60%;}\r\n.table-items .item-price{width:13%;}\r\n.table-items .item-total{width:13%;}\r\n.slogan { width:100%;text-align:center;padding:10px 0; }\r\nh3.title { padding:10px 0 20px 0;text-align: center;width:100%;margin:0; }\r\nh4.footer-title {margin:20px 0 0 0;padding:0;font-weight:bold;padding:8px;}\r\n\r\n#table-print .summary {margin:0;width:100%;}\r\n#table-print table.summary tr td,#table-print table.summary tr th{padding:7px 6px 0 6px;}\r\ntable.no-border .border-bottom {border-bottom:1px solid #ccc !important;padding:8px 6px;}\r\n.summary-total-entry {font-size:120%;}\r\n\r\n.row-fluid{width:100%;}\r\n.row-fluid > div{width:auto;}\r\n.pull-right{ float:right !important; }\r\n.pull-left{ float:left !important; }\r\n\r\nimg{max-width:260px;}\r\n\r\n.text-right, .item-total,.item-quantity,.item-price{ text-align:right; }\r\n#table-print td.item-quantity{border-right:0 none !important; }\r\n#table-print td.item-entity{border-left:0 none !important;}\r\n.text-center{ text-align:center !important; }\r\n\r\n.clear{clear:both;}', 96, '210mm;297mm', '15;15;10;10', NULL, 0, '0000-00-00 00:00:00', 'de-DE'),
(3, 0, 1, 'documents', 19, 0, 'COM_SECRETARY_INVOICE_W_DISCOUNT', '<div id="table-print">\r\n<table class="table no-border">\r\n  <tbody>\r\n  	<tr>\r\n      <td>{address}</td>\r\n      <td class="text-right">{logo}</td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n\r\n<div class="slogan text-center">{slogan}</div>\r\n  \r\n<h3 class="title">{document-title}</h3>\r\n  \r\n<table class="table no-border">\r\n  <tbody>\r\n  	<tr>\r\n      <td rowspan="3">\r\n        <div>{contact-gender} {contact-firstname} {contact-lastname}</div>\r\n        <div>{contact-street}</div>\r\n        <div>{contact-zip} {contact-location}</div>\r\n      </td>\r\n      <td class="text-right" height="15" width="60">Nr:</td>\r\n      <td class="text-right" height="15" width="80">{nr}</td>\r\n    </tr>\r\n    <tr>\r\n      <td class="text-right" height="15" width="60">Datum:</td>\r\n      <td class="text-right" height="15" width="80">{created}</td>\r\n    </tr>\r\n    <tr>\r\n      <td colspan="2"> </td>\r\n    </tr>\r\n    <tr>\r\n      <td colspan="2"> <div class="auftrag">Ihr Auftrag: {title}</div> </td>\r\n    </tr>\r\n    \r\n  </tbody>\r\n</table>\r\n  \r\n<table class="table table-items">\r\n  \r\n  <thead>\r\n    <tr>\r\n      <th colspan="2">Anzahl</th>\r\n      <th>Bezeichnung</th>\r\n      <th>Einzelpreis</th>\r\n      <th>Gesamtpreis</th>\r\n    </tr>\r\n  </thead>\r\n  \r\n  <tbody>\r\n    {item_start}\r\n    <tr>\r\n      <td class="item-quantity">{item_quantity}</td>\r\n      <td class="item-entity">{item_entity}</td>\r\n      <td class="item-title">{item_title}<br />{item_desc}</td>\r\n      <td class="item-price">{item_price}</td>\r\n      <td class="item-total">{item_total}</td>\r\n   </tr>\r\n    {item_end}\r\n  </tbody>\r\n  \r\n</table>\r\n\r\n<table class="table no-border">\r\n  <tbody>\r\n  <tr>\r\n	<td>{note}</td>\r\n\r\n	<td class="text-right">\r\n	<table class="table no-border summary">\r\n		<tbody>\r\n          \r\n          <tr>\r\n            <td>Rabatt</td>\r\n            <td class="text-right">- {discount} {currency}</td>\r\n          </tr>\r\n          \r\n          <tr class="border-bottom">\r\n            <td height="20" class="border-bottom">Gesamt</td>\r\n            <td height="20" width="100" class="border-bottom text-right">{total} {currency}</td>\r\n          </tr>\r\n\r\n          <tr>\r\n            <td>Netto</td>\r\n            <td class="text-right">{subtotal} {currency}</td>\r\n          </tr>\r\n\r\n         {taxtotal_start}\r\n         <tr>\r\n            <td>{taxtotal_percent}% Mwst</td>\r\n            <td class="text-right">{taxtotal_value}</td>\r\n         </tr>\r\n        {taxtotal_end}\r\n\r\n        </tbody>\r\n	</table>\r\n	</td>\r\n  \r\n  </tr>\r\n  </tbody>\r\n</table>\r\n\r\n<h4 class="footer-title"></h4>\r\n<table class="table no-border">\r\n  <tbody>\r\n    <tr>\r\n      <td style="width: 33%;" valign="top"></td>\r\n      <td style="width: 33%;" valign="top"></td>\r\n      <td style="width: 33%;" valign="top"></td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n  \r\n</div>', NULL, NULL, 'body{ font-family:Arial,Helvetica,sans-serif;font-size:12px; }\r\n#table-print .pull-right{ float:right !important; }\r\n#table-print .pull-left{ float:left !important; }\r\n\r\n#table-print{}\r\ntable { background-color: transparent; border-collapse: collapse;  border-spacing: 0;width: 100%;margin-bottom:14px; }\r\ntable tr th, table tr td {padding:8px; border-top: 1px solid #ddd; line-height: 1.42857; vertical-align:top;}\r\n\r\n#table-print .table,#table-print .row-fluid{ width:100%; }\r\n\r\n#table-print th,#table-print td{ padding:0;margin:0;vertical-align:top;border:1px solid #ccc;}\r\ntable.no-border,table.no-border td,table.no-border th,table.no-border tr{border:0 none !important;}\r\n\r\n.table-items thead th{background-color:#eee;font-weight:normal;}\r\n.table-items .item-quantity{width:8%;}\r\n.table-items .item-entity{width:6%;}\r\n.table-items .item-title{width:60%;}\r\n.table-items .item-price{width:13%;}\r\n.table-items .item-total{width:13%;}\r\n.slogan { width:100%;text-align:center;padding:10px 0; }\r\nh3.title { padding:10px 0 20px 0;text-align: center;width:100%;margin:0; }\r\nh4.footer-title {margin:20px 0 0 0;padding:0;font-weight:bold;padding:8px;}\r\n\r\n#table-print .summary {margin:0;width:100%;}\r\n#table-print table.summary tr td,#table-print table.summary tr th{padding:7px 6px 0 6px;}\r\ntable.no-border .border-bottom {border-bottom:1px solid #ccc !important;padding:8px 6px;}\r\n.summary-total-entry {font-size:120%;}\r\n\r\n.row-fluid{width:100%;}\r\n.row-fluid > div{width:auto;}\r\n\r\nimg{max-width:260px;}\r\n\r\n.text-right, .item-total,.item-quantity,.item-price{ text-align:right; }\r\n#table-print td.item-quantity{border-right:0 none !important; }\r\n#table-print td.item-entity{border-left:0 none !important;}\r\n.text-center{ text-align:center !important; }\r\n\r\n.clear{clear:both;}', 96, '210mm;297mm', '15;15;10;10', NULL, 0, '0000-00-00 00:00:00', 'de-DE'),
(4, 0, 1, 'messages', 1, 0, 'COM_SECRETARY_CORRESPONDENCE', '<table style="height: 24px; width: 100%;">\r\n<tbody>\r\n<tr>\r\n<td> <span style="text-align: left;">{address}</span></td>\r\n<td style="text-align: right;"> {logo}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p> </p>\r\n<p> </p>\r\n<p>Anrede</p>\r\n<p>Anschrift</p>\r\n<p style="text-align: right;">{created}</p>\r\n<p style="text-align: center;"><span style="text-align: center;"><strong>{title}</strong></span></p>\r\n<p> </p>\r\n<p>Text</p>\r\n<p> </p>\r\n<p>mit freundlichen Grüßen</p>\r\n<p> </p>\r\n<p> </p>', NULL, NULL, '', 96, '210mm;297mm', '15;15;10;10', NULL, 0, '0000-00-00 00:00:00', 'de-DE'),
(5, 0, 0, 'messages', 19, 0, 'Kontakt Formular', '<h2>Kontakt</h2>\r\n\r\n<div class="contact">\r\n  <table>\r\n    <tr>\r\n    	<td>{contact-category-title}</td>\r\n    </tr>\r\n    <tr>\r\n    	<td>{contact-firstname} {contact-lastname}</td>\r\n    </tr>\r\n    <tr>\r\n    	<td>{contact-street}</td>\r\n    </tr>\r\n    <tr>\r\n    	<td>{contact-zip} {contact-location}</td>\r\n    </tr>\r\n  </table>\r\n</div>\r\n\r\n<hr />\r\n\r\n{form-start}\r\n<div class="form">\r\n  <table>\r\n    <tr>\r\n    	<td>{form-standard-name-label title=Ihr Name}</td>\r\n    	<td>{form-standard-name}</td>\r\n    </tr>\r\n    <tr>\r\n    	<td>{form-standard-email-label title=Email}</td>\r\n    	<td>{form-standard-email}</td>\r\n    </tr>\r\n    <tr>\r\n    	<td>Ihre Telefonnummer</td>\r\n    	<td>{form-field-phone}</td>\r\n    </tr>\r\n    <tr>\r\n    	<td>{form-standard-subject-label title=Betreff}</td>\r\n    	<td>{form-standard-subject}</td>\r\n    </tr>\r\n    <tr>\r\n    	<td valign="top">{form-standard-text-label title=Nachricht}</td>\r\n    	<td>{form-standard-text}</td>\r\n    </tr>\r\n    <tr>\r\n    	<td>Kopie an mich</td>\r\n    	<td>{form-standard-copy}</td>\r\n    </tr>\r\n    <tr>\r\n    	<td></td>\r\n    	<td>{form-standard-send}</td>\r\n    </tr>\r\n  </table>\r\n</div>\r\n{form-end}\r\n', NULL, NULL, '', 96, '210mm;297mm', '15;15;10;10', '[[3,"Phone","","text"]]', 0, '0000-00-00 00:00:00', 'de-DE'),
(6, 0, 1, 'documents', 19, 0, 'Ihre {document-title} vom {created}', 'Guten Tag {contact-gender} {contact-lastname},<br><br>anbei übersende ich Ihnen Ihre {document-title} vom {created}.<br><br>Ihre {document-title} liegt im PDF-Format vor. Um die {document-title} zu lesen oder auszudrucken, benötigen Sie das Programm Acrobat Reader von Adobe, welches Sie kostenlos über diesen Link herunterladen können: https://get.adobe.com/de/reader/<br><br>mit freundlichen Grüßen<br><br>{user-name}<br>{address}', NULL, NULL, '', 96, '210mm;297mm', '15;15;10;10', '', 0, '0000-00-00 00:00:00', 'de-DE'),
(7, 0, 1, 'documents', 19, 0, '{document-title} vom {created}', 'Guten Tag {contact-gender} {contact-lastname},<br><br>anbei übersende ich Ihnen ein {document-title} für Ihren Auftrag: {title}.<br><br>Das {document-title} liegt im PDF-Format vor. Um es zu lesen oder auszudrucken, benötigen Sie das Programm Acrobat Reader von Adobe, welches Sie kostenlos über diesen Link herunterladen können: https://get.adobe.com/de/reader/<br><br>mit freundlichen Grüßen<br><br>{user-name}<br>{address}', NULL, NULL, '', 96, '210mm;297mm', '15;15;10;10', '', 0, '0000-00-00 00:00:00', 'de-DE'),
(8, 0, 1, 'documents', 19, 0, 'Mahnung', '<div id="table-print">\r\n\r\n<table class="table no-border">\r\n  <tbody>\r\n  	<tr>\r\n      <td>{address}</td>\r\n      <td class="text-right">{logo}</td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n\r\n<div class="slogan text-center">{slogan}</div>\r\n<h3 class="title">{document-title}</h3>\r\n \r\n<table class="table no-border">\r\n  <tbody>\r\n  	<tr>\r\n      <td rowspan="3">\r\n        <div>{contact-gender} {contact-firstname} {contact-lastname}</div>\r\n        <div>{contact-street}</div>\r\n        <div>{contact-zip} {contact-location}</div>\r\n      </td>\r\n      <td class="text-right" height="15" width="60">Nr:</td>\r\n      <td class="text-right" height="15" width="80">{nr}</td>\r\n    </tr>\r\n    \r\n    <tr>\r\n      <td class="text-right" height="15" width="60">Datum:</td>\r\n      <td class="text-right" height="15" width="80">{created}</td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n<br />\r\nSehr geehrte/r {contact-gender} {contact-lastname},<br /><br />für den unten stehenden Betrag konnten wir noch keinen Zahlungseingang feststellen<br /><br />\r\n\r\n<table class="table no-border summary">\r\n          <tr class="border-bottom">\r\n            <td height="20">Rechnungs-Nr.</td>\r\n            <td height="20">Titel</td>\r\n            <td height="20">Datum</td>\r\n            <td height="20">Fälligkeit</td>\r\n            <td height="20">Betrag</td>\r\n          </tr>\r\n\r\n{item_doc_start}\r\n    <tr>\r\n      <td class="item-nr">{item_doc_nr}</td>\r\n      <td class="item-title">{item_doc_title}</td>\r\n      <td class="item-created">{item_doc_created}</td>\r\n      <td class="item-deadline">{item_doc_deadline}</td>\r\n      <td class="item-total">{item_doc_total}</td>\r\n   </tr>\r\n{item_doc_end}\r\n\r\n</table>\r\n<br />\r\n<table class="table no-border summary">\r\n  <tr>\r\n    <td class="text-right">Gesamt: <strong>{total}</strong> {currency}</td>\r\n  </tr>\r\n</table>\r\n\r\n<br />\r\nBitte überweisen Sie den noch ausstehenden Betrag von <strong>{total} {currency}</strong> bis zum {deadline} auf eines unserer Konten. Sollten Sie die Rechnung bereits beglichen haben, so danken wir Ihnen und bitten Sie, dieses Schreiben als gegenstandslos zu betrachten.\r\n<br /><br />\r\nMit freundlichen Grüßen\r\n<br /><br />\r\n{user-name}\r\n\r\n<h4 class="footer-title"></h4>\r\n<table class="table no-border">\r\n  <tbody>\r\n    <tr>\r\n      <td style="width: 33%;" valign="top"></td>\r\n      <td style="width: 33%;" valign="top"></td>\r\n      <td style="width: 33%;" valign="top"></td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n</div>', NULL, NULL, 'body{ font-family:Arial,Helvetica,sans-serif;font-size:12px; }\r\n#table-print .pull-right{ float:right !important; }\r\n#table-print .pull-left{ float:left !important; }\r\n\r\n#table-print{}\r\ntable { background-color: transparent; border-collapse: collapse;  border-spacing: 0;width: 100%;margin-bottom:14px; }\r\ntable tr th, table tr td {padding:8px; border-top: 1px solid #ddd; line-height: 1.42857; vertical-align:top;}\r\n\r\n#table-print .table,#table-print .row-fluid{ width:100%; }\r\n\r\n#table-print th,#table-print td{ padding:0;margin:0;vertical-align:top;border:1px solid #ccc;}\r\ntable.no-border,table.no-border td,table.no-border th,table.no-border tr{border:0 none !important;}\r\n\r\n.table-items thead th{background-color:#eee;font-weight:normal;}\r\n.table-items .item-quantity{width:8%;}\r\n.table-items .item-entity{width:6%;}\r\n.table-items .item-title{width:60%;}\r\n.table-items .item-price{width:13%;}\r\n.table-items .item-total{width:13%;}\r\n.slogan { width:100%;text-align:center;padding:10px 0; }\r\nh3.title { padding:10px 0 20px 0;text-align: center;width:100%;margin:0; }\r\nh4.footer-title {margin:20px 0 0 0;padding:0;font-weight:bold;padding:8px;}\r\n\r\n#table-print .summary {margin:0;width:100%;}\r\n#table-print table.summary tr td,#table-print table.summary tr th{padding:7px 6px 0 6px;}\r\ntable.no-border .border-bottom {border-bottom:1px solid #ccc !important;padding:8px 6px;}\r\n.summary-total-entry {font-size:120%;}\r\n\r\n.row-fluid{width:100%;}\r\n.row-fluid > div{width:auto;}\r\n\r\nimg{max-width:260px;}\r\n\r\n.text-right, .item-total,.item-quantity,.item-price{ text-align:right; }\r\n#table-print td.item-quantity{border-right:0 none !important; }\r\n#table-print td.item-entity{border-left:0 none !important;}\r\n.text-center{ text-align:center !important; }\r\n\r\n.clear{clear:both;}', 96, '210mm;297mm', '15;15;10;10', '[]', 0, '0000-00-00 00:00:00', 'de-DE'),
(9, 0, 1, 'documents', 19, 0, 'Mahnung mit Geb&uuml;hr', '<div id="table-print">\r\n\r\n<table class="table no-border">\r\n  <tbody>\r\n  	<tr>\r\n      <td>{address}</td>\r\n      <td class="text-right">{logo}</td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n\r\n<div class="slogan text-center">{slogan}</div>\r\n<h3 class="title">{document-title}</h3>\r\n \r\n<table class="table no-border">\r\n  <tbody>\r\n  	<tr>\r\n      <td rowspan="3">\r\n        <div>{contact-gender} {contact-firstname} {contact-lastname}</div>\r\n        <div>{contact-street}</div>\r\n        <div>{contact-zip} {contact-location}</div>\r\n      </td>\r\n      <td class="text-right" height="15" width="60">Nr:</td>\r\n      <td class="text-right" height="15" width="80">{nr}</td>\r\n    </tr>\r\n    \r\n    <tr>\r\n      <td class="text-right" height="15" width="60">Datum:</td>\r\n      <td class="text-right" height="15" width="80">{created}</td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n<br />\r\nSehr geehrte/r {contact-gender} {contact-lastname},<br /><br />für den unten stehenden Betrag konnten wir noch keinen Zahlungseingang feststellen<br /><br />\r\n\r\n<table class="table no-border summary">\r\n          <tr class="border-bottom">\r\n            <td height="20">Rechnungs-Nr.</td>\r\n            <td height="20">Titel</td>\r\n            <td height="20">Datum</td>\r\n            <td height="20">Fälligkeit</td>\r\n            <td height="20">Betrag</td>\r\n          </tr>\r\n\r\n{item_doc_start}\r\n    <tr>\r\n      <td class="item-nr">{item_doc_nr}</td>\r\n      <td class="item-title">{item_doc_title}</td>\r\n      <td class="item-created">{item_doc_created}</td>\r\n      <td class="item-deadline">{item_doc_deadline}</td>\r\n      <td class="item-total">{item_doc_total}</td>\r\n   </tr>\r\n{item_doc_end}\r\n\r\n</table>\r\n<br />\r\n<table class="table no-border summary">\r\n          <tr class="border-bottom">\r\n            <td height="20" colspan="2">Zusätzlich entstandene Kosten</td>\r\n          </tr>\r\n{item_start}\r\n<tr>\r\n  <td>{item_title}</td>\r\n  <td>{item_total}</td>\r\n</tr>\r\n{item_end}\r\n</table>\r\n<br />\r\n<table class="table no-border summary">\r\n  <tr>\r\n    <td class="text-right">Gesamt: <strong>{total}</strong> {currency}</td>\r\n  </tr>\r\n</table>\r\n\r\n<br />\r\nBitte überweisen Sie den noch ausstehenden Betrag von <strong>{total} {currency}</strong> bis zum {deadline} auf eines unserer Konten. Sollten Sie die Rechnung bereits beglichen haben, so danken wir Ihnen und bitten Sie, dieses Schreiben als gegenstandslos zu betrachten.\r\n<br /><br />\r\nMit freundlichen Grüßen\r\n<br /><br />\r\n{user-name}\r\n\r\n<h4 class="footer-title"></h4>\r\n<table class="table no-border">\r\n  <tbody>\r\n    <tr>\r\n      <td style="width: 33%;" valign="top"></td>\r\n      <td style="width: 33%;" valign="top"></td>\r\n      <td style="width: 33%;" valign="top"></td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n</div>', NULL, NULL, 'body{ font-family:Arial,Helvetica,sans-serif;font-size:12px; }\r\n#table-print .pull-right{ float:right !important; }\r\n#table-print .pull-left{ float:left !important; }\r\n\r\n#table-print{}\r\ntable { background-color: transparent; border-collapse: collapse;  border-spacing: 0;width: 100%;margin-bottom:14px; }\r\ntable tr th, table tr td {padding:8px; border-top: 1px solid #ddd; line-height: 1.42857; vertical-align:top;}\r\n\r\n#table-print .table,#table-print .row-fluid{ width:100%; }\r\n\r\n#table-print th,#table-print td{ padding:0;margin:0;vertical-align:top;border:1px solid #ccc;}\r\ntable.no-border,table.no-border td,table.no-border th,table.no-border tr{border:0 none !important;}\r\n\r\n.table-items thead th{background-color:#eee;font-weight:normal;}\r\n.table-items .item-quantity{width:8%;}\r\n.table-items .item-entity{width:6%;}\r\n.table-items .item-title{width:60%;}\r\n.table-items .item-price{width:13%;}\r\n.table-items .item-total{width:13%;}\r\n.slogan { width:100%;text-align:center;padding:10px 0; }\r\nh3.title { padding:10px 0 20px 0;text-align: center;width:100%;margin:0; }\r\nh4.footer-title {margin:20px 0 0 0;padding:0;font-weight:bold;padding:8px;}\r\n\r\n#table-print .summary {margin:0;width:100%;}\r\n#table-print table.summary tr td,#table-print table.summary tr th{padding:7px 6px 0 6px;}\r\ntable.no-border .border-bottom {border-bottom:1px solid #ccc !important;padding:8px 6px;}\r\n.summary-total-entry {font-size:120%;}\r\n\r\n.row-fluid{width:100%;}\r\n.row-fluid > div{width:auto;}\r\n\r\nimg{max-width:260px;}\r\n\r\n.text-right, .item-total,.item-quantity,.item-price{ text-align:right; }\r\n#table-print td.item-quantity{border-right:0 none !important; }\r\n#table-print td.item-entity{border-left:0 none !important;}\r\n.text-center{ text-align:center !important; }\r\n\r\n.clear{clear:both;}', 96, '210mm;297mm', '15;15;10;10', '[]', 0, '0000-00-00 00:00:00', 'de-DE');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_times` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `business` int(11) unsigned NOT NULL,
  `extension` char(16) NOT NULL DEFAULT 'system',
  `state` int(11) NOT NULL DEFAULT '1',
  `catid` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `location_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL DEFAULT '0',
  `contacts` text NOT NULL,
  `maxContacts` int(8) NOT NULL,
  `startDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `upload` varchar(30) NOT NULL,
  `text` text NOT NULL,
  `access` int(11) NOT NULL,
  `fields` text,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_ext_cat` (`business`,`extension`,`state`,`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__secretary_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business` int(11) NOT NULL DEFAULT '1',
  `extension` char(16) NOT NULL DEFAULT 'system',
  `itemID` int(20) NOT NULL,
  `title` varchar(50) NOT NULL,
  `folder` varchar(128) NOT NULL,
  `created` datetime NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ext` (`business`,`extension`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
