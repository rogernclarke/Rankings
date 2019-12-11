<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php echo $this->loadTemplate('chart'); ?>

<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>
<div>
	<!-- Display chart -->
	<div id="container" style="width:100%; height:400px;"></div>
	<!-- Display Event Totals table
	<table class="table-hover tt-table">
		<thead>
			<tr>
				<th class="tt-col-district-code"><?php echo JText::_('COM_RANKINGS_DISTRICT_CODE'); ?></th>
				<th class="tt-col-district-name"><?php echo JText::_('COM_RANKINGS_DISTRICT_NAME'); ?></th>
				<th class="tt-col-event-count"><?php echo JText::_('COM_RANKINGS_EVENT_COUNT'); ?></th>
			</tr>
		</thead>
		<tbody>
			<!-- Display Event row
			<?php foreach ($this->districtCounts as $districtCount) : ?>
				<?php $this->districtCount = $districtCount; ?>
				<?php echo $this->loadTemplate('district'); ?>		
			<?php endforeach; ?>
		</tbody>
	</table>-->
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</div>
