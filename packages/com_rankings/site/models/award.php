<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.6
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 *
 * Public functions
 * __construct  Constructs the event model
 * listItems    Returns a list of awards
 *
 * Protected functions
 * _buildQuery                  Builds the select query for awards
 * _buildSubqueryRankingEvent   Builds the subquery for rankings awards
 * _buildWhere                  Builds the where clause for the query for awards
 * _buildOrder                  Builds the order clause for the query for awards
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rankings Component Award Model
 */
class RankingsModelsAward extends RankingsModelsDefault
{
    /**
    * Protected fields
    **/
    protected $_award_name     = null;
    protected $_event_id       = null;
    protected $_list_type      = null;
    protected $_rider_id       = null;

    /**
     * listItems
     *
     * Gets a list of awards.
     *
     * @return  model The requested awards
     **/
    public function listItems($offset=null, $limit = null)
    {
        $awards = parent::listItems($offset, $limit);

        $n = count($awards);
        for($i=0;$i<$n;$i++)
        {
            $award = $awards[$i];

            // Set the ordinal for position
            if (isset($award->position))
            {
                $award->position = parent::_ordinal($award->position);
            }

            // Set the award name
            if ($award->team_ind)
            {
                $award->award_name = "Team (" . $award->award_basis . ")";
            } else {
                if (isset($award->age_category))
                {
                    $award->award_name = $award->age_category;
                }
                if (isset($award->gender))
                {
                    $award->award_name = $award->award_name . " " . $award->gender;
                }
                if (isset($award->category))
                {
                    $award->award_name = $award->award_name . " " . $award->category . " Category";
                }
                switch ($award->award_basis)
                {
                    case "Handicap":
                        $award->award_name = $award->award_name . $award->award_basis;
                        break;
                    case "Aggregate":
                    case "Bidlake":
                    case "Standard":
                        $award->award_name = $award->award_name . " (" . $award->award_basis . ")";
                        break;
                    default:
                        break;
                }
                if ($award->min_age > 0)
                {
                    $award->award_name = $award->award_name . " (" . $award->min_age;
                    if ($award->max_age < 100)
                    {
                        $award->award_name = $award->award_name . " - " . $award->max_age . ")";
                    } else {
                        $award->award_name = $award->award_name . "+";
                    }

                }
            }
        }

        return $awards;
    }

    /**
     * _buildQuery
     *
     * Builds the query to be used by the award model
     * 
     * @return object Query object
     **/
    protected function _buildQuery()
    {
        $query = $this->_db->getQuery(TRUE);

        $query
            ->select($this->_db->qn(array('a.rider_id', 'a.event_id', 'a.position', 'a.team_counter')))
            ->select($this->_db->qn(array('at.award_basis', 'at.team_ind', 'at.gender', 'at.age_category', 'at.category', 'at.min_age', 'at.max_age')))
            ->select($this->_db->qn(array('r.club_name', 'r.category_on_day')))
            ->select($this->_db->qn('r.distance') . ' AS ride_distance')
            ->select('CASE TIME_FORMAT(' . $this->_db->qn('r.time') . ', "%k")' . 
                ' WHEN 0 THEN TIME_FORMAT(' . $this->_db->qn('r.time') . ', "%i:%s")' . 
                ' ELSE TIME_FORMAT(' . $this->_db->qn('r.time') . ', "%k:%i:%s")' . 
                ' END' . 
                ' AS ride_time')
            ->select('IF (' . $this->_db->qn('r.time') . ' IN ("12:00:00", "24:00:00"), ' .
                $this->_db->qn('r.distance') . ' - ' . $this->_db->qn('r.vets_standard_distance') . ',' .
                ' CASE TIME_FORMAT(SUBTIME(' . $this->_db->qn('r.vets_standard_time') . ', ' . $this->_db->qn('r.time') . '), "%k")' . 
                ' WHEN 0 THEN TIME_FORMAT(SUBTIME(' . $this->_db->qn('r.vets_standard_time') . ', ' . $this->_db->qn('r.time') . '), "%i:%s")' . 
                ' ELSE TIME_FORMAT(SUBTIME(' . $this->_db->qn('r.vets_standard_time') . ', ' . $this->_db->qn('r.time') . '), "%k:%i:%s")' .
                ' END)' .
                ' AS vets_standard_result')
            ->select('IF (' . $this->_db->qn('r.time') . ' IN ("12:00:00", "24:00:00"), ' .
                $this->_db->qn('r.distance') . ' - ' . $this->_db->qn('r.predicted_distance') . ',' .
                ' CASE TIME_FORMAT(SUBTIME(' . $this->_db->qn('r.predicted_time') . ', ' . $this->_db->qn('r.time') . '), "%k")' . 
                ' WHEN 0 THEN TIME_FORMAT(SUBTIME(' . $this->_db->qn('r.predicted_time') . ', ' . $this->_db->qn('r.time') . '), "%i:%s")' . 
                ' ELSE TIME_FORMAT(SUBTIME(' . $this->_db->qn('r.predicted_time') . ', ' . $this->_db->qn('r.time') . '), "%k:%i:%s")' .
                ' END)' .
                ' AS handicap_result')
            ->select('CONCAT(' . $this->_db->qn('rr.first_name') . ', " ", ' . $this->_db->qn('rr.last_name') . ')' . 
                ' AS name')
            ->select($this->_db->qn(array('rr.blacklist_ind')))
            ->select($this->_db->qn(array('e.event_date', 'e.event_name')))
            ->select('CASE ' . $this->_db->qn('e.distance') . ' WHEN 0 THEN "Other"' . 
                ' ELSE ' . $this->_db->qn('e.distance') . 
                ' END' . 
                ' AS distance')
            ->select($this->_db->qn('team_result'))

            ->from($this->_db->qn('#__rides', 'r'))
            ->from($this->_db->qn('#__riders', 'rr'))
            ->from($this->_db->qn('#__events', 'e'))
            ->from($this->_db->qn('#__awards', 'a'))
            ->from($this->_db->qn('#__award_types', 'at'))
            ->from('(' . $this->_buildSubqueryTeamResults() . ') AS TR');

        return $query;
    }

