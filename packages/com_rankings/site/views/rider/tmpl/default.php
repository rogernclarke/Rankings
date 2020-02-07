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

<?php if (!$this->rider->blacklist_ind && !empty($this->rider->name)) : ?>
	<div class="tt-rider-title">
		<h1><?php echo $this->rider->name; ?></h1>
	</div>
	<div class="tt-rider-information">
		<div class="tt-rider-details">
			<div class="tt-club-name">
				<?php echo $this->rider->club_name; ?>
			</div>
			<div class="tt-age-gender-category">
				<?php echo $this->rider->age_gender_category; ?>
			</div>
		</div>
		<div class="tt-rider-rankings">
			<?php if (in_array($this->rider->status, array('Frequent','Qualified','Provisional'), true )) : ?>
				<div class="tt-rider-ranking">
					<div class="tt-rider-category">
						<div class="tt-ranking-type">
							<i class="fas fa-stopwatch" aria-hidden="true"></i><span class="hidden-phone">Time<br/>Trials</span>
						</div>
						<div class="tt-rider-category-status">
							<div class="tt-tag tt-tag-large tt-rider-category-<?php echo substr($this->rider->category, 0, 1); ?>">
								<span class="tt-tag-category-prefix">
									<?php echo substr($this->rider->category, 0, 1); ?>
								</span>
								<span class="tt-tag-category-suffix">
									<?php echo substr($this->rider->category, 1, 2); ?>
								</span>
							</div>
						</div>
					</div>
					<?php if (!empty($this->rider->status)) : ?>
						<div class="tt-rider-status">
							<div class="tt-tag tt-tag-small">
								<?php echo $this->rider->status; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if (in_array($this->rider->hc_status, array('Frequent','Qualified','Provisional'), true )) : ?>
				<div class="tt-rider-ranking">
					<div class="tt-rider-category">
						<div class="tt-ranking-type">
							<i class="fas fa-mountain" aria-hidden="true"></i><span class="hidden-phone">Hill<br/>Climbs</span>
						</div>
						<div class="tt-rider-category-status">
							<div class="tt-tag tt-tag-large tt-rider-category-<?php echo substr($this->rider->hc_category, 0, 1); ?>">
								<span class="tt-tag-category-prefix">
									<?php echo substr($this->rider->hc_category, 0, 1); ?>
								</span>
								<span class="tt-tag-category-suffix">
									<?php echo substr($this->rider->hc_category, 1, 2); ?>
								</span>
							</div>
						</div>
					</div>
					<?php if (!empty($this->rider->hc_status)) : ?>
						<div class="tt-rider-status">
							<div class="tt-tag tt-tag-small">
								<?php echo $this->rider->hc_status; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="tt-buttons">
			<input class="btn btn-info btn-small tt-btn-back" type="button" value="< Back" onClick="history.go(-1);return true;">
		</div>
	</div>
	<?php if ($this->state->get('filter.year') != $this->lastRunDate && (in_array($this->rider->status, array('Frequent','Qualified'), true ) | in_array($this->rider->hc_status, array('Frequent','Qualified'), true ))) : ; ?>
		<?php echo $this->loadTemplate('current_ranking'); ?>
	<?php endif; ?>
	<?php if ($this->state->get('filter.year') != $this->lastRunDate && count($this->ttEntries) > 0) : ?>
		<h3>Event Entries</h3>
		<?php $this->entries = array_merge($this->ttEntries, $this->hcEntries); ?>
		<?php echo $this->loadTemplate('entries'); ?>
	<?php endif; ?>
	<ul id="tt-accordion-rider" class="tt-accordion tt-level1">
		<?php if (count($this->rider->entries + $this->rider->hcentries + $this->rider->pending + $this->rider->hcpending + $this->rider->rides + $this->rider->hcrides) > 0) : ?>
			<li id="accordion-id1" class="tt-rider-season">
				<?php echo $this->loadTemplate('season'); ?>
			</li>
		<?php endif; ?>
	</ul>
<?php else : ?>
	<h1><?php echo "Rider not found"; ?></h1>
	<input class="btn btn-info btn-small tt-btn-back" type="button" value="< Back" onClick="history.go(-1);return true;">
<?php endif; ?>
<script>
	//set_tab("rider");
</script>
