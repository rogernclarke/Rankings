<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.1
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
     * Gets a list of events.
     *
     * @param   integer $offset Offset of first ride to return
     * @param   integer $limit  Number of rides to return
     * @return  model The requested rides
     **/
    public function listItems($offset=null, $limit = null)
    {
        $rides = parent::listItems($offset, $limit);

        // Set the ordinal for each position
        $n = count($rides);
        for($i=0;$i<$n;$i++)
        {
            $ride           = $rides[$i];
            $ride->position = parent::_ordinal($ride->position);
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
            ->select($this->_db->qn(array('r.rider_id', 'r.event_id', 'r.club_name', 'r.age_on_day', 'r.position', 'r.time', 'r.ranking_points', 'r.counting_ride_ind', 'r.category_on_day')))
            ->select($this->_db->qn('r.distance') . 'AS ride_distance')
            ->select($this->_db->qn('rr.blacklist_ind'))
            ->select('CONCAT(' . $this->_db->qn('rr.first_name') . ', " ", ' . $this->_db->qn('rr.last_name') . ')' . 
                ' AS name')
            ->select('CONCAT(' . $this->_db->qn('rr.age_category') . ', " ", ' . $this->_db->qn('rr.gender') . ')' . 
                ' AS age_gender_category');

        if ($this->_list_type === "event")
        {
            // Position variance is only applicable for rides displayed for an event
            $query
                ->select('CASE SIGN (' . $this->_db->qn('r.position_variance') . ')' . 
                    ' WHEN -1 THEN "arrow-down"' . 
                    ' WHEN 1 THEN "arrow-up"' . 
                    ' ELSE IF (' .  $this->_db->qn('r.ranked_rider_ind') . ' = TRUE, "arrow-left", "circle")' . 
                    ' END' . 
                    ' AS position_variance_ind')
                ->select('ABS(' . $this->_db->qn('r.position_variance') . ')' .  
                    ' AS position_variance_value');
        }
        else
        {
            if ($this->_list_type === "rider")
            {
                // Improved score is only applicable for rides displayed for a rider
                $query
                    ->select('CASE ' . $this->_db->qn('r.improved_score_ind') . 
                        ' WHEN -1 THEN "arrow-down"' . 
                        ' WHEN 1 THEN "arrow-up"' . 
                        ' ELSE "circle"' . 
                        ' END' . 
                        ' AS improved_ride')
                // Rider category after event is only applicable for rides displayed for a rider
                    ->select($this->_db->qn('rh.category') . ' AS category_after_day')
                    ->from  ($this->_db->qn('#__rider_history', 'rh'));
            }
            // Event details are applicable for rides displayed for a rider or rankings
            $query
                ->select($this->_db->qn(array('e.event_date','e.event_name')))
                ->select('CASE ' . $this->_db->qn('e.distance') . ' WHEN 0 THEN "Other"' . 
                    ' ELSE ' . $this->_db->qn('e.distance') . 
                    ' END' . 
                    ' AS distance');
        }

       $query
            ->from($this->_db->qn('#__rides', 'r'))
            ->from($this->_db->qn('#__riders', 'rr'))
            ->from($this->_db->qn('#__events', 'e'));

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
            case "event":
                $query
                    ->where($this->_db->qn('e.event_id') . ' = ' . (int) $this->_event_id)
                    ->where($this->_db->qn('r.rider_id') . ' = ' . $this->_db->qn('rr.rider_id'))
                    ->where($this->_db->qn('r.event_id') . ' = ' . $this->_db->qn('e.event_id'));;
                break;
            case "rider":
                $query
                    ->where($this->_db->qn('r.rider_id') . ' = ' . (int) $this->_rider_id)
                    ->where($this->_db->qn('r.rider_id') . ' = ' . $this->_db->qn('rr.rider_id'))
                    ->where($this->_db->qn('r.event_id') . ' = ' . $this->_db->qn('e.event_id'))
                    ->where($this->_db->qn('r.rider_id') . ' = ' . $this->_db->qn('rh.rider_id'))
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
            case "event":
                $query
                    ->order($this->_db->qn('r.time') . ' ASC')
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