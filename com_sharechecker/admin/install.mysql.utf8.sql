ALTER TABLE `#__content`
  ADD `uri` TEXT NOT NULL,
  ADD `uri_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  ADD `uri_check_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  ADD `error_uri` TEXT NOT NULL,
  ADD `undefined_uri` TEXT NOT NULL,
  ADD INDEX (`uri_date`),
  ADD INDEX (`uri_check_date`);