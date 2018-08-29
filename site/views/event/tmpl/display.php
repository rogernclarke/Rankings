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

<h1 class="page-header"><?php echo $this->event->event_name; ?></h1>
<div>
    <div>
        <a style="float:right" href="#return" class="btn btn-info btn-small"><?php echo JText::_('COM_RANKINGS_EVENT_BACK'); ?></a>
    </div>
</div>
<div id="tt-event-date"><?php echo date('jS F Y', strtotime($this->event->event_date)); ?>
</div>
<div id="tt-distance"><?php echo $this->event->distance . ' miles'; ?>
</div>
<div id="tt-course">
    <span class="tt-label"><?php echo JText::_('COM_RANKINGS_COURSE'); ?>
    </span>
    <?php echo $this->event->course_code; ?>
</div>
<?php if(count($this->event->rides)>0)
{ ?>
    <h2><?php echo JText::_('COM_RANKINGS_EVENT_RESULTS'); ?></h2>
    <div id="tt-rider-count"><?php echo count($this->event->rides) . ' riders'; ?>
    </div>
    <table class="tt-table" id="tt-event-ride-list" cellpadding="0" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th width="5%" align="left">
                    <?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?>
                </th>
                <th width="15%" align="left">
                    <?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?>
                </th>
                <th width="40%" align="left">
                    <?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?>
                </th>
                <th width="20%" align="left">
                    <?php echo JText::_('COM_RANKINGS_RIDER_CATEGORY'); ?>
                </th>
                <th width="10%" align="left">
                    <?php echo JText::_('COM_RANKINGS_RIDE_TIME'); ?>
                </th>
                <th width="10%" align="left">
                    <?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php for($i=0, $n = count($this->event->rides); $i<$n; $i++) 
            {
                $this->_eventListView->ride = $this->event->rides[$i]; ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td>
                        <?php echo $i+1; ?>
                    </td>
                    <td>
                        <a href="<?php echo JRoute::_('index.php?option=com_rankings&task=rider.display&cid=' . $this->_eventListView->ride->rider_id); ?>"><?php echo $this->_eventListView->ride->name; ?>
                        </a>
                    </td>
                    <td>
                        <?php echo $this->_eventListView->ride->club_name; ?>
                    </td>
                    <td>
                        <?php echo $this->_eventListView->ride->age_category; ?>
                    </td>
                    <td>
                        <?php echo ltrim(ltrim(date('G:i:s', strtotime($this->_eventListView->ride->time)), '0'), ':'); ?>
                    </td>
                    <td>
                        <?php echo $this->_eventListView->ride->ranking_points; ?>
                    </td>
                </tr>
            <?php
            } ?>
        </tbody>       
    </table>
<?php
} 
else
{ ?>
    <h2><?php echo JText::_('COM_RANKINGS_EVENT_NO_RESULTS'); ?></h2>
<?php
} ?>