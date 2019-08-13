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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

Factory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "certificate.cancel" || document.formvalidator.isValid(document.getElementById("adminForm")))
		{
			jQuery("#permissions-sliders select").attr("disabled", "disabled");
			Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};
');
?>
<div class="">
	<form action="<?php echo Route::_('index.php?option=com_tjcertificate&view=certificate&layout=edit&id=' . (int) $this->item->id, false);?>"
	 method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
		<div class="form-horizontal">

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_TJCERTIFICATE_TITLE_CERTIFICATE')); ?>
		<div class="row-fluid">
			<div class="span9">
				<?php echo $this->form->renderField('unique_certificate_id'); ?>
				<?php echo $this->form->renderField('certificate_template_id'); ?>
				<?php echo $this->form->renderField('generated_body'); ?>
				<?php echo $this->form->renderField('client'); ?>
				<?php echo $this->form->renderField('client_id'); ?>
				<?php echo $this->form->renderField('user_id'); ?>
				<?php echo $this->form->renderField('issued_on'); ?>
				<?php echo $this->form->renderField('expired_on'); ?>
				<?php echo $this->form->renderField('state'); ?>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	</form>
</div>
