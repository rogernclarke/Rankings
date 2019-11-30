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

foreach ($this->rider->riderhistories as $riderhistory)
{
	$effectiveDate 	= strtotime($riderhistory->effective_date) * 1000;
	$score 			= $riderhistory->score;
	$data[] 		= "[$effectiveDate, $score]";
}

?>
<script>document.addEventListener('DOMContentLoaded', function () {
		var myChart = Highcharts.chart('container', {
			/*chart: {
				type: 'bar'
			},*/
			title: {
				text: 'TT Score'
			},
			xAxis: {
				type: 'datetime',
				dateTimeLabelFormats: {day: '%b'}
				//categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov']
			},
			yAxis: {
				title: {
					text: 'Score'
				}
			},
			series: [{
				data: [<?php echo join($data, ','); ?>]
			}]
			/*series: [{
				name: '2018',
				data: [2166, 2140, 2066, 1498, 1419, 1286, 1147, 1094, 1107, 1105]
			}, {
				name: '2019',
				data: [1101, 1119, 1107, 1091, 1149, 1148, 1196, 1199, 1093, 1083]
			}]*/
		});
	});</script>

<?php if(!$this->rider->blacklist_ind && !empty($this->rider->name)) : ?>
	<div class="tt-rider-heading">
		<div class="tt-rider-name">
			<h1><?php echo $this->rider->name; ?></h1>
		</div>
		<div class="tt-rider-category timetrials active">
			<i class="fas fa-stopwatch" aria-hidden="true"></i>
			<?php if(in_array($this->rider->status, array('Frequent rider','Qualified','Provisional',''), true )) : ?>
				<div class="tt-tag tt-tag-large tt-rider-category-<?php echo substr($this->rider->category, 0, 1); ?>">
					<span class="tt-tag-category-prefix">
						<?php echo substr($this->rider->category, 0, 1); ?>
					</span>
					<span class="tt-tag-category-suffix">
						<?php echo substr($this->rider->category, 1, 2); ?>
					</span>
				</div>
			<?php endif; ?>
			<?php if (!empty($this->rider->status)) : ?>
				<div class="tt-rider-status">
					<div class="tt-tag tt-tag-small">
						<?php echo $this->rider->status; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="tt-rider-category hillclimbs">
			<i class="fas fa-mountain" aria-hidden="true"></i>
			<?php if ($this->rider->hc_status !== "Lapsed") : ?>
				<div class="tt-tag tt-tag-large tt-rider-category-<?php echo substr($this->rider->hc_category, 0, 1); ?>">
					<span class="tt-tag-category-prefix">
						<?php echo substr($this->rider->hc_category, 0, 1); ?>
					</span>
					<span class="tt-tag-category-suffix">
						<?php echo substr($this->rider->hc_category, 1, 2); ?>
					</span>
				</div>
			<?php endif; ?>
			<?php if (!empty($this->rider->hc_status)) : ?>
				<div class="tt-rider-status">
					<div class="tt-tag tt-tag-small">
						<?php echo $this->rider->hc_status; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="tt-rider-details">
		<div class="tt-rider">
			<div class="tt-club-name">
				<?php echo $this->rider->club_name; ?>
			</div>
			<div class="tt-age-gender-category">
				<?php echo $this->rider->age_gender_category; ?>
			</div>
			<div class="tt-buttons">
				<input class="btn btn-info btn-small tt-btn-back" type="button" value="< Back" onClick="history.go(-1);return true;">
			</div>
		</div>
		<?php if (!in_array($this->rider->status, array('Provisional', 'Lapsed', ''), true )) : ?>
			<div class="tt-rider-rank timetrials active">
				<div class="tt-overall">
					<?php echo 'Overall #' . $this->rider->overall_rank; ?>
				</div>
				<div>
					<i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>
					<?php echo 'Rank #' . $this->rider->gender_rank; ?>
				</div>
				<div>
					<i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>
					<?php echo $this->rider->age_category . ' #' . $this->rider->age_category_rank; ?>
				</div>
				<div>
					<i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>
					<?php echo $this->rider->district_name . ' #' . $this->rider->district_rank; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if (!in_array($this->rider->hc_status, array('Provisional', 'Lapsed', ''), true )) : ?>
			<div class="tt-rider-rank hillclimbs">
				<div class="tt-overall">
					<?php echo 'Overall #' . $this->rider->hc_overall_rank; ?>
				</div>
				<div>
					<i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>
					<?php echo 'Rank #' . $this->rider->hc_gender_rank; ?>
				</div>
				<div>
					<i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>
					<?php echo $this->rider->age_category . ' #' . $this->rider->hc_age_category_rank; ?>
				</div>
				<div>
					<i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>
					<?php echo $this->rider->district_name . ' #' . $this->rider->hc_district_rank; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php if(count($this->rider->ttRides + $this->rider->hcRides) > 0) : ?>
		<section class="tt-rides-section" id="tt-rider-tabs">
			<!-- Nav tabs -->
			<ul class="tt-nav-tabs" role="tablist">
				<li role="presentation" class="active">
					<a href="#timetrials" aria-controls="time trials" role="tab" data-toggle="tab" data-context="rider">
						<i class="fas fa-stopwatch" aria-hidden="true"></i>
						<p>Time Trials</p>
					</a>
				</li>
				<li role="presentation">
					<a href="#hillclimbs" aria-controls="hill climbs" role="tab" data-toggle="tab" data-context="rider">
						<i class="fas fa-mountain" aria-hidden="true"></i>
						<p>Hill Climbs</p>
					</a>
				</li>
				<li role="presentation">
					<a href="#awards" aria-controls="awards" role="tab" data-toggle="tab" data-context="rider">
						<i class="fas fa-trophy" aria-hidden="true"></i>
						<p>Awards</p>
					</a>
				</li>
			</ul>
			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="timetrials">
					<div class="tt-nav-tab-content">
						<?php if (count($this->rider->ttRides) == 0) : ?>
							<div class="tt-list-heading">
								<div class="tt-list-title">
									<h2><?php echo "No results since 1st January 2017"; ?></h2>
								</div>
							</div>
						<?php else : ?>
							<!-- Charts -->
							<div id="container" style="width:100%; height:400px;"></div>
							<!-- List content -->
							<div class="tt-list-heading">
								<div class="tt-list-title">
									<h2><?php echo JText::_('COM_RANKINGS_RIDER_RESULTS'); ?></h2>
								</div>
							</div>
							<div class="tt-tab-panel">
								<! -- Display Rides table -->
								<table class="table-hover tt-table tt-rides">
								<thead>
									<tr>
										<th class="tt-col-event-date"><?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?></th>
										<th class="tt-col-event-name"><?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?></th>
										<th class="tt-col-ride-distance visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?></th>
										<th class="tt-col-ride-distance hidden-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?></th>
										<th class="tt-col-ride-position visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?></th>
										<th class="tt-col-ride-position hidden-desktop hidden-phone"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION_SHORT'); ?></th>
										<th class="tt-col-ride-result hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDE_RESULT'); ?></th>
										<th class="tt-col-rider-ride-points"><?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?></th>
									</tr>
								</thead>
								<tbody>
									<! -- Display Ride row -->
									<?php foreach($this->rider->ttRides as $i => $ride) : ?>
										<?php if(($i == 0) or ($i > 0 && date('Y', strtotime($ride->event_date)) != date('Y', strtotime($this->rider->ttRides[$i-1]->event_date)))) : ?>
											<tr class="tt-table-year-row">
												<td colspan="6"><?php echo date('Y', strtotime($ride->event_date)); ?></td>
											</tr>
										<?php endif; ?>
										<?php $this->ride = $ride; ?>
										<?php echo $this->loadTemplate('ride'); ?>
									<?php endforeach; ?>
								</tbody>       
								</table>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="hillclimbs">
					<div class="tt-nav-tab-content">
						<?php if (count($this->rider->hcRides) == 0) : ?>
							<div class="tt-list-heading">
								<div class="tt-list-title">
									<h2><?php echo "No results since 1st January 2017"; ?></h2>
								</div>
							</div>
						<?php else : ?>
							<div class="tt-list-heading">
								<div class="tt-list-title">
									<h2><?php echo JText::_('COM_RANKINGS_RIDER_RESULTS'); ?></h2>
								</div>
							</div>
							<div class="tt-tab-panel">
								<table class="table-hover tt-table tt-rides">
								<thead>
									<tr>
										<th class="tt-col-event-date"><?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?></th>
										<th class="tt-col-event-name"><?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?></th>
										<th class="tt-col-ride-distance visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?></th>
										<th class="tt-col-ride-distance hidden-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?></th>
										<th class="tt-col-ride-position visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?></th>
										<th class="tt-col-ride-position hidden-desktop hidden-phone"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION_SHORT'); ?></th>
										<th class="tt-col-ride-result hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDE_RESULT'); ?></th>
										<th class="tt-col-rider-ride-points"><?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($this->rider->hcRides as $i => $ride) : ?> 
										<?php if(($i == 0) or ($i > 0 && date('Y', strtotime($ride->event_date)) != date('Y', strtotime($this->rider->hcRides[$i-1]->event_date)))) : ?>
											<tr class="tt-table-year-row">
												<td colspan="6"><?php echo date('Y', strtotime($ride->event_date)); ?></td>
											</tr>
										<?php endif; ?>
										<?php $this->ride = $ride; ?>
										<?php echo $this->loadTemplate('ride'); ?>
									<?php endforeach; ?>
								</tbody>       
								</table>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="awards">
					<div class="tt-nav-tab-content">
						<div class="tt-list-heading">
							<div class="tt-list-title">
								<h2><?php echo JText::_('COM_RANKINGS_EVENT_AWARDS'); ?></h2>
							</div>
						</div>
						<?php if (count($this->rider->awards) > 0) : ?>
							<table class="table-hover tt-table tt-rider-awards-list">
								<thead>
									<tr>
										<th class="tt-col-event-date"><?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?></th>
										<th class="tt-col-event-name"><?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?></th>
										<th class="tt-col-ride-distance visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?></th>
										<th class="tt-col-ride-distance hidden-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?></th>
										<th class="tt-col-award-name"><?php echo JText::_('COM_RANKINGS_AWARD_NAME'); ?></th>
										<th class="tt-col-award-result"><?php echo JText::_('COM_RANKINGS_AWARD_RESULT'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php $previousEventId = null; ?>
									<?php foreach($this->rider->awards as $i => $award) : ?>
										<?php if($previousEventId !== $award->event_id) : ?>
											<?php $newEvent      	= true; ?>
											<?php $eventAwardCount  = 0; ?>
											<?php $previousEventId  = $award->event_id; ?>
											<?php for($j = $i; $j < count($this->rider->awards) && $award->event_id === $this->rider->awards[$j]->event_id; $j++) : ?>
												<?php $eventAwardCount++; ?>
											<?php endfor; ?>
										<?php else : ?>
											<?php $newEvent = false; ?>
										<?php endif; ?>
										<?php if (($i == 0) or ($i > 0 && date('Y', strtotime($award->event_date)) != date('Y', strtotime($this->rider->awards[$i-1]->event_date)))): ?>
											<tr class="tt-table-year-row">
												<td colspan="7"><?php echo date('Y', strtotime($award->event_date)); ?></td>
											</tr>
										<?php endif; ?>
										<?php $this->award            = $award; ?>
										<?php $this->newEvent         = $newEvent; ?>
										<?php $this->eventAwardCount  = $eventAwardCount; ?>
										<?php echo $this->loadTemplate('award'); ?>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else : ?>
							<h3><?php echo "No awards to date..."; ?></h3>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
	<?php else : ?>
		<h2><?php echo "No results since 1st January 2017"; ?></h2>
	<?php endif; ?>
<?php else : ?>
	<h1><?php echo "Rider not found"; ?></h1>
	<input class="btn btn-info btn-small tt-btn-back" type="button" value="< Back" onClick="history.go(-1);return true;">
<?php endif; ?>
<script>
	set_tab("rider");
</script>
