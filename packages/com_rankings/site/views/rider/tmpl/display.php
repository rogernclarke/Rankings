<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.6
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

<?php if(!$this->rider->blacklist_ind && !empty($this->rider->name))
{ ?>
    <div class="tt-rider-heading">
        <div class="tt-rider-name">
            <h1><?php echo $this->rider->name; ?></h1>
        </div>
        <div class="tt-rider-category">
            <?php if (in_array($this->rider->status, array('Frequent rider','Qualified','Provisional'), true ))
            { ?>
                <div class="tt-tag tt-tag-large tt-rider-category-<?php echo substr($this->rider->category, 0, 1) ; ?>">
                    <span class="tt-tag-category-prefix">
                        <?php echo substr($this->rider->category,0,1); ?>
                    </span>
                    <span class="tt-tag-category-suffix">
                        <?php echo substr($this->rider->category,1,2); ?>
                    </span>
                </div>
            <?php
            }
            if (!empty($this->rider->status))
            { ?>
                <div class="tt-rider-status">
                    <div class="tt-tag tt-tag-small">
                        <?php echo $this->rider->status; ?>
                    </div>
                </div>
            <?php
            } ?>
        </div>
    </div>

    <div class="tt-rider-details">
        <div class="tt-rider">
            <div class="tt-club-name">
                <?php echo $this->rider->club_name; ?>
            </div>
            <div class="tt-age-gender-category">
                <?php echo $this->rider->age_gender_category; ?>
            </div>
            <div class="tt-buttons">
                <input class="btn btn-info btn-small tt-btn-back" type="button" value="< Back" onClick="history.go(-1);return true;">
            </div>
        </div>
        <?php if (!in_array($this->rider->status, array('Provisional', 'Lapsed', ''), true ))
        { ?>
            <div class="tt-rider-rank">
                <div class="tt-overall">
                    <?php echo 'Overall #' . $this->rider->overall_rank; ?>
                </div>
                <div>
                    <i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>
                    <?php echo 'Rank #' . $this->rider->gender_rank; ?>
                </div>
                <div>
                    <i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>
                    <?php echo $this->rider->age_category . ' #' . $this->rider->age_category_rank; ?>
                </div>
                <div>
                    <i class="fa fa-<?php echo $this->rider->gender_icon; ?>"></i>
                    <?php echo $this->rider->district_name . ' #' . $this->rider->district_rank; ?>
                </div>
            </div>
        <?php
        } ?>
    </div>

    <?php if (count($this->rider->rides) > 0)
    { ?>
        <section class="tt-rides-section" id="tt-rider-tabs">
            <!-- Nav tabs -->
            <ul class="tt-nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#results" aria-controls="results" role="tab" data-toggle="tab">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <p>Results</p>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#awards" aria-controls="awards" role="tab" data-toggle="tab">
                        <i class="fa fa-trophy" aria-hidden="true"></i>
                        <p>Awards</p>
                    </a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="results">
                    <div class="tt-nav-tab-content">
                        <div class="tt-list-heading">
                            <div class="tt-list-title">
                                <h2><?php echo JText::_('COM_RANKINGS_RIDER_RESULTS'); ?></h2>
                            </div>
                        </div>
                        <div class="tt-tab-panel">
                            <table class="table-hover tt-table tt-rider-rides">
                            <thead>
                                <tr>
                                    <th class="tt-col-event-date"><?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?></th>
                                    <th class="tt-col-event-name"><?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?></th>
                                    <th class="tt-col-ride-distance visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?></th>
                                    <th class="tt-col-ride-distance hidden-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?></th>
                                    <th class="tt-col-ride-position visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?></th>
                                    <th class="tt-col-ride-position hidden-desktop hidden-phone"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION_SHORT'); ?></th>
                                    <th class="tt-col-ride-result hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDE_RESULT'); ?></th>
                                    <th class="tt-col-rider-ride-points"><?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for($i=0, $n = count($this->rider->rides); $i<$n; $i++) 
                                {
                                    $this->_ridesListView->ride = $this->rider->rides[$i];

                                    If (($i==0) or ($i>0 && date('Y', strtotime($this->rider->rides[$i]->event_date)) != date('Y', strtotime($this->rider->rides[$i-1]->event_date))))
                                    { ?>
                                        <tr class="tt-table-year-row">
                                            <td colspan="6"><?php echo date('Y', strtotime($this->_ridesListView->ride->event_date)); ?></td>
                                        </tr>
                                    <?php 
                                    } ?>
                                    <tr class="row<?php echo $i % 2; ?> tt-rankings-<?php if ($this->_ridesListView->ride->counting_ride_ind) { echo "counting-ride";} else { echo "non-counting-ride";} ?>">
                                        <td class="tt-col-event-date"><?php echo date('d M', strtotime($this->_ridesListView->ride->event_date)); ?></td>
                                        <td class="tt-table-event-link tt-col-event-name">
                                            <div class="tt-flex-container">
                                                <div class="tt-event-name-container">
                                                    <div class="tt-event-name">
                                                        <a href="<?php echo JRoute::_('index.php?Itemid=454&option=com_rankings&task=event.display&cid=' . $this->_ridesListView->ride->event_id); ?>" rel="nofollow"><?php echo $this->_ridesListView->ride->event_name; ?>
                                                        </a>
                                                    </div>
                                                    <?php if (count($this->_ridesListView->ride->awards) > 0)
                                                    { ?>
                                                        <div class="tt-rider-awards hidden-phone" style="min-width: <?php echo count($this->_ridesListView->ride->awards) * 20; ?>px;">
                                                            <?php for($j=1; $j<4; $j++)
                                                            {
                                                                foreach ($this->_ridesListView->ride->awards as $award)
                                                                {
                                                                    if (substr($award->position, 0, 1) == $j)
                                                                    {
                                                                        if ($j == 1)
                                                                        { ?>
                                                                            <i class="fas fa-trophy tt-award-position-<?php echo substr($award->position, 0, 1);?>" title="<?php echo $award->position . ' ' . $award->award_name; ?>" aria-hidden="true"></i>
                                                                        <?php
                                                                        } else { ?>
                                                                            <i class="fas fa-award tt-award-position-<?php echo substr($award->position, 0, 1);?>" title="<?php echo $award->position . ' ' . $award->award_name; ?>" aria-hidden="true"></i>
                                                                        <?php
                                                                        }
                                                                    }
                                                                }
                                                            } ?>
                                                        </div>
                                                    <?php
                                                    } ?>
                                                </div>
                                                <?php if ($this->_ridesListView->ride->category_after_day != '')
                                                { ?>
                                                    <div class="tt-rider-category hidden-small-phone">
                                                        <div class="tt-tag tt-tag-very-small tt-rider-category-<?php echo substr($this->_ridesListView->ride->category_after_day, 0, 1) ;?>"><?php echo $this->_ridesListView->ride->category_after_day; ?></div>
                                                    </div>
                                                <?php
                                                } ?>
                                            </div>
                                        </td>
                                        <td class="tt-col-ride-distance visible-desktop"><?php if(!empty($this->_ridesListView->ride->ride_distance))
                                            {
                                                echo abs($this->_ridesListView->ride->distance) . ' hours';
                                            } else {
                                                echo abs($this->_ridesListView->ride->distance) . ' miles';
                                            } ?></td>
                                        <td class="tt-col-ride-distance hidden-desktop"><?php if(!empty($this->_ridesListView->ride->ride_distance))
                                            {
                                                echo abs($this->_ridesListView->ride->distance);
                                            } else {
                                                echo floor($this->_ridesListView->ride->distance);
                                            } ?></td>
                                        <td class="tt-col-ride-position hidden-phone"><?php echo $this->_ridesListView->ride->position; ?></td>
                                        <td class="tt-col-ride-result hidden-phone"><?php if(!empty($this->_ridesListView->ride->ride_distance))
                                            {
                                                echo abs($this->_ridesListView->ride->ride_distance);
                                            } else {
                                                echo $this->_ridesListView->ride->time;
                                            } ?></td>
                                        <td class="tt-col-rider-ride-points"><?php echo $this->_ridesListView->ride->ranking_points; ?><i class="fa fa-<?php echo $this->_ridesListView->ride->improved_ride; ?>"></i></td>
                                    </tr>
                                <?php
                                } ?>
                            </tbody>       
                            </table>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="awards">
                    <div class="tt-nav-tab-content">
                        <div class="tt-list-heading">
                            <div class="tt-list-title">
                                <h2><?php echo JText::_('COM_RANKINGS_EVENT_AWARDS'); ?></h2>
                            </div>
                        </div>

                        <?php if (count($this->rider->awards) > 0)
                        { ?>
                            <table class="table-hover tt-table tt-rider-awards-list">
                                <thead>
                                    <tr>
                                        <th class="tt-col-event-date"><?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?></th>
                                        <th class="tt-col-event-name"><?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?></th>
                                        <th class="tt-col-ride-distance visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?></th>
                                        <th class="tt-col-ride-distance hidden-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?></th>
                                        <th class="tt-col-award-name"><?php echo JText::_('COM_RANKINGS_AWARD_NAME'); ?></th>
                                        <th class="tt-col-award-result"><?php echo JText::_('COM_RANKINGS_AWARD_RESULT'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $previous_event_id = null;

                                    for($i=0, $n = count($this->rider->awards); $i<$n; $i++)
                                    {
                                        $this->_awardsListView->award = $this->rider->awards[$i];

                                        if ($previous_event_id !== $this->_awardsListView->award->event_id)
                                        {
                                            $new_event = TRUE;
                                            $event_award_count = 0;
                                            $previous_event_id = $this->_awardsListView->award->event_id;
                                            for($j=$i; $j<$n && $this->_awardsListView->award->event_id === $this->rider->awards[$j]->event_id; $j++)
                                            {
                                                $event_award_count = $event_award_count + 1;
                                            }
                                        } else {
                                            $new_event = FALSE;
                                        }

                                        if (($i==0) or ($i>0 && date('Y', strtotime($this->rider->awards[$i]->event_date)) != date('Y', strtotime($this->rider->awards[$i-1]->event_date))))
                                        { ?>
                                            <tr class="tt-table-year-row">
                                                <td colspan="7"><?php echo date('Y', strtotime($this->_awardsListView->award->event_date)); ?></td>
                                            </tr>
                                        <?php 
                                        } ?>
                                            <tr>
                                                <?php if ($new_event)
                                                { ?>
                                                    <td class="tt-col-event-date" rowspan="<?php echo $event_award_count; ?>"><?php echo date('d M', strtotime($this->_awardsListView->award->event_date)); ?></td>
                                                    <td class="tt-table-event-link tt-col-event-name" rowspan="<?php echo $event_award_count; ?>">
                                                        <a href="<?php echo JRoute::_('index.php?Itemid=454&option=com_rankings&task=event.display&cid=' . $this->_awardsListView->award->event_id); ?>" rel="nofollow"><?php echo $this->_awardsListView->award->event_name; ?></a>
                                                    </td>
                                                    <td class="tt-col-ride-distance visible-desktop" rowspan="<?php echo $event_award_count; ?>"><?php if(!empty($this->_awardsListView->award->ride_distance))
                                                    {
                                                        echo abs($this->_awardsListView->award->distance) . ' hours';
                                                    } else {
                                                        echo abs($this->_awardsListView->award->distance) . ' miles';
                                                    } ?></td>
                                                    <td class="tt-col-ride-distance hidden-desktop" rowspan="<?php echo $event_award_count; ?>"><?php if(!empty($this->_awardsListView->award->ride_distance))
                                                    {
                                                        echo abs($this->_awardsListView->award->distance);
                                                    } else {
                                                        echo floor($this->_awardsListView->award->distance);
                                                    } ?></td>
                                                <?php
                                                } ?>
                                                <td class="tt-col-award-name"><?php echo $this->_awardsListView->award->position . ' ' . $this->_awardsListView->award->award_name; ?></td>
                                                <td class="tt-col-award-result"><?php switch ($this->_awardsListView->award->award_basis)
                                                {
                                                    case "Standard":
                                                        echo $this->_awardsListView->award->vets_standard_result;
                                                        break;
                                                    case "Handicap":
                                                        echo $this->_awardsListView->award->handicap_result;
                                                        break;
                                                    default:
                                                        if ($this->_awardsListView->award->team_ind)
                                                        {
                                                            echo $this->_awardsListView->award->team_result;
                                                        } else {
                                                            if (!empty($this->_awardsListView->award->ride_distance))
                                                            {
                                                                echo $this->_awardsListView->award->ride_distance;
                                                            } else {
                                                                echo $this->_awardsListView->award->ride_time;
                                                            }
                                                        }
                                                        break;
                                                } ?></td>
                                            </tr>
                                    <?php
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6">
                                            <div class="tt-table-counters">
                                                <div class="tt-pages-counter pull-left"><?php //echo $this->pagination->getPagesCounter(); ?></div>
                                                <div class="tt-results-counter pull-right"><?php //echo $this->pagination->getResultsCounter(); ?></div>
                                            </div>
                                            <div class="pagination tt-pagination"><?php //echo $this->pagination->getPagesLinks(); ?></div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php
                        } else
                        {?>
                            <h3><?php echo "No awards to date..."; ?></h3>
                        <?php
                        } ?>
                    </div>
                </div>
            </div>
        </section>
    <?php
    } else
    {?>
        <h2><?php echo "No results since 1st January 2017"; ?></h2>
    <?php
    }
} else { ?>
    <h1><?php echo "Rider not found"; ?></h1>
    <input class="btn btn-info btn-small tt-btn-back" type="button" value="< Back" onClick="history.go(-1);return true;">
<?php
} ?>