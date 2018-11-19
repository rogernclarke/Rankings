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

/**
 * Rankings Component Ride Model
 */
class RankingsModelsRide extends RankingsModelsDefault
{
    /**
    * Protected fields
    **/
    protected $_rider_id = null;
    protected $_event_id = null;
    protected $_last_run_date = null;
    protected $_list_type = null;
    protected $_ranking_status = null;

    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();

        // Get the last rankings calculation date
        $this->_lastrundate = $this->_getLastRunDate();
    }

    public function listItems($offset=null, $limit = null)
    {
        $rides = parent::listItems($offset, $limit);

        $n = count($rides);

        for($i=0;$i<$n;$i++)
        {
            $ride = $rides[$i];
            $ride->position = parent::_ordinal($ride->position);
        }

        return $rides;
    }

    /**
     * Builds the query to be used by the rankings model
     * 
     * @return object Query object
     **/
    protected function _buildQuery()
    {
        $query = $this->_db->getQuery(TRUE);
        
        $name = "CONCAT(rr.first_name, ' ', rr.last_name) as name";
        $category = "CONCAT(rr.age_category, ' ', rr.gender) as age_gender_category";
        $distance = "CASE e.distance WHEN 0 THEN 'Other' ELSE e.distance END as distance";
        $improved_ride = "CASE r.improved_score_ind WHEN -1 THEN 'arrow-down' WHEN 1 THEN 'arrow-up' ELSE 'circle' END as improved_ride";
        $position_variance_ind = "CASE SIGN(r.position_variance) WHEN -1 THEN 'arrow-down' WHEN 1 THEN 'arrow-up' ELSE IF (r.ranked_rider_ind = TRUE, 'arrow-left', 'circle') END as position_variance_ind";
        $position_variance_value = "ABS(r.position_variance) as position_variance_value";

        $query->select($this->_db->quoteName(array('r.rider_id', 'r.event_id', 'r.club_name', 'r.age_on_day', 'r.position', 'r.time', 'r.ranking_points', 'r.counting_ride_ind', 'r.position_variance')));
        $query->from('#__rides as r');

        $query->select($this->_db->quoteName(array('rr.blacklist_ind')));
        $query->select($name);
        $query->select($category);
        $query->select($improved_ride);
        $query->select($position_variance_ind);
        $query->select($position_variance_value);
        $query->leftjoin('#__riders as rr on rr.rider_id = r.rider_id');

        $query->select($this->_db->quoteName(array('e.event_date','e.event_name')));
        $query->select($distance);
        $query->leftjoin('#__events as e on e.event_id = r.event_id');
        
        if ($this->_list_type === "rankings")
        {
            $query->where('r.rider_id = ' . (int) $this->_rider_id);
            $query->order('e.event_date DESC');

            if ($this->_ranking_status === "Complete")
            {
                $query->setlimit(8);
            }

            $outerQuery = $this->_db->getQuery(TRUE);

            $outerQuery->select($this->_db->quoteName(array('name', 'age_gender_category', 'distance', 'rider_id', 'blacklist_ind', 'event_id', 'club_name', 'age_on_day', 'position', 'time', 'ranking_points', 'counting_ride_ind', 'improved_ride', 'position_variance', 'position_variance_ind', 'position_variance_value', 'event_date','event_name', 'distance')));
            $outerQuery->from('(' . $query . ') as T1');

            return $outerQuery;
        } else {
            return $query;
        }

    }

    /**
     * Builds the filter for the query
     * 
     * @param object Query object
     * @return object Query object
     **/
    protected function _buildWhere($query)
    {
        switch ($this->_list_type)
        {
            case "rider":
                $query->where('r.rider_id = ' . (int) $this->_rider_id);
                break;
            case "event":
                $query->where('e.event_id = ' . (int) $this->_event_id);
                break;
            case "rankings":
                $query->where('rider_id = ' . (int) $this->_rider_id);

                switch ($this->_ranking_status)
                {
                    case "Frequent rider":
                        $query->where('event_date >= DATE_SUB("' . $this->_lastrundate . '", INTERVAL 4 MONTH)');
                        break;
                    default:
                        $query->where('event_date >= DATE_SUB("' . $this->_lastrundate . '", INTERVAL 1 YEAR)');
                }
                break;
        }

        return $query;
    }
    /**
     * Builds the sort for the query
     * 
     * @param object Query object
     * @return object Query object
     **/
    protected function _buildOrder($query)
    {
        switch ($this->_list_type)
        {
            case "rider":
                $query->order('e.event_date DESC');
                break;
            case "event":
                $query->order('r.time ASC');
                break;
            case "rankings":
                $query->order('ranking_points ASC');
                //$query->order('counting_ride_ind');
                $query->order('event_date DESC');
                break;
            default:
                $query->order('e.event_date DESC');
                $query->order('r.time ASC');
        }

        return $query;
    }
}