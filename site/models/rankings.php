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
 * Rankings Component Rankings Model
 */
class RankingsModelsRankings extends RankingsModelsDefault
{
    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();
    }

    public function listItems()
    {
        $rankings = parent::listItems();

        $rideModel = new RankingsModelsRide();
        $n = count($rankings);

        for($i=0;$i<$n;$i++)
        {
            $rider = $rankings[$i];

            $rideModel->set('_rider_id', $rider->rider_id);
            $rideModel->set('_list_type', "rankings");
            $rider->rides = $rideModel->listItems();
        }

        return $rankings;
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

        $query->select($this->_db->quoteName(array('rr.rider_id', 'rr.first_name', 'rr.last_name', 'rr.gender', 'rr.age', 'rr.age_category', 'rr.club_name', 'rr.updated_date')));
        $query->select($name);
        $query->from('#__riders as rr');

        $query->select($this->_db->quoteName(array('rh.rank','rh.ranking_points')));
        $query->leftjoin('#__rider_history as rh on rh.rider_id = rr.rider_id');

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
        if(is_numeric($this->_id))
        {
            $query->where('rr.rider_id = ' . (int) $this->_id);
        }

        $subQuery = $this->_db->getQuery(TRUE);

        $subQuery->select('max(rh2.effective_date)');
        $subQuery->from('#__rider_history as rh2');
        $subQuery->where('rh2.rider_id = rh.rider_id');

        $query->where('rh.effective_date = (' . $subQuery . ')');

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
        $query->order('rh.rank ASC');

        return $query;
    }
}