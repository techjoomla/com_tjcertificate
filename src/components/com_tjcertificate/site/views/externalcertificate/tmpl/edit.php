<?php
/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('behavior.modal');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('bootstrap.framework');
HTMLHelper::StyleSheet('media/com_tjcertificate/css/tjCertificate.css');

$options = array();
$options['relative'] = true;
HTMLHelper::script('com_tjcertificate/tjmedia.js', $options);

?>
<form action="" class="form-validate form-horizontal"
    method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-6">
					<h3 class="header">
						<?php
							echo (empty($this->item->id)) ? Text::_('COM_TJCERTIFICATE_ADD_EXTERNAL_CERTIFICATE') : Text::_('COM_TJCERTIFICATE_EDIT_EXTERNAL_CERTIFICATE');
						?>
					</h3>
				<div class="form-group" style="display:none;">
					<label class="col-sm-6"><?php echo $this->form->getLabel('id'); ?></label>
					<div class="col-sm-6"><?php echo $this->form->getInput('id'); ?></div>
				</div>
				<?php
					if ($this->manage)
					{
						echo $this->form->renderField('assigned_user_id');
					}
					 echo $this->form->renderField('name'); 
					 echo $this->form->renderField('unique_certificate_id');
					 echo $this->form->renderField('cert_url'); 
					 echo $this->form->renderField('issuing_org'); 
					 echo $this->form->renderField('issued_on'); 
					 echo $this->form->renderField('expired_on'); 
					 echo $this->form->renderField('status'); 
					 echo $this->form->renderField('cert_file');
					 ?>
					 <?php 	
					 if ($this->item->mediaData[0]) {
						$token = Session::getFormToken();
						echo '<input type="hidden" name="oldFiles" value="'. $this->item->mediaData[0]->media_id . '">';
						$downloadAttachmentLink = JUri::root() . 'index.php?option=com_tjcertificate&task=externalcertificate.downloadCertificate&' .
						JSession::getFormToken() . '=1' . '&mediaId=' . $this->item->mediaData[0]->media_id . '&certificate=' . $this->item->cert_file . '&certificateId=' . $this->item->id;
					?>
					<div class="control-group">
					<div class="controls w-100 control-group-fwidth">
					 <ul class="list-unstyled">
						<li>
							<a
								class="mr-20"
								href="<?php echo Route::_($downloadAttachmentLink);?>"
								target=""
								title="<?php echo $this->escape(strip_tags($this->item->mediaData[0]->title));?>">
								<?php echo $this->item->mediaData[0]->title;?>
								<i class="fa fa-download" aria-hidden="true"></i>
							</a>
							
							<i class="fa fa-trash"
									title="<?php echo Text::_('COM_TJCERTIFICATE_ATTACHMENT_DELETE');?>"
									data-mid="<?php echo $this->item->mediaData[0]->media_id;?>"
									data-aid="<?php echo $this->item->id;?>"
									onclick="tjMediaFile.deleteAttachment('externalcertificate.deleteAttachment', this, '<?php echo $token ?>')"></i>
						</li>
					</ul>
					</div>
					</div>
					<?php } ?>
					<?php
					 echo $this->form->renderField('created_by');
					 echo $this->form->renderField('comment'); 
				?>
			</div>
		</div>
	</div>	

	<div class="control-group">
		<div class="controls">
			<button onclick="Joomla.submitbutton('externalcertificate.save');" type="button" class="btn btn-primary"><?php echo Text::_('JSUBMIT'); ?></button>
			<button type="button" class="btn btn-default"  onclick="Joomla.submitbutton('externalcertificate.cancel')">
					<span><?php echo Text::_('JCANCEL'); ?></span>
			</button>
		</div>
	</div>
	
	<input type="hidden" name="jform[id]" id="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="option" value="com_tjcertificate"/>
	<input type="hidden" name="task" value="externalcertificate.save"/>
	<?php echo JHtml::_('form.token'); ?>
</form>

<script type="text/javascript">
var allowedAttachments = '<?php echo $this->allowedFileExtensions; ?>';
var attachmentMaxSize  = '<?php echo $this->uploadLimit; ?>';
</script>
