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
use Joomla\CMS\Language\Text;

JLoader::import('components.com_tjcertificate.includes.tjcertificate', JPATH_ADMINISTRATOR);

/**
 * Certificates view
 *
 * @since  1.1.0
 */
class TjCertificateViewCertificates extends JViewLegacy
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
	 * Display the  view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return mixed
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$this->user	= Factory::getUser();

		if (!$this->user->id)
		{
			$url      = base64_encode(JUri::getInstance()->toString());
			$loginUrl = JRoute::_('index.php?option=com_users&view=login&return=' . $url, false);
			$app->enqueueMessage(Text::_('COM_TJCERTIFICATE_ERROR_LOGIN_MESSAGE'), 'error');
			$app->redirect($loginUrl);

			return false;
		}

		// Get state
		$this->state = $this->get('State');

		$layout = $app->input->get('layout', "my");

		if ($layout == 'my')
		{
			// Show only logged-in user certificates
			$this->state->set('filter.user_id', $this->user->id);
		}

		// This calls model function getItems()
		$this->items = $this->get('Items');

		// Get pagination
		$this->pagination = $this->get('Pagination');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		parent::display($tpl);
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
		);
	}
}
