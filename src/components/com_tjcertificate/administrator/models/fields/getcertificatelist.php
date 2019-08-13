<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

JFormHelper::loadFieldClass('list');

/**
 * Custom field to list all public and logged-in user's private certificate templates
 *
 * @since  1.0.0
 */
class JFormFieldGetCertificateList extends JFormFieldList
{
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   11.4
	 */
	protected function getOptions()
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('title,id');
		$query->from('#__tjlms_certificate_template');
		$query->where($db->quoteName('state') . " = 1");
		$query->where('(' . $db->quoteName('access') . " = 1 OR " . $db->quoteName('created_by') . " = " . $db->quote($user->id) . ')');
		$db->setQuery($query);

		$certlist = $db->loadObjectList();
		$options = array();
		$options[0] = JHtml::_('select.option', '', JText::_('COM_TJLMS_CERTIFICATE_SELECT_CERTIFICATE'));

		foreach ($certlist as $cert)
		{
			$options[] = JHtml::_('select.option', $cert->id, $cert->title);
		}

		return $options;
	}
}
