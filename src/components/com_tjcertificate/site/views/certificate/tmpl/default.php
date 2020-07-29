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

HTMLHelper::_('stylesheet', 'components/com_tjcertificate/assets/font-awesome-4.1.0/css/font-awesome.min.css');

if ($this->showSearchBox)
{
	?>
	<form action="<?php echo Route::_('index.php?option=com_tjcertificate&view=certificate'); ?>" method="post" name="adminForm" id="adminForm">
		<div class="tj-search-filters">
			<div class="btn-wrapper input-append">
				<input type="text" name="certificate" id="certificate"
				value="<?php echo $this->uniqueCertificateId; ?>" placeholder="<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_TEMPLATE_ENTER_CERTIFICATE_ID'); ?>">
				<button type="submit" class="btn hasTooltip" title="" aria-label="Search" data-original-title="Search">
					<span class="icon-search" aria-hidden="true"></span>
				</button>
			</div>
		</div>
	</form>
	<?php
}


// Tjlms course/ jt event info HTML
if (!empty($this->contentHtml))
{
	echo $this->contentHtml;
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

		<!-- Social Sharing button start-->
		<?php if ($this->params->get('social_sharing')) { ?>
		<div class="share" id="share-btn-grp">
			<?php if ($this->params->get('facebook_share')) { ?>
				 <div>
					<a href="https://www.facebook.com/sharer/sharer.php?u="><i class="fa fa-facebook-square fa-2x" aria-hidden="true"></i></a>
				</div>
			<?php } ?>
			<?php if ($this->params->get('linkedin_share')) { ?>
				<div>
					<a href="https://www.linkedin.com/shareArticle?mini=true&url=title="><i class="fa fa-linkedin-square fa-2x" aria-hidden="true"></i></a>
				</div>
			<?php } ?>
			<?php if ($this->params->get('twitter_share')) { ?>
				<div>
					<a href="https://twitter.com/intent/tweet?url=text="><i class="fa fa-twitter-square fa-2x" aria-hidden="true"></i></a>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		<!-- Social Sharing button end-->
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
