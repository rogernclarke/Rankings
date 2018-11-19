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

<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

<div class="tt-rider-heading">
    <div class="tt-rider-name">
        <h1>
            <?php echo $this->rider->name; ?>
        </h1>
    </div>
    <?php if(!$this->rider->blacklist_ind)
    {
        if (!in_array($this->rider->status, array('Lapsed', ''), true )) 
        { ?>
            <div class="tt-rider-category">     
                <div class="tt-tag tt-tag-large tt-rider-category-<?php echo substr($this->rider->category, 0, 1) ; ?>">
                    <span class="tt-tag-category-prefix">
                        <?php echo substr($this->rider->category,0,1); ?>
                    </span>
                    <span class="tt-tag-category-suffix">
                        <?php echo substr($this->rider->category,1,2); ?>
                    </span>
                </div>
            </div>
        <?php
        }
    } ?>
</div>

<?php if(!$this->rider->blacklist_ind)
{
    if (in_array($this->rider->status, array('Provisional','Frequent rider'), true ))
    { ?>
        <div class="tt-rider-status">
            <div class="tt-tag tt-tag-small">
                <?php echo $this->rider->status; ?>
            </div>
        </div>
    <?php
    }
} ?>

<div class="tt-rider-details">
    <div class="tt-rider">
        <div class="tt-club-name">
            <?php echo $this->rider->club_name; ?>
        </div>
        <?php if(!$this->rider->blacklist_ind)
        { ?>
            <div class="tt-age-gender-category">
                <?php echo $this->rider->age_gender_category; ?>
            </div>
        <?php
        } ?>
    </div>
    <?php if(!$this->rider->blacklist_ind)
    {
        if (!in_array($this->rider->status, array('Provisional', 'Lapsed', ''), true ))
        { ?>
            <div class="tt-rider-rank">
                <div>
                    <?php echo 'Rank #' . $this->rider->gender_rank; ?>
                </div>
                <div>
                    <?php echo 'Overall #' . $this->rider->overall_rank; ?>
                </div>
            </div>
        <?php
        }
    } ?>
</div>

<input class="btn btn-info btn-small tt-btn-back" type="button" value="< Back" onClick="history.go(-1);return true;">

<?php if(!$this->rider->blacklist_ind)
{
    if (count($this->rider->rides) > 0)
    { ?>
        <h2><?php echo JText::_('COM_RANKINGS_RIDER_RESULTS'); ?></h2>
        <table class="table-hover tt-table" id="tt-rider-ride-list">
        <thead>
            <tr>
                <th class="tt-col-event-date">
                    <?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?>
                </th>
                <th class="tt-col-event-name">
                    <?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?>
                </th>
                <th class="tt-col-event-distance visible-desktop">
                    <?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?>
                </th>
                <th class="tt-col-event-distance visible-phone">
                    <?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?>
                </th>
                <th class="tt-col-event-position hidden-phone">
                    <?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?>
                </th>
                <th class="tt-col-ride-time">
                    <?php echo JText::_('COM_RANKINGS_RIDE_TIME'); ?>
                </th>
                <th class="tt-col-ranking-points hidden-phone">
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
                        <td colspan="6">
                            <?php echo date('Y', strtotime($this->_ridesListView->ride->event_date)); ?>
                        </td>
                    </tr>
                <?php 
                } ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="tt-col-event-date">
                        <?php echo date('d M y', strtotime($this->_ridesListView->ride->event_date)); ?>
                    </td>
                    <td class="tt-table-event-link tt-col-event-name">
                        <a href="<?php echo JRoute::_('index.php?Itemid=454&option=com_rankings&task=event.display&cid=' . $this->_ridesListView->ride->event_id); ?>" rel="nofollow"><?php echo $this->_ridesListView->ride->event_name; ?>
                        </a>
                    </td>
                    <td class="tt-col-event-distance visible-desktop">
                        <?php echo $this->_ridesListView->ride->distance . ' miles'; ?>
                    </td>
                    <td class="tt-col-event-distance visible-phone">
                        <?php echo $this->_ridesListView->ride->distance; ?>
                    </td>
                    <td class="tt-col-event-position hidden-phone">
                        <div class="tt-event-position">
                            <?php echo $this->_ridesListView->ride->position; ?>
                        </div>
                    </td>
                    <td class="tt-col-ride-time">
                        <?php echo ltrim(ltrim(date('G:i:s', strtotime($this->_ridesListView->ride->time)), '0'), ':'); ?>
                    </td>
                    <td class="tt-col-ranking-points hidden-phone">
                        <?php echo $this->_ridesListView->ride->ranking_points; ?>
                        <i class="fa fa-<?php echo $this->_ridesListView->ride->improved_ride; ?>"></i>
                    </td>
                </tr>
            <?php
            } ?>
        </tbody>       
        </table>
    <?php
    } else
    {?>
        <h2><?php echo "No results since 1st January 2017"; ?></h2>
    <?php
    }
} else { ?>
    <h2><?php echo "This rider has opted out of Spindata"; ?></h2>
<?php
} ?>