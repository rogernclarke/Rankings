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

<div class="accordion-item-title">
	<span class="indicator">
		<span>+</span>
	</span>
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
<div class="accordion-item-content" style="display:none; opacity:0;">
	
</div>
