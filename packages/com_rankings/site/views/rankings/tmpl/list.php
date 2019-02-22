<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.3
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<h1><?php echo JText::_('COM_RANKINGS_RANKINGS'); ?></h1>

<form action="<?php echo JRoute::_('index.php?option=com_rankings'); ?>" method="post" name="AdminForm" id="AdminForm">
	<div class="tt-list-form">
		<div class="form-horizontal tt-list-filters">
			<fieldset class="adminform">
				<div>
					<?php
						$this->form->setValue('filter_gender', null, $this->escape($this->state->get('filter.gender')));
						$this->form->setValue('filter_age_category', null, $this->escape($this->state->get('filter.age_category')));
						$this->form->setValue('filter_district_code', null, $this->escape($this->state->get('filter.district_code')));
						$this->form->setValue('filter_club_name', null, $this->escape($this->state->get('filter.club_name')));
						$this->form->setValue('filter_name', null, $this->escape($this->state->get('filter.name')));
						$this->form->setValue('check_status', null, $this->escape($this->state->get('check.status')));
						echo $this->form->renderFieldset('rankings_list_toolbar');
    					if (($this->state->get('filter.age_category') != 'All' &&
    						!empty($this->state->get('filter.age_category'))) ||
    						($this->state->get('filter.district_code') != 'All' &&
    						!empty($this->state->get('filter.district_code'))) ||
    						$this->state->get('filter.name') != '' ||
    						$this->state->get('filter.club_name') != '')
						{ 
							$position_ind = TRUE;
							$columns = 7;
						} else {
							$position_ind = FALSE;
							$columns = 6;
						}
					?>
				</div>
			</fieldset>
			<div class="btn-group pull-right">
				<button type="submit" class="btn hasTooltip tt-list-filter-btn" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>

				<button type="button" class="btn hasTooltip tt-list-filter-btn" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('jform_gender_filter').value='All';document.getElementById('jform_age_category_filter').value='All';document.getElementById('jform_district_code_filter').value='All';document.getElementById('jform_name_filter').value='';document.getElementById('jform_club_name_filter').value='';document.getElementById('jform_status_check').value=0;document.getElementById('reset_check').value=1;this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="btn-group pull-right tt-list-limit hidden-phone">
			<label for="com_rankings.rankings.limit">
				<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>
    <table id="tt-table-rankings" class="table-hover tt-table">
		<thead>
			<tr>
				<?php if ($position_ind)
				{ ?>
					<th class="tt-col-position">
	            	</th>
	            <?php
	        	} ?>
				<th class="tt-col-selector">
	            </th>
				<th class="tt-col-rank-status">
	                <?php echo JText::_('COM_RANKINGS_RANK'); ?>
    	    	</th>
    	    	<th class="tt-col-rider-name">
	                <?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?>
    	    	</th>
    	    	<th class="tt-col-age-gender-category hidden-tablet hidden-phone">
	                <?php echo JText::_('COM_RANKINGS_RIDER_AGE_GENDER_CATEGORY'); ?>
    	    	</th>
    	    	<th class="tt-col-club-name hidden-phone">
	                <?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?>
    	    	</th>
        		<th class="tt-col-score">
            	    <?php echo JText::_('COM_RANKINGS_RIDER_SCORE'); ?>
        		</th>
        	</tr>
		</thead>
		<tbody>
			<?php for($i=0, $n = count($this->rankings); $i<$n; $i++) 
    		{
    			$this->_rankingsListView->rider = $this->rankings[$i]; ?>
    			<tr class="row-<?php echo $i % 2; ?>">
    				<?php 
    				if ($position_ind)
					{ ?>
    					<td class="tt-col-position">
    						<?php echo $this->_rankingsListView->rider->position; ?>
    					</td>
    				<?php 
    				} ?>
    				<td class="tt-col-selector">
    					<button type="button" onclick="ttToggleRides(<?php echo $i+1; ?>)" class="btn tt-btn-selector">
    						<i id="tt-rankings-row-<?php echo $i+1; ?>-icon" class="fa fa-angle-right fa-lg"></i>
    					</button>
    				</td>
    				<td class="tt-col-rank-status">
    					<div class="tt-rank-status">
    						<div class="tt-rank">
    							<?php switch ($this->state->get('filter.gender'))
    							{
    								case "Female":
    								case "Male":
    									echo $this->_rankingsListView->rider->gender_rank;
    									break;
    								default:
    									echo $this->_rankingsListView->rider->overall_rank;
	    						} ?>
	    					</div>
        					<?php if ($this->_rankingsListView->rider->status === 'Provisional')
        					{ ?>
            					<div class="tt-rider-status">
    								<div class="tt-tag tt-tag-very-small hidden-phone">
            							<?php echo "Provisional"; ?>
            						</div>
            						<div class="tt-tag tt-tag-very-small hidden-desktop hidden-tablet">
            							<?php echo "P"; ?>
            						</div>
            					</div>
        					<?php
        					} else { ?>
        						<div class="tt-rank-change">
        							<?php switch ($this->state->get('filter.gender'))
    							{
    								case "Female":
    								case "Male": ?>
    									<i class="fa fa-<?php echo $this->_rankingsListView->rider->change_in_gender_rank_ind; ?>"></i>
        								<?php if (!$this->_rankingsListView->rider->change_in_gender_rank_value == 0)
                                		{
                                    		echo $this->_rankingsListView->rider->change_in_gender_rank_value;
                                		}
    									break;
    								default: ?>
    									<i class="fa fa-<?php echo $this->_rankingsListView->rider->change_in_overall_rank_ind; ?>"></i>
        								<?php if (!$this->_rankingsListView->rider->change_in_overall_rank_value == 0)
                                		{
                                    		echo $this->_rankingsListView->rider->change_in_overall_rank_value;
                                		}
	    						} ?>
                                </div>
        					<?php
        					} ?>
    					</div>
    				</td>
					<td class="tt-col-rider-name tt-table-rider-link">
						<a href="<?php echo JRoute::_('index.php?Itemid=816&option=com_rankings&task=rider.display&cid='.$this->_rankingsListView->rider->rider_id); ?>"><?php echo $this->_rankingsListView->rider->name; ?>
						</a>
					</td>
					<td class="tt-col-age-gender-category hidden-tablet hidden-phone">
						<?php echo $this->_rankingsListView->rider->age_gender_category; ?>
					</td>
					<td class="tt-col-club-name hidden-phone">
						<?php echo $this->_rankingsListView->rider->club_name; ?>
					</td>
					<td class="tt-col-score">
						<?php echo $this->_rankingsListView->rider->score; ?>
					</td>
				</tr>
				<tr id="tt-rankings-<?php echo $i+1; ?>-rides" style="display:none">
					<td colspan="<?php echo $columns; ?>">
						<div class="tt-rides-box">
							<table class="table-hover tt-table tt-rankings-rides">
								<thead>
									<tr>
										<th class="tt-col-event-date">
											<?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?>
										</th>
										<th class="tt-col-event-name">
											<?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?>
										</th>
										<th class="tt-col-event-distance hidden-phone">
											<?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?>
										</th>
										<th class="tt-col-event-distance visible-phone">
											<?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?>
										</th>
										<th class="tt-col-ranking-points hidden-phone">
											<?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?>
										</th>
										<th class="tt-col-ranking-points hidden-desktop hidden-tablet">
											<?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS_SHORT'); ?>
										</th>
									</tr>
								</thead>
								<tbody>
									<?php for($j=0, $max = count($this->rankings[$i]->rides); $j<$max; $j++)
    								{
    									$this->_ridesListView->ride = $this->rankings[$i]->rides[$j]; ?>
										<tr id="tt-rankings-<?php echo $i+1; ?>-rides-<?php echo $j+1; ?>" class="tt-rankings-<?php if ($this->_ridesListView->ride->counting_ride_ind) { echo "counting-ride";} else { echo "non-counting-ride";} ?>">
											<td class="tt-col-event-date">
												<?php echo date('d M y', strtotime($this->_ridesListView->ride->event_date)); ?>
											</td>
											<td class="tt-col-event-name tt-table-event-link">
                        <a href="<?php echo JRoute::_('index.php?Itemid=454&option=com_rankings&task=event.display&cid=' . $this->_ridesListView->ride->event_id); ?>" rel="nofollow"><?php echo $this->_ridesListView->ride->event_name; ?>
                        </a>
                    						</td>
											<td class="tt-col-event-distance hidden-phone">
												<?php if(!empty($this->_ridesListView->ride->ride_distance))
    						                    {
                            						echo $this->_ridesListView->ride->distance . ' hours';
                        						} else {
                            						echo abs($this->_ridesListView->ride->distance) . ' miles';
                        						} ?>
											</td>
											<td class="tt-col-event-distance visible-phone">
												<?php echo abs($this->_ridesListView->ride->distance); ?>
											</td>
											<td class="tt-col-ranking-points">
												<?php echo $this->_ridesListView->ride->ranking_points; ?>
											</td>
										</tr>
									<?php
    								} ?>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
    		<?php
    		} ?>
    	</tbody>       
    </table>
    <div class="pagination tt-pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="check_reset" id="reset_check" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php
?>