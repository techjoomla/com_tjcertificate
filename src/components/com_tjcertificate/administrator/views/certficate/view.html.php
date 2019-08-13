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
use Joomla\CMS\MVC\View\HtmlView;

/**
 * View to edit
 *
 * @since  1.0.0
 */
class CertficateViewCertficate extends HtmlView
{
	/**
	 * The JForm object
	 *
	 * @var  JForm
	 */
	protected $form;

	/**
	 * The certficate helper
	 *
	 * @var  object
	 */
	protected $certficateHelper;

	/**
	 * The active item
	 *
	 * @var  object
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var  object
	 */
	protected $state;

	/**
	 * The actions the user is authorised to perform
	 *
	 * @var  JObject
	 */
	protected $canDo;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');
		$this->input = Factory::getApplication()->input;
		$this->canDo = JHelperContent::getActions('com_tjcertificate', 'certficate', $this->item->id);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function addToolbar()
	{
		$user       = Factory::getUser();
		$userId     = $user->id;
		$isNew      = ($this->item->id == 0);
		JLoader::import('administrator.components.com_tjcertificate.helpers.certficate', JPATH_SITE);

		$this->certficateHelper = new CertficateHelper;
		$checkedOut = $this->isCheckedOut($userId);

		// Built the actions for new and existing records.
		$canDo = $this->canDo;
		$layout = Factory::getApplication()->input->get("layout");

		$this->sidebar = JHtmlSidebar::render();

		// For new records, check the create permission.
		if ($layout != "default")
		{
			Factory::getApplication()->input->set('hidemainmenu', true);

			JToolbarHelper::title(
				JText::_('COM_TJCERTIFICATE_PAGE_' . ($checkedOut ? 'VIEW_CERTIFICATE' : ($isNew ? 'ADD_CERTIFICATE' : 'EDIT_CERTIFICATE'))),
				'pencil-2 certficate-add'
			);

			if ($isNew)
			{
				JToolbarHelper::save('certficate.save');
				JToolbarHelper::cancel('certficate.cancel');
			}
			else
			{
				$itemEditable = $this->isEditable($canDo, $userId);

				// Can't save the record if it's checked out and editable
				$this->canSave($checkedOut, $itemEditable);
				JToolbarHelper::cancel('certficate.cancel', 'JTOOLBAR_CLOSE');
			}
		}
		else
		{
			JToolbarHelper::title(
				JText::_('COM_TJCERTIFICATE_PAGE_VIEW_CERTIFICATE')
			);

			$app = Factory::getApplication();

			JLoader::import('administrator.components.com_tjcertificate.helpers.certficate', JPATH_SITE);
			CertficateHelper::addSubmenu('certficate');

			if ($app->isAdmin())
			{
				$this->sidebar = JHtmlSidebar::render();
			}
		}

		JToolbarHelper::divider();
	}

	/**
	 * Can't save the record if it's checked out and editable
	 *
	 * @param   boolean  $checkedOut    Checked Out
	 *
	 * @param   boolean  $itemEditable  Item editable
	 *
	 * @return void
	 */
	protected function canSave($checkedOut, $itemEditable)
	{
		if (!$checkedOut && $itemEditable)
		{
			JToolbarHelper::save('certficate.save');
		}
	}

	/**
	 * Is editable
	 *
	 * @param   Object   $canDo   Checked Out
	 *
	 * @param   integer  $userId  User ID
	 *
	 * @return boolean
	 */
	protected function isEditable($canDo, $userId)
	{
		// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
		return $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId);
	}

	/**
	 * Is Checked Out
	 *
	 * @param   integer  $userId  User ID
	 *
	 * @return boolean
	 */
	protected function isCheckedOut($userId)
	{
		return !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
	}
}
