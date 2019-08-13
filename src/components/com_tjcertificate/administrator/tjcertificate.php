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
use Joomla\CMS\MVC\Controller\BaseController;

JLoader::import('components.com_tjcertificate.includes.certificate', JPATH_ADMINISTRATOR);

JLoader::registerPrefix('Certificate', JPATH_ADMINISTRATOR);
JLoader::register('CertificateController', JPATH_ADMINISTRATOR . '/controller.php');

// Execute the task.
$controller = BaseController::getInstance('Certificate');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
