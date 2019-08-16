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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');
$app = Factory::getApplication();
$input = $app->input;

// In case of modal
$isModal = $input->get('layout') == 'modal' ? true : false;
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

Factory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "template.cancel" || document.formvalidator.isValid(document.getElementById("adminForm")))
		{
			jQuery("#permissions-sliders select").attr("disabled", "disabled");
			Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};
');
?>
<div class="tj-page">
	<div class="row-fluid">
		<form action="<?php echo Route::_('index.php?option=com_tjcertificate&view=template&layout=edit&id=' . (int) $this->item->id, false);
		?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
			<div class="form-horizontal">
				<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
				<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'general', Text::_('COM_TJCERTIFICATE_VIEW_CERTIFICATE_TEMPLATES')); ?>
				<div class="row-fluid">
					<div class="span8">
						<?php echo $this->form->renderField('title'); ?>
						<?php echo $this->form->renderField('body'); ?>
						<?php echo $this->form->renderField('replacement_tags'); ?>
						<?php echo $this->form->renderField('client'); ?>
						<?php echo $this->form->renderField('is_public'); ?>
						<?php echo $this->form->renderField('state'); ?>

						<?php echo $this->form->getInput('created_by'); ?>
						<?php echo $this->form->getInput('modified_on'); ?>
						<?php echo $this->form->getInput('modified_by'); ?>
						<?php echo $this->form->getInput('ordering'); ?>
						<?php echo $this->form->getInput('checked_out'); ?>
						<?php echo $this->form->getInput('checked_out_time'); ?>
					</div>
					<div class="span4">
						<div class="alert alert-info">
							<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_REPLACEMENT_TAG_INFO'); ?>
						</div>
						<table class="table table-bordered">
							<thead class="thead-default">
								<tr>
									<th><?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_REPLACEMENT_TAG_TITLE'); ?></th>
									<th><?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_REPLACEMENT_TAG_DESC'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php

								$replacementTags = $this->form->getValue('replacement_tags');

								if (!empty($replacementTags))
								{
									$replacementTags = json_decode($replacementTags);

									foreach ($replacementTags as $tags)
									{
									?>
									<tr>
										<td scope="row"><?php echo $this->escape($tags->name); ?></td>
										<td><?php echo $this->escape($tags->description); ?></td>
									</tr>
									<?php
									}
								} ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
				<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>
				<input type="hidden" name="task" value="" />
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
