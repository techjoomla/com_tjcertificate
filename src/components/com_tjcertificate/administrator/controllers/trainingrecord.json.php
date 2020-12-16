<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
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
use Joomla\CMS\Table\Table;

JLoader::import("/techjoomla/media/storage/local", JPATH_LIBRARIES);

/**
 * The Tj Certificate Training Record controller
 *
 * @since  __DEPLOY_VERSION__
 */
class TjCertificateControllerTrainingRecord extends FormController
{
	/**
	 * Function to delete the record attachment
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

		if (!$user->id)
		{
			return false;
		}

		$clientId = $app->input->get('certificateId', 0, 'INT');
		$mediaId  = $app->input->get('mediaId', 0, 'INT');

		if (!$mediaId && !$clientId)
		{
			echo new JsonResponse(null, Text::_("JERROR_ALERTNOAUTHOR"), true);
			$app->close();
		}

		$model     = $this->getModel();
		$mediaPath = TJCERT::getMediaPath();
		$client    = TJCERT::getClient();
		$result    = $model->deleteMedia($mediaId, $mediaPath, $client, $clientId);

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

	/**
	 * Method to delete the record from frontend.
	 *
	 * @return  void|boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function delete()
	{
		$app = Factory::getApplication();

		if (!Session::checkToken())
		{
			$app->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
			echo new JsonResponse(null, null, true);
			$app->close();
		}

		$user          = Factory::getUser();
		$mediaPath     = TJCERT::getMediaPath();
		$client        = TJCERT::getClient();
		$certificateId = $app->input->getInt('certificateId');
		$manageOwn     = $user->authorise('certificate.external.manageown', $client);
		$manage        = $user->authorise('certificate.external.manage', $client);

		// If manageOwn permission then check record owner can only deleting own record
		if ($manageOwn && !$manage)
		{
			$table = TJCERT::table("certificates");
			$table->load(array('id' => (int) $certificateId, 'user_id' => $user->id));

			if (!$table->id)
			{
				echo new JsonResponse(null, Text::_('COM_TJCERTIFICATE_ERROR_SOMETHING_WENT_WRONG'), true);
				$app->close();
			}
		}

		$model = TJCERT::model('Certificate', array('ignore_request' => true));

		if ($manageOwn || $manage)
		{
			// Remove the item
			if ($model->delete($certificateId))
			{
				// Delete media
				$model  = $this->getModel();
				JLoader::import("/techjoomla/media/tables/xref", JPATH_LIBRARIES);
				$tableXref = Table::getInstance('Xref', 'TJMediaTable');
				$tableXref->load(array('client_id' => $certificateId));

				if ($tableXref->media_id)
				{
					$model->deleteMedia($tableXref->media_id, $mediaPath, $client, $certificateId);
				}

				echo new JResponseJson($result, Text::_('COM_TJCERTIFICATE_CERTIFICATE_DELETED_SUCCESSFULLY'), false);
				$app->close();
			}
			else
			{
				echo new JResponseJson(null, Text::_('COM_TJCERTIFICATE_CERTIFICATE_DELETED_FAILED'), true);
				$app->close();
			}
		}
	}
}
