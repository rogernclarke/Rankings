<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php if (count($this->ttEntries) + count($this->ttPending) + count($this->ttRides) > 0) : $ttdisplay = true; else : $ttdisplay = false; endif; ?>
<?php if (count($this->hcEntries) + count($this->hcPending) + count($this->hcRides) > 0) : $hcdisplay = true; else : $hcdisplay = false; endif; ?>

<div class="accordion-item-title">
	<!-- <span class="indicator">
		<span>+</span>
	</span> -->
	<!-- Form -->
	<form action="<?php echo JRoute::_('riders?task=rider.display&cid=' . $this->rider->rider_id); ?>" method="post" name="AdminForm" id="AdminForm">
		<fieldset class="adminform">
			<div>
				<?php
				// Set filter values for display and render
				$this->form->setValue('filter_year', null, $this->escape($this->state->get('filter.year')));
				echo $this->form->renderFieldset('rider_toolbar'); ?>
			</div>
		</fieldset>
	</form>
	<h2>Season</h2>
</div>
<div class="accordion-item-content">
	<div class="tt-rider-section-content">
		<div class="tt-tabs-stage">
			<ul class="nav nav-tabs tt-tabs" role="tablist">
				<li role="presentation" class="<?php if ($ttdisplay) : echo 'active'; else : echo 'disabled'; endif; ?>" style="width:50%">
					<?php if ($ttdisplay) : ?>
						<a href="#timetrials" aria-controls="Time Trials" role="tab" data-toggle="tab" data-context=""><h3><i class="fas fa-stopwatch" aria-hidden="true"></i>Time Trials</h3></a>
					<?php else : ?>
						<a href="" aria-controls="Time Trials" role="tab" data-toggle="tab" data-context=""><h3><i class="fas fa-stopwatch" aria-hidden="true"></i>Time Trials</h3></a>
					<?php endif; ?>
				</li>
				<li role="presentation" class="<?php if (!$hcdisplay) : echo 'disabled'; elseif (!$ttdisplay) : echo 'active'; endif; ?>" style="width:50%">
					<?php if ($hcdisplay) : ?>
						<a href="#hillclimbs" aria-controls="Hill Climbs" role="tab" data-toggle="tab" data-context=""><h3><i class="fas fa-mountain" aria-hidden="true"></i>Hill Climbs</h3></a>
					<?php else : ?>
						<a href="" aria-controls="Hill Climbs" role="tab" data-toggle="tab" data-context=""><h3><i class="fas fa-mountain" aria-hidden="true"></i>Hill Climbs</h3></a>
					<?php endif; ?>
				</li>
			</ul>
			<div class="tab-content">
				<?php if ($ttdisplay) : ?>
					<div class="tab-pane active" id="timetrials" role="tabpanel" >
						<div class="tt-rider-rank timetrials">
							<?php if (in_array($this->ttriderhistory->status, array('Frequent','Qualified'), true )) : ?>
								<div class="tt-column">
									<table class="tt-table-rankings">
										<tbody>
											<tr class="row1">
												<td class="tt-col-ranking-type tt-overall"><i class="fas fa-stopwatch" aria-hidden="true"></i>Overall</td>
												<td class="tt-col-ranking-value tt-overall"><?php echo '#' . $this->ttriderhistory->overall_rank; ?></td>
											</tr>
											<tr class="row2">
												<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>Rank</td>
												<td class="tt-col-ranking-value"><?php echo '#' . $this->ttriderhistory->gender_rank; ?></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="tt-column">
									<table class="tt-table-rankings">
										<tbody>
											<tr class="row1">
												<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i><?php echo $this->rider->age_category; ?></td>
												<td class="tt-col-ranking-value"><?php echo '#' . $this->ttriderhistory->age_category_rank; ?></td>
											</tr>
											<tr class="row2">
												<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i><?php echo $this->rider->district_name; ?></td>
												<td class="tt-col-ranking-value"><?php echo '#' . $this->ttriderhistory->district_rank; ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							<?php else : ?>
								<?php echo 'Provisional Ranking Status'; ?>
							<?php endif; ?>
						</div>
						<?php if (in_array($this->ttriderhistory->status,array('Frequent','Qualified','Provisional'), true )) : ?>
							<!-- Charts -->
							<div id="ttrides-chart-container" class="chart-container"></div>
							<?php echo $this->loadTemplate('ttchart'); ?>
						<?php endif; ?>
						<div class="tt-accordion-container">
							<ul id="tt-accordion-season-timetrial" class="tt-accordion tt-level2">
								<?php if ($this->state->get('filter.year') == $this->lastRunDate && count($this->ttEntries) > 0) : ?>
									<li id="accordion-id1-tt1">
										<div class="accordion-item-title">
											<span class="indicator">
												<span>+</span>
											</span>
											<h3>Event Entries</h3>
										</div>
										<div class="accordion-item-content" style="display:none;">
											<?php $this->entries = $this->ttEntries; ?>
											<?php echo $this->loadTemplate('entries'); ?>
										</div>
									</li>
								<?php endif; ?>
								<?php if (count($this->ttPending) > 0) : ?>
									<li id="accordion-id1-tt2">
										<div class="accordion-item-title">
											<span class="indicator">
												<span>+</span>
											</span>
											<h3>Pending Results</h3>
										</div>
										<div class="accordion-item-content" style="display:none;">
											<?php $this->pending = $this->ttPending; ?>
											<?php echo $this->loadTemplate('pending_results'); ?>
										</div>
									</li>
								<?php endif; ?>
								<?php if (count($this->ttRides) > 0) : ?>
									<li id="accordion-id1-tt3">
										<div class="accordion-item-title">
											<span class="indicator">
												<span>+</span>
											</span>
											<h3>Results</h3>
										</div>
										<div class="accordion-item-content" style="display:none;">
											<?php $this->rides = $this->ttRides; ?>
											<?php echo $this->loadTemplate('rides'); ?>
										</div>
									</li>
								<?php endif; ?>
								<?php if (count($this->ttAwards) > 0) : ?>
									<li id="accordion-id1-tt4">
										<div class="accordion-item-title">
											<span class="indicator">
												<span>+</span>
											</span>
											<h3>Awards</h3>
										</div>
										<div class="accordion-item-content" style="display:none; opacity:0;">
											<?php $this->awards = $this->ttAwards; ?>
											<?php echo $this->loadTemplate('awards'); ?>
										</div>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($hcdisplay) : ?>
					<div class="tab-pane <?php if (!$ttdisplay) : echo 'active'; endif; ?>" id="hillclimbs" role="tabpanel" >
						<div class="tt-rider-rank hillclimbs">
							<?php if (in_array($this->hcriderhistory->status, array('Frequent','Qualified'), true )) : ?>
								<div class="tt-column">
									<table class="tt-table-rankings">
										<tbody>
											<tr class="row1">
												<td class="tt-col-ranking-type tt-overall"><i class="fas fa-mountain" aria-hidden="true"></i>Overall</td>
												<td class="tt-col-ranking-value tt-overall"><?php echo '#' . $this->hcriderhistory->overall_rank; ?></td>
											</tr>
											<tr class="row2">
												<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>Rank</td>
												<td class="tt-col-ranking-value"><?php echo '#' . $this->hcriderhistory->gender_rank; ?></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="tt-column">
									<table class="tt-table-rankings">
										<tbody>
											<tr class="row1">
												<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i><?php echo $this->rider->age_category; ?></td>
												<td class="tt-col-ranking-value"><?php echo '#' . $this->hcriderhistory->age_category_rank; ?></td>
											</tr>
											<tr class="row2">
												<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i><?php echo $this->rider->district_name; ?></td>
												<td class="tt-col-ranking-value"><?php echo '#' . $this->hcriderhistory->district_rank; ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							<?php else : ?>
								<?php echo 'Provisional Ranking Status'; ?>
							<?php endif; ?>
						</div>
						<?php if (in_array($this->hcriderhistory->status,array('Frequent','Qualified','Provisional'), true )) : ?>
							<!-- Charts -->
							<div id="hcrides-chart-container" class="chart-container"></div>
							<?php echo $this->loadTemplate('hcchart'); ?>
						<?php endif; ?>
						<div class="tt-accordion-container">
							<ul id="tt-accordion-season-hillclimb" class="tt-accordion tt-level2">
								<?php if ($this->state->get('filter.year') == $this->lastRunDate && count($this->hcEntries) > 0) : ?>
									<li id="accordion-id1-hc1">
										<div class="accordion-item-title">
											<span class="indicator">
												<span>+</span>
											</span>
											<h3>Event Entries</h3>
										</div>
										<div class="accordion-item-content" style="display:none;">
											<?php $this->entries = $this->hcEntries; ?>
											<?php echo $this->loadTemplate('entries'); ?>
										</div>
									</li>
								<?php endif; ?>
								<?php if (count($this->hcPending) > 0) : ?>
									<li id="accordion-id1-hc2">
										<div class="accordion-item-title">
											<span class="indicator">
												<span>+</span>
											</span>
											<h3>Pending Results</h3>
										</div>
										<div class="accordion-item-content" style="display:none;">
											<?php $this->pending = $this->hcPending; ?>
											<?php echo $this->loadTemplate('pending_results'); ?>
										</div>
									</li>
								<?php endif; ?>
								<?php if (count($this->hcRides) > 0) : ?>
									<li id="accordion-id1-hc3">
										<div class="accordion-item-title">
											<span class="indicator">
												<span>+</span>
											</span>
											<h3>Results</h3>
										</div>
										<div class="accordion-item-content" style="display:none;">
											<?php $this->rides = $this->hcRides; ?>
											<?php echo $this->loadTemplate('rides'); ?>
										</div>
									</li>
								<?php endif; ?>
								<?php if (count($this->hcAwards) > 0) : ?>
									<li id="accordion-id1-hc4">
										<div class="accordion-item-title">
											<span class="indicator">
												<span>+</span>
											</span>
											<h3>Awards</h3>
										</div>
										<div class="accordion-item-content" style="display:none; opacity:0;">
											<?php $this->awards = $this->hcAwards; ?>
											<?php echo $this->loadTemplate('awards'); ?>
										</div>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
