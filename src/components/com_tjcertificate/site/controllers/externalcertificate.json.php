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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Response\JsonResponse;

JLoader::import("/techjoomla/media/storage/local", JPATH_LIBRARIES);

/**
 * The Tj Certificate External Certificate controller
 *
 * @since  __DEPLOY_VERSION__
 */
class TjCertificateControllerExternalCertificate extends FormController
{
	/**
	 * Function to delete the timelog activity attachment
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function deleteAttachment()
	{
		$app = Factory::getApplication();

		if (!Session::checkToken())
		{
			$app->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
			echo new JsonResponse(null, null, true);
			$app->close();
		}

		// Get the current user id
		$user = Factory::getuser();
		$app  = Factory::getApplication();

		if (!$user->id)
		{
			return false;
		}

		$params   = ComponentHelper::getParams('com_tjcertificate');
		$filePath = $params->get('file_path', 'media/com_tjcertificate/external');
		$clientId = $app->input->get('activityId', '', 'INT');
		$mediaId  = $app->input->get('mediaId', '', 'INT');

		if (!$mediaId && !$clientId)
		{
			echo new JsonResponse(null, Text::_("JERROR_ALERTNOAUTHOR"), true);
			$app->close();
		}

		$model  = $this->getModel();
		$result = $model->deleteMedia($mediaId, $filePath, 'com_tjcertificate', $clientId);

		if ($result)
		{
			echo new JResponseJson($result, Text::_('COM_TJCERTIFICATE_ATTACHMENT_DELETED_SUCCESSFULLY'), false);
			$app->close();
		}
		else
		{
			echo new JResponseJson(null, Text::_('COM_TJCERTIFICATE_ATTACHMENT_DELETED_FAILED'), true);
			$app->close();
		}
	}
}
