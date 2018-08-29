<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    0.0.1
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<h1 class="page-header"><?php echo $this->rider->name; ?></h1>
<div>
    <div>
        <a style="float:right" href="#return" class="btn btn-info btn-small"><?php echo JText::_('COM_RANKINGS_RIDER_BACK'); ?></a>
    </div>
</div>
<div id="tt-rider-club"><?php echo $this->rider->club_name; ?>
</div>
<div id="tt-rider-category"><?php echo $this->rider->category; ?>
</div>
<h2><?php echo JText::_('COM_RANKINGS_RIDER_RESULTS'); ?></h2>
<table class="tt-table" id="tt-rider-ride-list" cellpadding="0" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>
                    <?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?>
                </th>
                <th class="hidden-phone">
                    <?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?>
                </th>
                <th>
                    <?php echo JText::_('COM_RANKINGS_RIDE_TIME'); ?>
                </th>
                <th class="hidden-phone">
                    <?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php for($i=0, $n = count($this->rider->rides); $i<$n; $i++) 
            {
                $this->_ridesListView->ride = $this->rider->rides[$i];

                If (($i==0) or ($i>0 && date('Y', strtotime($this->rider->rides[$i]->event_date)) != date('Y', strtotime($this->rider->rides[$i-1]->event_date))))
                { ?>
                    <tr class="tt-table-year-row">
                        <th colspan="6">
                            <?php echo date('Y', strtotime($this->_ridesListView->ride->event_date)); ?>
                        </th>
                    </tr>
                <?php 
                } ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td>
                        <?php echo date('jS F', strtotime($this->_ridesListView->ride->event_date)); ?>
                    </td>
                    <td>
                        <a href="<?php echo JRoute::_('index.php?option=com_rankings&task=event.display&cid=' . $this->_ridesListView->ride->event_id); ?>"><?php echo $this->_ridesListView->ride->event_name; ?>
                        </a>
                    </td>
                    <td>
                        <?php echo $this->_ridesListView->ride->distance; ?>
                    </td>
                    <td class="hidden-phone">
                        <?php echo $this->_ridesListView->ride->position; ?>
                    </td>
                    <td>
                        <?php echo ltrim(ltrim(date('G:i:s', strtotime($this->_ridesListView->ride->time)), '0'), ':'); ?>
                    </td>
                    <td class="hidden-phone">
                        <?php echo $this->_ridesListView->ride->ranking_points; ?>
                    </td>
                </tr>
            <?php
            } ?>
        </tbody>       
    </table>