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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

if ($this->showSearchBox)
{
	?>
	<form action="<?php echo Route::_('index.php?option=com_tjcertificate&view=certificate'); ?>" method="post" name="adminForm" id="adminForm">
		<div class="tj-search-filters">
			<div class="btn-wrapper input-append">
				<input type="text" name="certificate" id="certificate"
				value="<?php echo $this->uniqueCertificateId; ?>" placeholder="Enter Certificate Id">
				<button type="submit" class="btn hasTooltip" title="" aria-label="Search" data-original-title="Search">
					<span class="icon-search" aria-hidden="true"></span>
				</button>
			</div>
		</div>
	</form>
	<?php
}

if ($this->certificate)
{
	if ($this->certificate->getUserId() == Factory::getUser()->id)
	{
		?>
		<div class="techjoomla-bootstrap">
			<div class="table-responsive">
				<table cellpadding="5">
					<tr>
						<?php
						if ($this->tmpl != 'component')
						{
							?>
							<td>
								<input type="button" class="btn btn-blue" onclick="printCertificate('certificateContent')"
								value="<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_PRINT');?>" />
							</td>
							<?php
						}
						?>
						<?php
						if ($this->certificate->getDownloadUrl())
						{
							?>
							<td>
								<a class="btn btn-primary btn-medium" href="<?php echo $this->certificate->getDownloadUrl();?>">
									<?php
										echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_DOWNLOAD_PDF');
									?>
								</a>
							</td>
							<?php
						}
						?>
					</tr>
				</table>
			</div>
		<div>
		<?php
	}
?>
<div id="certificateContent">
<?php
	echo $this->certificate->generated_body;
?>
</div>
<?php
}
?>

<script type="text/javascript">
function printCertificate(elementId) {
	var printContent        = document.getElementById(elementId).innerHTML;
	var originalContent     = document.body.innerHTML;
	document.body.innerHTML = printContent;
	window.print();
	document.body.innerHTML = originalContent;
}
</script>
<style type="text/css">
	.icon-search:before {
		content: "\f002";
		font-family: "FontAwesome";
	}
	.tj-search-filters .input-append{
		display: inline-flex;
	}
</style>
