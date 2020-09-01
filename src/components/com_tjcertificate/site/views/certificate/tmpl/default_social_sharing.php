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

use Joomla\CMS\Factory;
$document = Factory::getDocument();

$sharingOptions = $this->params->get('sharing_option');
$description = $this->item->description ? $this->item->description : $this->item->short_desc;
?>
<!-- Social Sharing button start-->
<?php if ($this->params->get('social_sharing'))
{
	// For facebook and linkedin
	$config = Factory::getConfig();
	$siteName = $config->get('sitename');
	$document->addCustomTag('<meta property="og:title" content="' . $this->escape($this->item->title) . '" />');
	$document->addCustomTag('<meta property="og:image" content="' . $this->imageUrl . '" />');
	$document->addCustomTag('<meta property="og:description" content="' . $this->escape($description) . '" />');
	$document->addCustomTag('<meta property="og:site_name" content="' . $this->escape($siteName) . '" />');

	// For twitter
	$document->addCustomTag('<meta name="twitter:card" content="summary_large_image" />');
	$document->addCustomTag('<meta name="twitter:site" content="' . $siteName . '">');
	$document->addCustomTag('<meta name="twitter:title" content="' . $this->item->title . '">');
	$document->addCustomTag('<meta name="twitter:description" content="' . $this->escape($description) . '">');
	$document->addCustomTag('<meta name="twitter:image" content="' . $this->imageUrl . '">');

	if ($this->params->get('social_sharing_type') === 'addthis')
	{
		$pid = $this->params->get('addthis_publishid');

		if (!empty($pid))
		{
			$add_this_js = 'http://s7.addthis.com/js/300/addthis_widget.js';
			$document->addScript($add_this_js);
			$add_this_share = '
			<!-- AddThis Button BEGIN -->
			<div class="addthis_toolbox addthis_default_style">
			<a class="addthis_button_facebook_like" fb:like:layout="button_count" class="addthis_button" addthis:url="' . $this->courseDetailsUrl . '"></a>
			<a class="addthis_button_google_plusone" g:plusone:size="medium" class="addthis_button" addthis:url="' . $this->courseDetailsUrl . '"></a>
			<a class="addthis_button_tweet" class="addthis_button" addthis:url="' . $this->courseDetailsUrl . '"></a>
			<a class="addthis_button_pinterest_pinit" class="addthis_button" addthis:url="' . $this->courseDetailsUrl . '"></a>
			<a class="addthis_counter addthis_pill_style" class="addthis_button" addthis:url="' . $this->courseDetailsUrl . '"></a>
			</div>
			<script type="text/javascript">
				var addthis_config ={ pubid: "' . $pid . '"};
			</script>
			<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid="' . $pid . '"></script>
			<!-- AddThis Button END -->';

			// Output all social sharing buttons
			echo' <div id="rr" style="">
				<div class="social_share_container">
				<div class="social_share_container_inner">'
				. $add_this_share .
				'</div>
			</div>
			</div>
			';
		}
	}
	else
	{
	?>
		<div class="share" id="share-btn-grp">
			<?php 
			if (in_array("facebook", $sharingOptions))
			{
			?>
				 <div>
					<a id="fb" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($this->imageUrl);?>"
						<i class="fa fa-facebook-square fa-2x" aria-hidden="true"></i>
					</a>
				</div>
			<?php 
			} ?>
			<?php 
			if (in_array("linkedin", $sharingOptions))
			{
			?>
				<div>
					<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($this->imageUrl);?>&title=<?php echo urlencode($this->item->title);?>"><i class="fa fa-linkedin-square fa-2x" aria-hidden="true"></i></a>
				</div>
			<?php 
			} ?>
			<?php
			if (in_array("twitter", $sharingOptions))
			{
			?>
				<div>
					<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($this->imageUrl);?>&text=<?php echo urlencode($this->item->title);?>">
						<i class="fa fa-twitter-square fa-2x" aria-hidden="true"></i>
					</a>
				</div>
			<?php
			}
			?>
		</div>
<?php 
	}
} ?>
<!-- Social Sharing button end-->
