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

<h1><?php echo $this->escape($this->params->get('page_title')); ?></h1>

<form action="<?php echo JRoute::_('index.php?option=com_rankings'); ?>" method="post" name="AdminForm" id="AdminForm">
	<div class="tt-statistics-form">
		<! –– Display filters -->
		<div class="form-horizontal tt-statistics-filters">
			<fieldset class="adminform">
				<div>
					<?php
					// Set filter values for display and render
					$this->form->setValue('filter_year', null, $this->escape($this->state->get('filter.year')));
					echo $this->form->renderFieldset('statistics_toolbar'); ?>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<div>
	<!-- Display Riders table -->
	<table class="table-hover tt-table">
		<thead>
			<tr>
				<th class="tt-col-rider-name"><?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?></th>
				<th class="tt-col-club-name"><?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?></th>
				<th class="tt-col-ride-total"><?php echo JText::_('COM_RANKINGS_RIDE_TOTAL'); ?></th>
			</tr>
		</thead>
		<tbody>
			<!-- Display Event row -->
			<?php foreach ($this->riders as $rider) : ?>
				<?php $this->rider = $rider; ?>
				<?php echo $this->loadTemplate('rider'); ?>		
			<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</div>
