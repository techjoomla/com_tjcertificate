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
		$params = ComponentHelper::getParams('com_tjcertificate');
		$input  = Factory::getApplication()->input;

		$this->uniqueCertificateId = $input->get('certificate', '', 'STRING');
		$this->showSearchBox       = $input->getInt('show_search', $params->get('show_search_box'));
		$this->tmpl                = $input->get('tmpl', '', 'STRING');

		if (!empty($this->uniqueCertificateId))
		{
			$certificate = TJCERT::Certificate();
			$this->certificate = $certificate::validateCertificate($this->uniqueCertificateId);

			if (!$this->certificate->id)
			{
				JError::raiseWarning(500, Text::_('COM_TJCERTIFICATE_ERROR_CERTIFICATE_EXPIRED'));
			}
		}

		// If certificate view is private then view is available only for certificate owner
		if (!$params->get('certificate_scope') && Factory::getUser()->id != $this->certificate->getUserId())
		{
			JError::raiseWarning(500, Text::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		// Get HTML
		$model = TJCERT::model('Certificate', array('ignore_request' => true));
		$this->contentHtml = $model->getCertificateProviderInfo($this->certificate->getClientId(), $this->certificate->getClient());

		parent::display($tpl);
	}
}
