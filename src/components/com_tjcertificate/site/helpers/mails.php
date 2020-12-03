<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
jimport('techjoomla.tjnotifications.tjnotifications');

use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Registry\Registry;

/**
 * Class TjCertificateMailsHelper
 *
 * @since  __DEPLOY_VERSION__
 */
class TjCertificateMailsHelper
{
	protected $params;

	protected $siteConfig;

	protected $sitename;

	protected $siteadminname;

	protected $user;

	protected $client;

	protected $tjnotifications;

	protected $siteinfo;

	/**
	 * Method acts as a consturctor
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct()
	{
		$this->params              = ComponentHelper::getParams('com_tjcertificate');
		$this->siteConfig          = Factory::getConfig();
		$this->sitename            = $this->siteConfig->get('sitename');
		$this->siteadminname       = $this->siteConfig->get('fromname');
		$this->user                = Factory::getUser();
		$this->client              = "com_tjcertificate";
		$this->tjnotifications     = new Tjnotifications;

		$this->siteinfo            = new stdClass;
		$this->siteinfo->sitename  = $this->sitename;
		$this->siteinfo->adminname = Text::_('COM_TJCERTIFICATE_SITEADMIN');
	}

	/**
	 * Send mails when record is created
	 *
	 * @param   OBJECT  $recordDetails  Campaigns Detail
	 *
	 * @return void
	 * 
	 * @since	__DEPLOY_VERSION__
	 */
	public function onAfterCreateRecord($recordDetails)
	{
		$adminEmailArray = array();
		$adminEmail      = (!empty($this->params->get('email'))) ? $this->params->get('email') : $this->siteConfig->get('mailfrom');
		$adminEmailArray = explode(',', $adminEmail);
		$userIdArray = $this->getUserIdFromEmail($adminEmailArray);
		$adminRecipients = array(
			'email' => array(
				'to' => $adminEmailArray
			)
		);

		foreach ($userIdArray as $userId)
		{
			array_unshift($adminRecipients, Factory::getUser($userId));
		}

		$userEmailArray = array();

		if ($recordDetails->getUserId())
		{
			$user = Factory::getUser($recordDetails->getUserId());
			$userEmailArray[] = $user->email;
		}

		$recipients   = array('email' => array('to' => $userEmailArray));

		$adminkey   = "createRecordMailToAdmin";
		$userkey    = "createRecordMailToUser";

		$siteInfo           = new stdClass;
		$siteInfo->sitename = $this->sitename;

		$replacements           = new stdClass;
		$replacements->info     = $this->siteinfo;
		$replacements->record   = $recordDetails;
		$replacements->user     = $user;

		$options = new Registry;
		$options->set('record', $recordDetails);

		// Mail to User after record added on behalf

		if ($recordDetails->getUserId() != $recordDetails->created_by)
		{
			$assignUserkey = "assignRecordMailToUser";
			$replacements->assigner = Factory::getUser($recordDetails->created_by);

			$this->tjnotifications->send($this->client, $assignUserkey, $recipients, $replacements, $options);
		}
		else
		{
			// Mail to site admin
			$this->tjnotifications->send($this->client, $adminkey, $adminRecipients, $replacements, $options);

			// Mail to User
			$this->tjnotifications->send($this->client, $userkey, $recipients, $replacements, $options);
		}

		return;
	}

	/**
	 * Send mail when record is edited
	 *
	 * @param   OBJECT  $recordDetails  Record Detail
	 * 
	 * @param   int     $isPublished    Record state
	 *
	 * @return void
	 * 
	 * @since	__DEPLOY_VERSION__
	 */
	public function onAfterRecordStateChange($recordDetails, $isPublished)
	{
		$userEmailArray = array();

		if ($recordDetails->user_id)
		{
			$recordOwner = Factory::getUser($recordDetails->user_id);
			$userEmailArray[] = $recordOwner->email;
		}

		$recipients   = array('email' => array('to' => $userEmailArray));

		$replacements         = new stdClass;
		$replacements->info   = $this->siteinfo;
		$replacements->record = $recordDetails;
		$replacements->user   = $recordOwner;

		$siteinfo = new stdClass;
		$siteinfo->sitename = $this->sitename;

		$options = new Registry;
		$options->set('record', $recordDetails);

		// Mail to record owner
		if ($isPublished == 1)
		{
			$this->tjnotifications->send($this->client, "recordApprovedMailToUser", $recipients, $replacements, $options);
		}
		elseif ($isPublished == 0)
		{
			$this->tjnotifications->send($this->client, "recordRejectedMailToUser", $recipients, $replacements, $options);
		}

		return;
	}

	/**
	 * Send mail when record is deleted
	 *
	 * @param   OBJECT  $recordDetails  record Detail
	 *
	 * @return void
	 * 
	 * @since	__DEPLOY_VERSION__
	 */
	public function onAfterRecordDeleted($recordDetails)
	{
		$adminEmailArray = array();
		$adminEmail      = (!empty($this->params->get('email'))) ? $this->params->get('email') : $this->siteConfig->get('mailfrom');
		$adminEmailArray = explode(',', $adminEmail);
		$userIdArray = $this->getUserIdFromEmail($adminEmailArray);
		$adminRecipients = array(
			'email' => array(
				'to' => $adminEmailArray
			)
		);

		foreach ($userIdArray as $userId)
		{
			array_unshift($adminRecipients, Factory::getUser($userId));
		}

		$userEmailArray = array();

		if ($recordDetails->user_id)
		{
			$recordOwner = Factory::getUser($recordDetails->user_id);
			$userEmailArray[] = $recordOwner->email;
		}

		$recipients   = array('email' => array('to' => $userEmailArray));

		$replacements         = new stdClass;
		$replacements->info   = $this->siteinfo;
		$replacements->record = $recordDetails;
		$replacements->user   = $recordOwner;

		$siteinfo = new stdClass;
		$siteinfo->sitename = $this->sitename;

		$options = new Registry;
		$options->set('record', $recordDetails);

		$this->tjnotifications->send($this->client, "recordDeleteMailToUser", $recipients, $replacements, $options);

		$this->tjnotifications->send($this->client, "recordDeleteMailToAdmin", $adminRecipients, $replacements, $options);

		return;
	}

	/**
	 * Method to create recipient array
	 *
	 * @param   ARRAY  $adminRecipients  Contains email object
	 *
	 * @return  array.
	 *
	 * @since	__DEPLOY_VERSION__
	 */
	public function getUserIdFromEmail($adminRecipients)
	{
		$finalUserIdRecipient = [];

		if (!empty($adminRecipients))
		{
			$db = JFactory::getDbo();

			foreach ($adminRecipients as $adminRecipient)
			{
				$query = $db->getQuery(true)
					->select($db->quoteName('id'))
					->from($db->quoteName('#__users'))
					->where($db->quoteName('email') . ' = ' . $db->quote($adminRecipient));
				$db->setQuery($query);
				$userId = $db->loadResult();

				$finalUserIdRecipient[] = $userId;
			}
		}

		return $finalUserIdRecipient;
	}
}
