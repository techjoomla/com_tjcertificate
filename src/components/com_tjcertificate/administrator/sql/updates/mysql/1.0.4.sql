ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `is_external` tinyint(1) NOT NULL DEFAULT '0' AFTER `expired_on`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `name` varchar(255) NOT NULL AFTER `is_external`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `issuing_org` varchar(255) NOT NULL AFTER `name`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `cert_url` text NULL AFTER `issuing_org`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `cert_file` varchar(255) NOT NULL AFTER `cert_url`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `access` tinyint(1) NOT NULL DEFAULT '0' AFTER `cert_file`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `created_by` int(11) NOT NULL AFTER `access`;
ALTER TABLE `#__tj_certificate_issue` ADD COLUMN `status` text NULL AFTER `created`;
