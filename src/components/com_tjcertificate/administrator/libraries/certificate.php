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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Router\Route;
use Joomla\Filesystem\File;
use Joomla\Registry\Registry;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Certificate class.  Handles all application interaction with a Certificate
 *
 * @since  1.0.0
 */
class TjCertificateCertificate extends CMSObject
{
	public $id = null;

	public $unique_certificate_id = "";

	public $certificate_template_id = 0;

	public $generated_body = "";

	public $client = "";

	public $client_id = 0;

	public $user_id = 0;

	public $state = 1;

	public $issued_on = null;

	public $expired_on = null;

	public $defaultCertPrefix = "CERT";

	protected static $certificateObj = array();

	/**
	 * Constructor activating the default information of the Certificate
	 *
	 * @param   int  $id  The unique event key to load.
	 *
	 * @since   1.0.0
	 */
	public function __construct($id = 0)
	{
		if (!empty($id))
		{
			$this->load($id);
		}
	}

	/**
	 * Returns the global Certificate object
	 *
	 * @param   integer  $id  The primary key of the certificate to load (optional).
	 *
	 * @return  TjCertificateCertificate  The Certificate object.
	 *
	 * @since   1.0.0
	 */
	public static function getInstance($id = 0)
	{
		if (!$id)
		{
			return new TjCertificateCertificate;
		}

		if (empty(self::$certificateObj[$id]))
		{
			$certificate = new TjCertificateCertificate($id);
			self::$certificateObj[$id] = $certificate;
		}

		return self::$certificateObj[$id];
	}

	/**
	 * Method to load a certificate object by certificate id
	 *
	 * @param   int  $id  The certificate id
	 *
	 * @return  boolean  True on success
	 *
	 * @since 1.0.0
	 */
	public function load($id)
	{
		$table = TjCertificateFactory::table("certificates");

		if (!$table->load($id))
		{
			return false;
		}

		$this->setProperties($table->getProperties());

		return true;
	}

