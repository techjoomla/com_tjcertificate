<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of courses
 *
 * @since  1.0.0
 */
class JFormFieldAllUsers extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'allusers';

	/**
	 * Fiedd to decide if options are being loaded externally and from xml
	 *
	 * @var		integer
	 * @since	2.2
	 */
	protected $loadExternally = 0;

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   11.4
	 */
	protected function getOptions()
	{

		$params = ComponentHelper::getParams('com_tjcertificate');
		$isAgencyEnabled = false;

		if (ComponentHelper::isEnabled('com_multiagency') && $params->get('enable_multiagency'))
		{
			$isAgencyEnabled = true;
		}

		if (!$isAgencyEnabled)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			// Select the required fields from the table.
			$query->select('u.id, u.name, u.username');
			$query->from('`#__users` AS u');
			$query->order($db->escape('u.name ASC'));

			$db->setQuery($query);

			// Get all users.
			$allUsers = $db->loadObjectList();

			$options = array();

			$options[] = HTMLHelper::_('select.option', "", Text::_('COM_TJCERTIFICATE_AGENCY_USER_SELECT'));

			foreach ($allUsers as $u)
			{
				$options[] = HTMLHelper::_('select.option', $u->id, $u->name);
			}

			if (!$this->loadExternally)
			{
				// Merge any additional options in the XML definition.
				$options = array_merge(parent::getOptions(), $options);
			}
			
		}

		return $options;
	}

	/**
	 * Method to get a list of options for a list input externally and not from xml.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   2.2
	 */
	public function getOptionsExternally()
	{
		$this->loadExternally = 1;

		return $this->getOptions();
	}
}
