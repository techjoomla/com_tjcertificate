<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of courses
 *
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldCourses extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	__DEPLOY_VERSION__
	 */
	protected $type = 'courses';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array   An array of JHtml options.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getOptions()
	{
		$db    = Factory::getDbo();
		$user  = Factory::getUser();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('c.id, c.title');
		$query->from($db->qn('#__tjlms_courses', 'c'));
		$query->join('LEFT', $db->qn('#__categories', 'cat') . ' ON (' . $db->qn('cat.id') . ' = ' . $db->qn('c.catid') . ')');
		$query->where($db->qn('c.state') . '= 1');
		$query->where($db->qn('cat.published') . '= 1');

		$nullDate	= $db->quote($db->getNullDate());
		$nowDate	= $db->quote(Factory::getDate()->toSql());
		$query->where('(c.start_date = ' . $nullDate . ' OR c.start_date <= ' . $nowDate . ')');

		$query->order($db->escape('c.title ASC'));

		$db->setQuery($query);

		// Get all courses.
		$allcourses = $db->loadObjectList();

		$options = array();

        $options[] = HTMLHelper::_('select.option', '', Text::_('COM_TJCERTIFICATE_CERTIFICATE_COURSES_FIELD'));

		foreach ($allcourses as $c)
		{
			$options[] = HTMLHelper::_('select.option', $c->id, $c->title);
		}

		return $options;
	}
}
