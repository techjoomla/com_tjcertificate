<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use TJQueue\Admin\TJQueueProduce;

if (ComponentHelper::getComponent('com_tjqueue', true)->enabled)
{
	jimport('tjqueueproduce', JPATH_ADMINISTRATOR . '/components/com_tjqueue/libraries');
}

/**
 * TjCertificate Training Record Model.
 *
 * @since  __DEPLOY_VERSION__
 */
class TjCertificateModelTrainingRecords extends AdminModel
{
	/**
	 * @var null  Item data
	 * @since  __DEPLOY_VERSION__
	 */
	protected $item = null;

	protected $comMultiAgency = 'com_multiagency';

	public $params;

	public $user;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($config = array())
	{
		$this->params = ComponentHelper::getParams('com_tjcertificate');
		$this->user   = Factory::getuser();

		parent::__construct($config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form         = $this->loadForm('com_tjcertificate.trainingrecords', 'trainingrecords', array('control' => 'jform', 'load_data' => $loadData));

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
	 * @return	mixed	The data for the form.
	 *
	 * @since	__DEPLOY_VERSION__
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_tjcertificate.edit.trainingrecords.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
			$data->assigned_user_id = $data->user_id;
		}

		return $data;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   \JForm  $form   The form to validate against.
	 * @param   Array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  array|boolean  Array of filtered data if valid, false otherwise.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function validate($form, $data, $group = null)
	{
		$return = true;
		$return = parent::validate($form, $data, $group);

		if (!empty($data['expired_on']) && $data['expired_on'] != '0000-00-00 00:00:00')
		{
			if ($data['issued_on'] > $data['expired_on'])
			{
				$this->setError(Text::_('COM_TJCERTIFICATE_EXPIRY_DATE_VALIDATION_MESSAGE'));
				$return = false;
			}
		}

		return $return;
	}

	/**
	 * Method to push data in queue.
	 *
	 * @param   array  $data  record data
	 *
	 * @return  boolean value.
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function queueRecords($data)
	{
		$return = [];

		$messageBody = new stdClass;
		$messageBody->user_id      = $data['user_id'];
		$messageBody->name         = $data['name'];
		$messageBody->issuing_org  = $data['issuing_org'];
		$messageBody->issued_on    = $data['issued_on'];
		$messageBody->expired_on   = $data['expired_on'];
		$messageBody->status       = $data['status'];
		$messageBody->client       = $data['client'];
		$messageBody->is_external  = $data['is_external'];
		$messageBody->state        = $data['state'];
		$messageBody->created_by   = $data['created_by'];
		$messageBody->notify       = $data['notify'];

		try
		{
			$TJQueueProduce = new TJQueueProduce;

			// Set message body
			$TJQueueProduce->message->setBody(json_encode($messageBody));

			// @Params client, value
			$TJQueueProduce->message->setProperty('client', 'certificate.records');
			$TJQueueProduce->produce();
		}
		catch (Exception $e)
		{
			$return['success'] = 0;
			$return['message'] = $e->getMessage();

			return $return;
		}

		$return['success'] = 1;
		$return['message'] = '';

		return $return;
	}
}
