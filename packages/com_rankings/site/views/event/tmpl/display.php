<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<div>
    <h1><?php echo $this->event->event_name; ?></h1>
</div>

<div class="tt-event-details">
    <div class="tt-event-date"><?php echo date('jS F Y', strtotime($this->event->event_date)); ?>
    </div>
    <?php
    if ($this->event->distance !== 'Other')
    { ?>
        <div class="tt-distance">
            <?php echo $this->event->distance . ' miles'; ?>
        </div>
    <?php
    } ?>
    <div class="tt-course">
        <span class="tt-label"><?php echo JText::_('COM_RANKINGS_COURSE'); ?>
        </span>
        <?php echo $this->event->course_code; ?>
    </div>
</div>

<input class="btn btn-info btn-small tt-btn-back" type="button" value="< Back" onClick="history.go(-1);return true;">

<?php if (count($this->event->rides) > 0)
{ ?>
    <div class="tt-list-heading">
        <div class="tt-list-title">
            <h2><?php echo JText::_('COM_RANKINGS_EVENT_RESULTS'); ?></h2>
        </div>
        <div class="tt-rider-count">
            <?php echo count($this->event->rides) . ' riders'; ?>
        </div>
    </div>

    <?php if(!$this->event->ranking_event_ind)
    {
        if(in_array(date('M', strtotime($this->event->event_date)), array("Nov", "Dec", "Jan")))
        { ?>
            <p class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i>Events in November, December or January are not awarded ranking points.</p>
        <?php
        } else { ?>
            <p class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i>Insufficient riders to award ranking points.</p>
        <?php
        }        
    } ?>

    <table class="table-hover tt-table" id="tt-event-ride-list">
        <thead>
            <tr>
                <th class="tt-col-event-position">
                    <?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?>
                </th>
                <th class="tt-col-rider-name">
                    <?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?>
                </th>
                <th class="tt-col-club-name hidden-phone">
                    <?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?>
                </th>
                <th class="tt-col-age-gender-category visible-desktop">
                    <?php echo JText::_('COM_RANKINGS_RIDER_CATEGORY'); ?>
                </th>
                <th class="tt-col-ride-time">
                    <?php echo JText::_('COM_RANKINGS_RIDE_TIME'); ?>
                </th>
                <th class="tt-col-ranking-points">
                    <?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php for($i=0, $n = count($this->event->rides); $i<$n; $i++) 
            {
                $this->_eventListView->ride = $this->event->rides[$i]; ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="tt-col-event-position">
                        <div class="tt-event-position">
                            <?php echo $this->_eventListView->ride->position; ?>
                        </div>
                        <div class="tt-event-position-variance">
                            <?php if(!$this->_eventListView->ride->blacklist_ind)
                            { ?>
                                <i class="fa fa-<?php echo $this->_eventListView->ride->position_variance_ind; ?>"></i>
                                <?php if (!$this->_eventListView->ride->position_variance_value == 0)
                                {
                                    echo $this->_eventListView->ride->position_variance_value;
                                }
                            } ?>
                        </div>
                    </td>
                    <td class="tt-table-rider-link tt-col-rider-name">
                        <a href="<?php echo JRoute::_('index.php?Itemid=816&option=com_rankings&task=rider.display&cid=' . $this->_eventListView->ride->rider_id); ?>"rel="nofollow"><?php echo $this->_eventListView->ride->name; ?>
                        </a>
                    </td>
                    <td class="tt-col-club-name hidden-phone">
                        <?php echo $this->_eventListView->ride->club_name; ?>
                    </td>
                    <td class="tt-col-age-gender-category visible-desktop">
                        <?php echo $this->_eventListView->ride->age_gender_category; ?>
                    </td>
                    <td class="tt-col-ride-time">
                        <?php echo ltrim(ltrim(date('G:i:s', strtotime($this->_eventListView->ride->time)), '0'), ':'); ?>
                    </td>
                    <td class="tt-col-ranking-points">
                        <?php if(!$this->_eventListView->ride->blacklist_ind)
                        {
                            echo $this->_eventListView->ride->ranking_points;
                        } ?>
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