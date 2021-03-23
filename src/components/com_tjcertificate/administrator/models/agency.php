<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Component\ComponentHelper;

/**
 * TjCertificate Agency Model.
 *
 * @since  __DEPLOY_VERSION__
 */
class TjCertificateModelAgency extends AdminModel
{
	protected $comMultiAgency = 'com_multiagency';

	public $params;

	public $user;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($config = array())
	{
		$this->params = ComponentHelper::getParams('com_tjcertificate');
		$this->user   = Factory::getuser();

		parent::__construct($config);
	}

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \JForm|boolean  A \JForm object on success, false on failure
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getForm($data = array(), $loadData = true)
	{
		return;
	}

	/**
	 * Function to get users
	 *
	 * @param   integer  $agencyId  agency id
	 * 
	 * @return  Object  Users object
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getUsers($agencyId = null)
	{
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('distinct(u.id), u.name');
		$query->from($db->quoteName('#__users', 'u'));
		$query->where($db->qn('u.block') . ' = 0');

		if (ComponentHelper::isEnabled($this->comMultiAgency) && $this->params->get('enable_multiagency'))
		{
			$query->join('INNER', '#__tj_cluster_nodes AS cn ON cn.user_id = u.id');
			$query->join('INNER', $db->qn('#__tj_clusters', 'clusters') .
					' ON (' . $db->qn('clusters.id') . ' = ' . $db->qn('cn.cluster_id') .
					' AND ' . $db->qn('clusters.client') . " = 'com_multiagency' ) ");
			$query->join('INNER', $db->qn('#__tjmultiagency_multiagency', 'ml') .
					' ON (' . $db->qn('ml.id') . ' = ' . $db->qn('clusters.client_id') . ')');

			$manageOwn = $this->user->authorise('core.manage.own.agency.user', $this->comMultiAgency);
			$manage    = $this->user->authorise('core.manage.all.agency.user', $this->comMultiAgency);

			// If user have only manage own permission and agency is not set then load own agency users
			if ($manageOwn && empty($manage) && empty($agencyId))
			{
				$loggedInUserAgencies = $this->getUserAgencies($this->user->id);

				foreach ($loggedInUserAgencies as $loggedInUserAgency)
				{
					$loggedInUserAgencyArr[] = $loggedInUserAgency->id;
				}

				$query->where($db->quoteName('ml.id') . ' in (' . implode(',', $loggedInUserAgencyArr) . ')');
			}
			elseif ($agencyId)
			{
				$query->where($db->qn('ml.id') . ' = ' . (int) $agencyId);
			}
		}

		$query->order($db->escape('u.name' . ' ' . 'asc'));

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Function to check user agency
	 *
	 * @param   int  $agencyId  agency id
	 * 
	 * @return  integer  The integer of the primary key
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function validateUserAgency($agencyId)
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('u.id'));
		$query->from($db->quoteName('#__users', 'u'));
		$query->join('INNER', $db->quoteName('#__tj_cluster_nodes', 'cn') . ' ON ' . $db->quoteName('cn.user_id') . '=' . $db->quoteName('u.id'));
		$query->join('INNER', $db->quoteName('#__tj_clusters', 'c') . ' ON ' . $db->quoteName('c.id') . '=' . $db->quoteName('cn.cluster_id'));
		$query->join('INNER', $db->quoteName('#__tjmultiagency_multiagency', 'ml') .
			' ON ' . $db->quoteName('ml.id') . ' = ' . $db->quoteName('c.client_id')
			);
		$query->where($db->qn('ml.id') . ' = ' . (int) $agencyId);
		$query->where($db->qn('cn.user_id') . ' = ' . (int) $this->user->id);
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Function to get user agency
	 *
	 * @param   int  $userId  user id
	 * 
	 * @return  integer  The integer of the primary key
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getUserAgencies($userId)
	{
		$db       = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('agency.id'));
		$query->from($db->quoteName('#__tjmultiagency_multiagency', 'agency'));
		$query->join('INNER', $db->quoteName('#__tj_clusters', 'c') . ' ON ' . $db->quoteName('c.client_id') . '=' . $db->quoteName('agency.id'));
		$query->join('INNER', $db->quoteName('#__tj_cluster_nodes', 'cn') . ' ON ' . $db->quoteName('cn.cluster_id') . '=' . $db->quoteName('c.id'));
		$query->Where($db->qn('agency.state') . '=' . 1);
		$query->where($db->quoteName('cn.user_id') . ' = ' . (int) $userId);
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
