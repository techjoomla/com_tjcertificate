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

use Joomla\CMS\Router\Route;

?>

<form action="<?php echo Route::_('index.php?option=com_tjcertificate&view=certificate'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="btn-wrapper input-append">
		<input type="text" name="certificate" id="certificate"
		value="<?php echo $this->uniqueCertificateId; ?>" placeholder="Enter Cerficate Id">
		<button type="submit" class="btn hasTooltip" title="" aria-label="Search" data-original-title="Search">
			<span class="icon-search" aria-hidden="true"></span>
		</button>
	</div>
</form>
<?php
if ($this->certificate)
{
?>
<div class="techjoomla-bootstrap">
	<div class="table-responsive">
		<table cellpadding="5">
			<tr>
				<td>
					<?php
					$printlink = 'index.php?option=com_tjlms&view=certificate&layout=pdf_gen&user_id=' . $this->userid . '&course_id=' . $this->course_id;
					?>
					<input type="button" class="btn btn-blue" onclick="printcertificate('certificatrediv')" value="<?php echo JText::_('COM_TJLMS_PRINT');?>" />
				</td>
				<td>
					<a  class="btn btn-primary btn-medium" href="<?php // @echo $this->comtjlmsHelper->tjlmsRoute($printlink, false);?>">
						<?php
							echo JText::_('COM_TJLMS_PRINT_PDF');
						?>
					</a>
				</td>
			</tr>
		</table>
	</div>
<div>

<div id="certificatrediv">
<?php
	echo $this->certificate->generated_body;
?>
</div>
<?php
}
