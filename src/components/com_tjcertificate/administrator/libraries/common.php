<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
jimport('techjoomla.tjnotifications.tjnotifications');

use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Router\Route;

/**
 * Class TjCertificateCommon for common function
 *
 * @since  __DEPLOY_VERSION__
 */
class TjCertificateCommon
{
	/**
	 * Common save method for save training record data.
	 *
	 * @param   array  $data  The name of the primary key of the URL variable.
	 *
	 * @return  boolean       Incase of success and error boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function saveTrainingRecord($data)
	{
		$app      = Factory::getApplication();
		$user     = Factory::getUser();
		$recordId = $app->input->getInt('id');
		$formData = $data->get('jform', array(), 'array');

		$params = ComponentHelper::getParams('com_tjcertificate');
		$model = TJCERT::model('TrainingRecord', array('ignore_request' => true));

		// Validate the posted data.
		$form = $model->getForm($formData, false);

		if (!$form)
		{
			throw new \Exception($model->getError(), 500);
		}

		$validData = $model->validate($form, $formData);

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
			$app->setUserState('com_tjcertificate.edit.trainingrecord.data', $formData);

			// Redirect back to the edit screen.
			$redirectUrl = Route::_('index.php?option=com_tjcertificate&view=trainingrecord&layout=edit&id=' . $recordId, false);
			$app->redirect($redirectUrl);

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

		$file = $data->files->get('jform', array(), 'array');

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

		return true;
	}
}
