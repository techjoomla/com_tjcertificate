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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Router\Route;

/**
 * The Tj Certificate Training Records controller
 *
 * @since  __DEPLOY_VERSION__
 */
class TjCertificateControllerTrainingRecords extends FormController
{
	protected $comMultiAgency = 'com_multiagency';

	/**
	 * Function to add multiple records
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function addRecords()
	{
		$app = Factory::getApplication();

		if (!Session::checkToken())
		{
			$app->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
			echo new JsonResponse(null, null, true);
			$app->close();
		}

		$user = Factory::getUser();
		$data = $app->input->post->get('jform', array(), 'array');

		$model = $this->getModel();

		// Validate the posted data.
		$form = $model->getForm($data, false);
		$data = $model->validate($form, $data);

		if ($data == false)
		{
			$errors = $model->getErrors();
			$this->processErrors($errors);
		}
		else
		{
			$params = ComponentHelper::getParams('com_tjcertificate');

			$userdata                 = array();
			$userdata['id']           = $data['id'];
			$userdata['name']         = $data['name'];
			$userdata['issuing_org']  = $data['issuing_org'];
			$userdata['issued_on']    = $data['issued_on'];
			$userdata['expired_on']   = $data['expired_on'];
			$userdata['status']       = $data['status'];
			$userdata['created_by']   = $data['created_by'];
			$userdata['notify']       = $data['notify_users'];
			$userdata['state']        = $data['state'];
			$userdata['client']       = "external";
			$userdata['is_external']  = 1;

			foreach ($data['assigned_user_id'] as $userId)
			{
				if (ComponentHelper::isEnabled($this->comMultiAgency) && $params->get('enable_multiagency'))
				{
					$manageOwn = $user->authorise('core.manage.own.agency.user', $this->comMultiAgency);
					$manage    = $user->authorise('core.manage.all.agency.user', $this->comMultiAgency);

					if ($manageOwn && empty($manage))
					{
						$agencyModel = TJCERT::model('Agency', array('ignore_request' => true));

						// Get agencies of logged-in user and assigned user
						$assignedUserAgencies = $agencyModel->getUserAgencies($userId);
						$loggedInUserAgencies = $agencyModel->getUserAgencies($user->id);

						// Convert object to array
						$assignedUserAgencyArr = array_column($assignedUserAgencies, 'id');
						$loggedInUserAgencyArr = array_column($loggedInUserAgencies, 'id');

						// Compare both users agencies
						$result = array_intersect($loggedInUserAgencyArr, $assignedUserAgencyArr);

						if (empty($result))
						{
							continue;
						}
					}
				}

				$userdata['user_id'] = $userId;

				if ($app->input->get('site', 'f', 'string') == 'f')
				{
					$redirectLink = Route::_('index.php?option=com_tjcertificate&view=certificates&layout=my', false);
				}
				else
				{
					$redirectLink = Route::_('index.php?option=com_tjcertificate&view=certificates', false);
				}

				if (ComponentHelper::isEnabled('com_tjqueue') && $params->get('tjqueue_records'))
				{
					$recordsModel = TJCERT::model('Trainingrecords', array('ignore_request' => true));
					$response     = $recordsModel->queueRecords($userdata);
					$msg          = ($response) ? Text::_("COM_TJCERTIFICATE_RECORDS_ADDED_TO_QUEUE_SUCCESSFULLY") : Text::_("COM_TJCERTIFICATE_RECORDS_FAILED");

					$returnData                 = array();
					$returnData['msg']          = $msg;
					$returnData['redirect_url'] = $redirectLink;
				}
				else
				{
					$certificateModel = TJCERT::model('Certificate', array('ignore_request' => true));
					$certificateModel->save($userdata);
					$certificateIds[] = $certificateModel->getState('certificate.id');

					$returnData = array();

					if ($certificateIds)
					{
						$error = false;
						$returnData['msg']          = Text::sprintf('COM_TJCERTIFICATE_TOTAL_RECORDS_ADDED', count($certificateIds));
						$returnData['redirect_url'] = $redirectLink;
					}
				}
			}

			echo new JsonResponse($returnData);
			$app->close();
		}
	}

	/**
	 * Method to procees errors
	 *
	 * @param   ARRAY  $errors  ERRORS
	 *
	 * @return  void
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function processErrors($errors)
	{
		if (!empty($errors))
		{
			$msg  = array();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$msg[] = $errors[$i]->getMessage();
				}
				else
				{
					$msg[] = $errors[$i];
				}
			}

			$errormsg = implode("<br>", $msg);
			echo new JsonResponse(0, $errormsg, true);
		}
	}
}
