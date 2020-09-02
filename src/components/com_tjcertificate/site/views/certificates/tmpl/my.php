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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Plugin\PluginHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.modal', 'a.modal');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'ci.id';

$dispatcher = JDispatcher::getInstance();
PluginHelper::importPlugin('content');
?>

<div class="tj-page tjBs3">
	<div class="row-fluid">
		<form action="<?php echo Route::_('index.php?option=com_tjcertificate&view=certificates&layout=my'); ?>" method="post" name="adminForm" id="adminForm">
			<div class="tj-search-filters">
			<?php
			// Search tools bar
			echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
			?>
			</div>
			<?php
			if (empty($this->items))
			{
			?>
				<div class="alert alert-info alert-no-items ">
					<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php
			}
			else
			{
				?>
					<table class="table table-striped" id="certificateList">
						<thead>
							<tr>
								<th width="1%" class="nowrap center hidden-phone"></th>
								<th>
									<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_UNIQUE_ID'); ?>
								</th>
								<th>
									<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_TYPE'); ?>
								</th>
								<th>
									<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_NAME'); ?>
								</th>
								<th>
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_ISSUED_DATE', 'ci.issued_on', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_EXPIRY_DATE', 'ci.expired_on', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="10">
									<?php echo $this->pagination->getListFooter(); ?>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php
							$urlOpts = array ();
							$urlOpts['popup'] = true;

							foreach ($this->items as $i => $item)
							{
								$data = $dispatcher->trigger('getCertificateClientData', array($item->client_id, $item->client));
								?>
								<tr class="row <?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->id; ?>">
								<td class="has-context">
									<div class="pull-left break-word">
										<a href="<?php echo TJCERT::Certificate($item->id)->getUrl('',false); ?>" title="<?php echo JText::sprintf('COM_USERS_EDIT_USER', $this->escape($item->name)); ?>" target="_blank" >
											<?php echo $this->escape($item->unique_certificate_id); ?>
										</a>
									</div>
								</td>
								<td>
									<?php 
										$client = str_replace(".", "_", $item->client);
										$client = strtoupper("COM_TJCERTIFICATE_CLIENT_" . $client);
										echo TEXT::_($client);
									?>
								</td>
								<td>
									<?php echo $data[0]->title; ?>
								</td>
								<td><?php echo HTMLHelper::date($item->issued_on, Text::_('DATE_FORMAT_LC')); ?></td>
								<td><?php
									if (!empty($item->expired_on) && $item->expired_on != '0000-00-00 00:00:00')
									{
										echo HTMLHelper::date($item->expired_on, Text::_('DATE_FORMAT_LC'));
									}
									else
									{
										echo '-';
									}
									?></td>
							</tr>
							<?php
								}
							?>
						<tbody>
					</table>
					<?php
					}
					?>
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />
					<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	</div>
</div>
<style type="text/css">
	.cert_modal{
		display: block !important;
		position: relative;
	}
	.icon-search:before {
		content: "\f002";
		font-family: "FontAwesome";
	}
	.tj-search-filters .input-append{
		display: inline-flex;
	}
</style>
