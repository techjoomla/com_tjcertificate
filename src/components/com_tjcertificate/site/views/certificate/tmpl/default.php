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
HTMLHelper::script('components/com_tjcertificate/assets/css/tjCertificate.css');

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
<div class="row tj-certificate">
	<div class="col-sm-12 col-md-9 tj-certificate-content mb-15">
		<?php
		if ($this->certificate)
		{?>
		<div id="certificateContent">
			<?php
				echo $this->certificate->generated_body;
			?>
		</div>
		<input id="certificateId" type="hidden" value="<?php echo $this->certificate->unique_certificate_id;?>"/>
		<?php
		}
		?>
	</div>
	<div class="col-sm-12 col-md-3 tj-certificate-deatils">

		<div class="tj-certificate-blocks">
			<div class="tj-certificate-blocks-heading">
				<h3>
					<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_CERTIFICATE_RECIPIENT');?>
				</h3>
			</div>
			<div class="tj-certificate-content bg-lightblue py-25 px-15">
				<h4 class="mt-0 pull-left mr-5"><?php
								echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_COMPLETED_BY');
							?></h4>
				<span class="fs-16"><strong><?php echo Factory::getUser($this->certificate->getUserId())->name; ?></strong></span>
			</div>
		</div>

		<?php 
		if ($this->certificate->getUserId() == Factory::getUser()->id)
		{
		?>	
		<div class="tj-certificate-blocks">
			<div class="tj-certificate-blocks-heading">
				<h3>
					<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_DOWNLOAD_SHARE');?>
				</h3>
			</div>
			<div class="tj-certificate-content p-15 br-1">

				<a class="d-block mb-15" id="btn-Convert-Html2Image" href="#"><i class="fa fa-download" aria-hidden="true"></i>
				<?php
								echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_DOWNLOAD_AS_IMAGE');
							?></a>

				<?php
				if ($this->certificate->getDownloadUrl())
				{
					?>
				
						<a class="d-block mb-15" href="<?php echo $this->certificate->getDownloadUrl();?>">
							<i class="fa fa-file-pdf-o" aria-hidden="true"></i>
							<?php
								echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_DOWNLOAD_PDF');
							?>
						</a>
					
					<?php
				}
				?>
				
				<span class="btn-print">
				<input type="button" class="btn-print" onclick="printCertificate('certificateContent')"
									value="<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_PRINT');?>" />
				</span>
				
				<div class="tj-certificate-sharing">
				<?php
					if (isset($this->item))
					{ 
						echo $this->loadTemplate('social_sharing');
					} 
				?>
				</div>		
				<div id="previewImage"></div>
		</div>
		</div>
		<?php
		}
		?>
		<div class="tj-certificate-blocks">
			<div class="tj-certificate-blocks-heading">
				<h3>
					<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_ABOUT_THE_COURSE');?>
				</h3>
			</div>
			<div class="tj-certificate-content">
				<?php
					//Tjlms course/ jt event info HTML
					if (!empty($this->contentHtml))
					{
						echo $this->contentHtml;
					} 
				?>
			</div>
		</div>
	</div>
</div>
