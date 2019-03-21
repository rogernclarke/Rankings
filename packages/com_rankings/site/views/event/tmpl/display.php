<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.4
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<div>
    <h1><?php echo $this->event->event_name; ?></h1>
</div>

<div class="tt-event-details">
    <div class="tt-event-date">
        <p><?php echo date('jS F Y', strtotime($this->event->event_date)); ?></p>
    </div>
    <?php
    if ($this->event->distance !== 'Other')
    { ?>
        <div class="tt-distance">
            <p><?php if($this->event->duration_event_ind)
            {
                echo $this->event->distance . ' hours';
            } else {
                echo (float) $this->event->distance . ' miles';
            } ?></p>
        </div>
    <?php
    } ?>
    <div class="tt-course">
        <span class="tt-label"><?php echo JText::_('COM_RANKINGS_COURSE'); ?></span>
        <span class="tt-text"><?php echo $this->event->course_code; ?></span>
    </div>
</div>

<input class="btn btn-info btn-small tt-btn-back" type="button" value="< Back" onClick="history.go(-1);return true;">

<?php if (count($this->event->entries) + count($this->event->rides) > 0)
{ ?>
    <section class="tt-rides-section" id="tt-event-tabs">
        <!-- Nav tabs -->
        <ul class="tt-nav-tabs" role="tablist">
            <li role="presentation" class="<?php if (!$this->event->startsheet_ind) { echo "disabled"; } else if (!$this->event->results_ind) { echo "active"; } ?>">
                <?php if ($this->event->startsheet_ind)
                { ?>
                    <a href="#startsheet" aria-controls="startsheet" role="tab" data-toggle="tab">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <p>Start Sheet</p>
                    </a>
                <?php
                } else { ?>
                    <div>
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <p>Start Sheet</p>
                    </div>
                <?php
                } ?>
            </li>
            <li role="presentation" class="<?php if ($this->event->results_ind) { echo "active"; } else { echo "disabled"; } ?>">
                <?php if ($this->event->results_ind)
                { ?>
                    <a href="#results" aria-controls="results" role="tab" data-toggle="tab">
                        <i class="fa fa-clipboard" aria-hidden="true"></i>
                        <p>Results</p>
                    </a>
                <?php
                } else { ?>
                    <div>
                        <i class="fa fa-clipboard" aria-hidden="true"></i>
                        <p>Results</p>
                    </div>
                <?php
                } ?>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane <?php if (!$this->event->results_ind && $this->event->startsheet_ind) { echo "active"; } ?>" id="startsheet">
                <div class="tt-nav-tab-content">
                    <div class="tt-list-heading">
                        <div class="tt-list-title">
                            <h2><?php echo JText::_('COM_RANKINGS_EVENT_STARTSHEET'); ?></h2>
                        </div>
                        <div class="tt-rider-count">
                            <p><?php echo count($this->event->entries) . ' entries'; ?></p>
                        </div>
                    </div>                        
                    <div class="tabs tabs-style-topline tt-tabs-startsheet">
                        <nav>
                            <ul>
                                <li id="tt-start-order"><button type="button" onclick="sort_bib();"><i class="fa fa-clock-o" aria-hidden="true"></i><p><?php echo JText::_('COM_RANKINGS_EVENT_STARTING_ORDER'); ?></p></button></li>
                                <li id="tt-finish-order"><button type="button" onclick="sort_predicted_finish();"><i class="fa fa-flag-checkered" aria-hidden="true"></i><p><?php echo JText::_('COM_RANKINGS_EVENT_PREDICTED_FINISHING_ORDER'); ?></p></button></li>
                                <li class="tab-current" id="tt-result-order"><button type="button" onclick="sort_predicted_position();"><i class="fa fa-sort-amount-asc" aria-hidden="true"></i><p><?php echo JText::_('COM_RANKINGS_EVENT_PREDICTED_FINISHING_POSITION'); ?></p></button></li>
                            </ul>
                        </nav>
                        <div class="tt-tab-panel">
                            <table class="table-hover tt-table" id="tt-event-startsheet">
                                <thead>
                                    <tr>
                                        <th class="tt-col-rider-bib" rowspan="2"><?php echo JText::_('COM_RANKINGS_RIDE_BIB'); ?></th>
                                        <th class="tt-col-rider-name" rowspan="2"><?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?></th>
                                        <th class="tt-col-club-name hidden-phone" rowspan="2"><?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?></th>
                                        <th class="tt-col-age-gender-category hidden-tablet hidden-phone" rowspan="2"><?php echo JText::_('COM_RANKINGS_RIDER_CATEGORY'); ?></th>
                                        <th class="tt-col-predicted-time-at-finish hidden-tablet hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDE_PREDICTED_TIME_AT_FINISH'); ?></th>
                                        <th class="tt-col-ride-predicted-position visible-desktop" rowspan="2"><?php echo JText::_('COM_RANKINGS_EVENT_PREDICTED_POSITION'); ?></th>
                                        <th class="tt-col-ride-predicted-result visible-desktop" rowspan="2"><?php if($this->event->duration_event_ind)
                                            {
                                                echo JText::_('COM_RANKINGS_RIDE_PREDICTED_DISTANCE');
                                            } else {
                                                echo JText::_('COM_RANKINGS_RIDE_PREDICTED_TIME');
                                            } ?></th>
                                        <th class="tt-col-ride-predicted hidden-desktop" colspan="2" rowspan="1"><?php echo JText::_('COM_RANKINGS_RIDE_PREDICTED'); ?></th>
                                    </tr>
                                    <tr class="hidden-desktop">
                                        <th class="tt-col-ride-predicted-position hidden-desktop" rowspan="1"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION_SHORT'); ?></th>
                                        <th class="tt-col-ride-predicted-result hidden-desktop" rowspan="1"><?php if($this->event->duration_event_ind)
                                            {
                                                echo JText::_('COM_RANKINGS_RIDE_DISTANCE');
                                            } else {
                                                echo JText::_('COM_RANKINGS_RIDE_TIME');
                                            } ?></th>
                                    </tr>
                                </thead>
                                <tbody id="tt-event-startsheet-body">
                                    <?php for($i=0, $n = count($this->event->entries); $i<$n; $i++) 
                                    {
                                        $this->_eventListView->entry = $this->event->entries[$i]; ?>
                                        <tr class="row<?php echo $i % 2; ?>">
                                            <td class="tt-col-rider-bib"><?php echo $this->_eventListView->entry->bib; ?></td>
                                            <td class="tt-table-rider-link tt-col-rider-name">
                                                <div class="tt-rider-name-container">
                                                    <?php if(!$this->_eventListView->entry->blacklist_ind && !empty($this->_eventListView->entry->form))
                                                    { ?>
                                                        <div class = "tt-rider-form">
                                                            <img src="/images/flame.png" alt="improving rider">
                                                        </div>
                                                    <?php
                                                    } ?>
                                                    <div class="tt-rider-name">
                                                        <?php if(!$this->_eventListView->entry->blacklist_ind)
                                                        { ?>
                                                            <a href="<?php echo JRoute::_('index.php?Itemid=816&option=com_rankings&task=rider.display&cid=' . $this->_eventListView->entry->rider_id); ?>"rel="nofollow"><?php echo $this->_eventListView->entry->name; ?></a>
                                                        <?php
                                                        } else { ?>
                                                            <p><?php echo $this->_eventListView->entry->name; ?></p>
                                                        <?php
                                                        } ?>
                                                    </div>
                                                    <?php if ($this->_eventListView->entry->category_on_day != '' && !$this->_eventListView->entry->blacklist_ind)
                                                    { ?>
                                                        <div class="tt-rider-category hidden-small-phone">
                                                            <div class="tt-tag tt-tag-very-small tt-rider-category-<?php echo substr($this->_eventListView->entry->category_on_day, 0, 1) ;?>"><?php echo $this->_eventListView->entry->category_on_day; ?></div>
                                                        </div>
                                                    <?php
                                                    } ?>
                                                </div>
                                            </td>
                                            <td class="tt-col-club-name hidden-phone"><?php echo $this->_eventListView->entry->club_name; ?></td>
                                            <td class="tt-col-age-gender-category hidden-tablet hidden-phone"><?php echo $this->_eventListView->entry->age_gender_category; ?></td>
                                            <td class="tt-col-predicted-time-at-finish hidden-tablet hidden-phone"><?php echo $this->_eventListView->entry->predicted_time_at_finish; ?></td>
                                            <td class="tt-col-ride-predicted-position"><?php if (!empty($this->_eventListView->entry->predicted_position))
                                            {
                                                echo trim($this->_eventListView->entry->predicted_position);
                                            } else {
                                                echo "-";
                                            } ?></td>
                                            <td class="tt-col-ride-result"><?php if($this->event->duration_event_ind)
                                            {
                                                echo $this->_eventListView->entry->predicted_distance;
                                            } else {
                                                if (!empty($this->_eventListView->entry->predicted_time))
                                                {
                                                    echo $this->_eventListView->entry->predicted_time;
                                                } else {
                                                    echo "-";
                                                }
                                            } ?></td>
                                        </tr>
                                    <?php
                                    } ?>
                                </tbody>       
                            </table>
                        </div><!-- /content -->
                    </div><!-- /tabs -->
                </div>
            </div>
            <div role="tabpanel" class="tab-pane <?php if ($this->event->results_ind) { echo "active"; } else if (!$this->event->startsheet_ind) { echo "active"; } ?>" id="results">
                <div class="tt-nav-tab-content">
                    <div class="tt-list-heading">
                        <div class="tt-list-title">
                            <h2><?php echo JText::_('COM_RANKINGS_EVENT_RESULTS'); ?></h2>
                        </div>
                        <div class="tt-rider-count">
                            <p><?php echo count($this->event->rides) . ' riders'; ?></p>
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

                    <div class="tabs tabs-style-topline tt-tabs-results">
                        <nav>
                            <ul>
                                <li class="tab-current" id="tt-overall-filter"><button type="button" onclick="filter_results_overall();"><i class="fa fa-clock-o" aria-hidden="true"></i><p>Overall</p></button></li>
                                <li id="tt-male-filter"><button type="button" onclick="filter_results_male();"><i class="fa fa-mars" aria-hidden="true"></i><p>Men</p></button></li>
                                <li id="tt-female-filter"><button type="button" onclick="filter_results_female();"><i class="fa fa-venus" aria-hidden="true"></i><p>Women</p></button></li>
                            </ul>
                        </nav>
                        <div class="tt-tab-panel">

                    <table class="table-hover tt-table" id="tt-event-results">
                        <thead>
                            <tr>
                                <th class="tt-col-event-position"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?></th>
                                <th class="tt-col-event-gender-position"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?></th>
                                <th class="tt-col-rider-name"><?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?></th>
                                <th class="tt-col-club-name hidden-phone"><?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?></th>
                                <th class="tt-col-age-gender-category visible-large"><?php echo JText::_('COM_RANKINGS_RIDER_CATEGORY'); ?></th>
                                <th class="tt-col-ride-predicted-result hidden-tablet hidden-phone" rowspan="2"><?php if($this->event->duration_event_ind)
                                {
                                    echo JText::_('COM_RANKINGS_RIDE_PREDICTED_DISTANCE');
                                } else {
                                    echo JText::_('COM_RANKINGS_RIDE_PREDICTED_TIME');
                                } ?></th>
                                <th class="tt-col-ride-result"><?php if($this->event->duration_event_ind)
                                {
                                    echo JText::_('COM_RANKINGS_RIDE_DISTANCE');
                                } else {
                                    echo JText::_('COM_RANKINGS_RIDE_TIME');
                                } ?></th>
                                <th class="tt-col-event-ride-points hidden-tablet hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?></th>
                                <th class="tt-col-event-ride-points hidden-desktop"><?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS_SHORT'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="tt-event-results-body">
                            <?php for($i=0, $n = count($this->event->rides); $i<$n; $i++) 
                            {
                                $this->_eventListView->ride = $this->event->rides[$i]; ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="tt-col-event-position">
                                        <div class="tt-event-position"><?php echo $this->_eventListView->ride->position; ?></div>
                                        <div class="tt-event-position-variance"><?php if(!$this->_eventListView->ride->blacklist_ind)
                                        { ?>
                                            <i class="fa fa-<?php echo $this->_eventListView->ride->position_variance_ind; ?>"></i><?php if (!$this->_eventListView->ride->position_variance_value == 0)
                                            {
                                                echo $this->_eventListView->ride->position_variance_value;
                                            }
                                        } ?></div>
                                    </td>
                                    <td class="tt-col-event-gender-position">
                                        <div class="tt-event-position"><?php echo $this->_eventListView->ride->gender_position; ?></div>
                                        <div class="tt-event-position-variance"></div>
                                    </td>
                                    <td class="tt-table-rider-link tt-col-rider-name">
                                        <div class="tt-rider-name-container">
                                            <?php if(!$this->_eventListView->entry->blacklist_ind && !empty($this->_eventListView->ride->form))
                                            { ?>
                                                <div class = "tt-rider-form">
                                                    <img src="/media/com_rankings/images/flame.png" alt="improving rider">
                                                    <?php if($this->_eventListView->ride->form > 3)
                                                    { ?>
                                                        <img src="/media/com_rankings/images/flame.png" alt="improving rider">
                                                    <?php
                                                    } ?>
                                                </div>
                                            <?php
                                            } ?>
                                            <div class="tt-rider-name">
                                                <?php if(!$this->_eventListView->ride->blacklist_ind)
                                                { ?>
                                                    <a href="<?php echo JRoute::_('index.php?Itemid=816&option=com_rankings&task=rider.display&cid=' . $this->_eventListView->ride->rider_id); ?>"rel="nofollow">   <?php echo $this->_eventListView->ride->name; ?></a>
                                                <?php 
                                                } else {?>
                                                    <p><?php echo $this->_eventListView->ride->name; ?></p>
                                                <?php
                                                } ?>
                                            </div>
                                            <?php if ($this->_eventListView->ride->category_on_day != '' && !$this->_eventListView->ride->blacklist_ind)
                                            { ?>
                                                <div class="tt-rider-category hidden-small-phone">
                                                    <div class="tt-tag tt-tag-very-small tt-rider-category-<?php echo substr($this->_eventListView->ride->category_on_day, 0, 1) ;?>"><?php echo $this->_eventListView->ride->category_on_day; ?></div>
                                                </div>
                                            <?php
                                            } ?>
                                        </div>
                                    </td>
                                    <td class="tt-col-club-name hidden-phone"><?php echo $this->_eventListView->ride->club_name; ?></td>
                                    <td class="tt-col-age-gender-category visible-large"><?php echo $this->_eventListView->ride->age_gender_category; ?></td>
                                    <td class="tt-col-ride-result hidden-tablet hidden-phone"><?php if($this->event->duration_event_ind)
                                    {
                                        echo $this->_eventListView->ride->predicted_distance;
                                    } else {
                                        if (!empty($this->_eventListView->ride->predicted_time))
                                        {
                                            echo $this->_eventListView->ride->predicted_time;
                                        } else {
                                            echo "-";
                                        }
                                    } ?></td>
                                    <td class="tt-col-ride-result"><?php if($this->event->duration_event_ind)
                                    {
                                        echo $this->_eventListView->ride->ride_distance;
                                    } else {
                                        echo $this->_eventListView->ride->time;
                                    } ?></td>
                                    <td class="tt-col-event-ride-points"><?php if(!$this->_eventListView->ride->blacklist_ind)
                                    {
                                        echo $this->_eventListView->ride->ranking_points;
                                    } ?></td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  
<?php
} 
else
{ ?>
    <h2><?php echo JText::_('COM_RANKINGS_EVENT_NO_RESULTS'); ?></h2>
<?php
} ?>