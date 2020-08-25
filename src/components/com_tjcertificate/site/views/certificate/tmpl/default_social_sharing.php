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
?>
<!-- Social Sharing button start-->
<?php if ($this->params->get('social_sharing'))
{
		// For facebook and linkedin
		$config = Factory::getConfig();
		$siteName = $config->get('sitename');
		$document->addCustomTag('<meta property="og:title" content="' . $this->item->title . '" />');
		$document->addCustomTag('<meta property="og:image" content="" />');
		$document->addCustomTag('<meta property="og:url" content="" />');
		$document->addCustomTag('<meta property="og:description" content="' . $this->item->description . '" />');
		$document->addCustomTag('<meta property="og:site_name" content="' . $siteName . '" />');
?>

<div class="share" id="share-btn-grp">
	<ul class="list-unstyled">
	<?php 
	if ($this->params->get('facebook_share'))
	{
	?>
		 <li>
<!--
			<a id="fb" href="https://www.facebook.com/sharer/sharer.php?u="><i class="fa fa-facebook-square fa-2x" aria-hidden="true"></i></a>
-->
			<a id="fb"><i class="fa fa-facebook-square fa-2x" aria-hidden="true"></i></a>
		</li>
	<?php 
	} ?>
	<?php 
	if ($this->params->get('linkedin_share'))
	{
	?>
		<li>
<!--
			<a id="linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url=&title"><i class="fa fa-linkedin-square fa-2x" aria-hidden="true"></i></a>
-->
			<a id="linkedin"><i class="fa fa-linkedin-square fa-2x" aria-hidden="true"></i></a>
		</li>
	<?php 
	} ?>
	<?php
	if ($this->params->get('twitter_share'))
	{
		$document->addCustomTag('<meta name="twitter:card" content="summary_large_image" />');
		$document->addCustomTag('<meta name="twitter:site" content="' . $siteName . '">');
		$document->addCustomTag('<meta name="twitter:title" content="' . $this->item->title . '">');
		$document->addCustomTag('<meta name="twitter:description" content="' . $this->item->description . '">');
		$document->addCustomTag('<meta name="twitter:image" content="">');

		?>
		<li>
			<a href="https://twitter.com/intent/tweet?url=text="><i class="fa fa-twitter-square fa-2x" aria-hidden="true"></i></a>
		</li>
	<?php 
	}
	?>
	</ul>
</div>
<?php 
} ?>
<!-- Social Sharing button end-->

<script>

jQuery("#fb").click(function(){

	//var certificateUrl = jQuery("#certificateUrl").val();
	
	//~ if (!certificateUrl)
	//~ {
		//~ saveCapture(document.querySelector("#certificateContent"));
	//~ }
	 
	window.open('https://www.facebook.com/sharer/sharer.php?u=');
});

jQuery("#linkedin").click(function(){
	//var certificateUrl = jQuery("#certificateUrl").val();

	//~ if (!certificateUrl)
	//~ {
		saveCapture(document.querySelector("#certificateContent"));
	//}

	//window.open('https://www.linkedin.com/shareArticle?mini=true&url='+ url);
});

</script>
