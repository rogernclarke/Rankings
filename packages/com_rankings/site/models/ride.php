<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.4
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 *
 * Public functions
 * __construct  Constructs the event model
 * listItems    Returns a list of rides
 *
 * Protected functions
 * _buildQuery                  Builds the select query for rides
 * _buildSubqueryRankingEvent   Builds the subquery for rankings rides
 * _buildWhere                  Builds the where clause for the query for rides
 * _buildOrder                  Builds the order clause for the query for rides
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rankings Component Ride Model
 */
class RankingsModelsRide extends RankingsModelsDefault
{
    /**
    * Protected fields
    **/
    protected $_event_duration = null;
    protected $_event_id       = null;
    protected $_last_run_date  = null;
    protected $_list_type      = null;
    protected $_ranking_status = null;
    protected $_rider_id       = null;

    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();

        // Get the last rankings calculation date
        $this->_last_run_date = $this->_getLastRunDate();
    }

    /**
     * listItems
     *
     * Gets a list of rides.
     *
     * @param   integer $offset Offset of first ride to return
     * @param   integer $limit  Number of rides to return
     * @return  model The requested rides
     **/
    public function listItems($offset=null, $limit = null)
    {
        $rides = parent::listItems($offset, $limit);

        $n = count($rides);
        for($i=0;$i<$n;$i++)
        {
            $ride           = $rides[$i];

            switch ($this->_list_type)
            {
                case "event_entries":
                    
                    // Set the ordinal for predicted position
                    if (!empty($ride->predicted_position)) {
                        $ride->predicted_position = parent::_ordinal($ride->predicted_position);
                    }

                    // Set the predicted time at finish line
                    if (!empty($this->_event_duration))
                    {                     
                        $ride->predicted_time_at_finish = gmdate("H:i:s", (date("H", strtotime($ride->start_time)) * 3600) + date("i", strtotime($ride->start_time)) * 60 + date("s", strtotime($ride->start_time)) + $this->_event_duration * 3600);
                    } else {
                        if (!empty($ride->predicted_time) && $ride->bib > 0)
                        {
                            $ride->predicted_time_at_finish = gmdate("H:i:s", (date("H", strtotime($ride->start_time)) * 3600) + date("i", strtotime($ride->start_time)) * 60 + date("s", strtotime($ride->start_time)) + (date("H", strtotime($ride->predicted_time)) * 3600) + date("i", strtotime($ride->predicted_time)) * 60 + date("s", strtotime($ride->predicted_time)));
                        } else {
                            $ride->predicted_time_at_finish = "-";
                        }
                    }

                    // Set bib number for reserve riders
                    if ($ride->bib == 0) {
                        $ride->bib = "Res";
                    }
                    break;
                case "event_results":
                case "rider":
                    // Set the ordinal for position
                    $ride->position = parent::_ordinal($ride->position);
                    $ride->gender_position = parent::_ordinal($ride->gender_position);
                    break;
            }
        }

        return $rides;
    }

    /**
     * _buildQuery
     *
     * Builds the query to be used by the ride model
     * 
     * @return object Query object
     **/
    protected function _buildQuery()
    {
        $query = $this->_db->getQuery(TRUE);

        $query
            ->select($this->_db->qn(array('r.rider_id', 'r.event_id', 'r.club_name', 'r.age_on_day', 'r.position', 'r.ranking_points', 'r.counting_ride_ind', 'r.category_on_day', 'r.predicted_position', 'r.bib', 'r.start_time', 'r.predicted_distance')))
            ->select($this->_db->qn('r.distance') . 'AS ride_distance')
            ->select('DATE_FORMAT(' . $this->_db->qn('r.time') . ', "%k:%i:%s") AS complete_time')
            ->select('CASE DATE_FORMAT(' . $this->_db->qn('r.time') . ', "%k")' . 
                ' WHEN 0 THEN DATE_FORMAT(' . $this->_db->qn('r.time') . ', "%i:%s")' . 
                ' ELSE DATE_FORMAT(' . $this->_db->qn('r.time') . ', "%k:%i:%s")' . 
                ' END' . 
                ' AS time')
            ->select('CASE DATE_FORMAT(' . $this->_db->qn('r.predicted_time') . ', "%k")' . 
                ' WHEN 0 THEN DATE_FORMAT(' . $this->_db->qn('r.predicted_time') . ', "%i:%s")' . 
                ' ELSE DATE_FORMAT(' . $this->_db->qn('r.predicted_time') . ', "%k:%i:%s")' . 
                ' END' . 
                ' AS predicted_time')
            ->select($this->_db->qn(array('rr.blacklist_ind', 'rr.gender')))
            ->select('CONCAT(' . $this->_db->qn('rr.first_name') . ', " ", ' . $this->_db->qn('rr.last_name') . ')' . 
                ' AS name')
            ->select('CONCAT(' . $this->_db->qn('rr.age_category') . ', " ", ' . $this->_db->qn('rr.gender') . ')' . 
                ' AS age_gender_category');

        switch ($this->_list_type)
        {
            case "event_entries":
                $query
                    ->select($this->_db->qn('r.pre_ride_form') . 'AS form')
                    ->from($this->_db->qn('#__rides', 'r'))
                    ->from($this->_db->qn('#__riders', 'rr'))
                    ->from($this->_db->qn('#__events', 'e'));
                break;

            case "event_results":

                // Position variance is only applicable for results displayed for an event
                $query
                    ->select($this->_db->qn('r.post_ride_form') . 'AS form')
                    ->select('CASE SIGN (' . $this->_db->qn('r.position_variance') . ')' . 
                        ' WHEN -1 THEN "arrow-down"' . 
                        ' WHEN 1 THEN "arrow-up"' . 
                        ' ELSE IF (' .  $this->_db->qn('r.ranked_rider_ind') . ' = TRUE, "arrow-left", "circle")' . 
                        ' END' . 
                        ' AS position_variance_ind')
                    ->select('ABS(' . $this->_db->qn('r.position_variance') . ')' .  
                        ' AS position_variance_value');

                // Gender position in list is only applicable for results displayed for an event
                $query
                    ->from($this->_db->qn('#__rides', 'r'))
                    ->from($this->_db->qn('#__riders', 'rr'))
                    ->from($this->_db->qn('#__events', 'e'));

                $subquery = $this->_buildSubqueryEventRides($query);

                // Create a new query
                $query = $this->_db->getQuery(TRUE);

                $query
                    ->select($this->_db->qn(array('rider_id', 'event_id', 'club_name', 'age_on_day', 'position', 'time', 'ranking_points', 'counting_ride_ind', 'category_on_day', 'predicted_position', 'predicted_time', 'bib', 'start_time', 'predicted_distance', 'ride_distance', 'blacklist_ind', 'name', 'age_gender_category', 'position_variance_ind', 'position_variance_value', 'form')))
                    ->select('IF (' . $this->_db->qn('gender') . ' = "Female",' . 
                        ' CASE' . 
                        ' WHEN @prev_value = ' . $this->_db->qn('position') . ' THEN @female_position_count' . 
                        ' WHEN @prev_value:= ' . $this->_db->qn('position') . ' THEN @female_position_count:=@female_sequence' . 
                        ' END,' . 
                        ' CASE' . 
                        ' WHEN @prev_value = ' . $this->_db->qn('position') . ' THEN @male_position_count' . 
                        ' WHEN @prev_value:= ' . $this->_db->qn('position') . ' THEN @male_position_count:=@male_sequence' . 
                        ' END)' . 
                        ' AS gender_position')
                    ->select('IF (' . $this->_db->qn('gender') . ' = "Female",' . 
                        ' @female_sequence:=@female_sequence + 1,' . 
                        ' @male_sequence:=@male_sequence + 1)' . 
                        ' AS sequence')
                    ->from('(' . $subquery . ') AS T1')
                    ->from('(' . $this->_buildSubqueryGenderPosition() . ') AS T2');
                break;

            case "rider":

                // Improved score is only applicable for rides displayed for a rider
                $query
                    ->select('CASE ' . $this->_db->qn('r.improved_score_ind') . 
                        ' WHEN -1 THEN "arrow-down"' . 
                        ' WHEN 1 THEN "arrow-up"' . 
                        ' ELSE "circle"' . 
                        ' END' . 
                        ' AS improved_ride')
                    
                // Post-ride form is applicable for rides displayed for a rider
                    ->select($this->_db->qn('r.post_ride_form') . 'AS form')

                // Rider category after event is only applicable for rides displayed for a rider
                    ->select($this->_db->qn('rh.category') . ' AS category_after_day')
                    ->from  ($this->_db->qn('#__rider_history', 'rh'));

                // Event details are applicable for rides displayed for a rider or rankings
                $query
                    ->select($this->_db->qn(array('e.event_date','e.event_name')))
                    ->select('CASE ' . $this->_db->qn('e.distance') . ' WHEN 0 THEN "Other"' . 
                        ' ELSE ' . $this->_db->qn('e.distance') . 
                        ' END' . 
                        ' AS distance');
                $query
                    ->from($this->_db->qn('#__rides', 'r'))
                    ->from($this->_db->qn('#__riders', 'rr'))
                    ->from($this->_db->qn('#__events', 'e'));
                break;

            case "rankings":

                // Event details are applicable for rides displayed for a rider or rankings
                $query
                    ->select($this->_db->qn(array('e.event_date','e.event_name')))
                    ->select('CASE ' . $this->_db->qn('e.distance') . ' WHEN 0 THEN "Other"' . 
                        ' ELSE ' . $this->_db->qn('e.distance') . 
                        ' END' . 
                        ' AS distance');
                $query
                    ->from($this->_db->qn('#__rides', 'r'))
                    ->from($this->_db->qn('#__riders', 'rr'))
                    ->from($this->_db->qn('#__events', 'e'));
                break;
        }

        if ($this->_list_type === "rankings")
        {
            $subquery = $this->_buildSubqueryCountingRides($query);

            // Create a new query
            $query = $this->_db->getQuery(TRUE);

            $query
                ->select($this->_db->qn(array('name', 'age_gender_category', 'distance', 'rider_id', 'blacklist_ind', 'event_id', 'club_name', 'age_on_day', 'position', 'time', 'ranking_points', 'counting_ride_ind', 'event_date','event_name', 'ride_distance')))
                ->from('(' . $subquery . ') AS T1');
        }

        return $query;
    }

    /**
     * _buildSubqueryCountingRides
     *
     * Builds the subquery used to determine if the event is a ranking event
     *
     * @param  object Query object
     * @return object Query object
     **/
    protected function _buildSubqueryCountingRides($query)
    {

        $query
            ->where($this->_db->qn('r.rider_id') . ' = ' . (int) $this->_rider_id)
            ->where($this->_db->qn('r.rider_id') . ' = ' . $this->_db->qn('rr.rider_id'))
            ->where($this->_db->qn('r.event_id') . ' = ' . $this->_db->qn('e.event_id'))
            ->where ($this->_db->qn('r.time') . ' > "00:00:00"')
            ->order($this->_db->qn('e.event_date') . ' DESC');

        if ($this->_ranking_status === "Complete")
        {
            // For riders with Complete status a maximum of 8 rides are displayed, otherwise retrieve all the rides that match the criteria
            $query
                ->setlimit(8);
        }

        return $query;
    }

    /**
     * _buildSubqueryRides
     *
     * Builds the subquery used to return the set of rides for which position is to be calculated
     * 
     * @return object Subquery object
     **/
    protected function _buildSubqueryEventRides($query)
    {
        $query
            ->where ($this->_db->qn('e.event_id') . ' = ' . (int) $this->_event_id)
            ->where ($this->_db->qn('r.rider_id') . ' = ' . $this->_db->qn('rr.rider_id'))
            ->where ($this->_db->qn('r.event_id') . ' = ' . $this->_db->qn('e.event_id'))
            ->where ($this->_db->qn('time') . ' > "00:00:00"')
            ->order ($this->_db->qn('time') . ' ASC')
            ->order ($this->_db->qn('ride_distance') . ' DESC');

        return $query;
    }

    /**
     * _buildSubqueryGenderPosition
     *
     * Builds the subquery used to calculate the gender position of the returned rides within an event
     * 
     * @return object Subquery object
     **/
    protected function _buildSubqueryGenderPosition()
    {
        $subquery = $this->_db->getQuery(TRUE);
        
        // First position is 1
        $offset = 1;

        $subquery
            ->select('@prev_value:=NULL, @female_position_count:=' . $offset . ' , @male_position_count:=' . $offset . ' , @female_sequence:=' . $offset . ' , @male_sequence:=' . $offset);

        return $subquery;
    }

    /**
     * _buildWhere
     *
     * Builds the filter for the query
     * 
     * @param  object Query object
     * @return object Query object
     **/
    protected function _buildWhere($query)
    {
        switch ($this->_list_type)
        {
            case "event_entries":
                $query
                    ->where($this->_db->qn('e.event_id') . ' = ' . (int) $this->_event_id)
                    ->where($this->_db->qn('r.rider_id') . ' = ' . $this->_db->qn('rr.rider_id'))
                    ->where($this->_db->qn('r.event_id') . ' = ' . $this->_db->qn('e.event_id'));
                break;
            case "event_results":
                break;
            case "rider":
                $query
                    ->where($this->_db->qn('r.rider_id') . ' = ' . (int) $this->_rider_id)
                    ->where($this->_db->qn('r.rider_id') . ' = ' . $this->_db->qn('rr.rider_id'))
                    ->where($this->_db->qn('r.event_id') . ' = ' . $this->_db->qn('e.event_id'))
                    ->where($this->_db->qn('r.rider_id') . ' = ' . $this->_db->qn('rh.rider_id'))
                    ->where($this->_db->qn('r.time') . ' > "00:00:00"')
                    ->where($this->_db->qn('rh.effective_date') . ' = (' . $this->_buildSubqueryNextHistory() . ')');
                break;
            case "rankings":
                if ($this->_ranking_status === "Frequent rider")
                {
                    $query
                        ->where($this->_db->qn('event_date') . ' >= DATE_SUB("' . $this->_last_run_date . '", INTERVAL 4 MONTH)');
                }
                else
                {
                    $query
                        ->where($this->_db->qn('event_date') . ' >= DATE_SUB("' . $this->_last_run_date . '", INTERVAL 1 YEAR)');
                }
                break;
        }

        return $query;
    }

    /**
     * _buildSubqueryNextHistory
     *
     * Builds the subquery used to determine the next rider history record after an event
     *
     * @return object Query object
     **/
    protected function _buildSubqueryNextHistory()
    {
        $subQuery = $this->_db->getQuery(TRUE);

        $subQuery
            ->select('MIN(' . $this->_db->qn('rh2.effective_date') . ')')
            ->from  ($this->_db->qn('#__rider_history', 'rh2'))
            ->where ($this->_db->qn('rh2.rider_id') . ' = ' . $this->_db->qn('r.rider_id'))
            ->where ($this->_db->qn('rh2.effective_date') . ' >= e.event_date');

        return $subQuery;
    }

    /**
     * _buildOrder
     *
     * Builds the sort for the query
     * 
     * @param  object Query object
     * @return object Query object
     **/
    protected function _buildOrder($query)
    {
        switch ($this->_list_type)
        {
            case "event_entries":
                $query
                    ->order('-' . $this->_db->qn('r.predicted_position') . ' DESC');
                break;
            case "event_results":
                $query
                    ->order($this->_db->qn('complete_time') . ' ASC')
                    ->order($this->_db->qn('ride_distance') . ' DESC');
                break;
            case "rider":
                $query
                    ->order($this->_db->qn('e.event_date') . ' DESC');
                break;
            case "rankings":
                $query
                    ->order($this->_db->qn('ranking_points') . ' ASC')
                    ->order($this->_db->qn('event_date') . ' DESC');
                break;
            default:
                $query
                    ->order($this->_db->qn('e.event_date') . ' DESC')
                    ->order($this->_db->qn('r.time') . ' ASC');
        }

        return $query;
    }
}