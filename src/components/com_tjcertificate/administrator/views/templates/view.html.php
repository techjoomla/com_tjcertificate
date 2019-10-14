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

/**
 * Templates view
 *
 * @since  1.0.0
 */
class TjCertificateViewTemplates extends HtmlView
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

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$app  = Factory::getApplication();
		$client = $app->input->get('client', "");

		// This calls model function getItems()
		$this->items = $this->get('Items');

		// Get state
		$this->state = $this->get('State');

		$this->component = $this->state->get('filter.component');

		if (!empty($client))
		{
			$this->state->set('filter.client', $client);
		}

		// Get pagination
		$this->pagination = $this->get('Pagination');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->user  = Factory::getUser();
		$this->canDo = JHelperContent::getActions('com_tjcertificate');

		// Add submenu
		TjCertificateHelper::addSubmenu('templates');

		// Add Toolbar
		$this->addToolbar();

		// Set sidebar
		$this->sidebar = JHtmlSidebar::render();

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
		JToolBarHelper::title(Text::_('COM_TJCERTIFICATE_VIEW_CERTIFICATE_TEMPLATES'), '');
		$canDo = $this->canDo;

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('template.add');
		}

		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('template.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::divider();
			JToolbarHelper::publish('templates.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('templates.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::archiveList('templates.archive', 'JTOOLBAR_ARCHIVE');
			JToolbarHelper::divider();
		}

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'templates.delete', 'JTOOLBAR_DELETE');
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
			'ct.id' => Text::_('JGRID_HEADING_ID'),
			'ct.title' => Text::_('COM_TJCERTIFICATE_LIST_CERTIFICATE_TEMPLATE_TITLE'),
			'ct.client' => Text::_('COM_TJCERTIFICATE_LIST_CERTIFICATE_TEMPLATE_CLIENT'),
			'ct.is_public' => Text::_('COM_TJCERTIFICATE_LIST_CERTIFICATE_TEMPLATE_ACCESS'),
			'ct.ordering' => Text::_('JGRID_HEADING_ORDERING'),
			'ct.state' => Text::_('JSTATUS'),
			'ct.created_by' => Text::_('COM_TJCERTIFICATE_LIST_CERTIFICATE_TEMPLATE_CREATED_BY'),
		);
	}
}
