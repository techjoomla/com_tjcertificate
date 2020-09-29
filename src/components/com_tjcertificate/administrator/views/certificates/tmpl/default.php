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

if ( $saveOrder )
{
	$saveOrderingUrl = 'index.php?option=com_tjcertificate&task=certificates.saveOrderAjax';
	HTMLHelper::_('sortablelist.sortable', 'certificateList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<div class="tj-page">
	<div class="row-fluid">
		<form action="<?php echo Route::_('index.php?option=com_tjcertificate&view=certificates'); ?>" method="post" name="adminForm" id="adminForm">

			<?php if (!empty( $this->sidebar))
			{
			?>
				<div id="j-sidebar-container" class="span2">
					<?php echo $this->sidebar; ?>
				</div>
				<div id="j-main-container" class="span10">
			<?php
			}
			else
			{
				?>
				<div id="j-main-container">
			<?php
			}
			// Search tools bar
			echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
			?>
			<?php
			if (empty($this->items))
			{
			?>
				<div class="alert alert-no-items">
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

								<th width="1%" class="center">
									<?php echo HTMLHelper::_('grid.checkall'); ?>
								</th>

								<th width="1%" class="nowrap center">
									<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'ci.state', $listDirn, $listOrder); ?>
								</th>

								<th>
									<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_UNIQUE_ID'); ?>
								</th>
								<th>
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_TEMPLATE', 'ci.certificate_template_id', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_CLIENT', 'ci.client', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_USER', 'ci.user_id', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_ISSUED_DATE', 'ci.issued_on', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_EXPIRY_DATE', 'ci.expired_on', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_TYPE_NAME'); ?>
								</th>
								<th>
									<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_COMMENT'); ?>
								</th>
								<th>
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_ID', 'ci.id', $listDirn, $listOrder); ?>
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
							foreach ($this->items as $i => $item)
							{
								$data = $dispatcher->trigger('getCertificateClientData', array($item->client_id, $item->client));
								$item->max_ordering = 0;

								$canEdit    = $this->canDo->get('core.edit');

								$canCheckin = $this->canDo->get('core.edit.state');

								$canChange  = $this->canDo->get('core.edit.state');

								$canEditOwn = $this->canDo->get('core.edit.own');
								?>
								<tr class="row <?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->id; ?>">
								<td class="center">
									<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
								</td>
								<td class="center">
									<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'certificates.', $canChange, 'cb'); ?>
								</td>
								<td class="has-context">
									<div class="pull-left break-word">
										<?php if ($canEdit || $canEditOwn)
										{
											?>
											<a class="hasTooltip modal" href="
											<?php echo Route::_('index.php?option=com_tjcertificate&view=certificate&layout=preview&tmpl=component&id=' . (int) $item->id, false);?>" title="
											<?php echo Text::_('JGLOBAL_PREVIEW'); ?>">
											<?php echo $this->escape($item->unique_certificate_id); ?></a>
											<?php
											}
											else
											{
												?>
											<span title="<?php echo Text::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->unique_certificate_id)); ?>">
											<?php echo $this->escape($item->unique_certificate_id); ?></span>
										<?php
										}?>

									</div>
								</td>
								<td><?php echo $this->escape($item->title); ?></td>
								<td><?php echo $this->escape($item->client); ?></td>
								<td><?php echo $this->escape($item->uname); ?></td>
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
								<td><?php echo $this->escape($item->comment); ?></td>
								<td>
									<?php
									echo (!empty($data[0]->title)) ? $data[0]->title : '-';
									?>
								</td>
								<td><?php echo (int) $item->id; ?></td>
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
					<input type="hidden" name="extension" value="<?php echo $this->component; ?>" />
					<input type="hidden" name="boxchecked" value="0" />
					<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
