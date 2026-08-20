-- Secretary 4.0.3

-- #__secretary_times backs events/projects/locations; each extension only renders and
-- submits a subset of these fields (e.g. maxContacts is events-only, access is
-- locations-only), so any NOT NULL column here without a database default fails to
-- save for the extensions that don't render it.
ALTER TABLE `#__secretary_times` MODIFY `location_id` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_times` MODIFY `catid` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_times` MODIFY `maxContacts` int(8) NOT NULL DEFAULT '0';
ALTER TABLE `#__secretary_times` MODIFY `access` int(11) NOT NULL DEFAULT '1';
