<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.8
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 *
 * Public functions
 * __construct  Constructs the event model
 * listItems    Returns a list of rankings
 *
 * Protected functions
 * _buildQuery                  Builds the select query for rankings
 * _buildSubqueryRiders         Builds the subquery for determining the set of riders to be returned when positions are being calculated
 * _buildSubqueryPosition       Builds the subquery for determining position
 * _buildWhere                  Builds the where clause for the query for rankings
 * _buildOrder                  Builds the order clause for the query for rankings
 * _determineFilters            Determines which filters are set
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rankings Component Ranking Model
 */
class RankingsModelsRanking extends RankingsModelsDefault
{
    /**
    * Public fields
    **/

    /**
    * Protected fields
    **/
    protected $_age_category_search     = FALSE;
    protected $_club_name_search        = FALSE;
    protected $_district_code_search    = FALSE;
    protected $_name_search             = FALSE;
    protected $_pfx                     = '';
    protected $_ranking_status_search   = array();

    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();

        // Specify filter fields for model
        $this->_filter_fields = array('year','gender', 'name', 'club_name', 'district_code', 'age_category');
        $this->_check_fields = array('status');

        // Determine which filters are set
        $this->_determineFilters();

        // Set column name prefix (hc_ for hill climbs or blank for time trials)
        if ($this->_id)
        {
            $this->_pfx = 'hc_';
        }
    }

    /**
     * listItems
     *
     * Gets a list of rankings.
     *
     * @return  model The requested rankings
     **/
    public function listItems()
    {
        //JFactory::getApplication()->enqueueMessage($this->getTotal(), 'debug');
        //JFactory::getApplication()->enqueueMessage($this->_id, 'debug');
        if ($this->_id  && $this->getTotal() < 20)
        {
            //JFactory::getApplication()->enqueueMessage("Set Status", 'debug');
            //$app = JFactory::getApplication();
            //$this->state->set('check.status', 1);
        }

        $rankings = parent::listItems();

        $rideModel = new RankingsModelsRide();
        $n         = count($rankings);

        // For each rider retrieve the list of rides to be shown on the rankings list
        for($i=0; $i<$n; $i++)
        {
            $rider = $rankings[$i];

            $rideModel->set('_rider_id', $rider->rider_id);
            $rideModel->set('_ranking_status', $rider->status);
            $rideModel->set('_list_type', "rankings");
            $rideModel->set('_hill_climb_ind', $this->_id);

            // For hill climbs only list rides for the year requested
            $rideModel->set('_year', str_replace(' ', '%',   $this->_db->escape(trim($this->getState('filter.year')), true)));

            $rider->rides = $rideModel->listItems(0,1000);
        }

        return $rankings;
    }

    /**
     * _buildQuery
     *
     * Builds the query to be used by the rankings model
     * 
     * @return object Query object
     **/
    protected function _buildQuery()
    {
        $query = $this->_db->getQuery(TRUE);

        $query
            ->select($this->_db->qn(array('rider_id', 'name', 'gender', 'age', 'age_category', 'club_name')))
            ->select($this->_db->qn(array($this->_pfx . 'score', $this->_pfx . 'overall_rank', $this->_pfx . 'gender_rank'), array('score', 'overall_rank', 'gender_rank')))
            ->select('CONCAT(' . $this->_db->qn('age_category') . ' , " ", ' . $this->_db->qn('gender') . ') AS age_gender_category')
            ->select('CASE ' . $this->_db->qn($this->_pfx . 'ranking_status') .  
                ' WHEN "F" THEN "Frequent rider"' . 
                ' WHEN "C" THEN "Complete"' . 
                ' WHEN "P" THEN "Provisional"' . 
                ' WHEN "L" THEN "Lapsed"' . 
                ' ELSE ""' . 
                ' END' .
                ' AS status')
            ->select('CASE SIGN(' . $this->_db->qn($this->_pfx . 'change_in_overall_rank') . ')' . 
                ' WHEN -1 THEN "arrow-down"' . 
                ' WHEN 1 THEN "arrow-up"' . 
                ' ELSE IF (' . $this->_db->qn($this->_pfx . 'newly_complete_ind') . ' = FALSE, "arrow-left", "circle")' . 
                ' END' .
                ' AS change_in_overall_rank_ind')
            ->select('ABS(' . $this->_db->qn($this->_pfx . 'change_in_overall_rank') . ')' .  
                    ' AS change_in_overall_rank_value')
            ->select('CASE SIGN(' . $this->_db->qn($this->_pfx . 'change_in_gender_rank') . ')' . 
                ' WHEN -1 THEN "arrow-down"' . 
                ' WHEN 1 THEN "arrow-up"' . 
                ' ELSE IF (' . $this->_db->qn($this->_pfx . 'newly_complete_ind') . ' = FALSE, "arrow-left", "circle")' . 
                ' END' .
                ' AS change_in_gender_rank_ind')
            ->select('ABS(' . $this->_db->qn($this->_pfx . 'change_in_gender_rank') . ')' .  
                    ' AS change_in_gender_rank_value');

            if ($this->_id)
            {
                $query
                    ->select($this->_db->qn('hc_category') . ' AS category')
                    ->select('"hc"' . ' AS rankings_type');
            } else {
                $query
                    ->select($this->_db->qn('category') . ' AS category')
                    ->select('"tt"' . ' AS rankings_type');
            }
        
        // Calculate position in list only if certain filters are applied
        if ($this->_age_category_search || $this->_district_code_search || $this->_name_search || $this->_club_name_search)
        {
            $query
                ->select('CASE' .
                    ' WHEN @prev_value = ' . $this->_db->qn($this->_pfx . 'overall_rank') . ' THEN @position_count' . 
                    ' WHEN @prev_value := ' . $this->_db->qn($this->_pfx . 'overall_rank') . ' THEN @position_count := @sequence' . 
                    ' END' .
                    ' AS position, @sequence:=@sequence + 1')
                ->from('(' . $this->_buildSubqueryRiders() . ') AS T1')
                ->from('(' . $this->_buildSubqueryPosition() . ') AS T2');
        } ELSE {
            $query
                ->from($this->_db->qn('#__rider_current'));
        }

        return $query;
    }

    /**
     * _buildSubqueryRiders
     *
     * Builds the subquery used to return the set of ranked riders for whom position is to be calculated
     * 
     * @return object Subquery object
     **/
    protected function _buildSubqueryRiders()
    {
        $subquery = $this->_db->getQuery(TRUE);
        
        // Determine if provisionally ranked riders are included
        $include_provisional_riders = $this->getState('check.status');

        if ($include_provisional_riders == 1)
        {
            $statuses = array("F", "C", "P");
        } else {
            $statuses = array("F", "C");
        }

        $subquery
            ->select('*')
            ->from  ($this->_db->qn('#__rider_current'))
            ->where ($this->_db->qn($this->_pfx . 'ranking_status') . ' IN ("' . implode('","', $statuses) . '")')
            ->order ($this->_db->qn($this->_pfx . 'overall_rank'));
//JFactory::getApplication()->enqueueMessage(JText::_($subquery), 'debug');
        return $subquery;
    }

    /**
     * _buildSubqueryPosition
     *
     * Builds the subquery used to calculate the position of the returned riders
     * 
     * @return object Subquery object
     **/
    protected function _buildSubqueryPosition()
    {
        $subquery = $this->_db->getQuery(TRUE);
        
        // First position is set from number of items per page + 1
        $offset = $this->getState('list.start') + 1;

        $subquery
            ->select('@prev_value:=NULL, @position_count:=' . $offset . ' , @sequence:=' . $offset);

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
        // Apply filters if specified

        // Filter by gender
        $search = $this->getState('filter.gender');

        if (!empty($search))
        {
            if ($search != 'All')
            {
                $search = $this->_db->q(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
                $query
                    ->where($this->_db->qn('gender') . ' = ' . $search);
            }
        }

        // Filter by age category
        if ($this->_age_category_search)
        {
            $search = $this->getState('filter.age_category');
            $search = $this->_db->q(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
            $query
                ->where($this->_db->qn('age_category') . ' = ' . $search);
        }

        // Filter by district
        if ($this->_district_code_search)
        {
            $search = $this->getState('filter.district_code');
            $search = $this->_db->q(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
            $query
                ->where($this->_db->qn('district_code') . ' = ' . $search);
        }

        // Filter by search in name
        if ($this->_name_search)
        {
            $search = $this->getState('filter.name');
            $search = $this->_db->q('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
            $query
                ->where($this->_db->qn('name') . ' LIKE ' . $search);
        }

        // Filter by search in club name
        if ($this->_club_name_search)
        {
            $search = $this->getState('filter.club_name');
            $search = $this->_db->q('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
            $query
                ->where($this->_db->qn('club_name') . ' LIKE ' . $search);
        }

        // Filter by ranking status
        $search = $this->getState('check.status');

        if ($search == 1)
        {
            $query
                ->where($this->_db->qn($this->_pfx . 'ranking_status') . ' IN ("C", "F", "P")');
        } else {
            $query
                ->where($this->_db->qn($this->_pfx . 'ranking_status') . ' IN ("C", "F")');
        }
//JFactory::getApplication()->enqueueMessage(JText::_($query), 'debug');       
        // Don't retrieve blacklisted riders
        $query
            ->where($this->_db->qn('blacklist_ind') . ' = 0');

        return $query;
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
        $query
            ->order($this->_db->qn($this->_pfx . 'overall_rank') . ' ASC, ' . $this->_db->qn($this->_pfx . 'score') . ' ASC, ' . $this->_db->qn($this->_pfx . 'ranking_status') . ' ASC');

        return $query;
    }

    /**
     * _determineFilters
     *
     * Builds the sort for the query
     * 
     **/
    protected function _determineFilters()
    {
        // Check filter by age category
        $search = $this->getState('filter.age_category');

        if (!empty($search))
        {
            if ($search != 'All')
            {
                $this->_age_category_search = TRUE;
            }
        }

        // Check filter by district
        $search = $this->getState('filter.district_code');

        if (!empty($search))
        {
            if ($search != 'All')
            {
                $this->_district_code_search = TRUE;
            }
        }

        // Check filter by search in name
        $search = $this->getState('filter.name');

        if (!empty($search))
        {
            $this->_name_search = TRUE;
        }

        // Check filter by search in club name
        $search = $this->getState('filter.club_name');

        if (!empty($search))
        {
            $this->_club_name_search = TRUE;
        }

        // Determine if provisionally ranked riders are included
        $search = $this->getState('check.status');

        if ($search == 1)
        {

        }
    }
}