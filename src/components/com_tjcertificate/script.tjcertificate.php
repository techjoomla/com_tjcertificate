<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * TjCertificateInstallerScript class.
 *
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 * @since       1.0
 */
class Com_TjcertificateInstallerScript
{
	/**
	 * Runs after install, update or discover_update
	 *
	 * @param   string      $type    install, update or discover_update
	 * @param   JInstaller  $parent  parent
	 *
	 * @return  boolean
	 */
	public function postflight($type, $parent)
	{
		JLoader::import('components.com_tjcertificate.includes.tjcertificate', JPATH_ADMINISTRATOR);

		$dataBaseModel = TJCERT::model('Database', array('ignore_request' => true));

		try
		{
			$dataBaseModel->allowEditOwnForManagerGroup();
		}
		catch (Exception $e)
		{
			return false;
		}

		$this->installNotificationsTemplates();

		return true;
	}

	/**
	 * Installed Notifications Templates
	 *
	 * @return  void
	 */
	public function installNotificationsTemplates()
	{
		jimport('joomla.application.component.model');
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tables');
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/models');
		$notificationsModel = JModelLegacy::getInstance('Notification', 'TJNotificationsModel');

		$filePath = JPATH_ADMINISTRATOR . '/components/com_tjcertificate/tjcertificateTemplate.json';
		$str = file_get_contents($filePath);
		$json = json_decode($str, true);

		$existingKeys = $notificationsModel->getKeys('com_tjcertificate');

		if (count($json) != 0)
		{
			foreach ($json as $template => $array)
			{
				// If template doesn't exist then we add notification template.
				if (!in_array($array['key'], $existingKeys))
				{
					$notificationsModel->createTemplates($array);
				}
			}
		}
	}
}
