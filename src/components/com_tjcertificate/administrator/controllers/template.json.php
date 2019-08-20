<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

jimport('joomla.filesystem.folder');

/**
 * The template controller
 *
 * @since  1.0.0
 */
class TjCertificateControllerTemplate extends FormController
{
	/**
	 * Function to load default template
	 *
	 * @return  object  object
	 */
	public function loadDefaultTemplate()
	{
		if (!JSession::checkToken('get'))
		{
			echo new JResponseJson(null, Text::_('JINVALID_TOKEN'), true);
		}
		else
		{
			$app   = Factory::getApplication();
			$input = $app->input;

			$defaultTemplate = $input->get('defaultTemplate', "");

			if (empty($defaultTemplate))
			{
				echo new JResponseJson(null, Text::_('COM_TJCERTIFICATE_ERROR_SOMETHING_WENT_WRONG'), true);

				return;
			}

			$templatePath = JPATH_SITE . "/components/com_tjcertificate/templates/" . $defaultTemplate;

			if (!JFolder::exists($templatePath))
			{
				echo new JResponseJson(null, Text::_('COM_TJCERTIFICATE_ERROR_INVALID_SAMPLE_CERTIFICATE'), true);

				return;
			}

			$htmlFile = $templatePath . '/template.html';
			$htmlData = file_get_contents($htmlFile);

			$cssFile = $templatePath . '/template.css';
			$cssData = file_get_contents($cssFile);

			$TjCertificateTemplate = TjCertificateTemplate::getInstance();

			$templateData = $TjCertificateTemplate->getEmogrify($htmlData, $cssData);

			if (!$templateData)
			{
				echo new JResponseJson(null, Text::_('COM_TJCERTIFICATE_ERROR_SOMETHING_WENT_WRONG'), true);

				return;
			}

			$templateData = stripslashes($templateData);

			echo new JResponseJson($templateData);
		}
	}
}
