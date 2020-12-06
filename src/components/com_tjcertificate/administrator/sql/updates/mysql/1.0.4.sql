ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `is_external` tinyint(1) NOT NULL DEFAULT '0' AFTER `expired_on`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `name` varchar(255) NOT NULL AFTER `is_external`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `issuing_org` varchar(255) NOT NULL AFTER `name`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `cert_url` text NULL AFTER `issuing_org`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `cert_file` varchar(255) NOT NULL AFTER `cert_url`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `access` tinyint(1) NOT NULL DEFAULT '0' AFTER `cert_file`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `created_by` int(11) NOT NULL AFTER `access`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `status` varchar(64) NULL AFTER `created_by`;

--
-- Table structure for table `#__tj_media_files`
--

CREATE TABLE IF NOT EXISTS `#__tj_media_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `type` varchar(250) NOT NULL,
  `path` varchar(250) COLLATE utf8mb4_bin NOT NULL,
  `state` tinyint(1) NOT NULL,
  `source` varchar(250) NOT NULL,
  `original_filename` varchar(250) COLLATE utf8mb4_bin NOT NULL,
  `size` int(11) NOT NULL,
  `storage` varchar(250) NOT NULL,
  `created_by` int(11) NOT NULL,
  `access` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Table structure for table `#__tj_media_files_xref`
--

CREATE TABLE IF NOT EXISTS `#__tj_media_files_xref` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `media_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `client` varchar(250) NOT NULL,
  `is_gallery` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;
