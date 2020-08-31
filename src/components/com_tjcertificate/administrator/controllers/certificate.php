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
use Joomla\CMS\Router\Route;

/**
 * The certificate controller
 *
 * @since  1.0.0
 */
class TjCertificateControllerCertificate extends FormController
{
	/**
	 * Method to download issued certificate.
	 *
	 * @return  boolean|string Certificate pdf url.
	 *
	 * @since 1.0
	 */
	public function download()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$uniqueCertificateId = $input->get('certificate', '');

		// Download for sending it in email
		$store = $input->get('store', '');

		if (empty($uniqueCertificateId))
		{
			$app->enqueueMessage(Text::_('COM_TJCERTIFICATE_ERROR_CERTIFICATE_EMPTY'), 'error');
			$app->redirect('index.php');
		}

		$certificate    = TJCERT::Certificate();
		$certificateObj = $certificate::validateCertificate($uniqueCertificateId);

		if (!$certificateObj->id)
		{
			$app->enqueueMessage(Text::_('COM_TJCERTIFICATE_ERROR_CERTIFICATE_EXPIRED'), 'error');
			$app->redirect('index.php');
		}

		echo $certificateObj->pdfDownload($store);
	}

	/**
	 * Method to upload certificate image.
	 *
	 * @return  boolean|string image url.
	 *
	 * @since 1.0
	 */
	public function uploadCertificate()
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$canvasOutput = $input->get('image', '', 'RAW');
		$certificateId = $input->get('certificateId', '', 'STRING');
		$canvasOutput = str_replace('data:image/png;base64,', '', $canvasOutput);

		// Replace all spaces with plus sign (helpful for larger images)
		$canvasOutput = str_replace(" ", "+", $canvasOutput);
		$canvasOutput = base64_decode($canvasOutput);
		$filename = $certificateId . '.png';

		if (!JFolder::exists(JPATH_SITE . '/media/com_tjcertificate/certificates/'))
		{
			JFolder::create(JPATH_SITE . '/media/com_tjcertificate/certificates', 0777);
		}

		$filePath = 'media/com_tjcertificate/certificates/';
		$dir = JPATH_SITE . '/' . $filePath;

		if (file_put_contents($dir . $filename, $canvasOutput))
		{
			echo Juri::root() . $filePath . $filename;
		}

		jexit();
	}
}
