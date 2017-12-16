
-- --------------------------------------------------------

DROP TABLE IF EXISTS "#__secretary_accounting" CASCADE;
DROP TABLE IF EXISTS "#__secretary_accounts" CASCADE;
DROP TABLE IF EXISTS "#__secretary_accounts_system" CASCADE;
DROP TABLE IF EXISTS "#__secretary_activities" CASCADE;
DROP TABLE IF EXISTS "#__secretary_businesses" CASCADE;
DROP TABLE IF EXISTS "#__secretary_connections" CASCADE;
DROP TABLE IF EXISTS "#__secretary_currencies" CASCADE;
DROP TABLE IF EXISTS "#__secretary_documents" CASCADE;
DROP TABLE IF EXISTS "#__secretary_entities" CASCADE;
DROP TABLE IF EXISTS "#__secretary_fields" CASCADE;
DROP TABLE IF EXISTS "#__secretary_folders" CASCADE;
DROP TABLE IF EXISTS "#__secretary_locations" CASCADE;
DROP TABLE IF EXISTS "#__secretary_markets" CASCADE;
DROP TABLE IF EXISTS "#__secretary_messages" CASCADE;
DROP TABLE IF EXISTS "#__secretary_newsletter" CASCADE;
DROP TABLE IF EXISTS "#__secretary_products" CASCADE;
DROP TABLE IF EXISTS "#__secretary_repetition" CASCADE;
DROP TABLE IF EXISTS "#__secretary_settings" CASCADE;
DROP TABLE IF EXISTS "#__secretary_status" CASCADE;
DROP TABLE IF EXISTS "#__secretary_subjects" CASCADE;
DROP TABLE IF EXISTS "#__secretary_tasks" CASCADE;
DROP TABLE IF EXISTS "#__secretary_times" CASCADE;
DROP TABLE IF EXISTS "#__secretary_uploads" CASCADE;
DROP TABLE IF EXISTS "#__secretary_templates" CASCADE;

/*
Table structure for table 'public.#__secretary_accounting'
*/

CREATE TABLE "#__secretary_accounting" (
	"id" SERIAL NOT NULL,
	"entry_id" INTEGER NOT NULL,
	"business" INTEGER NOT NULL,
	"currency" VARCHAR(20) ,
	"created_by" INTEGER NOT NULL,
	"created" TIMESTAMP,
	"title" VARCHAR(123)  NOT NULL,
	"state" INTEGER DEFAULT 26 NOT NULL,
	"soll" VARCHAR(255)  NOT NULL,
	"haben" VARCHAR(255)  NOT NULL,
	"total" DECIMAL(15,4) NOT NULL,
	"upload" VARCHAR(30) NULL,
	"fields" TEXT NULL
);
CREATE INDEX "idx__secretary_accounting_business_state" ON "#__secretary_accounting"("business", "state");

/*
Table structure for table 'public.#__secretary_accounts'
*/

CREATE TABLE "#__secretary_accounts" (
	"id" SERIAL NOT NULL,
	"business" INTEGER NOT NULL,
	"year" INTEGER NOT NULL,
	"kid" INTEGER NOT NULL,
	"budget" DECIMAL(15,4) NOT NULL,
	"soll" DECIMAL(15,4) NULL,
	"haben" DECIMAL(15,4) NULL,
	"history" TEXT NULL,
	"fields" TEXT NULL
);
CREATE INDEX "idx__secretary_accounts_business_year" ON "#__secretary_accounts"("business", "year");
CREATE INDEX "idx__secretary_accounts_kid" ON "#__secretary_accounts"("kid");

/*
Table structure for table 'public.#__secretary_accounts_system'
*/

CREATE TABLE "#__secretary_accounts_system" (
	"id" SERIAL NOT NULL,
	"parent_id" BIGINT DEFAULT 0 NOT NULL,
	"level" BIGINT DEFAULT 0 NOT NULL,
	"ordering" BIGINT DEFAULT 0 NOT NULL,
	"nr" VARCHAR(30)  NOT NULL,
	"title" VARCHAR(500)  NOT NULL,
	"description" TEXT,
	"type" VARCHAR(32)  DEFAULT '0' NOT NULL,
	"locked" SMALLINT DEFAULT 0 NOT NULL,
	"fields" TEXT NULL
);
CREATE INDEX "idx__secretary_accounts_system_state" ON "#__secretary_accounts_system"("type");
CREATE INDEX "idx__secretary_accounts_system_parent_id" ON "#__secretary_accounts_system"("parent_id");

/*
Table structure for table 'public.#__secretary_activities'
*/

CREATE TABLE "#__secretary_activities" (
	"id" SERIAL NOT NULL,
	"extension" VARCHAR(48)  DEFAULT 'system' NOT NULL,
	"business" BIGINT NOT NULL,
	"catid" INTEGER DEFAULT 0 NOT NULL,
	"itemID" INTEGER NOT NULL,
	"action" VARCHAR(255)  NOT NULL,
	"created" TIMESTAMP,
	"created_by" INTEGER NOT NULL
);
CREATE INDEX "idx__secretary_activities_business_ext" ON "#__secretary_activities"("business", "extension");

/*
Table structure for table 'public.#__secretary_businesses'
*/

CREATE TABLE "#__secretary_businesses" (
	"id" SERIAL NOT NULL,
	"asset_id" BIGINT DEFAULT 0 NOT NULL,
	"state" INTEGER DEFAULT 1 NOT NULL,
	"home" BIGINT NOT NULL,
	"title" TEXT NOT NULL,
	"slogan" VARCHAR(255) ,
	"address" TEXT,
	"upload" VARCHAR(30) NULL,
	"access" BIGINT NOT NULL,
	"defaultNote" TEXT NOT NULL,
	"currency" VARCHAR(50)  NOT NULL,
	"taxvalue" DECIMAL(4,2) NOT NULL,
	"taxPrepo" INTEGER NOT NULL,
	"guv1" VARCHAR(255) NULL,
	"guv2" VARCHAR(255) NULL,
	"selectedFolders" VARCHAR(255)  NOT NULL,
	"fields" TEXT,
	"createdEntry" INTEGER DEFAULT 0 NOT NULL,
	"created_by" INTEGER DEFAULT 0 NOT NULL,
	"checked_out" INTEGER DEFAULT 0 NOT NULL,
	"checked_out_time" TIMESTAMP
);
CREATE INDEX "idx__secretary_businesses_home" ON "#__secretary_businesses"("home");

/*
Table structure for table 'public.#__secretary_connections'
*/

CREATE TABLE "#__secretary_connections" (
	"extension" VARCHAR(16)  DEFAULT 'system' NOT NULL,
	"one" INTEGER NOT NULL,
	"two" INTEGER NOT NULL,
	"note" TEXT NOT NULL
);
CREATE INDEX "idx__secretary_connections" ON "#__secretary_connections"("extension", "one", "two");

/*
Table structure for table 'public.#__secretary_currencies'
*/

CREATE TABLE "#__secretary_currencies" (
	"id" SERIAL NOT NULL,
	"currency" VARCHAR(9)  DEFAULT 'EUR' NOT NULL,
	"title" VARCHAR(255)  DEFAULT 'Euro',
	"symbol" VARCHAR(10)  DEFAULT 'â‚¬'
);

/*
Dumping data for table 'public.#__secretary_currencies'
*/

INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (1, 'AUD', 'Australian dollar', 'A$');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (2, 'BGN', 'Lev', '??.');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (3, 'BRL', 'Real', 'R$');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (4, 'CAD', 'Canadian dollar', 'C$');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (5, 'CHF', 'Swiss franc', 'CHF');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (6, 'CNY', 'Yuan renminbi', '¥');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (7, 'CZK', 'Koruna', 'Kc');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (8, 'DKK', 'Krone', 'kr');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (9, 'EUR', 'Euro', '€');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (10, 'GBP', 'Pound sterling', '£');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (11, 'HKD', 'Hong Kong dollar', 'HK$');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (12, 'HRK', 'Kuna', 'kn');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (13, 'HUF', 'Forint', 'Ft');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (14, 'IDR', 'Rupiah', 'Rp');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (15, 'ILS', 'Shekel', '?');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (16, 'INR', 'Rupee', '?');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (17, 'JPY', 'Yen', '¥');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (18, 'KRW', 'Won', '?');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (19, 'MXN', 'Peso', 'MX$');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (20, 'MYR', 'Ringgit', 'RM');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (21, 'NZD', 'New Zealand dollar', 'NZ$');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (22, 'PHP', 'Peso', '?');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (23, 'PLN', 'Zloty', 'zl');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (24, 'RON', 'Leu', 'lei');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (25, 'RUB', 'Rouble', '???');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (26, 'SEK', 'Krona', 'kr');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (27, 'SGD', 'Singapore dollar', 'SG$');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (28, 'THB', 'Baht', '?');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (29, 'TRY', 'Lira', '?');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (30, 'USD', 'US dollar', '$');
INSERT INTO "#__secretary_currencies"("id", "currency", "title", "symbol") VALUES (31, 'ZAR', 'Rand', 'R');

