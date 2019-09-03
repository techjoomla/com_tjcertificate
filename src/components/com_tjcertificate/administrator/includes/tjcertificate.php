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

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;

$language = JFactory::getLanguage();
$language->load('com_tjcertificate');

JLoader::discover("TjCertificate", JPATH_ADMINISTRATOR . '/components/com_tjcertificate/libraries');

/**
 * Certificate factory class.
 *
 * This class creates table and model object by instantiating
 *
 * @since  1.0.0
 */
class TjCertificateFactory
{
	/**
	 * Retrieves a table from the table folder
	 *
	 * @param   string  $name  The table file name
	 *
	 * @return	Table object
	 *
	 * @since 	1.0.0
	 **/
	public static function table($name)
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjcertificate/tables');

		return Table::getInstance($name, 'TjCertificateTable');
	}

	/**
	 * Retrieves a model from the model folder
	 *
	 * @param   string  $name    The model name to instantiate
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return	BaseDatabaseModel object
	 *
	 * @since 	1.0.0
	 **/
	public static function model($name, $config = array())
	{
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjcertificate/models');

		return BaseDatabaseModel::getInstance($name, 'TjCertificateModel', $config);
	}
}
