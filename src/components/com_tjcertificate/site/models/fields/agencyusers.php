<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of allocated agencies
 *
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldAgencyUsers extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    __DEPLOY_VERSION__
	 */
	protected $type = 'users';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getOptions()
	{
		// Initialize array to store dropdown options
		$loggedInuser = Factory::getUser();
		$params = ComponentHelper::getParams('com_tjcertificate');
		$canManageAllAgencyUser = $loggedInuser->authorise('core.manage.all.agency.user', 'com_multiagency');

		$isAgencyEnabled = false;

		if (ComponentHelper::isEnabled('com_multiagency') && $params->get('enable_multiagency'))
		{
			$isAgencyEnabled = true;
		}

		if (!$isAgencyEnabled)
		{
			$db = Factory::getDBO();
			$query = $db->getQuery(true);
			$query->select('distinct(u.id), u.name');
			$query->from($db->quoteName('#__users', 'u'));
			$query->where($db->qn('u.block') . ' = 0');
			$query->order($db->escape('u.name' . ' ' . 'asc'));
			$db->setQuery($query);

			$users = $db->loadObjectList();

			$options = array();

			if ($loggedInuser->authorise('certificate.external.manage', 'com_tjcertificate'))
			{
				$options[] = HTMLHelper::_('select.option', "", Text::_('COM_TJCERTIFICATE_AGENCY_USER_SELECT'));

				foreach ($users as $user)
				{
					$options[] = HTMLHelper::_('select.option', $user->id, $user->name);
				}
			}
			else
			{
				$options[] = HTMLHelper::_('select.option', $loggedInuser->id, $loggedInuser->name);
			}
		}

		return $options;
	}
}
