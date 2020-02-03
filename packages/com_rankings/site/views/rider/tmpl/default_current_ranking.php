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

<div class="tt-rider-current-ranking">
	<div class="tt-rider-section-title">
		<h2>Current Ranking</h2>
	</div>
	<div class="tt-rider-section-content">
		<?php if (in_array($this->rider->status, array('Frequent','Qualified'), true )) : ?>
			<div class="tt-rider-rank timetrials">
				<div class="tt-rider-rank-content">
					<table class="tt-table-rankings">
						<thead>
							<tr>
								<th colspan="4"><h3><i class="fas fa-stopwatch" aria-hidden="true"></i>Time Trials</h3></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="tt-col-ranking-type tt-overall"><i class="fas fa-stopwatch" aria-hidden="true"></i>Overall</td>
								<td class="tt-col-ranking-value tt-overall"><?php echo '#' . $this->rider->overall_rank; ?></td>
								<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i><?php echo $this->rider->age_category; ?></td>
								<td class="tt-col-ranking-value"><?php echo '#' . $this->rider->age_category_rank; ?></td>
							</tr>
							<tr>
								<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>Rank</td>
								<td class="tt-col-ranking-value"><?php echo '#' . $this->rider->gender_rank; ?></td>
								<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i><?php echo $this->rider->district_name; ?></td>
								<td class="tt-col-ranking-value"><?php echo '#' . $this->rider->district_rank; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		<?php endif; ?>
		<?php if (in_array($this->rider->hc_status, array('Frequent','Qualified'), true )) : ?>
			<div class="tt-rider-rank hillclimbs">
				<div class="tt-rider-rank-content">
					<table class="tt-table-rankings">
						<thead>
							<tr>
								<th colspan="4"><h3><i class="fas fa-mountain" aria-hidden="true"></i>Hill Climbs</h3></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="tt-col-ranking-type tt-overall"><i class="fas fa-mountain" aria-hidden="true"></i>Overall</td>
								<td class="tt-col-ranking-value tt-overall"><?php echo '#' . $this->rider->hc_overall_rank; ?></td>
								<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i><?php echo $this->rider->age_category; ?></td>
								<td class="tt-col-ranking-value"><?php echo '#' . $this->rider->hc_age_category_rank; ?></td>
							</tr>
							<tr>
								<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>Rank</td>
								<td class="tt-col-ranking-value"><?php echo '#' . $this->rider->hc_gender_rank; ?></td>
								<td class="tt-col-ranking-type"><i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i><?php echo $this->rider->district_name; ?></td>
								<td class="tt-col-ranking-value"><?php echo '#' . $this->rider->hc_district_rank; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
