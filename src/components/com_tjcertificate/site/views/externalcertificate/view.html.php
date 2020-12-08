<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * External Certificate view
 *
 * @since  __DEPLOY_VERSION__
 */
class TjCertificateViewExternalCertificate extends HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	protected $create;

	protected $params;

	public $certificate = null;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function display($tpl = null)
	{
		$app         = Factory::getApplication();
		$this->input = $app->input;
		$this->user  = Factory::getUser();
		$layout = $this->input->get('layout');
		$id     = $this->input->getInt('id');

		if (!$this->user->id)
		{
			$url      = base64_encode(JUri::getInstance()->toString());
			$loginUrl = JRoute::_('index.php?option=com_users&view=login&return=' . $url, false);
			$app->enqueueMessage(Text::_('COM_TJCERTIFICATE_ERROR_LOGIN_MESSAGE'), 'error');
			$app->redirect($loginUrl);
		}

		$this->create = $this->user->authorise('certificate.external.create', 'com_tjcertificate');
		$this->manage = $this->user->authorise('certificate.external.manage', 'com_tjcertificate');

		if (!$this->create)
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$this->certificate = TJCERT::Certificate();
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');
		$this->params = JComponentHelper::getParams('com_tjcertificate');
		$this->allowedFileExtensions = $this->params->get('upload_extensions');
		$this->uploadLimit      = $this->params->get('upload_maxsize', '1024');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		parent::display($tpl);
	}
}
