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

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

/**
 * Certificate list controller class.
 *
 * @since  1.0.0
 */
class TjCertificateControllerCertificates extends AdminController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   STRING  $name    model name
	 * @param   STRING  $prefix  model prefix
	 *
	 * @return  object  The model.
	 *
	 * @since  1.0.0
	 */
	public function getModel($name = 'Certificate', $prefix = 'TjCertificateModel')
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	/**
	 * Method to delete the record from frontend.
	 *
	 * @return  void|boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function deleteCertificate()
	{
		$user = Factory::getUser();
		$app  = Factory::getApplication();
		$certificateId = $app->input->getInt('id');
		$manageOwn = $user->authorise('certificate.external.manageown', 'com_tjcertificate');
		$manage    = $user->authorise('certificate.external.manage', 'com_tjcertificate');

		// If manageOwn permission then check record owner can only deleting own record
		if ($manageOwn && !$manage)
		{
			$table = TJCERT::table("certificates");
			$table->load(array('id' => (int) $certificateId, 'user_id' => $user->id));

			if (!$table->id)
			{
				return false;
			}
		}

		$model = $this->getModel();

		// Remove the items.
		if ($model->delete($certificateId))
		{
			$this->setMessage(Text::_('COM_TJCERTIFICATE_CERTIFICATE_DELETED_SUCCESSFULLY'));
		}

		$this->setRedirect(Route::_('index.php?option=com_tjcertificate&view=certificates&layout=my&Itemid=' . $itemId, false));
	}

	/**
	 * Method to publish a list of records.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function publish()
	{
		$user = Factory::getUser();

		if (!$user->authorise('certificate.external.manage', 'com_tjcertificate'))
		{
			JError::raiseWarning(403, Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));

			return false;
		}

		$cid = Factory::getApplication()->input->get('cid', array(), 'array');
		$data = array(
			'publish' => 1,
			'unpublish' => 0
		);

		$task = $this->getTask();
		$value = JArrayHelper::getValue($data, $task, 0, 'int');

		// Get some variables from the request
		if (empty($cid))
		{
			throw new Exception(Text::_('COM_TJCERTIFICATE_NO_RECORD_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Publish the items.
			try
			{
				$model->publish($cid, $value);

				if ($value == 1)
				{
					$ntext = 'COM_TJCERTIFICATE_N_RECORD_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = 'COM_TJCERTIFICATE_N_RECORD_UNPUBLISHED';
				}

				$this->setMessage(Text::plural($ntext, count($cid)));
			}
			catch (Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}
		}

		$this->setRedirect('index.php?option=com_tjcertificate&view=certificates&layout=my');
	}
}
