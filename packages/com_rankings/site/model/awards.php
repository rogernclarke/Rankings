<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rankings Component Awards Model
 *
 * @since 2.0
 */
class RankingsModelAwards extends RankingsModelList
{
	/**
	 * Award name
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $awardName = null;

	/**
	 * Event ID
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $eventId = null;

	/**
	 * List Type - ranking, event or rider
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $listType = null;

	/**
	 * Rider ID
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $riderId = null;

	/**
	 * Constructor.
	 *
	 * @param   array  	$config  	An optional associative array of configuration settings.
	 * @param 	string 	$listType 	Type of list to return riders for (event entries, event results, ranking, rider)
	 *
	 * @see     \JModelList
	 * @since   2.0
	 */
	public function __construct($config = array(), $listType = 'rider')
	{
		parent::__construct($config);

		// Set list type
		$this->listType = $listType;
	}

	/**
	 * getItems
	 *
	 * Gets a list of awards.
	 *
	 * @return 	model 	The requested awards
	 *
	 * @since 2.0
	 */
	public function getItems()
	{
		$awards = parent::getItems();

		foreach ($awards as $award)
		{
			// Set the ordinal for position
			if (isset($award->position))
			{
				$award->position = $this->setOrdinal($award->position);
			}

			// Set the award name
			if ($award->team_ind)
			{
				$award->awardName = 'Team (' . $award->award_basis . ')';
			}
			else
			{
				if (isset($award->age_category))
				{
					$award->awardName = $award->age_category;
				}

				if (isset($award->gender))
				{
					$award->awardName .= ' ' . $award->gender;
				}

				if (isset($award->category))
				{
					$award->awardName .= ' ' . $award->category . " Category";
				}

				switch ($award->award_basis)
				{
					case "Handicap":
						$award->awardName .= $award->award_basis;
						break;
					case "Aggregate":
					case "Bidlake":
					case "Standard":
						$award->awardName .= ' (' . $award->award_basis . ')';
						break;
					default:
						break;
				}

				if ($award->min_age > 0)
				{
					$award->awardName .= ' (' . $award->min_age;
					
					if ($award->max_age < 100)
					{
						$award->awardName .= ' - ' . $award->max_age . ")";
					}
					else
					{
						$award->awardName .= '+';
					}
				}
			}
		}

		return $awards;
	}

	/**
	 * getQuerySelect
	 *
	 * Builds the query select to be used by the awards model
	 *
	 * @param 	object 	$db 	Database object
	 * @param 	object 	$query 	Query object
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 **/
	protected function getQuerySelect($db, $query)
	{
		// Get all awards
		$this->state->set('list.limit', 0);

		// Select required fields from awards
		$query
			->select($this->getState('list.select', 'a.*'))

		// Award type details
			->select('at.*')

		// Ride details
			->select($db->qn(array('r.club_name', 'r.category_on_day')))
			->select($db->qn('r.distance') . ' AS ride_distance')
			->select('CASE TIME_FORMAT(' . $db->qn('r.time') . ', "%k")' .
				' WHEN 0 THEN TRIM(TRAILING "00000" FROM(TRIM(LEADING "0" FROM TIME_FORMAT(' . $db->qn('r.time') . ', IF(' . $db->qn('e.hill_climb_ind') . ' = TRUE, "%i:%s.%f", "%i:%s")))))' .
				' ELSE TIME_FORMAT(' . $db->qn('r.time') . ', "%k:%i:%s")' .
				' END' .
				' AS ride_time'
			)
			->select('IF (' . $db->qn('r.time') . ' IN ("12:00:00", "24:00:00"), ' .
				$db->qn('r.distance') . ' - ' . $db->qn('r.vets_standard_distance') . ',' .
				' CASE TIME_FORMAT(SUBTIME(' . $db->qn('r.vets_standard_time') . ', ' . $db->qn('r.time') . '), "%k")' .
				' WHEN 0 THEN TIME_FORMAT(SUBTIME(' . $db->qn('r.vets_standard_time') . ', ' . $db->qn('r.time') . '), "%i:%s")' .
				' ELSE TIME_FORMAT(SUBTIME(' . $db->qn('r.vets_standard_time') . ', ' . $db->qn('r.time') . '), "%k:%i:%s")' .
				' END)' .
				' AS vets_standard_result'
			)
			->select('IF (' . $db->qn('r.time') . ' IN ("12:00:00", "24:00:00"), ' .
				$db->qn('r.distance') . ' - ' . $db->qn('r.predicted_distance') . ',' .
				' CASE TIME_FORMAT(SUBTIME(' . $db->qn('r.predicted_time') . ', ' . $db->qn('r.time') . '), "%k")' .
				' WHEN 0 THEN IF(TIME_FORMAT(SUBTIME(' . $db->qn('r.predicted_time') . ', ' . $db->qn('r.time') . '), "%i") = 0, TRIM(TRAILING "00000" FROM(TIME_FORMAT(SUBTIME(' . $db->qn('r.predicted_time') . ', ' . $db->qn('r.time') . '), IF(' . $db->qn('e.hill_climb_ind') . ' = TRUE, "%s.%f", "%s")))), TRIM(TRAILING "00000" FROM(TRIM(LEADING "0" FROM TIME_FORMAT(SUBTIME(' . $db->qn('r.predicted_time') . ', ' . $db->qn('r.time') . '), IF(' . $db->qn('e.hill_climb_ind') . ' = TRUE, "%i:%s.%f", "%i:%s"))))))' .
				' ELSE TIME_FORMAT(SUBTIME(' . $db->qn('r.predicted_time') . ', ' . $db->qn('r.time') . '), "%k:%i:%s")' .
				' END)' .
				' AS handicap_result'
			)

		// Rider details
			->select($db->qn(array('rr.blacklist_ind')))
			->select('CONCAT(' . $db->qn('rr.first_name') . ', " ", ' . $db->qn('rr.last_name') . ')' .
				' AS name'
			)

		// Event details
			->select($db->qn(array('e.event_date', 'e.event_name')))
			->select('CASE ' . $db->qn('e.distance') . ' WHEN 0 THEN "Other"' .
				' ELSE ' . $db->qn('e.distance') .
				' END' .
				' AS distance'
			)

		// Team result details
			->select($db->qn('team_result'));

		return $query;
	}