    /**
     * _buildSubqueryTeamResults
     *
     * Builds the subquery used to retrieve the team results for the teams within an event
     * 
     * @return object Subquery object
     **/
    protected function _buildSubqueryTeamResults()
    {
        $subquery = $this->_db->getQuery(TRUE);

        $subquery
            ->select($this->_db->qn(array('a.event_id', 'a.award_type_id', 'a.position')))
            ->select('CASE ' . $this->_db->qn('e.duration_event_ind') .
                ' WHEN 1 THEN IF (' . $this->_db->qn('at.award_basis') . ' = "Aggregate", SUM(' . $this->_db->qn('r.distance') . '), MIN(' . $this->_db->qn('r.distance') . '))' .
                ' ELSE IF (' . $this->_db->qn('at.award_basis') . ' = "Aggregate", ' . 
                    ' CASE TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(' . $this->_db->qn('r.time') . '))), "%k")' . 
                    ' WHEN 0 THEN TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(' . $this->_db->qn('r.time') . '))), "%i:%s")' . 
                    ' ELSE TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(' . $this->_db->qn('r.time') . '))), "%k:%i:%s")' .
                    ' END' . ', ' .
                    ' CASE TIME_FORMAT(MAX(' . $this->_db->qn('r.time') . '), "%k")' . 
                    ' WHEN 0 THEN TIME_FORMAT(MAX(' . $this->_db->qn('r.time') . '), "%i:%s")' . 
                    ' ELSE TIME_FORMAT(MAX(' . $this->_db->qn('r.time') . '), "%k:%i:%s")' .
                    ' END)' .
                ' END' .
                ' AS team_result')

            ->from($this->_db->qn('#__awards', 'a'))
            ->from($this->_db->qn('#__award_types', 'at'))
            ->from($this->_db->qn('#__rides', 'r'))
            ->from($this->_db->qn('#__events', 'e'))

            ->where($this->_db->qn('a.rider_id') . ' = ' . $this->_db->qn('r.rider_id'))
            ->where($this->_db->qn('a.event_id') . ' = ' . $this->_db->qn('r.event_id'))
            ->where($this->_db->qn('a.award_type_id') . ' = ' . $this->_db->qn('at.award_type_id'))
            ->where($this->_db->qn('a.event_id') . ' = ' . $this->_db->qn('e.event_id'))
            
            ->group($this->_db->qn('a.event_id'))
            ->group($this->_db->qn('a.award_type_id'))
            ->group($this->_db->qn('a.position'));

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
        switch ($this->_list_type)
        {
            case "event":
                $query
                    ->where($this->_db->qn('a.event_id') . ' = ' . (int) $this->_event_id);
                break;
            case "rider":
                $query
                    ->where($this->_db->qn('a.rider_id') . ' = ' . (int) $this->_rider_id);
                break;
        }

        $query
            ->where($this->_db->qn('a.rider_id') . ' = ' . $this->_db->qn('r.rider_id'))
            ->where($this->_db->qn('a.event_id') . ' = ' . $this->_db->qn('r.event_id'))
            ->where($this->_db->qn('a.rider_id') . ' = ' . $this->_db->qn('rr.rider_id'))
            ->where($this->_db->qn('a.event_id') . ' = ' . $this->_db->qn('e.event_id'))
            ->where($this->_db->qn('a.award_type_id') . ' = ' . $this->_db->qn('at.award_type_id'))
            ->where($this->_db->qn('a.event_id') . ' = ' . $this->_db->qn('TR.event_id'))
            ->where($this->_db->qn('a.award_type_id') . ' = ' . $this->_db->qn('TR.award_type_id'))
            ->where($this->_db->qn('a.position') . ' = ' . $this->_db->qn('TR.position'));

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
        switch ($this->_list_type)
        {
            case "event":
                $query
                    ->order($this->_db->qn('at.order') . ' ASC')
                    ->order($this->_db->qn('a.position') . ' ASC')
                    ->order($this->_db->qn('r.club_name') . ' ASC')
                    ->order($this->_db->qn('r.time') . ' ASC')
                    ->order($this->_db->qn('r.distance') . ' DESC');
                break;
            case "rider":
                $query
                    ->order($this->_db->qn('e.event_date') . ' DESC')
                    ->order($this->_db->qn('e.event_id') . ' ASC')
                    ->order($this->_db->qn('at.order') . ' ASC');
                break;
        }

        return $query;
    }
}