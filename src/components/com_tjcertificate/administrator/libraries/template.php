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
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Certificate template class. Handles all application interaction with a Certificate Template
 *
 * @since  1.0.0
 */
class TjCertificateTemplate extends CMSObject
{
	public $id = null;

	public $title = "";

	public $body = "";

	public $template_css = "";

	public $client = "";

	public $ordering = 0;

	public $state = 1;

	public $checked_out = null;

	public $checked_out_time = null;

	public $created_on = null;

	public $created_by = 0;

	public $modified_on = null;

	public $modified_by = 0;

	public $is_public = 1;

	public $params = "";

	public static $replacementTagFile = "certificateReplacements.json";

	protected static $certificateTemplateObj = array();

	/**
	 * Constructor activating the default information of the Certificate template
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

		$this->checked_out_time = $this->created_on = $this->modified_on = $db->getNullDate();
	}

	/**
	 * Returns the global certificate template object
	 *
	 * @param   integer  $id  The primary key of the certificate template id to load (optional).
	 *
	 * @return  Object  Certificate template object.
	 *
	 * @since   1.0.0
	 */
	public static function getInstance($id = 0)
	{
		if (!$id)
		{
			return new TjCertificateTemplate;
		}

		if (empty(self::$certificateTemplateObj[$id]))
		{
			$certificateTemplate = new TjCertificateTemplate($id);
			self::$certificateTemplateObj[$id] = $certificateTemplate;
		}

		return self::$certificateTemplateObj[$id];
	}

	/**
	 * Method to load a certificate template object by certificate template id
	 *
	 * @param   int  $id  The certificate template id
	 *
	 * @return  boolean  True on success
	 *
	 * @since 1.0.0
	 */
	public function load($id)
	{
		$table = TjCertificateFactory::table("templates");

		if (!$table->load($id))
		{
			return false;
		}

		$this->setProperties($table->getProperties());

		return true;
	}

	/**
	 * Method to save the Certificate template object to the database
	 *
	 * @return  boolean  True on success
	 *
	 * @since 1.0.0
	 * @throws  \RuntimeException
	 */
	public function save()
	{
		// Create the certificate template table object
		$table = TjCertificateFactory::table("templates");
		$table->bind($this->getProperties());

		$currentDateTime = Factory::getDate()->toSql();

		$user = Factory::getUser();

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
				$table->created_on = $currentDateTime;
				$table->created_by = $user->id;
			}
			else
			{
				$table->modified_on = $currentDateTime;
				$table->modified_by = $user->id;
			}

			// Store the user data in the database
			if (!($table->store()))
			{
				$this->setError($table->getError());

				return false;
			}

			$this->id = $table->id;

			// Fire the onTjCertificateTemplateAfterSave event.
			$dispatcher = \JEventDispatcher::getInstance();

			$dispatcher->trigger('onTjCertificateTemplateAfterSave', array($isNew, $this));
		}
		catch (\Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Method to bind an associative array of data to a certificate template object
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
	 * Function to get the inline css html code from the emogrifier
	 *
	 * @param   string  $html  html
	 * @param   string  $css   css
	 *
	 * @return  boolean|string
	 */
	public function getEmogrify($html, $css)
	{
		jimport('techjoomla.emogrifier.tjemogrifier');

		if (class_exists('InitEmogrifier'))
		{
			InitEmogrifier::initTjEmogrifier();

			$emogrify = new TJEmogrifier($html, $css);

			try
			{
				return $emogrify->emogrify();
			}
			catch (\Exception $e)
			{
				$this->setError($e->getMessage());

				return false;
			}
		}

		return false;
	}

	/**
	 * Function to get the JSON formated template replacement tags
	 *
	 * @param   string  $client  Client
	 *
	 * @return  boolean|string
	 */
	public static function loadTemplateReplacementsByClient($client)
	{
		if (empty($client))
		{
			return false;
		}

		$clientDetails = explode(".", $client);
		$component     = $clientDetails[0];
		$folder        = $clientDetails[1];

		$replacementTagPath = TJ_CERTIFICATE_REPLACEMENT_TAG . '/' . $component . '/' . $folder . '/' . self::$replacementTagFile;

		if (JFile::exists($replacementTagPath))
		{
			return file_get_contents($replacementTagPath);
		}
	}
}
