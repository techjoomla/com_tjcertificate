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
class JFormFieldGetClientList extends JFormFieldList
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

		$clientByUser = $this->getAttribute('clientByUser');

		$options[] = JHtml::_('select.option', '', Text::_('COM_TJCERTIFICATE_CERTIFICATE_FILTER_CERTIFICATE_CLIENT_SELECT'));

		// Get Private/Created by logged-in user's templates
		if ($user->id)
		{
			// Create a new query object.
			$query = $db->getQuery(true);

			$query->select('DISTINCT (`client`)');
			$query->from('#__tj_certificate_issue');
			$query->where($db->quoteName('state') . ' = ' . (int) 1);

			if (!empty($clientByUser))
			{
				$query->where($db->quoteName('user_id') . ' = ' . (int) $user->id);
			}

			$db->setQuery($query);

			$listobjects = $db->loadObjectList();

			if (!empty($listobjects))
			{
				foreach ($listobjects as $obj)
				{
					$options[] = JHtml::_('select.option', $obj->client, $obj->client);
				}
			}
		}

		return $options;
	}
}
