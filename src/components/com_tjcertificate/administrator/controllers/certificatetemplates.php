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
/**
 * Certificate template list controller class.
 *
 * @since  1.0.0
 */
class CertificateControllerCertificateTemplates extends AdminController
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
	public function getModel($name = 'CertificateTemplate', $prefix = 'CertificateModel')
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
}
