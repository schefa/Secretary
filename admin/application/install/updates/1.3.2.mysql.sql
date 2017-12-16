-- Secretary 1.3.2 (2015-11-08)

ALTER TABLE `#__secretary_fields` ADD UNIQUE(`hard`);

INSERT INTO `#__secretary_fields` (`id`, `extension`, `title`, `description`, `hard`, `type`, `values`, `standard`, `required`) VALUES
(NULL, 'system', 'COM_SECRETARY_GENDER', '', 'anrede', 'list', '["COM_SECRETARY_GENDER_MR","COM_SECRETARY_GENDER_MRS",""]', '2', 0);

UPDATE `#__secretary_subjects` SET `gender` = `gender` - 1;
UPDATE `#__secretary_documents` SET `subject` = replace(`subject`,'["1",','["0",');
UPDATE `#__secretary_documents` SET `subject` = replace(`subject`,'["2",','["1",');
UPDATE `#__secretary_documents` SET `subject` = replace(`subject`,'["3",','["2",');
