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
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('jquery.token');
HTMLHelper::_('behavior.framework');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

$options['relative'] = true;
HTMLHelper::_('script', 'com_tjcertificate/template.min.js', $options);

$app = Factory::getApplication();
$input = $app->input;

$client    = $input->getCmd('client', '');
$extension = $input->getCmd('extension', '');

$clientUrlAppend = '';

if (!empty($extension))
{
	$clientUrlAppend = '&extension=' . $extension;
}
elseif (!empty($client))
{
	$clientUrlAppend = '&client=' . $client;
}

?>
<div class="tj-page">
	<div class="row-fluid">
	<form action="<?php echo Route::_('index.php?option=com_tjcertificate&view=certificate&layout=edit&id=' . (int) $this->item->id . $clientUrlAppend, false);?>"
	 method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
	 <?php if (!empty( $this->sidebar))
		{
		?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
		<?php
		}
		else
		{
			?>
			<div id="j-main-container">
		<?php
		}
		?>
		<div class="form-horizontal">

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'general', Text::_('COM_TJCERTIFICATE_TITLE_CERTIFICATE')); ?>
		<div class="row-fluid">
			<?php echo $this->form->renderField('unique_certificate_id'); ?>
			<?php echo $this->form->renderField('certificate_template_id'); ?>
			<?php echo $this->form->renderField('generated_body'); ?>
			<?php echo $this->form->renderField('client'); ?>
			<?php echo $this->form->renderField('client_id'); ?>
			<?php echo $this->form->renderField('user_id'); ?>
			<?php echo $this->form->renderField('comment'); ?>
			<?php echo $this->form->renderField('issued_on'); ?>
			<?php echo $this->form->renderField('expired_on'); ?>
			<?php echo $this->form->renderField('state'); ?>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	</form>
</div>
</div>

<!-- Modal -->
<style>
	.modal-body {
	    overflow-y: auto;
	}
</style>
<div id="templatePreview" class="modal fade" role="dialog">
	<div class="modal-dialog">
	<button type="button" class="close" data-dismiss="modal" style="width: 40px;opacity: 0.7;">&times;</button>
	<!-- Modal content-->
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title"><?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_MODAL_HEADER'); ?></h4>
			<p class="alert alert-info hide" id="show-info"><?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_MODAL_HEADER_INFO'); ?></p>
		</div>
		<div class="modal-body" id="previewTempl">
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	</div>

	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function () {

		template.previewTemplate('jform_generated_body');

		jQuery(document).on("change", "#jform_certificate_template_id", function () {
			template.renderCustomTemplate(this.value);
		});
	});


</script>
