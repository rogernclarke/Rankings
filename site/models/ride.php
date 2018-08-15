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
    protected $_list_type = null;

    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();
    }

    public function listItems()
    {
        $rides = parent::listItems();

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

        $query->select($this->_db->quoteName(array('r.rider_id', 'r.event_id', 'r.club_name', 'r.age_on_day', 'r.position', 'r.time', 'r.ranking_points')));
        $query->from('#__rides as r');

        $query->select($name);
        $query->select($this->_db->quoteName(array('rr.age_category')));
        $query->leftjoin('#__riders as rr on rr.rider_id = r.rider_id');

        $query->select($this->_db->quoteName(array('e.event_date','e.event_name','e.distance')));
        $query->leftjoin('#__events as e on e.event_id = r.event_id');

        return $query;
    }

    /**
     * Builds the filter for the query
     * 
     * @param object Query object
     * @return object Query object
     **/
    protected function _buildWhere($query)
    {
        if(is_numeric($this->_event_id))
        {
            $query->where('r.event_id = ' . (int) $this->_event_id);
        }
        if(is_numeric($this->_rider_id))
        {
            $query->where('r.rider_id = ' . (int) $this->_rider_id);
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
                $query->order('r.ranking_points DESC');
                break;
            default:
                $query->order('e.event_date DESC');
                $query->order('r.time ASC');
        }

        return $query;
    }
}