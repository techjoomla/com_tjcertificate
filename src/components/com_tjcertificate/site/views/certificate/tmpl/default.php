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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::StyleSheet('components/com_tjcertificate/assets/font-awesome-4.1.0/css/font-awesome.min.css');
HTMLHelper::script('components/com_tjcertificate/assets/html2canvas/js/html2canvas.js');
HTMLHelper::script('components/com_tjcertificate/assets/tjCertificate.js');

if ($this->showSearchBox)
{
	?>
	<form action="<?php echo Route::_('index.php?option=com_tjcertificate&view=certificate'); ?>" method="post" name="adminForm" id="adminForm">
		<div class="tj-search-filters">
			<div class="btn-wrapper input-append">
				<input type="text" name="certificate" id="certificate" 
					value="<?php echo $this->uniqueCertificateId;?>" 
					placeholder="<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_ENTER_CERTIFICATE_ID'); ?>">
				<button type="submit" class="btn hasTooltip" title="" aria-label="Search" data-original-title="Search">
					<span class="icon-search" aria-hidden="true"></span>
				</button>
			</div>
		</div>
	</form>
	<?php
}


// Client info HTML
if (!empty($this->contentHtml))
{
	echo $this->contentHtml;
}

if ($this->certificate)
{
	if ($this->certificate->getUserId() == Factory::getUser()->id)
	{
		?>
		<a id="btn-Convert-Html2Image" href="#"><?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_DOWNLOAD');?></a>
		<div class="techjoomla-bootstrap">
			<div class="table-responsive">
				<table cellpadding="5">
					<tr>
						<td>
							<input type="button" class="btn btn-blue" onclick="printCertificate('certificateContent')"
							value="<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_PRINT');?>" />
						</td>
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
		if (isset($this->item))
		{
			echo $this->loadTemplate('social_sharing');
		}
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
<input id="certificateId" type="hidden" value="<?php echo $this->certificate->unique_certificate_id;?>"/>
<style type="text/css">
	.icon-search:before {
		content: "\f002";
		font-family: "FontAwesome";
	}
	.tj-search-filters .input-append{
		display: inline-flex;
	}
</style>
