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

<?php if(!empty($this->event->event_name)) : ?>
	<div>
		<h1><?php echo $this->event->event_name; ?></h1>
	</div>
	<div class="tt-event-details">
		<div class="tt-event">
			<div class="tt-event-date">
				<p><?php echo date('jS F Y', strtotime($this->event->event_date)); ?></p>
			</div>
			<div class="tt-distance">
				<p>
					<?php if ($this->event->duration_event_ind) : ?>
						<?php echo abs($this->event->distance) . ' hours'; ?>
					<?php else : ?>
						<?php if ($this->event->hill_climb_ind) : ?>
							<?php echo JText::_('COM_RANKINGS_HILL_CLIMB'); ?>
							<?php if ($this->event->distance > 0) : ?>
								<?php echo ' - ' . (float) $this->event->distance . ' miles'; ?>
							<?php endif; ?>
						<?php else : ?>
							<?php echo (float) $this->event->distance . ' miles'; ?>
						<?php endif; ?>
					<?php endif; ?>
				</p>
			</div>
			<div class="tt-course">
				<span class="tt-label"><?php echo JText::_('COM_RANKINGS_COURSE'); ?></span>
				<span class="tt-text"><?php echo $this->event->course_code; ?></span>
			</div>
			<div class="tt-buttons">
				<input class="btn btn-info btn-small tt-btn-back" type="button" value="< Back" onClick="history.go(-1);return true;">
			</div>
		</div>
		<div class="tt-event-external">
			<div class="tt-ctt-link">
				<a href="https://www.cyclingtimetrials.org.uk/race-details/<?php echo $this->event->event_id; ?>" target="_blank">CTT website event page <i class="fas fa-external-link-alt"></i></a>
			</div>
		</div>
	</div>

	

	<?php if (count($this->event->entries) + count($this->event->results) > 0) : ?>
		<section class="tt-rides-section" id="tt-event-tabs">
			<!-- Nav tabs -->
			<ul class="tt-nav-tabs" role="tablist">
				<li role="presentation" class="<?php if (!$this->event->startsheet_ind) : echo "disabled"; elseif (!$this->event->results_ind) : echo "active"; endif; ?>">
					<?php if ($this->event->startsheet_ind) : ?>
						<a href="#startsheet" aria-controls="startsheet" role="tab" data-toggle="tab" data-context="event">
							<i class="fa fa-search" aria-hidden="true"></i>
							<p>Start Sheet</p>
						</a>
					<?php else : ?>
						<div>
							<i class="fa fa-search" aria-hidden="true"></i>
							<p>Start Sheet</p>
						</div>
					<?php endif; ?>
				</li>
				<li role="presentation" class="<?php if ($this->event->results_ind) : echo "active"; else : echo "disabled"; endif; ?>">
					<?php if ($this->event->results_ind) : ?>
						<a href="#results" aria-controls="results" role="tab" data-toggle="tab" data-context="event">
							<i class="fas fa-paste" aria-hidden="true"></i>
							<p>Results</p>
						</a>
					<?php else : ?>
						<div>
							<i class="fas fa-paste" aria-hidden="true"></i>
							<p>Results</p>
						</div>
					<?php endif; ?>
				</li>
				<li role="presentation" class="<?php if (!$this->event->results_ind) : echo "disabled"; endif; ?>">
					<?php if ($this->event->results_ind) : ?>
						<a href="#awards" aria-controls="awards" role="tab" data-toggle="tab" data-context="event">
							<i class="fas fa-award" aria-hidden="true"></i>
							<p>Awards</p>
						</a>
					<?php else : ?>
						<div>
							<i class="fas fa-award" aria-hidden="true"></i>
							<p>Awards</p>
						</div>
					<?php endif; ?>
				</li>
			</ul>
			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane <?php if (!$this->event->results_ind && $this->event->startsheet_ind) : echo "active"; endif; ?>" id="startsheet">
					<div class="tt-nav-tab-content">
						<div class="tt-list-heading">
							<div class="tt-list-title">
								<h2><?php echo JText::_('COM_RANKINGS_EVENT_STARTSHEET'); ?></h2>
							</div>
							<div class="tt-rider-count">
								<p><?php echo count($this->event->entries) . ' entries'; ?></p>
							</div>
						</div>                        
						<div class="tabs tabs-style-topline tt-tabs-startsheet">
							<nav>
								<ul>
									<li id="tt-start-order"><button type="button" onclick="sort_bib();"><i class="far fa-clock-o" aria-hidden="true"></i><p><?php echo JText::_('COM_RANKINGS_EVENT_STARTING_ORDER'); ?></p></button></li>
									<?php if(!$this->event->duration_event_ind && $this->event->predictedResults) : ?>
										<li id="tt-finish-order"><button type="button" onclick="sort_predicted_finish();"><i class="fa fa-flag-checkered" aria-hidden="true"></i><p><?php echo JText::_('COM_RANKINGS_EVENT_PREDICTED_FINISHING_ORDER'); ?></p></button></li>
									<?php endif; ?>
									<li class="tab-current" id="tt-result-order"><button type="button" onclick="sort_predicted_position();"><i class="fa fa-sort-amount-asc" aria-hidden="true"></i><p><?php echo JText::_('COM_RANKINGS_EVENT_PREDICTED_FINISHING_POSITION'); ?></p></button></li>
								</ul>
							</nav>
							<div class="tt-tab-panel">
								<table class="table-hover tt-table" id="tt-event-startsheet">
									<thead>
										<tr>
											<th class="tt-col-rider-bib" rowspan="2"><?php echo JText::_('COM_RANKINGS_RIDE_BIB'); ?></th>
											<th class="tt-col-rider-start-time hidden-phone" rowspan="2"><?php echo JText::_('COM_RANKINGS_RIDE_START_TIME'); ?></th>
											<th class="tt-col-rider-name" rowspan="2"><?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?></th>
											<th class="tt-col-club-name hidden-phone" rowspan="2"><?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?></th>
											<th class="tt-col-age-gender-category hidden-tablet hidden-phone" rowspan="2"><?php echo JText::_('COM_RANKINGS_RIDER_CATEGORY'); ?></th>
											<th class="tt-col-predicted-time-at-finish hidden-tablet hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDE_PREDICTED_TIME_AT_FINISH'); ?></th>
											<th class="tt-col-ride-predicted-position visible-desktop" rowspan="2"><?php echo JText::_('COM_RANKINGS_EVENT_PREDICTED_POSITION'); ?></th>
											<th class="tt-col-ride-predicted-result visible-desktop" rowspan="2">
												<?php if($this->event->duration_event_ind) : ?>
													<?php echo JText::_('COM_RANKINGS_RIDE_PREDICTED_DISTANCE'); ?>
												<?php  else : ?>
													<?php echo JText::_('COM_RANKINGS_RIDE_PREDICTED_TIME'); ?>
												<?php endif; ?>
											</th>
											<th class="tt-col-ride-predicted hidden-desktop" colspan="2" rowspan="1"><?php echo JText::_('COM_RANKINGS_RIDE_PREDICTED'); ?></th>
										</tr>
										<tr class="hidden-desktop">
											<th class="tt-col-ride-predicted-position hidden-desktop" rowspan="1"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION_SHORT'); ?></th>
											<th class="tt-col-ride-predicted-result hidden-desktop" rowspan="1">
												<?php if($this->event->duration_event_ind) : ?>
													<?php echo JText::_('COM_RANKINGS_RIDE_DISTANCE_SHORT'); ?>
												<?php else : ?>
													<?php echo JText::_('COM_RANKINGS_RIDE_TIME'); ?>
												<?php endif; ?>
											</th>
										</tr>
									</thead>
									<tbody id="tt-event-startsheet-body">
										<?php foreach($this->event->entries as $entry) : ?>
											<?php $this->entry = $entry; ?>
											<?php echo $this->loadTemplate('entry'); ?>
										<?php endforeach; ?>
									</tbody>       
								</table>
							</div><!-- /content -->
						</div><!-- /tabs -->
					</div>
				</div>
				<div role="tabpanel" class="tab-pane <?php if ($this->event->results_ind) { echo "active"; } else if (!$this->event->startsheet_ind) { echo "active"; } ?>" id="results">
					<div class="tt-nav-tab-content">
						<div class="tt-list-heading">
							<div class="tt-list-title">
								<h2><?php echo JText::_('COM_RANKINGS_EVENT_RESULTS'); ?></h2>
							</div>
							<div class="tt-rider-count">
								<p><?php echo count($this->event->results) . ' riders'; ?></p>
							</div>
						</div>

						<?php if(!$this->event->ranking_event_ind) : ?>
							<?php if(in_array(date('M', strtotime($this->event->event_date)), array("Nov", "Dec", "Jan"))) : ?>
								<p class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i><?php echo JText::_('COM_RANKINGS_EVENT_OUT_OF_SEASON'); ?></p>
							<?php else : ?>
								<p class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i><?php echo JText::_('COM_RANKINGS_EVENT_INSUFFICIENT_DATA'); ?></p>
							<?php endif; ?>
						<?php endif; ?>

						<div class="tabs tabs-style-topline tt-tabs-results">
							<nav>
								<ul>
									<li class="tab-current" id="tt-overall-filter"><button type="button" onclick="filter_results_overall();"><i class="far fa-clock-o" aria-hidden="true"></i><p>Overall</p></button></li>
									<?php if ($this->event->maleResults) : ?>
										<li id="tt-male-filter"><button type="button" onclick="filter_results_male();"><i class="fa fa-mars" aria-hidden="true"></i><p>Men</p></button></li>
									<?php endif; ?>
									<?php if ($this->event->femaleResults) :?>
										<li id="tt-female-filter"><button type="button" onclick="filter_results_female();"><i class="fa fa-venus" aria-hidden="true"></i><p>Women</p></button></li>
									<?php endif; ?>
									<?php if ($this->event->vetsResults) : ?>
										<li id="tt-veterans-filter"><button type="button" onclick="filter_results_veterans();"><i class="fa fa-plus" aria-hidden="true"></i><p>Veterans</p></button></li>
									<?php endif; ?>
								</ul>
							</nav>
							<div class="tt-tab-panel">

								<table class="table-hover tt-table" id="tt-event-results">
									<thead>
										<tr>
											<th class="tt-col-event-position"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?></th>
											<th class="tt-col-event-gender-position"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?></th>
											<th class="tt-col-event-vets-position"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?></th>
											<th class="tt-col-rider-name"><?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?></th>
											<th class="tt-col-club-name hidden-phone"><?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?></th>
											<th class="tt-col-age-gender-category visible-large"><?php echo JText::_('COM_RANKINGS_RIDER_CATEGORY'); ?></th>
											<th class="tt-col-ride-predicted-result hidden-tablet hidden-phone" rowspan="2">
												<?php if($this->event->duration_event_ind) : ?>
													<?php echo JText::_('COM_RANKINGS_RIDE_PREDICTED_DISTANCE'); ?>
												<?php else : ?>
													<?php echo JText::_('COM_RANKINGS_RIDE_PREDICTED_TIME'); ?>
												<?php endif; ?>
											</th>
											<th class="tt-col-ride-result hidden-tablet hidden-phone">
												<?php if($this->event->duration_event_ind) : ?>
													<?php echo JText::_('COM_RANKINGS_RIDE_DISTANCE'); ?>
												<?php else : ?>
													<?php echo JText::_('COM_RANKINGS_RIDE_TIME'); ?>
												<?php endif; ?>
											</th>
											<th class="tt-col-ride-result hidden-desktop">
												<?php if($this->event->duration_event_ind) : ?>
													<?php echo JText::_('COM_RANKINGS_RIDE_DISTANCE_SHORT'); ?>
												<?php else : ?>
													<?php echo JText::_('COM_RANKINGS_RIDE_TIME'); ?>
												<?php endif; ?>
											</th>
											<th class="tt-col-event-ride-points hidden-tablet hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?></th>
											<th class="tt-col-event-ride-points hidden-desktop"><?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS_SHORT'); ?></th>
											<th class="tt-col-ride-vets-standard-time hidden-tablet hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDE_VETS_STANDARD'); ?></th>
											<th class="tt-col-ride-vets-standard-time hidden-desktop"><?php echo JText::_('COM_RANKINGS_RIDE_VETS_STANDARD_SHORT'); ?></th>
											<th class="tt-col-ride-vets-standard-result"><?php echo JText::_('COM_RANKINGS_RIDE_VETS_STANDARD_RESULT'); ?></th>
										</tr>
									</thead>
									<tbody id="tt-event-results-body">
										<?php foreach($this->event->results as $result) : ?>
											<?php $this->result = $result; ?>
											<?php echo $this->loadTemplate('result'); ?>
										<?php endforeach; ?>
									</tbody>
								</table>
								<div class="tt-tab-vets-footer">
									<p><?php echo JText::_('COM_RANKINGS_EVENT_VETS_FOOTER_MESSAGE'); ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="awards">
					<div class="tt-nav-tab-content">
						<div class="tt-list-heading">
							<div class="tt-list-title">
								<h2><?php echo JText::_('COM_RANKINGS_EVENT_AWARDS'); ?></h2>
							</div>
						</div>
						<?php $previousAwardName = null;

						foreach ($this->event->awards as $award) : ?>
							<?php if ($previousAwardName !== $award->awardName) : ?>
								<?php if (isset($previousAwardName)) : ?>
									</tbody>
								</table>
								<?php endif; ?>
								<table class="table-hover tt-table tt-event-awards">
									<thead>
										<tr>
											<?php if (!$award->team_ind) : ?>
												<th class="tt-col-award-position"></th>
												<th class="tt-col-award-rider-name"></th>
												<th class="tt-col-award-club-name hidden-phone"></th>
												<th class="tt-col-award-result"></th>
											<?php else : ?>
												<th class="tt-col-award-position"></th>
												<th class="tt-col-award-club-name"></th>
												<th class="tt-col-award-riders hidden-small-phone"></th>
												<th class="tt-col-award-individual-result hidden-phone"></th>
												<th class="tt-col-award-team-result"></th>
											<?php endif; ?>
										</tr>
										<tr>
											<th class="tt-col-award-name" colspan="5"><?php echo $award->awardName; ?></th>
										</tr>
									</thead>
									<tbody>
								<?php $previousAwardName = $award->awardName; ?>
							<?php endif; ?>
							<?php $this->award = $award; ?>
							<?php echo $this->loadTemplate('award'); ?>
						<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>
	<?php else : ?>
		<h2><?php echo JText::_('COM_RANKINGS_EVENT_NO_RESULTS'); ?></h2>
	<?php endif; ?>
<?php else : ?>
	<h1><?php echo "Event not found"; ?></h1>
	<input class="btn btn-info btn-small tt-btn-back" type="button" value="< Back" onClick="history.go(-1);return true;">
<?php endif; ?>
<script>
	set_tab("event");
</script>
