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

		$this->setRedirect(Route::_('index.php?option=com_tjcertificate&view=certificates&layout=my', false));

		// Flush the data from the session.
		$app->setUserState('com_tjcertificate.edit.externalcertificate.data', null);
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
		$this->setRedirect(Route::_('index.php?option=com_tjcertificate&view=certificates&layout=my', false));
	}
}
