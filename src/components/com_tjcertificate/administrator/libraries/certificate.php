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
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;

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

		$db = Factory::getDbo();

		$this->issued_on = $this->expired_on = $db->getNullDate();
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
			$this->setError(JText::_('COM_TJCERTIFICATE_EMPTY_DATA'));

			return false;
		}

		// Bind the array
		if (!$this->setProperties($array))
		{
			$this->setError(\JText::_('COM_TJCERTIFICATE_BINDING_ERROR'));

			return false;
		}

		// Make sure its an integer
		$this->id = (int) $this->id;

		return true;
	}

	/**
	 * Method to issue certificate.
	 *
	 * @param   integer     $userId        User Id.
	 * @param   integer     $templateId    Template Id.
	 * @param   Array       $replacements  Array contains replacement.
	 * @param   JParameter  $options       Object contains Jparameters like prefix, expiry_date.
	 *
	 * @return  boolean|object Certificate Object.
	 *
	 * @since 1.0
	 */
	public function issueCertificate($userId, $templateId, $replacements, $options)
	{
		if (empty($userId) || empty($templateId))
		{
			return false;
		}

		// Get template details
		$template = TjCertificateTemplate::getInstance($templateId);

		if (empty($template->id))
		{
			return false;
		}

		// Generate certificate body
		$certificateBody = $this->generateCertificateBody($template->body, $replacements);

		// Save certificate

		// Generate unique certficate id
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
	public function generateCertificateBody($templateBody, $replacements)
	{
		$templateBody = stripslashes($templateBody);

		foreach ($replacements as $index => $data)
		{
			$templateBody = str_ireplace('[' . $index . ']', $data, $templateBody);
		}

		return $templateBody;
	}
}
