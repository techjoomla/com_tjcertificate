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

jimport('joomla.application.component.view');

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

JLoader::import('components.com_tjcertificate.includes.tjcertificate', JPATH_ADMINISTRATOR);

/**
 * Certificate view
 *
 * @since  1.0.0
 */
class TjCertificateViewCertificate extends JViewLegacy
{
	public $certificate = null;

	public $uniqueCertificateId = null;

	public $showSearchBox = null;

	public $contentHtml = null;

	public $item;

	public $mediaPath = null;

	public $certificateUrl = null;

	public $fileName = null;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since  1.0.0
	 */
	public function display($tpl = null)
	{
		$this->params = ComponentHelper::getParams('com_tjcertificate');
		$input  = Factory::getApplication()->input;

		$this->uniqueCertificateId = $input->get('certificate', '', 'STRING');
		$this->showSearchBox       = $input->getInt('show_search', $this->params->get('show_search_box'));
		$this->tmpl                = $input->get('tmpl', '', 'STRING');

		if (!empty($this->uniqueCertificateId))
		{
			$certificate = TJCERT::Certificate();
			$this->certificate = $certificate::validateCertificate($this->uniqueCertificateId);

			if (!$this->certificate->id)
			{
				JError::raiseWarning(500, Text::_('COM_TJCERTIFICATE_ERROR_CERTIFICATE_EXPIRED'));

				return false;
			}
		}

		// If certificate view is private then view is available only for certificate owner
		if (!$this->params->get('certificate_scope') && Factory::getUser()->id != $this->certificate->getUserId())
		{
			JError::raiseWarning(500, Text::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		$this->fileName  = $this->certificate->unique_certificate_id . '.png';
		$this->mediaPath = 'media/com_tjcertificate/certificates/';
		$this->imagePath = Uri::root() . $this->mediaPath . $this->fileName;

		$certificateUrl = 'index.php?option=com_tjcertificate&view=certificate&certificate=' . $this->certificate->unique_certificate_id;
		$this->certificateUrl = Uri::root() . substr(Route::_($certificateUrl), strlen(Uri::base(true)) + 1);

		// Get HTML
		$clientId = $this->certificate->getClientId();
		$client   = $this->certificate->getClient();
		$model = TJCERT::model('Certificate', array('ignore_request' => true));
		$this->contentHtml = $model->getCertificateProviderInfo($clientId, $client);

		$dispatcher = JDispatcher::getInstance();
		PluginHelper::importPlugin('content');
		$result = $dispatcher->trigger('getCertificateClientData', array($clientId, $client));
		$this->item = $result[0];

		parent::display($tpl);
	}
}
