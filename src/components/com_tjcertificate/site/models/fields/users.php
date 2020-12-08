<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Supports an HTML select list of allocated agencies
 *
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldUsers extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.0.0
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

		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('distinct(id), name');
		$query->from($db->quoteName('#__users'));
		$query->where($db->qn('block') . ' = 0');
		$query->order($db->escape('name' . ' ' . 'asc'));
		$db->setQuery($query);
		$users = $db->loadObjectList();

		$options = array();

		if ($loggedInuser->authorise('certificate.external.manage', 'com_tjcertificate'))
		{
			$options[] = HTMLHelper::_('select.option', "", Text::_('COM_TJCERTIFICATE_SELECT_USER'));

			foreach ($users as $user)
			{
				$options[] = HTMLHelper::_('select.option', $user->id, $user->name);
			}
		}
		else
		{
			$options[] = HTMLHelper::_('select.option', $loggedInuser->id, $loggedInuser->name);
		}


		return $options;
	}
}
