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
use Joomla\CMS\Factory;

/**
 * Custom field to list default sample templates and client based if client is set
 *
 * @since  1.0.0
 */
class JFormFieldGetSampleTemplateList extends JFormFieldList
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

		$app   = Factory::getApplication();
		$input = $app->input;

		$user = Factory::getUser();
		$db = Factory::getDbo();

		$client = $input->get('client', '');

		$options[] = JHtml::_('select.option', '', Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_FORM_SELECT_SAMPLE_TEMPLATE'));

		// Get client based default templates
		if (!empty($client))
		{
			$component = explode(".", $client)[0];

			// Get client based sample templates
			$clientSampleTemplatePath = JPATH_ROOT . "/media/" . $component . "/templates";

			if (JFolder::exists($clientSampleTemplatePath))
			{
				$clientSampleTemplatePath = JPath::clean($clientSampleTemplatePath);

				// Get a list of folders in the search path with the given filter.
				$clientSampleFolders = JFolder::folders($clientSampleTemplatePath, '', false, true);

				// Build the options list from the list of folders.
				if (is_array($clientSampleFolders))
				{
					$options[] = JHtml::_('select.option', '<OPTGROUP>',
						Text::sprintf('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_FORM_SELECT_CLIENT_SAMPLE_TEMPLATE_DEFAULT', $component)
					);

					foreach ($clientSampleFolders as $folder)
					{
						// Remove the root part and the leading /
						$folder = trim(str_replace($clientSampleTemplatePath, '', $folder), '/');

						$options[] = JHtml::_('select.option', $component . ':' . $folder, $folder);
					}
				}
			}
		}

		// Get default sample templates
		$defaultSampleTemplatePath = JPATH_ROOT . "/media/com_tjcertificate/templates";

		if (JFolder::exists($defaultSampleTemplatePath))
		{
			$defaultSampleTemplatePath = JPath::clean($defaultSampleTemplatePath);

			// Get a list of folders in the search path with the given filter.
			$defaultSampleFolders = JFolder::folders($defaultSampleTemplatePath, '', false, true);

			// Build the options list from the list of folders.
			if (is_array($defaultSampleFolders))
			{
				$options[] = JHtml::_('select.option', '<OPTGROUP>', Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_FORM_SELECT_SAMPLE_TEMPLATE_DEFAULT'));

				foreach ($defaultSampleFolders as $folder)
				{
					// Remove the root part and the leading /
					$folder = trim(str_replace($defaultSampleTemplatePath, '', $folder), '/');

					$options[] = JHtml::_('select.option', 'com_tjcertificate:' . $folder, $folder);
				}
			}
		}

		return $options;
	}
}
