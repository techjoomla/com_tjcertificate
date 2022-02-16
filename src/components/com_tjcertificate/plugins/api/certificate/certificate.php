<?php
/**
 * @package     TJ.Certificate
 * @subpackage  Api.TjCertificate
 *
 * @copyright   Copyright (C) 2009 - 2022 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

/**
 * Plugin API TjCertificate
 *
 * @since  1.0.0
 */
class PlgAPICertificate extends ApiPlugin
{
	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *
	 * @since   3.7.0
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config = array());

		ApiResource::addIncludePath(dirname(__FILE__) . '/certificate');

		/*load language file for plugin frontend*/
		$lang = JFactory::getLanguage();
		$lang->load('plg_api_certificate', JPATH_ADMINISTRATOR,'',true);

		// Set the login resource to be public
		$this->setResourceAccess('download', 'public', 'post');
	}
}
