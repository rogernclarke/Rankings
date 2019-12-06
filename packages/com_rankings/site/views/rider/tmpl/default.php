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

<?php echo $this->loadTemplate('chart'); ?>

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

	<?php if(count($this->ttRides + $this->hcRides) > 0) : ?>
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
				<?php echo $this->loadTemplate('ttrides'); ?>
				<?php echo $this->loadTemplate('hcrides'); ?>
				<?php echo $this->loadTemplate('awards'); ?>
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
