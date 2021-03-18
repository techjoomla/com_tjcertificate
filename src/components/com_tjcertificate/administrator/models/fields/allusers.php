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
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldAllUsers extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	__DEPLOY_VERSION__
	 */
	protected $type = 'allusers';

	/**
	 * Fiedd to decide if options are being loaded externally and from xml
	 *
	 * @var		integer
	 * @since	__DEPLOY_VERSION__
	 */
	protected $loadExternally = 0;

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getOptions()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('u.id, u.name, u.username');
		$query->from('`#__users` AS u');
		$query->where($db->qn('u.block') . ' = 0');
		$query->order($db->escape('u.name' . ' ' . 'asc'));
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

		return $options;
	}

	/**
	 * Method to get a list of options for a list input externally and not from xml.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getOptionsExternally()
	{
		$this->loadExternally = 1;

		return $this->getOptions();
	}
}
