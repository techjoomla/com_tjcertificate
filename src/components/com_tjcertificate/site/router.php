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

JLoader::registerPrefix('TjCertificate', JPATH_SITE . '/components/com_tjcertificate/');

/**
 * Class TjCertificateRouter
 *
 * @since  1.0.0
 */
class TjCertificateRouter extends JComponentRouterBase
{
	/**
	 * Build method for URLs
	 * This method is meant to transform the query parameters into a more human
	 * readable form. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   1.0.0
	 */
	public function build(&$query)
	{
		$segments = array();
		$view     = null;

		if (isset($query['task']))
		{
			$segments[] = 'action';
			$taskParts  = explode('.', $query['task']);
			$view       = $taskParts[0];
			$segments[] = $view;
			$segments[] = $taskParts[1];

			if ($query['task'] == 'certificate.download')
			{
				$segments[] = $query['certificate'];
				unset($query['certificate']);
			}

			unset($query['task']);
		}

		if (isset($query['view']))
		{
			$segments[] = $query['view'];
			$view = $query['view'];

			if ($view == 'certificate')
			{
				if (isset($query['certificate']))
				{
					$segments[] = $query['certificate'];
					unset($query['certificate']);
				}
			}

			unset($query['view']);
		}

		return $segments;
	}

	/**
	 * Parse method for URLs
	 * This method is meant to transform the human readable URL back into
	 * query parameters. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   1.0.0
	 */
	public function parse(&$segments)
	{
		$vars = array();

		// View is always the first element of the array
		$vars['view'] = array_shift($segments);

		switch ($vars['view'])
		{
			case 'action':

				$vars['task'] = $segments[0] . '.' . $segments[1];

				if ($vars['task'] = 'certificate.download')
				{
					if (isset($segments[2]))
					{
						$vars['certificate'] = $segments[2];
					}
				}

			break;

			case 'certificate':

				$vars['certificate'] = $segments[0];

			break;

			default:
		}

		return $vars;
	}
}
