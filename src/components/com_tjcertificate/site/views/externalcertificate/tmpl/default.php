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
?>
<fieldset id="users-profile-core">
	<legend>
		<?php echo JText::_('COM_TJCERTIFICATE_EXTERNAL_CERTIFICATE_DETAIL_VIEW_HEAD'); ?>
	</legend>
	<dl class="dl-horizontal">
		<dt>
			<?php echo JText::_('COM_TJCERTIFICATE_FORM_LBL_CERTIFICATE_NAME'); ?>
		</dt>
		<dd>
			<?php echo $this->escape($this->item->name); ?>
		</dd>
		<?php if ($this->item->cert_url) { ?>
		<dt>
			<?php echo JText::_('COM_TJCERTIFICATE_FORM_LBL_CERTIFICATE_URL'); ?>
		</dt>
		<dd>
			<a href="<?php echo $this->escape($this->item->cert_url); ?>" target="_blank"><?php echo $this->escape($this->item->cert_url); ?></a>
		</dd>
		<?php } ?>
		<dt>
			<?php echo JText::_('COM_TJCERTIFICATE_FORM_LBL_ISSUE_ORG'); ?>
		</dt>
		<dd>
			<?php echo $this->escape($this->item->issuing_org); ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_TJCERTIFICATE_CERTIFICATE_FORM_LBL_CERTIFICATE_STATUS'); ?>
		</dt>
		<dd>
			<?php echo ucfirst($this->escape($this->item->status)); ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_TJCERTIFICATE_CERTIFICATE_FORM_LBL_CERTIFICATE_ISSUED_DATE'); ?>
		</dt>
		<dd>
			<?php echo JHtml::_('date', $this->item->issued_on, JText::_('COM_TJCERTIFICATE_CERTIFICATE_DATE_FORMAT')); ?>
		</dd>
		<?php if ($this->item->expired_on != "0000-00-00 00:00:00") { ?> 
		<dt>
			<?php echo JText::_('COM_TJCERTIFICATE_CERTIFICATE_FORM_LBL_CERTIFICATE_EXPIRY_DATE'); ?>
		</dt>
		<dd>
			<?php echo JHtml::_('date', $this->item->expired_on, JText::_('COM_TJCERTIFICATE_CERTIFICATE_DATE_FORMAT')); ?>
		</dd>
		<?php } ?>
		<?php if ($this->item->cert_file) { ?>
		<dt>
		</dt>
		<dd>
			<img src="media/com_tjcertificate/external/image/<?php echo $this->item->cert_file; ?>">
		</dd>
		<?php } ?>
		<?php if ($this->item->comment) { ?>
		<dt>
			<?php echo JText::_('COM_TJCERTIFICATE_CERTIFICATE_FORM_LBL_CERTIFICATE_COMMENT'); ?>
		</dt>
		<dd>
			<?php echo $this->escape($this->item->comment); ?>
		</dd>
		<?php } ?>
	</dl>
</fieldset>
