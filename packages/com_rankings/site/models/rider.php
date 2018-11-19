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
        $this->_filter_fields = array('name', 'club_name', 'district_code');
        $this->_id_name = 'rider_id';
        $this->_table_name = '#__riders';
    }

    function getItem() 
    {
        $rider = parent::getItem();

        if (isset ($rider->rider_id))
        {
            // Get Riders for the rider
            $rideModel = new RankingsModelsRide();
            $rideModel->set('_rider_id', $rider->rider_id);
            $rideModel->set('_list_type', "rider");
            $rider->rides = $rideModel->listItems(0,1000);

            // Update hits for the rider
            $this->_updateHits();
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

        $query->select($this->_db->quoteName(array('rider_id', 'name', 'gender', 'age', 'age_category', 'age_gender_category', 'club_name', 'blacklist_ind', 'score', 'overall_rank', 'gender_rank', 'category')));

        $status = "CASE WHEN ranking_status='F' THEN 'Frequent rider' WHEN ranking_status='C' THEN 'Complete' WHEN ranking_status='P' THEN 'Provisional' WHEN ranking_status='L' THEN 'Lapsed' WHEN ranking_status='' THEN '' END AS status";

        $query->select($status);

        $query->from('#__rider_current');

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
            $query->where('rider_id = ' . $this->_id);
        }
        else
        {
            // Retrieve by filters

            // Filter by district
            $search = $this->getState('filter.district_code');

            if (!empty($search))
            {
                if ($search != 'All')
                {
                    $search = $this->_db->quote(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
                    $query->where('district_code = ' . $search);
                }
            }

            // Filter by search in name
            $search = $this->getState('filter.name');

            if (!empty($search))
            {
                $search = $this->_db->quote('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                $query->where('(name LIKE ' . $search . ')');
            }

            // Filter by search in club name
            $search = $this->getState('filter.club_name');

            if (!empty($search))
            {
                $search = $this->_db->quote('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                $query->where('(club_name LIKE ' . $search . ')');
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
        $query->order('(select REPLACE(name,".","~")) ASC');

        return $query;
    }
}