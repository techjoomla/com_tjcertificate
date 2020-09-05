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
use Joomla\CMS\HTML\HTMLHelper;

$options['relative'] = true;
HTMLHelper::StyleSheet('media/com_tjcertificate/vendors/font-awesome-4.1.0/css/font-awesome.min.css');
HTMLHelper::StyleSheet('media/com_tjcertificate/css/tjCertificate.css');
HTMLHelper::StyleSheet('media/com_tjlms/vendors/artificiers/artficier.css');
HTMLHelper::script('media/com_tjcertificate/vendors/html2canvas/js/html2canvas.js');
HTMLHelper::script('com_tjcertificate/certificateImage.js', $options);

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
?>
<?php
if ($this->certificate)
{?>
	<div class="tj-certificate tjBs3">
		<div class="tj-certificate-top mb-25">
			<h4 class=""><?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_DETAIL_VIEW_HEAD');?></h4>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-9">
				<h1 class="font-600 m-0"><?php echo $this->item->title; ?></h1>
			</div>
		</div>
		<div class="row mt-25">
			<div class="col-xs-12 col-md-8">
				<?php
					// Certificate provider info
					if (!empty($this->contentHtml))
					{
						echo $this->contentHtml;
					}
				?>
			</div>
			<div class="col-xs-12 col-md-4">
			<?php 
					if ($this->certificate->getUserId() == Factory::getUser()->id)
					{
					?>	
					<div class="tj-certificate-share-download pull-right">
						<div class="">
						<a data-placement="bottom" class="tj-certificate-btn" data-toggle="popover" data-container="body" data-placement="left" type="button" data-html="true" id="download-popover"><i class="fa fa-arrow-circle-o-down mr-10" aria-hidden="true"></i><?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_DOWNLOAD');?></a>
						<div id="download-popover-content" class="hide">
						<a class="d-block mb-15" id="btn-Convert-Html2Image" href="#"><i class="fa fa-download mr-5" aria-hidden="true"></i>
							<?php
											echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_DOWNLOAD_AS_IMAGE');
										?></a>

							<?php
							if ($this->certificate->getDownloadUrl())
							{
								?>
							
									<a class="d-block mb-15" href="<?php echo $this->certificate->getDownloadUrl();?>">
										<i class="fa fa-file-pdf-o mr-5" aria-hidden="true"></i>
										<?php
											echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_DOWNLOAD_PDF');
										?>
									</a>
								
								<?php
							}
							?>
							
							<span class="btn-print">
							<input type="button" class="btn-print" onclick="certificateImage.printCertificate('certificateContent')"
												value="<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_PRINT');?>" />
							</span>
						</div>

						<?php 
						if ($this->params->get('social_sharing'))
						{?>
							<a data-placement="bottom" data-toggle="popover" data-container="body" data-placement="left" type="button" data-html="true" id="sharing-popover" class="tj-certificate-btn"><i class="fa fa-share-square-o mr-10" aria-hidden="true"></i>								<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_DOWNLOAD_SHARE');?></a>
						<?php
						} ?>		
						<div id="sharing-popover-content" class="hide">
							<div class="tj-certificate-sharing">
							<?php
								if (isset($this->item))
								{
									echo $this->loadTemplate('social_sharing');
								}
							?>
							</div>
						</div>	
					</div>
					</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
		<div class="col-sm-12 tj-certificate-content mb-15 mt-25">
			<div id="certificateContent">
				<?php
					echo $this->certificate->generated_body;
				?>
			</div>
			<div id="previewImage" class="tj-certificate-image"></div>
			<input id="certificateId" type="hidden" value="<?php echo $this->certificate->unique_certificate_id;?>"/>
		</div>
		<div class="col-sm-12 tj-certificate-bottom">
			<div class="fs-16">
				<?php echo TEXT::sprintf('COM_TJCERTIFICATE_CERTIFICATE_VERIFICATION_NOTE', Factory::getUser($this->certificate->getUserId())->name, $this->item->title, HTMLHelper::_('date', $this->certificate->issued_on, "F j, Y")); ?>
			</div>
		</div>
	</div>
<?php
}
?>

<script>
  jQuery("#download-popover").popover({
   html: true,
   content: function() {
          return jQuery('#download-popover-content').html();
        }
   });
   jQuery("#sharing-popover").popover({
   html: true,
   content: function() {
          return jQuery('#sharing-popover-content').html();
        }
   });
 </script>
