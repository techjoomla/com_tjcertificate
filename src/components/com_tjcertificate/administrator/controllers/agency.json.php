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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * The Tj Certificate Agency controller
 *
 * @since  __DEPLOY_VERSION__
 */
class TjCertificateControllerAgency extends FormController
{
	/**
	 * Function to get agency users
	 *
	 * @return  void|boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getAgencyUsers()
	{
		$app = Factory::getApplication();

		if (!Session::checkToken())
		{
			$app->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
			echo new JsonResponse(null, null, true);
			$app->close();
		}

		// Get the current user id
		$user = Factory::getuser();

		if (!$user->id)
		{
			return false;
		}

		if (!$user->authorise('core.manage.own.agency.user', 'com_multiagency'))
		{
			return false;
		}

		$agencyId = $app->input->get('agency_id', 0, 'INT');

		if ($agencyId)
		{
			if (!$user->authorise('core.manage.all.agency.user', 'com_multiagency'))
			{
				$result = $this->checkUserAgency($agencyId);

				if (!$result)
				{
					echo new JsonResponse(null, Text::_('JERROR_ERROR'), true);
					$app->close();
				}
			}
		}

		$userOptions = array();

		// Initialize array to store dropdown options
		$userOptions[] = HTMLHelper::_('select.option', "", Text::_('COM_TJCERTIFICATE_AGENCY_USER_SELECT'));

		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('distinct(u.id), u.name');
		$query->from($db->quoteName('#__users', 'u'));

		if ($agencyId)
		{
			$query->join('INNER', '#__tj_cluster_nodes AS cn ON cn.user_id = u.id');
			$query->join('INNER', $db->qn('#__tj_clusters', 'clusters') .
					' ON (' . $db->qn('clusters.id') . ' = ' . $db->qn('cn.cluster_id') .
					' AND ' . $db->qn('clusters.client') . " = 'com_multiagency' ) ");
			$query->join('INNER', $db->qn('#__tjmultiagency_multiagency', 'ml') .
					' ON (' . $db->qn('ml.id') . ' = ' . $db->qn('clusters.client_id') . ')');
			$query->where($db->qn('ml.id') . ' = ' . (int) $agencyId);
		}

		$query->order($db->escape('u.name' . ' ' . 'asc'));

		$db->setQuery($query);

		$users = $db->loadObjectList();

		if (!empty($users))
		{
			foreach ($users as $user)
			{
				$userOptions[] = HTMLHelper::_('select.option', $user->id, trim($user->name));
			}
		}

		echo new JsonResponse($userOptions);
		$app->close();
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
	private function checkUserAgency($agencyId)
	{
		$db    = Factory::getDBO();
		$user  = Factory::getuser();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('u.id'));
		$query->from($db->quoteName('#__users', 'u'));
		$query->join('INNER', $db->quoteName('#__tj_cluster_nodes', 'cn') . ' ON ' . $db->quoteName('cn.user_id') . '=' . $db->quoteName('u.id'));
		$query->join('INNER', $db->quoteName('#__tj_clusters', 'c') . ' ON ' . $db->quoteName('c.id') . '=' . $db->quoteName('cn.cluster_id'));
		$query->join('INNER', $db->quoteName('#__tjmultiagency_multiagency', 'ml') .
			' ON ' . $db->quoteName('ml.id') . ' = ' . $db->quoteName('c.client_id')
			);
		$query->where($db->qn('ml.id') . ' = ' . (int) $agencyId);
		$query->where($db->qn('cn.user_id') . ' = ' . (int) $user->id);
		$db->setQuery($query);

		return $db->loadResult();
	}
}
