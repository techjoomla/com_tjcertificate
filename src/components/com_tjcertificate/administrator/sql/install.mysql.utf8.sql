CREATE TABLE IF NOT EXISTS `#__tj_certificate_templates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `unique_code` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `template_css` text NULL,
  `client` varchar(100) NOT NULL COMMENT 'e.g. com_jticketing.event, com_tjlms.course',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL,
  `checked_out` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_public` tinyint(1) NOT NULL COMMENT '1-Private, 2-Public',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY unqk_code (`unique_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tj_certificate_issue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `unique_certificate_id` varchar(150) NOT NULL,
  `certificate_template_id` int(11) NOT NULL,
  `generated_body` text NOT NULL,
  `client` varchar(100) NOT NULL,
  `client_id` int(11) NOT NULL,
  `client_issued_to` int(11) NOT NULL,
  `client_issued_to_name` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NULL,
  `state` tinyint(3) NOT NULL,
  `issued_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expired_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_external` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `issuing_org` varchar(255) NOT NULL,
  `cert_url` text NULL,
  `cert_file` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `access` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(64) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY unqk_certificate_id (`unique_certificate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

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
