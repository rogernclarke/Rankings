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
 * Rankings Component Rankings Model
 */
class RankingsModelsRankings extends RankingsModelsDefault
{
    protected $_position_ind = FALSE; // Set to true if position calculation is required

    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();

        // Specify filter fields for model
        $this->_filter_fields = array('gender', 'name', 'club_name', 'district_code', 'age_category');
        $this->_check_fields = array('status');
    }

    public function listItems()
    {
        $this->set('_position_ind', $this->getPositionInd());

        $rankings = parent::listItems();

        $rideModel = new RankingsModelsRide();
        $n = count($rankings);

        for($i=0;$i<$n;$i++)
        {
            $rider = $rankings[$i];

            $rideModel->set('_rider_id', $rider->rider_id);
            $rideModel->set('_ranking_status', $rider->status);
            $rideModel->set('_list_type', "rankings");
            
            $rider->rides = $rideModel->listItems(0,1000);
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
        
        $query->select($this->_db->quoteName(array('rider_id', 'name', 'gender', 'age', 'age_category', 'age_gender_category', 'club_name', 'score', 'overall_rank', 'gender_rank', )));

        $status = "CASE WHEN ranking_status='F' THEN 'Frequent rider' WHEN ranking_status='C' THEN 'Complete' WHEN ranking_status='P' THEN 'Provisional' WHEN ranking_status='L' THEN 'Lapsed' WHEN ranking_status='' THEN '' END AS status";
        $change_in_overall_rank_ind = "CASE SIGN(change_in_overall_rank) WHEN -1 THEN 'arrow-down' WHEN 1 THEN 'arrow-up' ELSE IF (newly_complete_ind = FALSE, 'arrow-left', 'circle') END as change_in_overall_rank_ind";
        $change_in_overall_rank_value = "ABS(change_in_overall_rank) as change_in_overall_rank_value";
        $change_in_gender_rank_ind = "CASE SIGN(change_in_gender_rank) WHEN -1 THEN 'arrow-down' WHEN 1 THEN 'arrow-up' ELSE IF (newly_complete_ind = FALSE, 'arrow-left', 'circle') END as change_in_gender_rank_ind";
        $change_in_gender_rank_value = "ABS(change_in_gender_rank) as change_in_gender_rank_value";

        $query->select($status);
        $query->select($change_in_overall_rank_ind);
        $query->select($change_in_overall_rank_value);
        $query->select($change_in_gender_rank_ind);
        $query->select($change_in_gender_rank_value);

        if ($this->_position_ind)
        {
            $position = "CASE WHEN @prev_value = overall_rank THEN @position_count WHEN @prev_value := overall_rank THEN @position_count := @sequence END AS position, @sequence:=@sequence + 1";
            $query->select($position);

            $start = $this->getState('list.start') + 1;

            $search = $this->getState('check.status');
            if ($search == 1)
            {
                $query->from('(select * from tt_rider_current where ranking_status in("F","C","P") ORDER BY overall_rank) as t1, (SELECT @prev_value:=NULL, @position_count:=' . $start . ', @sequence:=' . $start . ') as t2');
            } else {
                $query->from('(select * from tt_rider_current where ranking_status in("F","C") ORDER BY overall_rank) as t1, (SELECT @prev_value:=NULL, @position_count:=' . $start . ', @sequence:=' . $start . ') as t2');
            }

            //$query->from('(select * from tt_rider_current where ranking_status in("F","C","P") ORDER BY overall_rank) as t1, (SELECT @prev_value:=NULL, @position_count:=' . $start . ', @sequence:=' . $start . ') as t2');
        } ELSE {
            $query->from('#__rider_current');
        }

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
            $query->where('rider_id = ' . (int) $this->_id);
        }
        else
        {
            // Retrieve by filters

            // Filter by gender
            $search = $this->getState('filter.gender');

            if (!empty($search))
            {
                if ($search != 'All')
                {
                    $search = $this->_db->quote(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
                    $query->where('gender = ' . $search);
                }
            }

            // Filter by age category
            $search = $this->getState('filter.age_category');

            if (!empty($search))
            {
                if ($search != 'All')
                {
                    $search = $this->_db->quote(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
                    $query->where('age_category = ' . $search);
                }
            }

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

            // Filter by status
            $search = $this->getState('check.status');

            if ($search == 1)
            {
                $query->where('ranking_status in ("C", "F", "P")');
            } else {
                $query->where('ranking_status in ("C", "F")');
            }
        }
        
        $query->where('blacklist_ind = 0');

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
        $query->order('overall_rank ASC, score ASC, ranking_status ASC');

        return $query;
    }
    /**
     * Determines if position should be calculated
     * 
     * @return boolean Position indicator
     **/
    public function getPositionInd()
    {
        $position_ind = FALSE;

        // Check filter by age category
        $search = $this->getState('filter.age_category');

        if (!empty($search))
        {
            if ($search != 'All')
            {
                $position_ind = TRUE;
            }
        }

        // Check filter by district
        $search = $this->getState('filter.district_code');

        if (!empty($search))
        {
            if ($search != 'All')
            {
                $position_ind = TRUE;
            }
        }

        // Check filter by search in name
        $search = $this->getState('filter.name');

        if (!empty($search))
        {
            $position_ind = TRUE;
        }

        // Check filter by search in club name
        $search = $this->getState('filter.club_name');

        if (!empty($search))
        {
            $position_ind = TRUE;
        }

        return $position_ind;
    }
}