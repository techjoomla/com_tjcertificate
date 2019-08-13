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

/**
 * Certificate helper.
 *
 * @since  1.0.0
 */
class CertificateHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  view name string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
		$layout = JFactory::getApplication()->input->get('layout', '', 'STRING');

		if ($layout != "default")
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_TJCERTIFICATE_VIEW_CERTIFICATE_TEMPLATES'),
				'index.php?option=com_tjcertificate&view=certificatetemplates',
				$vName == 'certificatetemplates'
			);

			JHtmlSidebar::addEntry(
				JText::_('COM_TJCERTIFICATE_VIEW_CERTIFICATES'),
				'index.php?option=com_tjcertificate&view=certificates',
				$vName == 'certificates'
			);
		}
		else
		{
			$client = JFactory::getApplication()->input->get('client', '', 'STRING');

			// Set ordering.
			$full_client = explode('.', $client);

			// Eg com_jgive
			$component = $full_client[0];
			$eName = str_replace('com_', '', $component);
			$file = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $component . '/helpers/' . $eName . '.php');

			if (file_exists($file))
			{
				require_once $file;

				$prefix = ucfirst(str_replace('com_', '', $component));
				$cName = $prefix . 'Helper';

				if (class_exists($cName))
				{
					if (is_callable(array($cName, 'addSubmenu')))
					{
						$lang = JFactory::getLanguage();

						// Loading language file from the administrator/language directory then
						// Loading language file from the administrator/components/*extension*/language directory
						$lang->load($component, JPATH_BASE, null, false, false)
						|| $lang->load($component, JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $component), null, false, false)
						|| $lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
						|| $lang->load($component, JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $component), $lang->getDefault(), false, false);

						// Call_user_func(array($cName, 'addSubmenu'), 'categories' . (isset($section) ? '.' . $section : ''));
						call_user_func(array($cName, 'addSubmenu'), $vName);
					}
				}
			}
		}
	}
}
