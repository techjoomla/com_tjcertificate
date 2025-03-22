<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;

jimport('joomla.form.helper');
FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of events
 *
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldEventsList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var   string
	 * @since __DEPLOY_VERSION__
	 */
	protected $type = 'eventslist';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return array An array of JHtml options.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function getOptions()
	{
		$options     = array();
		$eventsModel = JT::model('events', array('ignore_request' => true));

		// Get all events options
		$eventList = $eventsModel->getItems();
	
		$options[] = HTMLHelper::_('select.option', '', Text::_('COM_TJCERTIFICATE_CERTIFICATE_EVENTS_FIELD'));

		if (!empty($eventList))
		{
			foreach ($eventList as $key => $event)
			{
				$eventId    = (int) $event->id;
				$eventName  = htmlspecialchars($event->title);

				$options[]  = HTMLHelper::_('select.option', $eventId, $eventName);
			}
		}

		return $options;
	}
}
