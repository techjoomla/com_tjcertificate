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

JLoader::import('components.com_tjcertificate.includes.tjcertificate', JPATH_ADMINISTRATOR);

/**
 * Custom field to list all public and logged-in user's private certificate templates
 *
 * @since  1.0.0
 */
class JFormFieldCertificateTemplates extends JFormFieldList
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

		$app  = Factory::getApplication()->input;
		$user = Factory::getUser();
		$db   = Factory::getDbo();

		$client = $this->getAttribute('client');

		if (empty($client))
		{
			$client = $app->get('extension', '');
		}

		$options[] = JHtml::_('select.option', '', Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_FIELD_SELECT'));

		// Get Private/Created by logged-in user's templates
		if ($user->id)
		{
			// Get template model
			$model = TJCERT::model('Templates', array('ignore_request' => true));

			if (!empty($client))
			{
				$model->setState('filter.client', $client);
			}

			$model->setState('filter.state', 1);
			$model->setState('filter.is_public', 1);
			$model->setState('filter.created_by', $user->id);

			$certlist = $model->getItems();

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
		// Get template model
		$model = TJCERT::model('Templates', array('ignore_request' => true));

		if (!empty($client))
		{
			$model->setState('filter.client', $client);
		}

		$model->setState('filter.state', 1);
		$model->setState('filter.is_public', 2);

		$certlist = $model->getItems();

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