	/**
	 * getQueryFrom
	 *
	 * Builds the query from to be used by the awards model
	 *
	 * @param 	object 	$db 		Database object
	 * @param 	object 	$query 		Query object
	 * @param 	boolean $joinTeam 	Set to false if team subquery is not to be joined
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getQueryFrom($db, $query, $joinTeam = true)
	{
		$query
			->from($db->qn('#__awards', 'a'))

		// Join over the award type
			->join('INNER', $db->qn('#__award_types', 'at') .
				' ON (' . $db->qn('a.award_type_id') . ' = ' . $db->qn('at.award_type_id') . ')'
			)

		// Join over the ride
			->join('LEFT', $db->qn('#__rides', 'r') .
				' ON (' . $db->qn('a.event_id') . ' = ' . $db->qn('r.event_id') .
				' AND ' . $db->qn('a.rider_id') . ' = ' . $db->qn('r.rider_id') . ')'
			)

		// Join over the rider
			->join('LEFT', $db->qn('#__riders', 'rr') .
				' ON (' . $db->qn('a.rider_id') . ' = ' . $db->qn('rr.rider_id') . ')'
			)

		// Join over the event
			->join('LEFT', $db->qn('#__events', 'e') .
				' ON (' . $db->qn('a.event_id') . ' = ' . $db->qn('e.event_id') . ')'
			);

		// Join over the team results
		if ($joinTeam)
		{
			$query
				->join('LEFT', '(' . $this->getSubqueryTeamResults() . ') AS tr' .
					' ON (' . $db->qn('a.event_id') . ' = ' . $db->qn('tr.event_id') .
					' AND ' . $db->qn('a.award_type_id') . ' = ' . $db->qn('tr.award_type_id') .
					' AND ' . $db->qn('a.position') . ' = ' . $db->qn('tr.position') . ')'
				);
		}

		return $query;
	}

	/**
	 * getQueryFilters
	 *
	 * Builds the query filters to be used by the awards model
	 *
	 * @param 	object 	$db 	Database object
	 * @param 	object 	$query 	Query object
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getQueryFilters($db, $query)
	{
		switch ($this->listType)
		{
			case "event":
				$query
					->where($db->qn('a.event_id') . ' = ' . (int) $this->eventId);
				break;

			case "rider":
				$query
					->where($db->qn('a.rider_id') . ' = ' . (int) $this->riderId);
				break;
		}

		return $query;
	}

	/**
	 * getQueryOrder
	 *
	 * Builds the query ordering to be used by the awards model
	 *
	 * @param 	object 	$db 	Database object
	 * @param 	object 	$query 	Query object
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getQueryOrder($db, $query)
	{
		switch ($this->listType)
		{
			case "event":
				$query
					->order($db->qn('at.order') . ' ASC')
					->order($db->qn('a.position') . ' ASC')
					->order($db->qn('team_counter') . ' ASC');
				break;

			case "rider":
				$query
					->order($db->qn('e.event_date') . ' DESC')
					->order($db->qn('e.event_id') . ' ASC')
					->order($db->qn('at.order') . ' ASC');
				break;
		}

		return $query;
	}

	/**
	 * getSubqueryTeamResults
	 *
	 * Builds the subquery used to retrieve the team results for the teams within an event
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getSubqueryTeamResults()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		$query
			->select($db->qn(array('a.event_id', 'a.award_type_id', 'a.position')))
			->select('CASE ' . $db->qn('e.duration_event_ind') .
				' WHEN 1 THEN IF (' . $db->qn('at.award_basis') . ' = "Aggregate", SUM(' . $db->qn('r.distance') . '), MIN(' . $db->qn('r.distance') . '))' .
				' ELSE IF (' . $db->qn('at.award_basis') . ' = "Aggregate", ' .
					' CASE TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(' . $db->qn('r.time') . '))), "%k")' .
					' WHEN 0 THEN TRIM(TRAILING "00000" FROM(TRIM(LEADING "0" FROM TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(' . $db->qn('r.time') . ')) + SUM(MICROSECOND(' . $db->qn('r.time') . '))/1000000), IF(' . $db->qn('e.hill_climb_ind') . ' = TRUE, "%i:%s.%f", "%i:%s")))))' .
					' ELSE TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(' . $db->qn('r.time') . '))), "%k:%i:%s")' .
					' END, ' .
					' CASE TIME_FORMAT(MAX(' . $db->qn('r.time') . '), "%k")' .
					' WHEN 0 THEN TRIM(TRAILING "00000" FROM(TRIM(LEADING "0" FROM TIME_FORMAT(MAX(' . $db->qn('r.time') . '), IF(' . $db->qn('e.hill_climb_ind') . ' = TRUE, "%i:%s.%f", "%i:%s")))))' .
					' ELSE TIME_FORMAT(MAX(' . $db->qn('r.time') . '), "%k:%i:%s")' .
					' END)' .
				' END' .
				' AS team_result'
			);

		$query = $this->getQueryFrom($db, $query, false);

		// Group data
		$query
			->group($db->qn('a.event_id'))
			->group($db->qn('a.award_type_id'))
			->group($db->qn('a.position'));

		return $query;
	}
}
