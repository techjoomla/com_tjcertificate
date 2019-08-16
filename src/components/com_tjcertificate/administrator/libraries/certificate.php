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

	public $defaultCertificateIdPrefix = "CERT";

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
	 * Method to get certificate url.
	 *
	 * @param   boolean  $popUp  Url open in popup
	 *
	 * @return  string Certificate url.
	 *
	 * @since 1.0
	 */
	public function getUrl($popUp = false)
	{
		$url = 'index.php?option=com_tjcertificate&view=certificate&certificate=' . $this->unique_certificate_id;

		if ($popUp)
		{
			$url .= '&tmpl=component';
		}

		return Route::_($url);
	}

	/**
	 * Method to get certificate download url.
	 *
	 * @return  string Certificate download url.
	 *
	 * @since 1.0
	 */
	public function getDownloadUrl()
	{
		$url = 'index.php?option=com_tjcertificate&view=certificate&layout=pdfdownload&certificate=' . $this->unique_certificate_id;

		return Route::_($url);
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

		if (empty($table->id))
		{
			return false;
		}

		// Check if certificate expired
		if ($table->expired_on != '0000-00-00 00:00:00')
		{
			$now                 = new DateTime(Factory::getDate('now', 'UTC')->format('Y-m-d'));
			$cerficateExpiryDate = new DateTime(Factory::getDate($table->expired_on, 'UTC')->format('Y-m-d'));

			if ($now > $cerficateExpiryDate)
			{
				return false;
			}
		}

		return self::getInstance($table->id);
	}

	/**
	 * Method to issue certificate.
	 *
	 * @param   Array       $certificateDetails  Array contains certificate details.
	 * @param   Array       $replacements        Array contains replacement.
	 * @param   JParameter  $options             Object contains Jparameters like prefix, expiry_date.
	 *
	 * @return  boolean|object Certificate Object.
	 *
	 * @since 1.0
	 */
	public static function issueCertificate($certificateDetails, $replacements, $options)
	{
		if (empty($certificateDetails['user_id']) || empty($certificateDetails['certificate_template_id']))
		{
			return false;
		}

		// Get template details
		$template = TjCertificateTemplate::getInstance($certificateDetails['certificate_template_id']);

		if (empty($template->id))
		{
			return false;
		}

		// Generate certificate body
		$certificateBody = self::generateCertificateBody($template->body, $replacements);

		// Create array to store issued cerficate
		$issueCertificate                            = array ();
		$issueCertificate['certificate_template_id'] = $template->id;
		$issueCertificate['generated_body']          = $certificateBody;
		$issueCertificate['client']                  = $certificateDetails['client'];
		$issueCertificate['client_id']               = $certificateDetails['client_id'];
		$issueCertificate['user_id']                 = $certificateDetails['user_id'];
		$issueCertificate['state']                   = 1;

		// Get extra options if available
		$params     = ComponentHelper::getParams('com_tjcertificate');
		$db         = Factory::getDbo();
		$prefix     = $options->get('prefix', $params->get('certificate_prefix', $cerficateInstance->defaultCertificateIdPrefix));
		$expiryDate = $options->get('expiry_date', $db->getNullDate());

		if (!empty($expiryDate))
		{
			$issueCertificate['expired_on'] = $expiryDate;
		}

		// Save certificate
		$model = TjCertificateFactory::model("Certificate", array('ignore_request' => true));
		$model->save($issueCertificate);

		$cerficateInstance = self::getInstance($model->getState('certificate.id'));

		// Generate unique certficate id - start
		$uniqueCertificateId = $cerficateInstance->generateUniqueCertId($cerficateInstance->id, $prefix);

		$cerficateInstance->unique_certificate_id = $uniqueCertificateId;

		$cerficateInstance->save();
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
	public static function generateCertificateBody($templateBody, $replacements)
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
	 * @param   integer  $certificateId       Generated Certificate Id
	 * @param   String   $prefix              Certificate prefix
	 * @param   integer  $randomStringLength  The length of unique string
	 *
	 * @return   string
	 *
	 * @since    1.0.0
	 */
	protected function generateUniqueCertId($certificateId, $prefix, $randomStringLength = 0)
	{
		if (empty($randomStringLength) || $randomStringLength > 30 || $randomStringLength < 0)
		{
			$randomStringLength = rand(5, 30);
		}
		else
		{
			$randomStringLength = rand(5, $randomStringLength);
		}

		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$certificateString = '';

		for ($i = 0; $i < $randomStringLength; $i++)
		{
			$certificateString .= $characters[rand(0, $charactersLength - 1)];
		}

		// Check if random string exists
		$cerficateInstance = self::getInstance();
		$certificateString = $prefix . '-' . $certificateString . '-' . $certificateId;
		$table = TjCertificateFactory::table("certificates");

		$table->load(array('unique_certificate_id' => $certificateString));

		if (!empty($table->unique_certificate_id))
		{
			$cerficateInstance->generateUniqueCertId($certificateId, $prefix, $randomStringLength);
		}

		return $certificateString;
	}
}
