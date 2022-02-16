<?php
/**
 * @package     TJ.Certificate
 * @subpackage  Api.TjCertificate
 *
 * @copyright   Copyright (C) 2009 - 2022 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
JLoader::import('components.com_tjcertificate.includes.tjcertificate', JPATH_ADMINISTRATOR);

/**
 * Certificate Api.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_api
 *
 * @since       1.0.7
 */
class CertificateApiResourceDownload extends ApiResource
{
	protected $item = array();

	protected $userId = 0;

	/**
	 * Function to download certificate.
	 *
	 * @return void
	 */
	public function post()
	{
		$result    = new stdClass;
		$input     = JFactory::getApplication()->input;
		$userId    = $input->get('user_id', 0, 'INT');
		$username  = $input->get('username', '', 'STRING');
		$email     = $input->get('email', '', 'STRING');
		$contextId = $input->get('context_id', 0, 'INT');
		$context   = $input->get('context', '', 'STRING'); // 'com_jticketing.event', 'com_tjlms.course'

		if (empty($userId) && empty($username) && empty($email))
		{
			ApiError::raiseError("400", JText::_('PLG_API_TJCERTIFICATE_REQUIRED_DATA_EMPTY_MESSAGE'), 'APIValidationException');
		}
		elseif ($username)
		{
			$userId = JUserHelper::getUserId($username);
		}
		elseif ($email)
		{
			$userId = $this->getUserByEmail($email);
		}

		$user = JFactory::getUser($userId);

		if (empty($user) || empty($contextId) || empty($context))
		{
			ApiError::raiseError("400", JText::_('PLG_API_TJCERTIFICATE_REQUIRED_DATA_EMPTY_MESSAGE'), 'APIValidationException');
		}
		else
		{
			$certificate     = TJCERT::Certificate();
			$certificateData = $certificate::getIssued($context, $contextId, $user->id);

			if (!$certificateData[0]->id)
			{
				ApiError::raiseError("400", JText::_('PLG_API_TJCERTIFICATE_BAD_REQUEST_MESSAGE'), 'APIValidationException');
			}

			$certificateObj = $certificate::validateCertificate($certificateData[0]->unique_certificate_id);

			echo $certificateObj->pdfDownload(1);

			return;
		}
	}

	/**
	 * Function to fetch user id by email
	 *
	 * @param   string   $email  User email
	 *
	 * @return  integer   User Id.
	 *
	 * @since   1.0
	 */
	private function getUserByEmail($email)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__users'))
			->where($db->quoteName('email') . ' = ' . $db->quote($email));
		$db->setQuery($query);
		$user = $db->loadResult();

		return $user;
	}
}
