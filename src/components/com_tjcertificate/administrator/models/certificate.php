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
use \Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

/**
 * Item Model for an Certificate.
 *
 * @since  1.0.0
 */
class TjCertificateModelCertificate extends AdminModel
{
	public $defaultCertificateIdPrefix = "CERT";

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @since   1.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_tjcertificate.certificate', 'certificate', array('control' => 'jform', 'load_data' => $loadData));

		return empty($form) ? false : $form;
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Certificates', $prefix = 'TjCertificateTable', $config = array())
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjcertificate/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	$data  The data for the form.
	 *
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_tjcertificate.edit.certificate.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function save($data)
	{
		$pk   = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('certificate.id');
		$certificate = TjCertificateCertificate::getInstance($pk);

		$params = JComponentHelper::getParams('com_tjcertificate');

		// Bind the data.
		if (!$certificate->bind($data))
		{
			$this->setError($certificate->getError());

			return false;
		}

		$result = $certificate->save();

		// Store the data.
		if (!$result)
		{
			$this->setError($certificate->getError());

			return false;
		}

		$this->setState('certificate.id', $certificate->id);

		// Generate unique certificate Id
		if (empty($pk))
		{
			// Check if prefix is passed by the client
			if (!empty($data['prefix']))
			{
				$this->defaultCertificateIdPrefix = $data['prefix'];
			}
			else
			{
				$this->defaultCertificateIdPrefix = $params->get('certificate_prefix', $this->defaultCertificateIdPrefix);
			}

			$data['unique_certificate_id'] = $this->generateUniqueCertId($certificate->id);

			$this->save($data);
		}

		return true;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return   void
	 *
	 * @since    1.0.0
	 */

	protected function populateState()
	{
		$jinput = Factory::getApplication()->input;
		$id = ($jinput->get('id'))?$jinput->get('id'):$jinput->get('id');
		$this->setState('certificate.id', $id);
	}

	/**
	 * Method to generate unique certificate Id.
	 *
	 * @param   integer  $certificateId  Generated Certificate Id
	 *
	 * @param   integer  $length         The length of unique string
	 *
	 * @return   string
	 *
	 * @since    1.0.0
	 */
	protected function generateUniqueCertId($certificateId, $length = 0)
	{
		if (empty($length) || $length > 30)
		{
			$length = rand(5, 30);
		}

		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$certificateString = '';

		for ($i = 0; $i < $length; $i++)
		{
			$certificateString .= $characters[rand(0, $charactersLength - 1)];
		}

		// Check if random string exists
		$certificateString = $this->defaultCertificateIdPrefix . '-' . $certificateString . '-' . $certificateId;
		$table = TjCertificateFactory::table("certificates");

		$table->load(array('unique_certificate_id' => $checkString));

		if (!empty($table->unique_certificate_id))
		{
			$this->generateUniqueCertId($certificateId, $length);
		}

		return $certificateString;
	}
}