	/**
	 * Method to save the Certificate object to the database
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0.0
	 * @throws  \RuntimeException
	 */
	public function save()
	{
		// Create the widget table object
		$table = TjCertificateFactory::table("certificates");
		$table->bind($this->getProperties());

		// Allow an exception to be thrown.
		try
		{
			// Check and store the object.
			if (!$table->check())
			{
				$this->setError($table->getError());

				return false;
			}

			// Check if new record
			$isNew = empty($this->id);

			if ($isNew)
			{
				$table->issued_on = Factory::getDate()->toSql();
			}

			// Store the user data in the database
			if (!($table->store()))
			{
				$this->setError($table->getError());

				return false;
			}

			$this->id = $table->id;

			// Fire the onTjCertificateAfterSave event.
			$dispatcher = \JEventDispatcher::getInstance();

			$dispatcher->trigger('onTjCertificateAfterSave', array($isNew, $this));
		}
		catch (\Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Method to bind an associative array of data to a certificate object
	 *
	 * @param   array  &$array  The associative array to bind to the object
	 *
	 * @return  boolean  True on success
	 *
	 * @since 1.0.0
	 */
	public function bind(&$array)
	{
		if (empty ($array))
		{
			$this->setError(Text::_('COM_TJCERTIFICATE_EMPTY_DATA'));

			return false;
		}

		// Bind the array
		if (!$this->setProperties($array))
		{
			$this->setError(Text::_('COM_TJCERTIFICATE_BINDING_ERROR'));

			return false;
		}

		// Make sure its an integer
		$this->id = (int) $this->id;

		return true;
	}

	/**
	 * Method to get issued certificate list
	 *
	 * @param   integer  $templateId  Template Id
	 *
	 * @param   string   $client      Client e.g. com_tjlms.course
	 *
	 * @param   integer  $clientId    Specific client id
	 *
	 * @param   integer  $userId      User Id
	 *
	 * @param   integer  $limitStart  Limit start or page number
	 *
	 * @param   integer  $limit       Number of records
	 *
	 * @return  boolean|array Issued certificate array
	 *
	 * @since 1.0
	 */
	public static function getCertificate($templateId = 0, $client = '', $clientId = 0, $userId = 0, $limitStart = 0, $limit = 20)
	{
		if (empty($templateId) && empty($client))
		{
			return false;
		}

		$model = TjCertificateFactory::model('Certificates', array('ignore_request' => true));

		if (!empty($templateId))
		{
			$model->setState('filter.certificate_template_id', $templateId);
		}

		if (!empty($client))
		{
			$model->setState('filter.client', $client);
		}

		if (!empty($clientId))
		{
			$model->setState('filter.client_id', $clientId);
		}

		if (!empty($userId))
		{
			$model->setState('filter.user_id', $userId);
		}

		$model->setState('list.limit', $limit);
		$model->setState('list.start', $limitStart);

		return $model->getItems();
	}

	/**
	 * Method to get certificate url.
	 *
	 * @param   boolean  $popUp          Url open in popup
	 *
	 * @param   boolean  $showSearchBox  Show search box
	 *
	 * @return  string Certificate url.
	 *
	 * @since 1.0
	 */
	public function getUrl($popUp = false, $showSearchBox = true)
	{
		$url = 'index.php?option=com_tjcertificate&view=certificate&certificate=' . $this->unique_certificate_id;

		if ($popUp)
		{
			$url .= '&tmpl=component';
		}

		$url .= '&show_search=' . $showSearchBox;

		return Route::_($url);
	}

	/**
	 * Method to get certificate download url.
	 *
	 * @return  boolean|string Certificate download url.
	 *
	 * @since 1.0
	 */
	public function getDownloadUrl()
	{
		if (JFile::exists(JPATH_SITE . '/libraries/techjoomla/dompdf/autoload.inc.php'))
		{
			$url = 'index.php?option=com_tjcertificate&view=certificate&layout=pdfdownload&certificate=' . $this->unique_certificate_id;

			return Route::_($url);
		}

		return false;
	}

	/**
	 * Method to get certificate download url.
	 *
	 * @param   boolean  $download  Download as attachment for emails
	 *
	 * @return  boolean|string Certificate pdf url.
	 *
	 * @since 1.0
	 */
	public function pdfDownload($download = 0)
	{
		if (JFile::exists(JPATH_SITE . '/libraries/techjoomla/dompdf/autoload.inc.php'))
		{
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');

			$html = $this->generated_body;

			// Get template details
			$template = TjCertificateTemplate::getInstance($this->certificate_template_id);

			$templateParams = new Registry($template->params);
			$pageSize       = $templateParams->get('certifcate_page_size', 'A4');
			$orientation    = $templateParams->get('orientation', 'portrait');
			$font           = $templateParams->get('certificate_font', 'DeJaVu Sans');

			// If the pagesize is custom then get the correct size and width.
			if ($pageSize === 'custom')
			{
				$height   = $templateParams->get('certificate_pdf_width', '80') * 28.3465;
				$width    = $templateParams->get('certificate_pdf_height', '80') * 28.3465;
				$pageSize = array(0, 0, $width, $height);
			}

			// If the font is custom then get the custmized font.
			if ($font === 'custom')
			{
				$font = $templateParams->get('certificate_custom_font', 'DeJaVu Sans');
			}

			require_once JPATH_SITE . "/libraries/techjoomla/dompdf/autoload.inc.php";

			$html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body>' . $html . '</body></html>';

			if (get_magic_quotes_gpc())
			{
				$html = stripslashes($html);
			}

			// Set font for the pdf download.
			$options = new Options;
			$options->setDefaultFont($font);

			$domPDF = new DOMPDF($options);
			$domPDF->loadHTML($html);

			// Set the page size and oriendtation.
			$domPDF->setPaper($pageSize, $orientation);

			// Render PDF
			$domPDF->render();

			// Certificate name
			$certificatePdfName = File::makeSafe(Text::sprintf("COM_TJCERTIFICATE_CERTIFICATE_DOWNLOAD_FILE_NAME", $this->unique_certificate_id) . ".pdf");

			// Download as attachment for emails
			if ($download == 1)
			{
				file_put_contents($certificatePdfName, $domPDF->output());

				header('Content-Description: File Transfer');
				header('Cache-Control: public');
				header('Content-Type: application/pdf');
				header("Content-Transfer-Encoding: binary");
				header('Content-Disposition: attachment; filename="' . basename($certificatePdfName) . '"');
				header('Content-Length: ' . filesize($certificatePdfName));

				ob_clean();
				flush();
				readfile($certificatePdfName);
				jexit();
			}

			$domPDF->stream($certificatePdfName, array("Attachment" => 1));

			jexit();
		}

		return false;
	}

	/**
	 * Method to validate issued certificate.
	 *
	 * @param   String  $uniqueCertificateId  Unique certicate Id.
	 *
	 * @return  boolean|object Certificate Table Object.
	 *
	 * @since 1.0
	 */
	public static function validateCertificate($uniqueCertificateId)
	{
		$table = TjCertificateFactory::table("certificates");

		$table->load(array('unique_certificate_id' => $uniqueCertificateId));

		if (empty($table->id) || $table->state != 1)
		{
			return false;
		}

		// Check if certificate expired
		if ($table->expired_on != '0000-00-00 00:00:00')
		{
			$now                   = new DateTime(Factory::getDate('now', 'UTC')->format('Y-m-d'));
			$certificateExpiryDate = new DateTime(Factory::getDate($table->expired_on, 'UTC')->format('Y-m-d'));

			if ($now > $certificateExpiryDate)
			{
				return false;
			}
		}

		return self::getInstance($table->id);
	}

	/**
	 * Method to issue certificate.
	 *
	 * @param   Array       $replacements  Array contains replacement.
	 * @param   JParameter  $options       Object contains Jparameters like prefix, expiry_date.
	 *
	 * @return  boolean|object Certificate Object.
	 *
	 * @since 1.0
	 */
	public function issueCertificate($replacements, $options)
	{
		// Check user_id or certificate_template_id (this is needed to generate certificate body) is empty
		if (empty($this->user_id) || empty($this->certificate_template_id))
		{
			return false;
		}

		// Get template details
		$template = TjCertificateTemplate::getInstance($this->certificate_template_id);

		if (empty($template->id))
		{
			return false;
		}

		// Generate certificate body
		$this->generated_body = $this->generateCertificateBody($template->body, $replacements);

		// Emogrify generated body with template css is available
		$emogrData = $template->getEmogrify($this->generated_body, $template->template_css);

		if (!empty($emogrData))
		{
			$this->generated_body = $emogrData;
		}

		// Get expiry date option if available
		$db               = Factory::getDbo();
		$this->expired_on = $options->get('expiry_date', $db->getNullDate());

		// Save certificate first to generate certificate Id
		$this->save();

		// Get prefix option if available
		$params = ComponentHelper::getParams('com_tjcertificate');
		$this->defaultCertPrefix = $options->get('prefix', $params->get('certificate_prefix', $this->defaultCertPrefix));

		// Get certificate random string config
		$stringLength = $options->get('certificate_random_string_length', $params->get('certificate_random_string_length', 30));
		$fixedLength  = $options->get('certificate_fixed_random_string_length', $params->get('certificate_fixed_random_string_length', true));

		// Generate unique certficate id - start
		$this->unique_certificate_id = $this->generateUniqueCertId($stringLength, $fixedLength);

		// Save certificate again with unique certificate Id
		$this->save();
	}

	/**
	 * Method to generate certificate body.
	 *
	 * @param   String  $templateBody  Template Body.
	 * @param   Array   $replacements  Array contains replacement.
	 *
	 * @return  string Certificate body.
	 *
	 * @since 1.0
	 */
	protected function generateCertificateBody($templateBody, $replacements)
	{
		$templateBody = stripslashes($templateBody);

		foreach ($replacements as $index => $data)
		{
			$templateBody = str_ireplace('[' . $index . ']', $data, $templateBody);
		}

		return $templateBody;
	}

	/**
	 * Method to generate unique certificate Id.
	 *
	 * @param   integer  $randomStringLength  The length of unique string
	 *
	 * @param   boolean  $fixedLength         Generate fixed length random string
	 *
	 * @return   string
	 *
	 * @since    1.0.0
	 */
	protected function generateUniqueCertId($randomStringLength = 0, $fixedLength = true)
	{
		if (empty($randomStringLength) || $randomStringLength > 30 || $randomStringLength < 0)
		{
			$randomStringLength = rand(5, 30);
		}
		else
		{
			$randomStringLength = $fixedLength ? $randomStringLength : rand(5, $randomStringLength);
		}

		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$certificateString = '';

		for ($i = 0; $i < $randomStringLength; $i++)
		{
			$certificateString .= $characters[rand(0, $charactersLength - 1)];
		}

		// Check if random string exists
		$certificateString = $this->defaultCertPrefix . '-' . $certificateString . '-' . $this->id;
		$table = TjCertificateFactory::table("certificates");

		$table->load(array('unique_certificate_id' => $certificateString));

		if (!empty($table->unique_certificate_id))
		{
			$this->generateUniqueCertId($randomStringLength, $fixedLength);
		}

		return $certificateString;
	}
}
