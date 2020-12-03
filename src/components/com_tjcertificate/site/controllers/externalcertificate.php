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

JLoader::import("/techjoomla/media/storage/local", JPATH_LIBRARIES);

/**
 * The Tj Certificate External Certificate controller
 *
 * @since  __DEPLOY_VERSION__
 */
class TjCertificateControllerExternalCertificate extends FormController
{
	/**
	 * Method to save a external certficate data.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean|void  Incase of error boolean and in case of success void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function save($key = 'id', $urlVar = 'id')
	{
		// Check for request forgeries.
		$this->checkToken();
		$app      = Factory::getApplication();
		$user     = Factory::getUser();
		$recordId = $this->input->getInt('id');
		$params = ComponentHelper::getParams('com_tjcertificate');

		if (!$user->id)
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$data  = $app->input->get('jform', array(), 'array');
		$model = $this->getModel();

		// Validate the posted data.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			throw new \Exception($model->getError(), 500);
		}

		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			if (!empty($errors))
			{
				// Push up to three validation messages out to the user.
				for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
				{
					if ($errors[$i] instanceof Exception)
					{
						$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
					}
					else
					{
						$app->enqueueMessage($errors[$i], 'warning');
					}
				}
			}

			// Save the data in the session.
			$app->setUserState('com_tjcertificate.edit.externalcertificate.data', $data);

			// Redirect back to the edit screen.
			$redirectUrl = Route::_('index.php?option=com_tjcertificate&view=externalcertificate' . $this->getRedirectToItemAppend($recordId, $urlVar), false);
			$this->setRedirect($redirectUrl);

			return false;
		}

		if ($validData['assigned_user_id'])
		{
			$validData['user_id'] = $validData['assigned_user_id'];
		}
		else
		{
			$validData['user_id'] = $user->id;
		}

		$validData['client'] = "external";
		$validData['state'] = 0;
		$validData['is_external'] = 1;

		$file = $app->input->files->get('jform', array(), 'array');

		if (!empty($file['cert_file']))
		{
			$validData['old_media_ids'] = $app->input->get('oldFiles', 0, 'INT');
			$uploadData = $model->uploadMedia($file, $validData);
			$validData['cert_file'] = $uploadData['source'];
		}

		$certificateModel = TJCERT::model('Certificate', array('ignore_request' => true));

		$certificateModel->save($validData);

		$modelMediaXref = TJMediaXref::getInstance();

		if ($uploadData['id'])
		{
			$mediaData['id'] = '';
			$mediaData['client_id'] = $certificateModel->getState('certificate.id');
			$mediaData['media_id'] = $uploadData['id'];
			$mediaData['client'] = 'com_tjcertificate';
			$modelMediaXref->bind($mediaData);
			$modelMediaXref->save();
		}

		$this->setRedirect(JRoute::_('index.php?option=com_tjcertificate&view=certificates&layout=my', false));
	}

	/**
	 * Cancel operation
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function cancel()
	{
		// Check for request forgeries.
		$this->checkToken('request');
		$this->setRedirect(JRoute::_('index.php?option=com_tjcertificate&view=certificates&layout=my', false));
	}

	/**
	 * Method to publish a list of articles.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function publish()
	{
		parent::publish();

		$this->setRedirect('index.php?option=com_tjcertificate&view=certificates&layout=my');
	}

	/**
	 * Method to download certificate.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function downloadCertificate()
	{
		// CSRF token check
		Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));

		$user = Factory::getUser();
		$app  = Factory::getApplication();
		$params = ComponentHelper::getParams('com_tjcertificate');

		// Validate user login.
		if (empty($user->id))
		{
			$return = base64_encode((string) Uri::getInstance());
			$login_url_with_return = Route::_('index.php?option=com_users&return=' . $return);
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'notice');
			$app->redirect($login_url_with_return, 403);
		}

		$mediaId  = $app->input->get('mediaId', '', 'INT');

		$config              = array();
		$config['mediaId']   = $mediaId;

		// Assign client id as Campaign Id or Report Id or Giveback Id
		$config['client_id'] = $app->input->get('certificateId', '', 'INT');
		$config['client']    = 'com_timelog.activity';
		$mediaAttachmentData = TJMediaXref::getInstance($config);

		$folderName          = explode('.', $mediaAttachmentData->media->type);
		$downloadPath        = JPATH_SITE . '/' . $params->get('file_path', 'media/com_tjcertificate/external');

		// Making File Download path For e.g /file mime type + 's'/text.pdf Here mime type like application + s this is folder name
		$downloadPath        = $downloadPath . '/' . $folderName[0] . '/' . $mediaAttachmentData->media->source;

		$media = TJMediaStorageLocal::getInstance();
		$media->downloadMedia($downloadPath);
	}

	/**
	 * Function to delete the timelog activity attachment
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function deleteAttachment()
	{
		// Prevent CSRF attack
		Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));

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
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$model  = $this->getModel();
		$return = $model->deleteMedia($mediaId, $filePath, 'com_tjcertificate', $clientId);

		$result = array();
		$result['success'] = true;
		$result['message'] = Text::_('COM_TJCERTIFICATE_ATTACHMENT_DELETED_SUCCESSFULLY');

		if ($return == false)
		{
			$result['success'] = false;
			$result['message'] = Text::_('COM_TJCERTIFICATE_ATTACHMENT_DELETED_FAILED');
		}

		echo json_encode($result);
		jexit();
	}
}