/*
Table structure for table 'public.#__secretary_documents'
*/

CREATE TABLE "#__secretary_documents" (
	"id" SERIAL NOT NULL,
	"asset_id" INTEGER DEFAULT 0 NOT NULL,
	"business" BIGINT NOT NULL,
	"office" INTEGER DEFAULT 0 NOT NULL,
	"catid" INTEGER DEFAULT 0 NULL,
	"state" INTEGER DEFAULT 1 NOT NULL,
	"nr" VARCHAR(90)  NOT NULL,
	"title" VARCHAR(255)  NOT NULL,
	"subject" VARCHAR(255) ,
	"subjectid" INTEGER NOT NULL,
	"checked_out" INTEGER DEFAULT 0 NOT NULL,
	"checked_out_time" TIMESTAMP,
	"createdEntry" INTEGER DEFAULT 0 NOT NULL,
	"created_by" INTEGER NOT NULL,
	"created" DATE,
	"deadline" DATE,
	"paid" DECIMAL(15,4),
	"items" TEXT,
	"currency" VARCHAR(10) ,
	"upload" VARCHAR(30) NULL,
	"text" TEXT NOT NULL,
	"total" DECIMAL(15,4) NOT NULL,
	"subtotal" DECIMAL(15,4) NOT NULL,
	"taxtotal" VARCHAR(255) NULL,
	"taxtype" INTEGER DEFAULT 1 NOT NULL,
	"rabatt" DECIMAL(10,4) NOT NULL,
	"accounting_id" INTEGER DEFAULT 0 NOT NULL,
	"template" INTEGER,
	"fields" TEXT
);
CREATE INDEX "idx__secretary_documents_created_catid" ON "#__secretary_documents"("business", "created", "catid");
CREATE INDEX "idx__secretary_documents_catid_state" ON "#__secretary_documents"("business", "catid", "state");
CREATE INDEX "idx__secretary_documents_subjectid" ON "#__secretary_documents"("business", "subjectid");


/*
Table structure for table 'public.#__secretary_entities'
*/

CREATE TABLE "#__secretary_entities" (
	"id" SERIAL NOT NULL,
	"title" VARCHAR(50) NOT NULL,
	"description" VARCHAR(255) NULL
);
CREATE INDEX "idx__secretary_entities_title" ON "#__secretary_entities"("title");

/*
Table structure for table 'public.#__secretary_fields'
*/

CREATE TABLE "#__secretary_fields" (
	"id" SERIAL NOT NULL,
	"extension" VARCHAR(48)  DEFAULT 'system' NOT NULL,
	"title" VARCHAR(255) ,
	"description" TEXT NOT NULL,
	"hard" VARCHAR(128)  NOT NULL,
	"type" VARCHAR(96)  NOT NULL,
	"values" TEXT,
	"standard" TEXT,
	"required" SMALLINT DEFAULT '0' NOT NULL
);
CREATE INDEX "idx__secretary_fields_ext" ON "#__secretary_fields"("extension");

INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (1, 'documents', 'COM_SECRETARY_PRODUCT_USAGE', 'COM_SECRETARY_PRODUCT_USAGE_DESC', 'pUsage', 'list', '["JNO","COM_SECRETARY_PRODUCTS_USAGE_VERBRAUCH","COM_SECRETARY_PRODUCTS_USAGE_EINKAUF"]', '1', 1);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (2, 'documents', 'COM_SECRETARY_TEMPLATE', 'COM_SECRETARY_TEMPLATE_DESC', 'template', 'sql', 'SELECT * FROM `#__secretary_templates`', '1', 1);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (3, 'system', 'COM_SECRETARY_FIELD_TEXT', 'COM_SECRETARY_FIELD_TEXT_DESC', 'text', 'text', '', '', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (4, 'system', 'COM_SECRETARY_FIELD_NUMBER', 'COM_SECRETARY_FIELD_NUMBER_DESC', 'number', 'number', '', '0', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (5, 'system', 'COM_SECRETARY_FIELD_LIST', 'COM_SECRETARY_FIELD_LIST_DESC', 'list', 'list', '', '0', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (6, 'system', 'COM_SECRETARY_FIELD_DATE', 'COM_SECRETARY_FIELD_DATE_DESC', 'date', 'date', '0000-00-00', '2010-06-01', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (7, 'system', 'COM_SECRETARY_FIELD_COLOR', 'COM_SECRETARY_FIELD_COLOR_DESC', 'color', 'color', '', '', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (8, 'system', 'COM_SECRETARY_FIELD_URL', 'COM_SECRETARY_FIELD_URL_DESC', 'url', 'url', '', 'http://secretary.schefa.com', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (9, 'system', 'COM_SECRETARY_FIELD_TEXTAREA', 'COM_SECRETARY_FIELD_TEXTAREA_DESC', 'textarea', 'textarea', '', '', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (10, 'times', 'COM_SECRETARY_FIELD_TIMECOLOR', 'COM_SECRETARY_FIELD_TIMECOLOR_DESC', 'timeColor', 'color', '', '', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (11, 'newsletters', 'COM_SECRETARY_NEWSLETTER', 'COM_SECRETARY_NEWSLETTER_DESC', 'newsletter', 'sql', 'SELECT * FROM `#__secretary_folders` WHERE `extension` = ''newsletters''', '0', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (12, 'system', 'COM_SECRETARY_GENDER', '', 'anrede', 'list', '["COM_SECRETARY_GENDER_MR","COM_SECRETARY_GENDER_MRS",""]', '2', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (13, 'documents', 'COM_SECRETARY_EMAIL_TEMPLATE', 'COM_SECRETARY_EMAIL_TEMPLATE_DESC', 'emailtemplate', 'sql', 'SELECT * FROM `#__secretary_templates`', '1', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (14, 'documents', 'COM_SECRETARY_FIELD_SOLL', '', 'docsSoll', 'search', 'accounts_system', '0', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (15, 'documents', 'COM_SECRETARY_FIELD_HABEN', '', 'docsHaben', 'search', 'accounts_system', '0', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (16, 'documents', 'COM_SECRETARY_FIELD_SOLL_TAX', '', 'docsSollTax', 'accounts_tax', '19', '0', 0);
INSERT INTO "#__secretary_fields"("id", "extension", "title", "description", "hard", "type", "values", "standard", "required") VALUES (17, 'documents', 'COM_SECRETARY_FIELD_HABEN_TAX', '', 'docsHabenTax', 'accounts_tax', '19', '0', 0);

/*
Table structure for table 'public.#__secretary_folders'
*/

CREATE TABLE "#__secretary_folders" (
	"id" SERIAL NOT NULL,
	"asset_id" BIGINT DEFAULT 0 NOT NULL,
	"business" INTEGER NOT NULL,
	"extension" VARCHAR(48)  DEFAULT 'system' NOT NULL,
	"parent_id" BIGINT DEFAULT 0 NOT NULL,
	"ordering" INTEGER DEFAULT 0 NOT NULL,
	"state" INTEGER DEFAULT 1 NOT NULL,
	"level" BIGINT DEFAULT 0 NOT NULL,
	"title" VARCHAR(255)  NOT NULL,
	"alias" TEXT NOT NULL,
	"description" TEXT NULL,
	"fields" TEXT,
	"number" VARCHAR(32)  NOT NULL,
	"checked_out" BIGINT DEFAULT 0 NOT NULL,
	"checked_out_time" TIMESTAMP,
	"created_by" BIGINT DEFAULT 0 NOT NULL,
	"created_time" TIMESTAMP,
	"access" BIGINT DEFAULT 1 NOT NULL
);
CREATE INDEX "cat__secretary_folders_idx" ON "#__secretary_folders"("business", "extension", "state");
CREATE INDEX "idx__secretary_folders_ordering" ON "#__secretary_folders"("parent_id", "ordering");
CREATE INDEX "idx__secretary_folders_checkout" ON "#__secretary_folders"("checked_out");
CREATE INDEX "idx__secretary_folders_alias" ON "#__secretary_folders"("alias");

INSERT INTO "#__secretary_folders"("id", "asset_id", "business", "extension", "parent_id", "ordering", "state", "level", "title", "alias", "description", "fields", "number", "checked_out", "checked_out_time", "created_by", "created_time", "access") 
VALUES (1, 0, 0, 'system', 0, 1, 0, 0, 'ROOT', 0, '', '', '', 0, NULL, 601, '2011-01-01 00:00:01', 1);

/*
Table structure for table 'public.#__secretary_locations'
*/

CREATE TABLE "#__secretary_locations" (
	"id" SERIAL NOT NULL,
	"asset_id" INTEGER DEFAULT 0 NOT NULL,
	"business" INTEGER DEFAULT 0 NOT NULL,
	"state" INTEGER DEFAULT 0 NOT NULL,
	"extension" VARCHAR(48)  DEFAULT 'system' NOT NULL,
	"catid" INTEGER DEFAULT 0 NOT NULL,
	"title" VARCHAR(255) ,
	"description" TEXT,
	"street" VARCHAR(255) ,
	"zip" VARCHAR(11) ,
	"location" VARCHAR(255) ,
	"country" VARCHAR(64) ,
	"lat" DECIMAL(10,6) NOT NULL,
	"lng" DECIMAL(10,6) NOT NULL,
	"currency" VARCHAR(10) ,
	"upload" VARCHAR(30) ,
	"fields" TEXT,
	"created_by" INTEGER DEFAULT 0 NOT NULL,
	"checked_out" INTEGER DEFAULT 0 NOT NULL,
	"checked_out_time" TIMESTAMP
);
CREATE INDEX "idx_business_ext_catid" ON "#__secretary_locations"("business", "extension", "catid");

CREATE TABLE "#__secretary_markets" (
	"id" SERIAL NOT NULL,
	"catid" INTEGER DEFAULT 0 NOT NULL,
	"state" INTEGER DEFAULT 0 NOT NULL,
	"symbol" VARCHAR(12)  NOT NULL,
	"name" VARCHAR(255)  NOT NULL,
	"exch" VARCHAR(20)  NOT NULL,
	"exchType" VARCHAR(3)  NOT NULL,
	"quantity" INTEGER NOT NULL,
	"ek_price" DECIMAL(10,4) NOT NULL,
	"created" DATE
);

/*
Table structure for table 'public.#__secretary_messages'
*/

CREATE TABLE "#__secretary_messages" (
	"id" SERIAL NOT NULL,
	"business" INTEGER NOT NULL,
	"catid" INTEGER DEFAULT 0 NOT NULL,
	"refer_to" INTEGER DEFAULT 0 NOT NULL,
	"state" INTEGER DEFAULT 0 NOT NULL,
	"created" TIMESTAMP,
	"created_by" VARCHAR(255)  DEFAULT '0' NOT NULL,
	"created_by_alias" VARCHAR(255) ,
	"contact_to" BIGINT DEFAULT 0 NOT NULL,
	"contact_to_alias" VARCHAR(255) ,
	"priority" SMALLINT DEFAULT '0' NOT NULL,
	"subject" VARCHAR(255)  NOT NULL,
	"message" TEXT NOT NULL,
	"upload" VARCHAR(30) NULL,
	"template" INTEGER,
	"fields" TEXT
);
CREATE INDEX "idx__secretary_messages_business_catid" ON "#__secretary_messages"("business", "catid", "state");
CREATE INDEX "idx__secretary_messages_contactto_state" ON "#__secretary_messages"("business", "contact_to", "state");

/*
Table structure for table 'public.#__secretary_newsletter'
*/

CREATE TABLE "#__secretary_newsletter" (
	"listID" INTEGER NOT NULL,
	"contactID" INTEGER NOT NULL
);
CREATE INDEX "idx__secretary_newsletter_list" ON "#__secretary_newsletter"("listID");

/*
Table structure for table 'public.#__secretary_products'
*/

CREATE TABLE "#__secretary_products" (
	"id" SERIAL NOT NULL,
	"asset_id" INTEGER DEFAULT 0 NOT NULL,
	"business" BIGINT NOT NULL,
	"location" INTEGER DEFAULT 0 NOT NULL,
	"catid" INTEGER DEFAULT 0 NOT NULL,
	"state" INTEGER DEFAULT 1 NOT NULL,
	"year" INTEGER NOT NULL,
	"nr" VARCHAR(63)  NOT NULL,
	"title" TEXT NOT NULL,
	"description" TEXT NOT NULL,
	"upload" VARCHAR(30) NULL,
	"entity" VARCHAR(90) NOT NULL,
	"items" TEXT NULL,
	"history" TEXT,
	"contacts" TEXT,
	"taxRate" REAL NOT NULL,
	"priceCost" DECIMAL(15,4) NULL,
	"priceSale" DECIMAL(15,4) NULL,
	"quantityBought" DECIMAL(15,4) NULL,
	"quantityMax" DECIMAL(15,4) NULL,
	"quantityMin" DECIMAL(15,4) NULL,
	"quantity" DECIMAL(15,4) NULL,
	"totalBought" DECIMAL(15,4) NULL,
	"total" DECIMAL(15,4) NULL,
	"fields" TEXT,
	"template" INTEGER DEFAULT 0 NOT NULL,
	"created_by" INTEGER DEFAULT 0 NOT NULL,
	"checked_out" INTEGER  DEFAULT 0 NOT NULL,
	"checked_out_time" TIMESTAMP
);
CREATE INDEX "idx__secretary_products_cat" ON "#__secretary_products"("business", "year", "catid");

/*
Table structure for table 'public.#__secretary_repetition'
*/

CREATE TABLE "#__secretary_repetition" (
	"id" SERIAL NOT NULL,
	"extension" VARCHAR(48)  DEFAULT 'system' NOT NULL,
	"time_id" INTEGER NOT NULL,
	"startTime" INTEGER DEFAULT 0 NOT NULL,
	"nextTime" INTEGER DEFAULT 0 NOT NULL,
	"endTime" INTEGER NOT NULL,
	"intervall" INTEGER DEFAULT 0 NOT NULL,
	"int_in_words" VARCHAR(45)  NOT NULL
);
CREATE INDEX "idx__secretary_repetition_ext" ON "#__secretary_repetition"("extension");

/*
Table structure for table 'public.#__secretary_settings'
*/

CREATE TABLE "#__secretary_settings" (
	"id" SERIAL NOT NULL,
	"params" TEXT NOT NULL,
	"rules" TEXT NOT NULL
);
INSERT INTO "#__secretary_settings"("id", "params", "rules") VALUES (1, '{"templateColor":"white","pdf":"0","entitySelect":"0","numberformat":"0","currencyformat":"0","accessMissingNote":"0","cache":"1","gMapsAPI":"","gMapsContacts":"1","gMapsLocations":"1","activityCreated":"1","activityEdited":"0","activityDeleted":"1","documentExt":"jpeg,jpg,png,gif,pdf,doc,docx,odt","documentSize":"2500000","products_columns":["priceCost","quantityBought","totalBought","priceSale","quantity","total"],"contacts_columns":["category","street","zip","location"],"documents_frontend":"0","filterList":"1","messages_unread":"9","messages_chat":"1","messages_waitMsg":"3000","messages_waitPing":"10000"}', '');

/*
Table structure for table 'public.#__secretary_status'
*/

CREATE TABLE "#__secretary_status" (
	"id" SERIAL NOT NULL,
	"extension" VARCHAR(48)  DEFAULT 'system' NOT NULL,
	"ordering" INTEGER DEFAULT 0 NOT NULL,
	"title" VARCHAR(255)  NOT NULL,
	"description" VARCHAR(255)  NOT NULL,
	"closeTask" INTEGER NOT NULL,
	"class" VARCHAR(48)  NOT NULL,
	"icon" VARCHAR(48)  NOT NULL
);
CREATE INDEX "idx__secretary_status_ext" ON "#__secretary_status"("extension");
 
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (1, 'system', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 2, 'unpublish', 'circle-thin');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (2, 'system', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 1, 'publish', 'check-circle');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (3, 'system', 0, 'COM_SECRETARY_STATUS_ARCHIVED', 'COM_SECRETARY_STATUS_ARCHIVED_DESC', 1, '', 'archive');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (4, 'system', 0, 'COM_SECRETARY_STATUS_TRASH', 'COM_SECRETARY_STATUS_TRASH_DESC', 1, 'trash', 'trash');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (5, 'documents', 0, 'COM_SECRETARY_STATUS_OPEN', 'COM_SECRETARY_STATUS_DONE_ITEM', 6, 'unpaid', 'check-circle');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (6, 'documents', 0, 'COM_SECRETARY_STATUS_DONE', 'COM_SECRETARY_STATUS_OPEN_ITEM', 5, 'paid', 'circle-thin');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (7, 'documents', 0, 'COM_SECRETARY_STATUS_PENDING', 'COM_SECRETARY_STATUS_PENDING_ITEM', 5, 'paidso', 'coffee');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (8, 'documents', 0, 'COM_SECRETARY_STATUS_STORNO', 'COM_SECRETARY_STATUS_STORNO_ITEM', 6, 'canceled', 'trash');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (9, 'messages', 0, 'COM_SECRETARY_MESSAGES_OPTION_UNREAD', 'COM_SECRETARY_MESSAGES_MARK_AS_READ', 11, 'unpublish', 'circle-thin');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (10, 'messages', 0, 'JTRASHED', 'COM_SECRETARY_MESSAGES_MARK_AS_UNREAD', 9, 'unpublish', 'trash');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (11, 'messages', 0, 'COM_SECRETARY_MESSAGES_OPTION_READ', 'COM_SECRETARY_MESSAGES_MARK_AS_UNREAD', 9, 'publish', 'check-circle');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (12, 'subjects', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 13, 'unpublish', 'circle-thin');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (13, 'subjects', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 12, 'publish', 'check-circle');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (14, 'products', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 15, 'unpublish', 'circle-thin');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (15, 'products', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 14, 'publish', 'check-circle');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (16, 'businesses', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 17, 'unpublish', 'circle-thin');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (17, 'businesses', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 16, 'publish', 'check-circle');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (18, 'templates', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 19, 'unpublish', 'circle-thin');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (19, 'templates', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 18, 'publish', 'check-circle');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (20, 'times', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 21, 'unpublish', 'circle-thin');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (21, 'times', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 20, 'publish', 'check-circle');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (22, 'times', 0, 'COM_SECRETARY_STATUS_ARCHIVED', 'COM_SECRETARY_STATUS_ARCHIVED_DESC', 20, '', 'archive');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (23, 'folders', 0, 'COM_SECRETARY_STATUS_UNPUBLISHED', 'COM_SECRETARY_STATUS_UNPUBLISHED_DESC', 24, 'unpublish', 'circle-thin');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (24, 'folders', 0, 'COM_SECRETARY_STATUS_PUBLISHED', 'COM_SECRETARY_STATUS_PUBLISHED_DESC', 23, 'publish', 'check-circle');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (25, 'accountings', 0, 'COM_SECRETARY_BUCHEN_LOCKED', 'COM_SECRETARY_BUCHEN_LOCKED_DESC', 26, 'locked', 'check-circle');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (26, 'accountings', 0, 'COM_SECRETARY_BUCHEN_PLANED', 'COM_SECRETARY_BUCHEN_PLANED_DESC', 25, 'pending', 'coffee');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (27, 'accountings', 0, 'COM_SECRETARY_BUCHEN_DONE', 'COM_SECRETARY_BUCHEN_DONE_DESC', 26, 'done', 'circle-thin');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (28, 'accountings', 0, 'COM_SECRETARY_STORNIERT_PLANED', 'COM_SECRETARY_STORNIERT_PLANED_DESC', 29, 'canceled-pending', 'coffee');
INSERT INTO "#__secretary_status"("id", "extension", "ordering", "title", "description", "closeTask", "class", "icon") VALUES (29, 'accountings', 0, 'COM_SECRETARY_STORNIERT', 'COM_SECRETARY_STORNIERT_DESC', 28, 'canceled', 'trash');

/*
Table structure for table 'public.#__secretary_subjects'
*/

CREATE TABLE "#__secretary_subjects" (
	"id" SERIAL NOT NULL,
	"asset_id" INTEGER  DEFAULT 0 NOT NULL,
	"business" INTEGER NOT NULL,
	"state" INTEGER DEFAULT 1 NOT NULL,
	"catid" INTEGER DEFAULT 0 NOT NULL,
	"number" VARCHAR(31) ,
	"gender" INTEGER DEFAULT 0 NULL,
	"firstname" VARCHAR(255)  NOT NULL,
	"lastname" VARCHAR(255)  NOT NULL,
	"street" VARCHAR(255)  NOT NULL,
	"zip" VARCHAR(11)  NOT NULL,
	"location" VARCHAR(255) NULL,
	"country" VARCHAR(64) NULL,
	"phone" VARCHAR(30) ,
	"email" VARCHAR(255) ,
	"lat" DECIMAL(10,6) NULL,
	"lng" DECIMAL(10,6) NULL,
	"upload" VARCHAR(30) NULL,
	"created_by" INTEGER DEFAULT 0 NOT NULL,
	"created" DATE,
	"checked_out" INTEGER DEFAULT 0 NOT NULL,
	"checked_out_time" TIMESTAMP,
	"modified" TIMESTAMP,
	"connections" TEXT,
	"fields" TEXT,
	"template" INTEGER DEFAULT 0 NOT NULL
);
CREATE INDEX "idx__secretary_subjects_catid_state" ON "#__secretary_subjects"("business", "catid", "state");
CREATE INDEX "idx__secretary_subjects_zip" ON "#__secretary_subjects"("business", "zip");

/*
Table structure for table 'public.#__secretary_tasks'
*/

CREATE TABLE "#__secretary_tasks" (
	"id" SERIAL NOT NULL,
	"asset_id" INTEGER DEFAULT 0 NOT NULL,
	"business" BIGINT NOT NULL,
	"projectID" INTEGER NOT NULL,
	"parentID" INTEGER DEFAULT 0 NOT NULL,
	"level" SMALLINT DEFAULT 0 NOT NULL,
	"state" INTEGER DEFAULT 1 NOT NULL,
	"ordering" INTEGER DEFAULT 0 NOT NULL,
	"title" VARCHAR(255)  NOT NULL,
	"progress" DECIMAL(4,1) DEFAULT 0 NOT NULL,
	"contacts" TEXT NULL,
	"startDate" TIMESTAMP,
	"endDate" TIMESTAMP,
	"calctime" INTEGER DEFAULT 0 NOT NULL,
	"totaltime" INTEGER DEFAULT 0 NOT NULL,
	"upload" VARCHAR(255) NULL,
	"text" TEXT NULL,
	"fields" TEXT
);
CREATE INDEX "idx__secretary_tasks_project" ON "#__secretary_tasks"("business", "projectID", "parentID", "state");

/*
Table structure for table 'public.#__secretary_templates'
*/

CREATE TABLE "#__secretary_templates" (
	"id" SERIAL NOT NULL,
	"asset_id" BIGINT DEFAULT 0 NOT NULL,
	"business" BIGINT NOT NULL,
	"extension" VARCHAR(48)  DEFAULT 'system' NOT NULL,
	"state" INTEGER DEFAULT 1 NOT NULL,
	"catid" INTEGER DEFAULT 0 NOT NULL,
	"title" VARCHAR(50)  NOT NULL,
	"text" TEXT NOT NULL,
	"header" TEXT,
	"footer" TEXT,
	"css" TEXT NOT NULL,
	"dpi" SMALLINT DEFAULT 96 NOT NULL,
	"format" VARCHAR(16)  DEFAULT '210mm;297mm' NOT NULL,
	"margins" VARCHAR(256)  DEFAULT '15;15;10;10' NOT NULL,
	"fields" TEXT,
	"checked_out" INTEGER DEFAULT 0 NOT NULL,
	"checked_out_time" TIMESTAMP,
	"language" VARCHAR(21)  DEFAULT '*' NOT NULL
);
CREATE INDEX "idx__secretary_templates_ext" ON "#__secretary_templates"("business", "extension", "state", "catid");

/*
Dumping data for table 'public.#__secretary_templates'
*/

INSERT INTO "#__secretary_templates"("id", "asset_id", "business", "extension", "state", "catid", "title", "text", "header", "footer", "css", "dpi", "format", "margins", "fields", "checked_out", "checked_out_time", "language") VALUES (1, 0, 1, 'documents', 19, 0, 'COM_SECRETARY_INVOICE', '<div id="table-print">

<table class="table no-border">
  <tbody>
  	<tr>
      <td>{address}</td>
      <td class="text-right">{logo}</td>
    </tr>
  </tbody>
</table>

<div class="slogan text-center">{slogan}</div>
<h3 class="title">{document-title}</h3>
  
<table class="table no-border">
  <tbody>
  	<tr>
      <td rowspan="3">
        <div>{contact-gender} {contact-firstname} {contact-lastname}</div>
        <div>{contact-street}</div>
        <div>{contact-zip} {contact-location}</div>
      </td>
      <td class="text-right" height="15" width="60">Nr:</td>
      <td class="text-right" height="15" width="80">{nr}</td>
    </tr>
    
    <tr>
      <td class="text-right" height="15" width="60">Datum:</td>
      <td class="text-right" height="15" width="80">{created}</td>
    </tr>
    
    <tr>
      <td colspan="2"> </td>
    </tr>
    
    <tr>
      <td colspan="2">  <div class="auftrag">Ihr Auftrag: {title}</div> </td>
    </tr>
    
  </tbody>
</table>
  
<table class="table table-items">
  <thead>
    <tr>
      <th colspan="2">Anzahl</th>
      <th>Bezeichnung</th>
      <th>Einzelpreis</th>
      <th>Gesamtpreis</th>
    </tr>
  </thead>
  
  <tbody>
    {item_start}
    <tr>
      <td class="item-quantity">{item_quantity}</td>
      <td class="item-entity">{item_entity}</td>
      <td class="item-title">{item_title}<br />{item_desc}</td>
      <td class="item-price">{item_price}</td>
      <td class="item-total">{item_total}</td>
   </tr>
    {item_end}
  </tbody>
  
</table>

<table class="table no-border">
  <tbody>
  <tr>
	<td>{note}</td>

	<td class="text-right">
	<table class="table no-border summary">
		<tbody>
          
          <tr class="border-bottom">
            <td height="20" class="border-bottom">Gesamt</td>
            <td height="20" width="100" class="border-bottom text-right">{total} {currency}</td>
          </tr>

          <tr>
            <td>Netto</td>
            <td class="text-right">{subtotal} {currency}</td>
          </tr>

         {taxtotal_start}
         <tr>
            <td>{taxtotal_percent}% Mwst</td>
            <td class="text-right">{taxtotal_value}</td>
         </tr>
        {taxtotal_end}

        </tbody>
	</table>
	</td>
  </tr>
  </tbody>
</table>

<h4 class="footer-title"></h4>
<table class="table no-border">
  <tbody>
    <tr>
      <td style="width: 33%;" valign="top"></td>
      <td style="width: 33%;" valign="top"></td>
      <td style="width: 33%;" valign="top"></td>
    </tr>
  </tbody>
</table>
</div>', '', '', 'body{ font-family:Arial,Helvetica,sans-serif;font-size:12px; }
#table-print .pull-right{ float:right !important; }
#table-print .pull-left{ float:left !important; }

#table-print{}
table { background-color: transparent; border-collapse: collapse;  border-spacing: 0;width: 100%;margin-bottom:14px; }
table tr th, table tr td {padding:8px; border-top: 1px solid #ddd; line-height: 1.42857; vertical-align:top;}

#table-print .table,#table-print .row-fluid{ width:100%; }

#table-print th,#table-print td{ padding:0;margin:0;vertical-align:top;border:1px solid #ccc;}
table.no-border,table.no-border td,table.no-border th,table.no-border tr{border:0 none !important;}

.table-items thead th{background-color:#eee;font-weight:normal;}
.table-items .item-quantity{width:8%;}
.table-items .item-entity{width:6%;}
.table-items .item-title{width:60%;}
.table-items .item-price{width:13%;}
.table-items .item-total{width:13%;}
.slogan { width:100%;text-align:center;padding:10px 0; }
h3.title { padding:10px 0 20px 0;text-align: center;width:100%;margin:0; }
h4.footer-title {margin:20px 0 0 0;padding:0;font-weight:bold;padding:8px;}

#table-print .summary {margin:0;width:100%;}
#table-print table.summary tr td,#table-print table.summary tr th{padding:7px 6px 0 6px;}
table.no-border .border-bottom {border-bottom:1px solid #ccc !important;padding:8px 6px;}
.summary-total-entry {font-size:120%;}

.row-fluid{width:100%;}
.row-fluid > div{width:auto;}

img{max-width:260px;}

.text-right, .item-total,.item-quantity,.item-price{ text-align:right; }
#table-print td.item-quantity{border-right:0 none !important; }
#table-print td.item-entity{border-left:0 none !important;}
.text-center{ text-align:center !important; }

.clear{clear:both;}', 96, '210mm;297mm', '15;15;10;10', '', 0, NULL, 'de-DE');
INSERT INTO "#__secretary_templates"("id", "asset_id", "business", "extension", "state", "catid", "title", "text", "header", "footer", "css", "dpi", "format", "margins", "fields", "checked_out", "checked_out_time", "language") VALUES (2, 0, 1, 'documents', 19, 0, 'COM_SECRETARY_QUOTE', '<div id="table-print">
<table class="table no-border">
  <tbody>
  	<tr>
      <td>{address}</td>
      <td class="text-right">{logo}</td>
    </tr>
  </tbody>
</table>

  <div class="slogan text-center">{slogan}</div>
  
  <h3 class="title">{document-title}</h3>
  
<table class="table no-border">
  <tbody>
  	<tr>
      <td width="100">Angebot für</td>
      <td>
        {contact-gender} {contact-firstname} {contact-lastname}, {contact-street}, {contact-zip} {contact-location}
      </td>
      <td class="text-right" height="15" width="60">Datum:</td>
      <td class="text-right" height="15" width="80">{created}</td>
    </tr>
    {title}
    
    <tr>
      <td>Bauvorhaben</td>
      <td>{title}</td>
      <td> </td>
      <td> </td>
    </tr>
    
  </tbody>
</table>

<table class="table table-items">
  
  <thead>
    <tr>
      <th colspan="2">Anzahl</th>
      <th>Bezeichnung</th>
      <th>Einzelpreis</th>
      <th>Gesamtpreis</th>
    </tr>
  </thead>
  
  <tbody>
    {item_start}
    <tr>
      <td class="item-quantity">{item_quantity}</td>
      <td class="item-entity">{item_entity}</td>
      <td class="item-title">{item_title}<br />{item_desc}</td>
      <td class="item-price">{item_price}</td>
      <td class="item-total">{item_total}</td>
   </tr>
    {item_end}
  </tbody>
  
</table>

<table class="table no-border summary">
  <tbody>
  <tr>
	<td>Vielen Dank für Ihre Anfrage.
        <br />Für Rückfragen stehen wir Ihnen gerne zur Verfügung
        <br /><br />Mit freundlichen Grüßen
        <br /><br /><br /><br />
    </td>

	<td class="text-right">
	<table class="table no-border">
		<tbody>
          
          <tr class="border-bottom">
            <td height="20" class="border-bottom">Gesamt</td>
            <td height="20" width="100" class="border-bottom text-right">{total} {currency}</td>
          </tr>

          <tr>
            <td>Netto</td>
            <td class="text-right">{subtotal} {currency}</td>
          </tr>

         {taxtotal_start}
         <tr>
            <td>{taxtotal_percent}% Mwst</td>
            <td class="text-right">{taxtotal_value}</td>
         </tr>
        {taxtotal_end}

        </tbody>
	</table>
	</td>
  
  </tr>
  </tbody>
</table>
  
  
  
<h4 class="footer-title"></h4>

<table class="table no-border">
  <tbody>
    <tr>
      <td style="width: 33%;"></td>
      <td style="width: 33%;"></td>
      <td style="width: 33%;" valign="top"></td>
    </tr>
  </tbody>
</table>
</div>', '', '', 'body{
	font-family:Arial,Helvetica,sans-serif;font-size:12px;
}
#table-print{}
table { background-color: transparent; border-collapse: collapse;  border-spacing: 0;width: 100%;margin-bottom:14px; }
.table tr th, .table tr td { border-top: 1px solid #ddd; line-height: 1.42857;padding: 8px;  vertical-align:top;}

#table-print .table,#table-print .row-fluid{ width:100%; }

#table-print th,#table-print td{ padding:0;margin:0;vertical-align:top;border:1px solid #ccc;}
table.no-border,table.no-border td,table.no-border th,table.no-border tr{border:0 none !important;}

.table-items thead th{background-color:#eee;font-weight:normal;}
.table-items .item-quantity{width:8%;}
.table-items .item-entity{width:6%;}
.table-items .item-title{width:60%;}
.table-items .item-price{width:13%;}
.table-items .item-total{width:13%;}
.slogan { width:100%;text-align:center;padding:10px 0; }
h3.title { padding:10px 0 20px 0;text-align: center;width:100%;margin:0; }
h4.footer-title {margin:20px 0 0 0;padding:0;font-weight:bold;padding:8px;}

#table-print .summary {margin:0;width:100%;}
#table-print table.summary tr td,#table-print table.summary tr th{padding:7px 6px 0 6px;}
table.no-border .border-bottom {border-bottom:1px solid #ccc !important;padding:8px 6px;}
.summary-total-entry {font-size:120%;}

.row-fluid{width:100%;}
.row-fluid > div{width:auto;}
.pull-right{ float:right !important; }
.pull-left{ float:left !important; }

img{max-width:260px;}

.text-right, .item-total,.item-quantity,.item-price{ text-align:right; }
#table-print td.item-quantity{border-right:0 none !important; }
#table-print td.item-entity{border-left:0 none !important;}
.text-center{ text-align:center !important; }

.clear{clear:both;}', 96, '210mm;297mm', '15;15;10;10', '', 0, NULL, 'de-DE');
INSERT INTO "#__secretary_templates"("id", "asset_id", "business", "extension", "state", "catid", "title", "text", "header", "footer", "css", "dpi", "format", "margins", "fields", "checked_out", "checked_out_time", "language") VALUES (3, 0, 1, 'documents', 19, 0, 'COM_SECRETARY_INVOICE_W_DISCOUNT', '<div id="table-print">
<table class="table no-border">
  <tbody>
  	<tr>
      <td>{address}</td>
      <td class="text-right">{logo}</td>
    </tr>
  </tbody>
</table>

<div class="slogan text-center">{slogan}</div>
  
<h3 class="title">{document-title}</h3>
  
<table class="table no-border">
  <tbody>
  	<tr>
      <td rowspan="3">
        <div>{contact-gender} {contact-firstname} {contact-lastname}</div>
        <div>{contact-street}</div>
        <div>{contact-zip} {contact-location}</div>
      </td>
      <td class="text-right" height="15" width="60">Nr:</td>
      <td class="text-right" height="15" width="80">{nr}</td>
    </tr>
    <tr>
      <td class="text-right" height="15" width="60">Datum:</td>
      <td class="text-right" height="15" width="80">{created}</td>
    </tr>
    <tr>
      <td colspan="2"> </td>
    </tr>
    <tr>
      <td colspan="2"> <div class="auftrag">Ihr Auftrag: {title}</div> </td>
    </tr>
    
  </tbody>
</table>
  
<table class="table table-items">
  
  <thead>
    <tr>
      <th colspan="2">Anzahl</th>
      <th>Bezeichnung</th>
      <th>Einzelpreis</th>
      <th>Gesamtpreis</th>
    </tr>
  </thead>
  
  <tbody>
    {item_start}
    <tr>
      <td class="item-quantity">{item_quantity}</td>
      <td class="item-entity">{item_entity}</td>
      <td class="item-title">{item_title}<br />{item_desc}</td>
      <td class="item-price">{item_price}</td>
      <td class="item-total">{item_total}</td>
   </tr>
    {item_end}
  </tbody>
  
</table>

<table class="table no-border">
  <tbody>
  <tr>
	<td>{note}</td>

	<td class="text-right">
	<table class="table no-border summary">
		<tbody>
          
          <tr>
            <td>Rabatt</td>
            <td class="text-right">- {discount} {currency}</td>
          </tr>
          
          <tr class="border-bottom">
            <td height="20" class="border-bottom">Gesamt</td>
            <td height="20" width="100" class="border-bottom text-right">{total} {currency}</td>
          </tr>

          <tr>
            <td>Netto</td>
            <td class="text-right">{subtotal} {currency}</td>
          </tr>

         {taxtotal_start}
         <tr>
            <td>{taxtotal_percent}% Mwst</td>
            <td class="text-right">{taxtotal_value}</td>
         </tr>
        {taxtotal_end}

        </tbody>
	</table>
	</td>
  
  </tr>
  </tbody>
</table>

<h4 class="footer-title"></h4>
<table class="table no-border">
  <tbody>
    <tr>
      <td style="width: 33%;" valign="top"></td>
      <td style="width: 33%;" valign="top"></td>
      <td style="width: 33%;" valign="top"></td>
    </tr>
  </tbody>
</table>
  
</div>', '', '', 'body{ font-family:Arial,Helvetica,sans-serif;font-size:12px; }
#table-print .pull-right{ float:right !important; }
#table-print .pull-left{ float:left !important; }

#table-print{}
table { background-color: transparent; border-collapse: collapse;  border-spacing: 0;width: 100%;margin-bottom:14px; }
table tr th, table tr td {padding:8px; border-top: 1px solid #ddd; line-height: 1.42857; vertical-align:top;}

#table-print .table,#table-print .row-fluid{ width:100%; }

#table-print th,#table-print td{ padding:0;margin:0;vertical-align:top;border:1px solid #ccc;}
table.no-border,table.no-border td,table.no-border th,table.no-border tr{border:0 none !important;}

.table-items thead th{background-color:#eee;font-weight:normal;}
.table-items .item-quantity{width:8%;}
.table-items .item-entity{width:6%;}
.table-items .item-title{width:60%;}
.table-items .item-price{width:13%;}
.table-items .item-total{width:13%;}
.slogan { width:100%;text-align:center;padding:10px 0; }
h3.title { padding:10px 0 20px 0;text-align: center;width:100%;margin:0; }
h4.footer-title {margin:20px 0 0 0;padding:0;font-weight:bold;padding:8px;}

#table-print .summary {margin:0;width:100%;}
#table-print table.summary tr td,#table-print table.summary tr th{padding:7px 6px 0 6px;}
table.no-border .border-bottom {border-bottom:1px solid #ccc !important;padding:8px 6px;}
.summary-total-entry {font-size:120%;}

.row-fluid{width:100%;}
.row-fluid > div{width:auto;}

img{max-width:260px;}

.text-right, .item-total,.item-quantity,.item-price{ text-align:right; }
#table-print td.item-quantity{border-right:0 none !important; }
#table-print td.item-entity{border-left:0 none !important;}
.text-center{ text-align:center !important; }

.clear{clear:both;}', 96, '210mm;297mm', '15;15;10;10', '', 0, NULL, 'de-DE');
INSERT INTO "#__secretary_templates"("id", "asset_id", "business", "extension", "state", "catid", "title", "text", "header", "footer", "css", "dpi", "format", "margins", "fields", "checked_out", "checked_out_time", "language") VALUES (4, 0, 1, 'messages', 1, 0, 'COM_SECRETARY_CORRESPONDENCE', '<table style="height: 24px; width: 100%;">
<tbody>
<tr>
<td> <span style="text-align: left;">{address}</span></td>
<td style="text-align: right;"> {logo}</td>
</tr>
</tbody>
</table>
<p> </p>
<p> </p>
<p>Anrede</p>
<p>Anschrift</p>
<p style="text-align: right;">{created}</p>
<p style="text-align: center;"><span style="text-align: center;"><strong>{title}</strong></span></p>
<p> </p>
<p>Text</p>
<p> </p>
<p>mit freundlichen Grüßen</p>
<p> </p>
<p> </p>', '', '', '', 96, '210mm;297mm', '15;15;10;10', '', 0, NULL, 'de-DE');
INSERT INTO "#__secretary_templates"("id", "asset_id", "business", "extension", "state", "catid", "title", "text", "header", "footer", "css", "dpi", "format", "margins", "fields", "checked_out", "checked_out_time", "language") VALUES (5, 0, 0, 'messages', 19, 0, 'Kontakt Formular', '<h2>Kontakt</h2>

<div class="contact">
  <table>
    <tr>
    	<td>{contact-category-title}</td>
    </tr>
    <tr>
    	<td>{contact-firstname} {contact-lastname}</td>
    </tr>
    <tr>
    	<td>{contact-street}</td>
    </tr>
    <tr>
    	<td>{contact-zip} {contact-location}</td>
    </tr>
  </table>
</div>

<hr />

{form-start}
<div class="form">
  <table>
    <tr>
    	<td>{form-standard-name-label title=Ihr Name}</td>
    	<td>{form-standard-name}</td>
    </tr>
    <tr>
    	<td>{form-standard-email-label title=Email}</td>
    	<td>{form-standard-email}</td>
    </tr>
    <tr>
    	<td>Ihre Telefonnummer</td>
    	<td>{form-field-phone}</td>
    </tr>
    <tr>
    	<td>{form-standard-subject-label title=Betreff}</td>
    	<td>{form-standard-subject}</td>
    </tr>
    <tr>
    	<td valign="top">{form-standard-text-label title=Nachricht}</td>
    	<td>{form-standard-text}</td>
    </tr>
    <tr>
    	<td>Kopie an mich</td>
    	<td>{form-standard-copy}</td>
    </tr>
    <tr>
    	<td></td>
    	<td>{form-standard-send}</td>
    </tr>
  </table>
</div>
{form-end}
', '', '', '', 96, '210mm;297mm', '15;15;10;10', '[[3,"Phone","","text"]]', 0, NULL, 'de-DE');
INSERT INTO "#__secretary_templates"("id", "asset_id", "business", "extension", "state", "catid", "title", "text", "header", "footer", "css", "dpi", "format", "margins", "fields", "checked_out", "checked_out_time", "language") VALUES (6, 0, 1, 'documents', 19, 0, 'Ihre {document-title} vom {created}', 'Guten Tag {contact-gender} {contact-lastname},<br><br>anbei übersende ich Ihnen Ihre {document-title} vom {created}.<br><br>Ihre {document-title} liegt im PDF-Format vor. Um die {document-title} zu lesen oder auszudrucken, benötigen Sie das Programm Acrobat Reader von Adobe, welches Sie kostenlos über diesen Link herunterladen können: https://get.adobe.com/de/reader/<br><br>mit freundlichen Grüßen<br><br>{user-name}<br>{address}', '', '', '', 96, '210mm;297mm', '15;15;10;10', '', 0, NULL, 'de-DE');
INSERT INTO "#__secretary_templates"("id", "asset_id", "business", "extension", "state", "catid", "title", "text", "header", "footer", "css", "dpi", "format", "margins", "fields", "checked_out", "checked_out_time", "language") VALUES (7, 0, 1, 'documents', 19, 0, '{document-title} vom {created}', 'Guten Tag {contact-gender} {contact-lastname},<br><br>anbei übersende ich Ihnen ein {document-title} für Ihren Auftrag: {title}.<br><br>Das {document-title} liegt im PDF-Format vor. Um es zu lesen oder auszudrucken, benötigen Sie das Programm Acrobat Reader von Adobe, welches Sie kostenlos über diesen Link herunterladen können: https://get.adobe.com/de/reader/<br><br>mit freundlichen Grüßen<br><br>{user-name}<br>{address}', '', '', '', 96, '210mm;297mm', '15;15;10;10', '', 0, NULL, 'de-DE');
INSERT INTO "#__secretary_templates"("id", "asset_id", "business", "extension", "state", "catid", "title", "text", "header", "footer", "css", "dpi", "format", "margins", "fields", "checked_out", "checked_out_time", "language") VALUES (8, 0, 1, 'documents', 19, 0, 'Mahnung', '<div id="table-print">

<table class="table no-border">
  <tbody>
  	<tr>
      <td>{address}</td>
      <td class="text-right">{logo}</td>
    </tr>
  </tbody>
</table>

<div class="slogan text-center">{slogan}</div>
<h3 class="title">{document-title}</h3>
 
<table class="table no-border">
  <tbody>
  	<tr>
      <td rowspan="3">
        <div>{contact-gender} {contact-firstname} {contact-lastname}</div>
        <div>{contact-street}</div>
        <div>{contact-zip} {contact-location}</div>
      </td>
      <td class="text-right" height="15" width="60">Nr:</td>
      <td class="text-right" height="15" width="80">{nr}</td>
    </tr>
    
    <tr>
      <td class="text-right" height="15" width="60">Datum:</td>
      <td class="text-right" height="15" width="80">{created}</td>
    </tr>
  </tbody>
</table>
<br />
Sehr geehrte/r {contact-gender} {contact-lastname},<br /><br />für den unten stehenden Betrag konnten wir noch keinen Zahlungseingang feststellen<br /><br />

<table class="table no-border summary">
          <tr class="border-bottom">
            <td height="20">Rechnungs-Nr.</td>
            <td height="20">Titel</td>
            <td height="20">Datum</td>
            <td height="20">Fälligkeit</td>
            <td height="20">Betrag</td>
          </tr>

{item_doc_start}
    <tr>
      <td class="item-nr">{item_doc_nr}</td>
      <td class="item-title">{item_doc_title}</td>
      <td class="item-created">{item_doc_created}</td>
      <td class="item-deadline">{item_doc_deadline}</td>
      <td class="item-total">{item_doc_total}</td>
   </tr>
{item_doc_end}

</table>
<br />
<table class="table no-border summary">
  <tr>
    <td class="text-right">Gesamt: <strong>{total}</strong> {currency}</td>
  </tr>
</table>

<br />
Bitte überweisen Sie den noch ausstehenden Betrag von <strong>{total} {currency}</strong> bis zum {deadline} auf eines unserer Konten. Sollten Sie die Rechnung bereits beglichen haben, so danken wir Ihnen und bitten Sie, dieses Schreiben als gegenstandslos zu betrachten.
<br /><br />
Mit freundlichen Grüßen
<br /><br />
{user-name}

<h4 class="footer-title"></h4>
<table class="table no-border">
  <tbody>
    <tr>
      <td style="width: 33%;" valign="top"></td>
      <td style="width: 33%;" valign="top"></td>
      <td style="width: 33%;" valign="top"></td>
    </tr>
  </tbody>
</table>
</div>', '', '', 'body{ font-family:Arial,Helvetica,sans-serif;font-size:12px; }
#table-print .pull-right{ float:right !important; }
#table-print .pull-left{ float:left !important; }

#table-print{}
table { background-color: transparent; border-collapse: collapse;  border-spacing: 0;width: 100%;margin-bottom:14px; }
table tr th, table tr td {padding:8px; border-top: 1px solid #ddd; line-height: 1.42857; vertical-align:top;}

#table-print .table,#table-print .row-fluid{ width:100%; }

#table-print th,#table-print td{ padding:0;margin:0;vertical-align:top;border:1px solid #ccc;}
table.no-border,table.no-border td,table.no-border th,table.no-border tr{border:0 none !important;}

.table-items thead th{background-color:#eee;font-weight:normal;}
.table-items .item-quantity{width:8%;}
.table-items .item-entity{width:6%;}
.table-items .item-title{width:60%;}
.table-items .item-price{width:13%;}
.table-items .item-total{width:13%;}
.slogan { width:100%;text-align:center;padding:10px 0; }
h3.title { padding:10px 0 20px 0;text-align: center;width:100%;margin:0; }
h4.footer-title {margin:20px 0 0 0;padding:0;font-weight:bold;padding:8px;}

#table-print .summary {margin:0;width:100%;}
#table-print table.summary tr td,#table-print table.summary tr th{padding:7px 6px 0 6px;}
table.no-border .border-bottom {border-bottom:1px solid #ccc !important;padding:8px 6px;}
.summary-total-entry {font-size:120%;}

.row-fluid{width:100%;}
.row-fluid > div{width:auto;}

img{max-width:260px;}

.text-right, .item-total,.item-quantity,.item-price{ text-align:right; }
#table-print td.item-quantity{border-right:0 none !important; }
#table-print td.item-entity{border-left:0 none !important;}
.text-center{ text-align:center !important; }

.clear{clear:both;}', 96, '210mm;297mm', '15;15;10;10', '[]', 0, NULL, 'de-DE');
INSERT INTO "#__secretary_templates"("id", "asset_id", "business", "extension", "state", "catid", "title", "text", "header", "footer", "css", "dpi", "format", "margins", "fields", "checked_out", "checked_out_time", "language") VALUES (9, 0, 1, 'documents', 19, 0, 'Mahnung mit Geb&uuml;hr', '<div id="table-print">

<table class="table no-border">
  <tbody>
  	<tr>
      <td>{address}</td>
      <td class="text-right">{logo}</td>
    </tr>
  </tbody>
</table>

<div class="slogan text-center">{slogan}</div>
<h3 class="title">{document-title}</h3>
 
<table class="table no-border">
  <tbody>
  	<tr>
      <td rowspan="3">
        <div>{contact-gender} {contact-firstname} {contact-lastname}</div>
        <div>{contact-street}</div>
        <div>{contact-zip} {contact-location}</div>
      </td>
      <td class="text-right" height="15" width="60">Nr:</td>
      <td class="text-right" height="15" width="80">{nr}</td>
    </tr>
    
    <tr>
      <td class="text-right" height="15" width="60">Datum:</td>
      <td class="text-right" height="15" width="80">{created}</td>
    </tr>
  </tbody>
</table>
<br />
Sehr geehrte/r {contact-gender} {contact-lastname},<br /><br />für den unten stehenden Betrag konnten wir noch keinen Zahlungseingang feststellen<br /><br />

<table class="table no-border summary">
          <tr class="border-bottom">
            <td height="20">Rechnungs-Nr.</td>
            <td height="20">Titel</td>
            <td height="20">Datum</td>
            <td height="20">Fälligkeit</td>
            <td height="20">Betrag</td>
          </tr>

{item_doc_start}
    <tr>
      <td class="item-nr">{item_doc_nr}</td>
      <td class="item-title">{item_doc_title}</td>
      <td class="item-created">{item_doc_created}</td>
      <td class="item-deadline">{item_doc_deadline}</td>
      <td class="item-total">{item_doc_total}</td>
   </tr>
{item_doc_end}

</table>
<br />
<table class="table no-border summary">
          <tr class="border-bottom">
            <td height="20" colspan="2">Zusätzlich entstandene Kosten</td>
          </tr>
{item_start}
<tr>
  <td>{item_title}</td>
  <td>{item_total}</td>
</tr>
{item_end}
</table>
<br />
<table class="table no-border summary">
  <tr>
    <td class="text-right">Gesamt: <strong>{total}</strong> {currency}</td>
  </tr>
</table>

<br />
Bitte überweisen Sie den noch ausstehenden Betrag von <strong>{total} {currency}</strong> bis zum {deadline} auf eines unserer Konten. Sollten Sie die Rechnung bereits beglichen haben, so danken wir Ihnen und bitten Sie, dieses Schreiben als gegenstandslos zu betrachten.
<br /><br />
Mit freundlichen Grüßen
<br /><br />
{user-name}

<h4 class="footer-title"></h4>
<table class="table no-border">
  <tbody>
    <tr>
      <td style="width: 33%;" valign="top"></td>
      <td style="width: 33%;" valign="top"></td>
      <td style="width: 33%;" valign="top"></td>
    </tr>
  </tbody>
</table>
</div>', '', '', 'body{ font-family:Arial,Helvetica,sans-serif;font-size:12px; }
#table-print .pull-right{ float:right !important; }
#table-print .pull-left{ float:left !important; }

#table-print{}
table { background-color: transparent; border-collapse: collapse;  border-spacing: 0;width: 100%;margin-bottom:14px; }
table tr th, table tr td {padding:8px; border-top: 1px solid #ddd; line-height: 1.42857; vertical-align:top;}

#table-print .table,#table-print .row-fluid{ width:100%; }

#table-print th,#table-print td{ padding:0;margin:0;vertical-align:top;border:1px solid #ccc;}
table.no-border,table.no-border td,table.no-border th,table.no-border tr{border:0 none !important;}

.table-items thead th{background-color:#eee;font-weight:normal;}
.table-items .item-quantity{width:8%;}
.table-items .item-entity{width:6%;}
.table-items .item-title{width:60%;}
.table-items .item-price{width:13%;}
.table-items .item-total{width:13%;}
.slogan { width:100%;text-align:center;padding:10px 0; }
h3.title { padding:10px 0 20px 0;text-align: center;width:100%;margin:0; }
h4.footer-title {margin:20px 0 0 0;padding:0;font-weight:bold;padding:8px;}

#table-print .summary {margin:0;width:100%;}
#table-print table.summary tr td,#table-print table.summary tr th{padding:7px 6px 0 6px;}
table.no-border .border-bottom {border-bottom:1px solid #ccc !important;padding:8px 6px;}
.summary-total-entry {font-size:120%;}

.row-fluid{width:100%;}
.row-fluid > div{width:auto;}

img{max-width:260px;}

.text-right, .item-total,.item-quantity,.item-price{ text-align:right; }
#table-print td.item-quantity{border-right:0 none !important; }
#table-print td.item-entity{border-left:0 none !important;}
.text-center{ text-align:center !important; }

.clear{clear:both;}', 96, '210mm;297mm', '15;15;10;10', '[]', 0, NULL, 'de-DE');

/*
Table structure for table 'public.#__secretary_times'
*/

CREATE TABLE "#__secretary_times" (
	"id" SERIAL NOT NULL,
	"asset_id" INTEGER DEFAULT 0 NOT NULL,
	"business" BIGINT NOT NULL,
	"extension" VARCHAR(48)  DEFAULT 'system' NOT NULL,
	"state" INTEGER DEFAULT 1 NOT NULL,
	"catid" INTEGER NOT NULL,
	"ordering" INTEGER NOT NULL,
	"title" VARCHAR(255)  NOT NULL,
	"location_id" INTEGER NOT NULL,
	"document_id" INTEGER DEFAULT 0 NOT NULL,
	"contacts" TEXT NOT NULL,
	"maxContacts" INTEGER NULL,
	"startDate" TIMESTAMP,
	"endDate" TIMESTAMP,
	"created_by" INTEGER NOT NULL,
	"created" TIMESTAMP,
	"upload" VARCHAR(30) NULL,
	"text" TEXT NOT NULL,
	"access" INTEGER NOT NULL,
	"fields" TEXT,
	"checked_out" INTEGER DEFAULT 0 NOT NULL,
	"checked_out_time" TIMESTAMP
);
CREATE INDEX "idx__secretary_times_ext_cat" ON "#__secretary_times"("business", "extension", "state", "catid");

/*
Table structure for table 'public.#__secretary_uploads'
*/

CREATE TABLE "#__secretary_uploads" (
	"id" SERIAL NOT NULL,
	"business" INTEGER DEFAULT 1 NOT NULL,
	"extension" VARCHAR(48)  DEFAULT 'system' NOT NULL,
	"itemID" INTEGER NOT NULL,
	"title" VARCHAR(50)  NOT NULL,
	"folder" VARCHAR(128)  NOT NULL,
	"created" TIMESTAMP,
	"description" TEXT NULL
);
CREATE INDEX "idx__secretary_uploads_ext" ON "#__secretary_uploads"("business", "extension");

