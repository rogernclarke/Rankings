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
 * Rankings Component Rider Model
 */
class RankingsModelsRider extends RankingsModelsDefault
{
    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();
    }

    function getItem() 
    {
        $rider = parent::getItem();

        $rideModel = new RankingsModelsRide();
        $rideModel->set('_rider_id', $rider->rider_id);
        $rideModel->set('_list_type', "rider");
        $rider->rides = $rideModel->listItems();

        return $rider;
    }
    public function listItems()
    {
        $riders = parent::listItems();

        return $riders;
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

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('rr.rider_id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $this->_db->quote('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                $name = "CONCAT(rr.first_name,rr.last_name)";
                $query->where('(' . $name . ' LIKE ' . $search . ')');
                //$query->where('(rr.first_name LIKE ' . $search . ')');
            }
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
        $query->order('rr.first_name ASC');
        $query->order('rr.last_name ASC');

        return $query;
    }
}