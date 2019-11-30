<?php
/**
 * Rankings Latest Events Module for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>

<div class="owl-carousel owl-theme">
	<?php for ($j = 0; $j < count($events) / $itemRowCount; $j++) : ?>
		<div class="item" data-merge="1">
			<table class="table-hover tt-table tt-table-events">
				<thead>
					<tr>
						<th class="tt-col-event-date"><?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?></th>
						<th class="tt-col-event-name"><?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?></th>
						<th class="tt-col-event-course hidden-phone"><?php echo JText::_('COM_RANKINGS_COURSE'); ?></th>
						<th class="tt-col-event-distance hidden-phone"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?></th>
						<th class="tt-col-event-distance visible-phone"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i = $j * $itemRowCount, $n = $i + $itemRowCount; $i < $n; $i++) : ?>
						<tr>
							<td class="tt-col-event-date">
								<?php echo date('d M', strtotime($events[$i]->event_date)); ?>
							</td>
							<td class="tt-table-event-link tt-col-event-name">
								<div class="tt-flex-container">
									<div class="tt-event-name">
										<?php if ($events[$i]->hill_climb_ind) : ?>
											<i class="fas fa-mountain" aria-hidden="true"></i>
										<?php endif; ?>
										<a href="<?php echo $events[$i]->link; ?>"><?php echo $events[$i]->event_name; ?></a>
									</div>
									<?php if ($events[$i]->new_ind) : ?>
										<div class="tt-tag-container hidden-phone">
											<div class="tt-tag tt-tag-very-small tt-new"><?php echo JText::_('COM_RANKINGS_NEW'); ?></div>
										</div>
									<?php endif; ?>
								</div>
							</td>
							<td class="tt-col-event-course hidden-phone">
								<?php echo $events[$i]->course_code; ?>
							</td>
							<td class="tt-col-event-distance hidden-phone">
								<?php if ($events[$i]->duration_event_ind) : ?>
									<?php echo abs($events[$i]->distance) . ' hours'; ?>
								<?php elseif ($events[$i]->distance > 0) : ?>
									<?php echo round($events[$i]->distance, 1) . ' miles'; ?>
								<?php else : ?>
									<?php echo '-'; ?>
								<?php endif; ?>
							</td>
							<td class="tt-col-event-distance visible-phone">
								<?php if ($events[$i]->distance > 0) : ?>
									<?php echo round($events[$i]->distance, 1); ?>
								<?php else : ?>
									<?php echo '-'; ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endfor; ?>
				</tbody>       
			</table>
		</div>
	<?php endfor; ?>
</div>

<script>jQuery('.owl-carousel').owlCarousel({
	autoplay:false,
	autoplayTimeout:5000,
	autoplayHoverPause:true,
	items:1,
	loop:true,
	margin:24,
	nav:true,
	slideBy:1,
	responsive:{
		0:{
			items:1,
			margin:12
		},
		608:{
			items:1,
			margin:16
		},
		1000:{
		}
	}
})</script>
