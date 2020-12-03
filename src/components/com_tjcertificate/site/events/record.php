<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
JLoader::import('components.com_tjcertificate.helpers.mails', JPATH_SITE);

/**
 * Tjcertificate triggers class for record.
 *
 * @since  __DEPLOY_VERSION__
 */
class TjCertificateTriggerRecord
{
	/**
	 * Method acts as a consturctor
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct()
	{
		$app    = JFactory::getApplication();
		$this->menu   = $app->getMenu();
		$this->params = JComponentHelper::getParams('com_tjcertificate');
		$this->siteConfig = JFactory::getConfig();
		$this->sitename = $this->siteConfig->get('sitename');
		$this->user = JFactory::getUser();
		$this->tjnotifications = new Tjnotifications;
		$this->tjCertificateMailsHelper = new TjCertificateMailsHelper;
	}

	/**
	 * Trigger for Record after save
	 *
	 * @param   OBJECT  $recordDetails  Record Details
	 * 
	 * @param   int     $isNew          isNew = true / !isNew = false
	 *
	 * @return  void
	 * 
	 * @since	__DEPLOY_VERSION__
	 */
	public function onAfterRecordSave($recordDetails, $isNew)
	{
		switch ($isNew)
		{
			case true:
					/* Send mail on campaign create */
					$this->tjCertificateMailsHelper->onAfterCreateRecord($recordDetails);
				break;
		}

		return;
	}

	/**
	 * Trigger for Campaign state change
	 *
	 * @param   OBJECT  $recordDetails  Record Details
	 * 
	 * @param   int     $isPublished    isPublished = 1 / !isPublished = 0
	 *
	 * @return  void
	 * 
	 * @since	__DEPLOY_VERSION__
	 */
	public function onRecordStateChange($recordDetails, $isPublished)
	{
		switch ($isPublished)
		{
			case 1:
				/* Send mail on record approved */
				$this->tjCertificateMailsHelper->onAfterRecordStateChange($recordDetails, $isPublished);
				break;

			case 0:
				/* Send mail on record rejected */
				$this->tjCertificateMailsHelper->onAfterRecordStateChange($recordDetails, $isPublished);
				break;
		}

		return;
	}

	/**
	 * Trigger for campaign's Goal amount has reached for first time
	 *
	 * @param   OBJECT  $recordDetails  Record Details
	 *
	 * @return  void
	 * 
	 * @since	__DEPLOY_VERSION__
	 */
	public function onAfterRecordDeleted($recordDetails)
	{
		return $this->tjCertificateMailsHelper->onAfterRecordDeleted($recordDetails);
	}
}
