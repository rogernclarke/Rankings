<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.7
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 *
 * Public functions
 * __construct  Constructs the event model
 * getItem      Returns a specific rider
 *
 * Protected functions
 * _buildQuery  Builds the select query for rider(s)
 * _buildWhere  Builds the where clause for the query for rider(s)
 * _buildOrder  Builds the order clause for the query for riders
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

        // Set id name and table name for model
        $this->_id_name    = 'rider_id';
        $this->_table_name = '#__riders';
    }

    /**
     * getItem
     *
     * Gets a specific rider.
     *
     * @return  model The requested rider
     **/
    function getItem() 
    {
        $rider = parent::getItem();

        // Get time trial rides for the rider
        $rideModel    = new RankingsModelsRide();
        $rideModel->set('_rider_id', $rider->rider_id);
        $rideModel->set('_list_type', "rider");
        $rideModel->set('_hill_climb_ind', 0);
        $rider->tt_rides = $rideModel->listItems(0,1000);

        // Get hill climb rides for the rider
        $rideModel->set('_hill_climb_ind', true);
        $rider->hc_rides = $rideModel->listItems(0,1000);

        // Get awards for the rider
        $awardModel    = new RankingsModelsAward();
        $awardModel->set('_rider_id', $rider->rider_id);
        $awardModel->set('_list_type', "rider");

        //$rider->award = $awardModel;

        $rider->awards = $awardModel->listItems(0,1000);

        $_awards = $awardModel->listItems(0,1000);

        // Assign awards to rides
        $_ride_count = 0;
        foreach ($rider->tt_rides as $_ride)
        {
            $_award_count = 0;
            foreach ($rider->awards as $_award)
            //foreach ($_awards as $_award)
            {
                if ($_ride->event_id === $_award->event_id)
                {
                    $rider->tt_rides[$_ride_count]->awards[$_award_count] = $_award;
                    $_award_count++;
                }
            }
            $_ride_count++;
        }

        // Update hits for the rider
        $this->_updateHits();

        return $rider;
    }

    /**
     * _buildQuery
     *
     * Builds the query to be used by the riders model
     * 
     * @return object Query object
     **/
    protected function _buildQuery()
    {
        $query = $this->_db->getQuery(TRUE);

        $query
            ->select($this->_db->qn(array('rc.rider_id', 'rc.name', 'rc.gender', 'rc.age', 'rc.age_category', 'rc.club_name', 'rc.blacklist_ind', 'rc.score', 'rc.overall_rank', 'rc.gender_rank', 'rc.category', 'rc.district_rank', 'rc.age_category_rank', 'rc.hc_score', 'rc.hc_overall_rank', 'rc.hc_gender_rank', 'rc.hc_category', 'rc.hc_district_rank', 'rc.hc_age_category_rank', 'd.district_name')))
            ->select('CONCAT(' . $this->_db->qn('age_category') . ' , " ", ' . $this->_db->qn('gender') . ') AS age_gender_category')
            ->select('CASE ' . $this->_db->qn('rc.gender') . 
                        ' WHEN "Male" THEN "mars"' . 
                        ' WHEN "Female" THEN "venus"' . 
                        ' ELSE ""' . 
                        ' END' . 
                        ' AS gender_icon')
            ->select('CASE ' . $this->_db->qn('rc.ranking_status') . 
                ' WHEN "F" THEN "Frequent rider"' . 
                ' WHEN "C" THEN "Qualified"' . 
                ' WHEN "P" THEN "Provisional"' . 
                ' WHEN "L" THEN "Lapsed"' . 
                ' ELSE ""' . 
                ' END' .
                ' AS status')
            ->select('CASE ' . $this->_db->qn('rc.hc_ranking_status') . 
                ' WHEN "F" THEN "Frequent rider"' . 
                ' WHEN "C" THEN "Qualified"' . 
                ' WHEN "P" THEN "Provisional"' . 
                ' WHEN "L" THEN "Lapsed"' . 
                ' ELSE ""' . 
                ' END' .
                ' AS hc_status')
            ->from($this->_db->qn('#__rider_current', 'rc'))
            ->join('LEFT', $this->_db->qn('#__districts', 'd') . ' ON (' . $this->_db->qn('rc.district_code') . ' = ' . $this->_db->qn('d.district_code') . ')');

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
        if (isset($this->_id))
        {
            // Retrieve a specific rider by id
            $query
                ->where($this->_db->qn('rider_id') . ' = ' . $this->_id);
        }
        else
        {
            // Retrieve a list - apply filters if specified

            // Filter by district code
            $search = $this->getState('filter.district_code');

            if (!empty($search))
            {
                if ($search != 'All')
                {
                    $search = $this->_db->q(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
                    $query
                        ->where($this->_db->qn('rc.district_code') . ' = ' . $search);
                }
            }

            // Filter by search in name
            $search = $this->getState('filter.name');

            if (!empty($search))
            {
                $search = $this->_db->quote('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                $query
                    ->where($this->_db->qn('name') . ' LIKE ' . $search);
            }

            // Filter by search in club name
            $search = $this->getState('filter.club_name');

            if (!empty($search))
            {
                $search = $this->_db->quote('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                $query
                    ->where($this->_db->qn('club_name') . ' LIKE ' . $search);
            }

            // Don't retrieve blacklisted riders
            $query
                ->where($this->_db->qn('blacklist_ind') . ' = 0');
        }

        return $query;
    }

    /**
     * _buildOrder
     *
     * Builds the sort for the query (when a list is requested).
     * 
     * @param  object Query object
     * @return object Query object
     **/
    protected function _buildOrder($query)
    {
        $query
            ->order('(SELECT REPLACE(' . $this->_db->qn('name') . ',".","~")) ASC');

        return $query;
    }
}