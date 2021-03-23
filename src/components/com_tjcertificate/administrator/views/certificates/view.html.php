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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Certificates view
 *
 * @since  1.0.0
 */
class TjCertificateViewCertificates extends HtmlView
{
	/**
	 * An array of items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var  JPagination
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var  object
	 */
	protected $state;

	/**
	 * Form object for search filters
	 *
	 * @var  JForm
	 */
	public $filterForm;

	/**
	 * Logged in User
	 *
	 * @var  JObject
	 */
	public $user;

	/**
	 * The active search filters
	 *
	 * @var  array
	 */
	public $activeFilters;

	/**
	 * The sidebar markup
	 *
	 * @var  string
	 */
	protected $sidebar;

	/**
	 * The access varible
	 *
	 * @var  CMSObject
	 *
	 * @since  1.0.0
	 */
	protected $canDo;

	protected $params;

	public $isAgencyEnabled = false;

	protected $comMultiAgency = 'com_multiagency';
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		// This calls model function getItems()
		$this->items = $this->get('Items');

		// Get state
		$this->state = $this->get('State');

		$this->component = $this->state->get('filter.component');

		// Get pagination
		$this->pagination = $this->get('Pagination');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->user  = Factory::getUser();
		$this->canDo = JHelperContent::getActions('com_tjcertificate');

		// Add submenu
		TjCertificateHelper::addSubmenu('certificates');

		// Add Toolbar
		$this->addToolbar();

		// Set sidebar
		$this->sidebar = JHtmlSidebar::render();

		$this->params = ComponentHelper::getParams('com_tjcertificate');

		if (ComponentHelper::isEnabled($this->comMultiAgency) && $this->params->get('enable_multiagency'))
		{
			$this->isAgencyEnabled = true;
		}
		else
		{
			$this->filterForm->removeField('agency_id', 'filter');
		}

		// Display the view
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.0.0
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(Text::_('COM_TJCERTIFICATE_VIEW_CERTIFICATES'), '');
		$canDo = $this->canDo;

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('certificate.add');
		}

		if ($canDo->get('certificate.external.create'))
		{
			JToolbarHelper::addNew('trainingrecord.add', 'COM_TJCERTIFICATE_ADD_EXTERNAL_CERTIFICATE');
		}

		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('certificate.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::divider();
			JToolbarHelper::publish('certificates.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('certificates.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::archiveList('certificates.archive', 'JTOOLBAR_ARCHIVE');
			JToolbarHelper::divider();
		}

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'certificates.delete', 'JTOOLBAR_DELETE');
			JToolbarHelper::divider();
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolbarHelper::preferences('com_tjcertificate');
			JToolbarHelper::divider();
		}
	}

	/**
	 * Method to order fields
	 *
	 * @return ARRAY
	 */
	protected function getSortFields()
	{
		return array(
			'ci.id' => Text::_('JGRID_HEADING_ID'),
			'ci.certificate_template_id' => Text::_('COM_TJCERTIFICATE_CERTIFICATE_LIST_CERTIFICATE_TEMPLATE'),
			'ci.client' => Text::_('COM_TJCERTIFICATE_CERTIFICATE_LIST_CLIENT'),
			'ci.user_id' => Text::_('COM_TJCERTIFICATE_CERTIFICATE_LIST_USER'),
			'ci.state' => Text::_('JSTATUS'),
			'ci.issued_on' => Text::_('COM_TJCERTIFICATE_CERTIFICATE_LIST_ISSUED_DATE'),
			'ci.expired_on' => Text::_('COM_TJCERTIFICATE_CERTIFICATE_LIST_EXPIRY_DATE'),
		);
	}
}
