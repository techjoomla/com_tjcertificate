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
use Joomla\CMS\Language\Text;

/**
 * Custom field to list all public and logged-in user's private certificate templates
 *
 * @since  1.0.0
 */
class JFormFieldGetTemplateList extends JFormFieldList
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
		$options = array();

		$user = JFactory::getUser();
		$db = JFactory::getDbo();

		// Get Private/Created by logged-in user's templates
		if ($user->id)
		{
			// Create a new query object.
			$query = $db->getQuery(true);

			$query->select('id, title');
			$query->from('#__tj_certificate_templates');
			$query->where($db->quoteName('state') . ' = ' . (int) 1);
			$query->where($db->quoteName('is_public') . ' = ' . (int) 1);
			$query->where($db->quoteName('created_by') . ' = ' . (int) $user->id);

			$db->setQuery($query);

			$certlist = $db->loadObjectList();

			if (!empty($certlist))
			{
				$options[] = JHtml::_('select.option', '<OPTGROUP>', Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_FIELD_PRIVATE'));

				foreach ($certlist as $cert)
				{
					$options[] = JHtml::_('select.option', $cert->id, $cert->title);
				}
			}
		}

		// Get Public templates
		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('id, title');
		$query->from('#__tj_certificate_templates');
		$query->where($db->quoteName('state') . ' = ' . (int) 1);
		$query->where($db->quoteName('is_public') . ' = ' . (int) 2);

		$db->setQuery($query);

		$certlist = $db->loadObjectList();

		if (!empty($certlist))
		{
			$options[] = JHtml::_('select.option', '<OPTGROUP>', Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_FIELD_PUBLIC'));

			foreach ($certlist as $cert)
			{
				$options[] = JHtml::_('select.option', $cert->id, $cert->title);
			}
		}

		return $options;
	}
}
