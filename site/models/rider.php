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

        // Specify filter fields for model
        $this->_filter_fields = array('name', 'club_name');
    }

    function getItem() 
    {
        $rider = parent::getItem();

        if (isset ($rider->rider_id))
        {
            $rideModel = new RankingsModelsRide();
            $rideModel->set('_rider_id', $rider->rider_id);
            $rideModel->set('_list_type', "rider");
            $rider->rides = $rideModel->listItems();
        }

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

        $category = "CONCAT(rr.age_category, ' ', rr.gender) as category";
        $name = "CONCAT(rr.first_name, ' ', rr.last_name) as name";

        $query->select($this->_db->quoteName(array('rr.rider_id', 'rr.first_name', 'rr.last_name', 'rr.gender', 'rr.age', 'rr.age_category', 'rr.club_name', 'rr.updated_date')));
        $query->select($name);
        $query->select($category);
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
        if (isset($this->_id))
        {
            // Retrieve by id
            $query->where('rr.rider_id = ' . $this->_id);
        }
        else
        {
            // Retrieve by filters


            // Filter by search in name
            $search = $this->getState($this->_context . '.name.filter');

            if (!empty($search))
            {
            /*if (stripos($search, 'id:') === 0)
            {
                $query->where('rr.rider_id = ' . (int) substr($search, 3));
            }
            else
            {*/
                $search = $this->_db->quote('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                $name = "CONCAT(rr.first_name,rr.last_name)";
                $query->where('(' . $name . ' LIKE ' . $search . ')');
            }

            // Filter by search in club name
            $search = $this->getState($this->_context . '.club_name.filter');

            if (!empty($search))
            {
                /*if (stripos($search, 'id:') === 0)
                {
                    $query->where('rr.rider_id = ' . (int) substr($search, 3));
                }*/
            }
            else
            {
                $search = $this->_db->quote('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                $query->where('(rr.club_name LIKE ' . $search . ')');
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