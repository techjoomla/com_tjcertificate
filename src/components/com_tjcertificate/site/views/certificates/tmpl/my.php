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
use Joomla\CMS\Factory;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.modal', 'a.modal');
HTMLHelper::_('jquery.token');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'ci.id';

$dispatcher = JDispatcher::getInstance();
PluginHelper::importPlugin('content');

$options['relative'] = true;
HTMLHelper::_('script', 'com_tjcertificate/tjCertificateService.min.js', $options);
HTMLHelper::_('script', 'com_tjcertificate/certificate.js', $options);
HTMLHelper::StyleSheet('media/com_tjcertificate/css/tjCertificate.css');
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
			if ($this->create)
			{
				$recordFormLink = 'index.php?option=com_tjcertificate&view=trainingrecord&layout=edit';
				$addRecordLink = Route::_($recordFormLink);?>
				<div class="">
					<a class="btn btn-primary btn-small pull-right" href="<?php echo $addRecordLink;?>">
						<i class="icon-plus"></i><?php echo Text::_('COM_TJCERTIFICATE_ADD_EXTERNAL_CERTIFICATE'); ?>
					</a>
				</div>
			<?php
			}
			?>
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
									<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_LIST_VIEW_CERTIFICATE_ID'); ?>
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
								<th>
									<?php echo Text::_('COM_TJCERTIFICATE_CERTIFICATE_ACTIONS'); ?>
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
								$certificateObj = TJCERT::Certificate($item->id);
								$data = $dispatcher->trigger('getCertificateClientData', array($item->client_id, $item->client));
								?>
								<tr class="row <?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->id; ?>">
								<td class="has-context">
									<div class="pull-left break-word">
										<?php if (!$item->is_external) {?>
										<a href="<?php echo $certificateObj->getUrl('',false); ?>">
											<?php echo $this->escape($item->unique_certificate_id); ?>
										</a>
										<?php } ?>
										<?php if ($item->is_external) {?>
										<a href="<?php echo $certificateObj->getUrl('',false, true); ?>">
											<?php echo $this->escape($item->unique_certificate_id); ?>
										</a>
										<?php } ?>
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
									<?php
										if ($item->is_external)
										{
											echo $item->name;									
										}
										else
										{
											echo ($data[0]->title ? $data[0]->title : "-");
										}
									?>
								</td>
								<td><?php echo $certificateObj->getFormatedDate($item->issued_on); ?></td>
								<td>
									<?php
									if (!empty($item->expired_on) && $item->expired_on != '0000-00-00 00:00:00')
									{
										echo $certificateObj->getFormatedDate($item->expired_on);
									}
									else
									{
										echo '-';
									}
									?>
								</td>
									<td>
										<div class="hide">
										<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
										</div>
										<!-- If user have manage permission then permission to edit and delete any record -->
										<?php
										$editLink = 'index.php?option=com_tjcertificate&view=trainingrecord&layout=edit&id=' . $item->id;

										if ($this->manage && $item->is_external) 
										{ ?>
												<a class="d-inline-block" href="
												<?php echo $editLink; ?>" title="<?php echo Text::_('JACTION_EDIT'); ?>">
													<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
												</a>										
												<a class="d-inline-block p-5" onclick="certificate.deleteItem('<?php echo $item->id; ?>', this)" data-message="<?php echo Text::_('COM_TJCERTIFICATE_DELETE_CERTIFICATE_MESSAGE');?>" class="btn btn-mini delete-button" type="button"><i class="fa fa-trash"></i>
										<?php 
										} ?>

										<?php 
										if ((!$this->manage && $this->manageOwn) && ($item->is_external && $item->state != 1)) 
										{ ?>
												<a class="d-inline-block" href="
												<?php echo $editLink; ?>" title="<?php echo Text::_('JACTION_EDIT'); ?>">
													<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
												</a>										
												<a class="d-inline-block p-5" onclick="certificate.deleteItem('<?php echo $item->id; ?>', this)" data-message="<?php echo Text::_('COM_TJCERTIFICATE_DELETE_CERTIFICATE_MESSAGE');?>" class="btn btn-mini delete-button" type="button"><i class="fa fa-trash"></i>
										<?php 
										} ?>

										<?php 
										if ($this->manage && $item->is_external) 
										{ ?> 
											<a class="d-inline-block" onclick="listItemTask('cb<?php echo $i;?>', 'certificates.<?php echo ($item->state == -1 ||$item->state == 0)  ? 'publish' : 'unpublish';?>')" class="btn btn-mini" type="button">
											<?php 
											if ($item->state == -1 || $item->state == 0) 
											{ ?>
												<i class="fa fa-window-close"></i>
											<?php 
											} 
											elseif ($item->state == 1) 
											{ ?>
												<i class="fa fa-check-square"></i>
											<?php 
											} ?>
									<?php 
										} ?>
									<?php 
										if (!$item->is_external || !$this->manageOwn) 
										{
											echo "-";
										}
									?>

									</td>
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
