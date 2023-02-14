
--
-- Daten für Tabelle "#__secretary_businesses"
--

INSERT INTO "#__secretary_businesses" ("id", "asset_id", "state", "home", "title", "slogan", "address", "upload", "access", "defaultNote", "currency", "taxvalue", "taxPrepo", "guv1", "guv2", "selectedFolders", "fields", "createdEntry") VALUES
(1, 0, 1, 1, 'My Business', '', 'Street 123\r\n12345 Stadt\r\nDeutschland', '', 1, '', 'EUR', 19, 2, '["2"]', '', '{"documents":[2,3],"subjects":[4,5,6]}', '[[3,"Bank account","1234567891234567","text"]]', 1441029141);

--
-- Daten für Tabelle "#__secretary_folders"
--

INSERT INTO "#__secretary_folders" ("id", "asset_id", "business", "extension", "parent_id", "ordering", "state", "level", "title", "alias", "description", "fields", "number", "checked_out", "checked_out_time", "created_by", "created_time", "access") VALUES
(2, 0, 1, 'documents', 0, 1, 24, 1, 'COM_SECRETARY_INVOICES', 'COM_SECRETARY_INVOICE', '', '[[13,"COM_SECRETARY_EMAIL_TEMPLATE","6","emailtemplate"],[2,"COM_SECRETARY_TEMPLATE","1","template"],[1,"COM_SECRETARY_PRODUCT_USAGE","1","pUsage"]]', '{CNT start=0}', 0, '2010-01-01', 0, '2010-01-01', 1),
(3, 0, 1, 'documents', 0, 2, 24, 1, 'COM_SECRETARY_QUOTES', 'COM_SECRETARY_QUOTE', '', '[[13,"COM_SECRETARY_EMAIL_TEMPLATE","7","emailtemplate"]]', '', 0, '2010-01-01', 0, '2010-01-01', 1),
(4, 0, 1, 'subjects', 0, 1, 24, 1, 'COM_SECRETARY_CUSTOMERS', 'COM_SECRETARY_CUSTOMER', '', '[]', '', 0, '2010-01-01', 0, '2010-01-01', 1),
(5, 0, 1, 'subjects', 0, 2, 24, 1, 'COM_SECRETARY_EMPLOYEES', 'COM_SECRETARY_EMPLOYEE', '', '[[6,"COM_SECRETARY_BIRTHDAY","1999-01-01","date"]]', '', 0, '2010-01-01', 0, '2010-01-01', 1),
(6, 0, 1, 'subjects', 0, 3, 24, 1, 'COM_SECRETARY_SUPPLIERS', 'COM_SECRETARY_SUPPLIER', '', '[]', '', 0, '2010-01-01', 0, '2010-01-01', 1),
(7, 0, 1, 'documents', 0, 3, 24, 1, 'COM_SECRETARY_REMINDERS', 'COM_SECRETARY_REMINDER', '', '[]', '', 0, '2010-01-01', 0, '2010-01-01', 1);
