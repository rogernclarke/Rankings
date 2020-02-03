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
 * Rankings Component Rides Model
 *
 * @since 2.0
 */
class RankingsModelRides extends RankingsModelList
{
	/**
	 * Event ID
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $eventId = null;

	/**
	 * List Context - model context less component name
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $listContext = null;

	/**
	 * Column name prefix
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $prefix = null;

	/**
	 * Ranking status (for the rider)
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $rankingStatus = null;

	/**
	 * Rider ID
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $riderId = null;

	/**
	 * Year
	 *
	 * @var    integer
	 * @since  2.0
	 */
	protected $year = null;

	/**
	 * Constructor.
	 *
	 * @param   array  	$config  	An optional associative array of configuration settings.
	 *
	 * @see     \JModelList
	 * @since   2.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Set list type
		$this->listContext = substr($this->context, strpos($this->context, ".") + 1);

		// Set prefix if subcontext is rider or rankings
		if (strpos($this->listContext, 'rider') === 0 || strpos($this->listContext, 'rankings') === 0)
		{
			if (substr($this->listContext, strpos($this->listContext, ".") + 1, 2) == 'hc')
			{
				$this->prefix = 'hc_';
			}
		}

		// Store the year from the request, set to year of last run date if not set
		$this->year 		= (int) $this->getState('filter.year');
		$lastRunDateYear 	= date("Y", strtotime($this->getLastRunDate()));

		if (empty($this->year))
		{
			$this->year = $lastRunDateYear;
		}
	}

	/**
	 * getItems
	 *
	 * Gets a list of rides.
	 *
	 * @return 	model 	The requested rankings
	 *
	 * @since 2.0
	 */
	public function getItems()
	{
		$rides = parent::getItems();

		foreach ($rides as $ride)
		{
			switch ($this->listContext)
			{
				case "event.entries":
					// Set the ordinal for predicted position
					if (!empty($ride->predicted_position))
					{
						$ride->predicted_position = $this->setOrdinal($ride->predicted_position);
					}

					if (strpos($ride->predicted_time, "0") === 0)
					{
						$ride->predicted_time = substr($ride->predicted_time, 1);
					}
					break;

				case "event.results":
					// Set the ordinal for vets position
					if (isset($ride->vets_position))
					{
						$ride->vets_position = $this->setOrdinal($ride->vets_position);
					}
					if (strpos($ride->predicted_time, "0") === 0)
					{
						$ride->predicted_time = substr($ride->predicted_time, 1);
					}

				case "rider.ttrides":
				case "rider.hcrides":
					// Set the ordinal for position
					$ride->position = $this->setOrdinal($ride->position);
					$ride->gender_position = $this->setOrdinal($ride->gender_position);

					if (strpos($ride->time, "0") === 0)
					{
						$ride->time = substr($ride->time, 1);
					}
					break;
			}
		}

		return $rides;
	}

	/**
	 * getQuerySelect
	 *
	 * Builds the query select to be used by the rides model
	 *
	 * @param 	object 	$db 	Database object
	 * @param 	object 	$query 	Query object
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getQuerySelect($db, $query)
	{
		// Data elements specific to rides list types
		switch ($this->listContext)
		{
			case "event.entries":
				// Get all entries for an event
				$this->setState('list.limit', 0);

				// Data elements from rides table
				$query
					->select($db->qn(array('r.rider_id', 'r.club_name', 'r.category_on_day', 'r.predicted_position')))
					->select('IF(' . $db->qn('bib') . ' = 0, "Res", ' . $db->qn('bib') . ')' .
						' AS bib'
					)
					->select('TIME_FORMAT(' . $db->qn('r.start_time') . ', "%H:%i")' .
						' AS start_time'
					)
					->select('CASE TIME_FORMAT(' . $db->qn('r.predicted_time') . ', "%k")' .
						' WHEN 0 THEN TIME_FORMAT(' . $db->qn('r.predicted_time') . ', "%i:%s")' .
						' ELSE TIME_FORMAT(' . $db->qn('r.predicted_time') . ', "%k:%i:%s")' .
						' END' .
						' AS predicted_time'
					)
					->select('TIME_FORMAT(ADDTIME(' . $db->qn('r.start_time') . ', ' . $db->qn('r.predicted_time') . '), "%H:%i:%s")' .
						' AS predicted_time_at_finish'
					)
					->select('ROUND(' . $db->qn('r.predicted_distance') . ',2)' .
						'AS predicted_distance'
					)
					->select('CASE ' .
						' WHEN ' . $db->qn('r.pre_ride_form') . ' >= 27 THEN 2' .
						' WHEN ' . $db->qn('r.pre_ride_form') . ' >= 17 THEN 1' .
						' ELSE 0' .
						' END' .
						' AS form'
					)

				// Rider details
					->select($db->qn(array('rr.blacklist_ind', 'rr.gender')))
					->select('CONCAT(' . $db->qn('rr.first_name') . ', " ", ' . $db->qn('rr.last_name') . ')' .
						' AS name'
					)
					->select('CONCAT(' . $db->qn('r.age_category_on_day') . ', " ", ' . $db->qn('rr.gender') . ')' .
						' AS age_gender_category'
					);
				break;

			case "event.results":
				// Get all results for an event
				$this->setState('list.limit', 0);

				// Data elements from rides table
				$query
					->select($db->qn(array('er.rider_id', 'club_name', 'category_on_day', 'position', 'ranking_points')))
					->select('CASE ' .
						' WHEN ' . $db->qn('post_ride_form') . ' >= 27 THEN 2' .
						' WHEN ' . $db->qn('post_ride_form') . ' >= 17 THEN 1' .
						' ELSE 0' .
						' END' .
						' AS form'
					)
					->select('CASE TIME_FORMAT(' . $db->qn('predicted_time') . ', "%k")' .
						' WHEN 0 THEN TIME_FORMAT(' . $db->qn('predicted_time') . ', "%i:%s")' .
						' ELSE TIME_FORMAT(' . $db->qn('predicted_time') . ', "%k:%i:%s")' .
						' END' .
						' AS predicted_time'
					)
					->select('TIME_FORMAT(ADDTIME(' . $db->qn('start_time') . ', ' . $db->qn('predicted_time') . '), "%H:%i:%s")' .
						' AS predicted_time_at_finish'
					)
					->select('ROUND(' . $db->qn('predicted_distance') . ',2)' .
						'AS predicted_distance'
					)
					->select('CASE TIME_FORMAT(' . $db->qn('time') . ', "%k")' .
						' WHEN 0 THEN TRIM(TRAILING "00000" FROM(TIME_FORMAT(' . $db->qn('time') . ', IF(' . $db->qn('hill_climb_ind') . ' = TRUE, "%i:%s.%f", "%i:%s"))))' .
						' ELSE TIME_FORMAT(' . $db->qn('time') . ', "%k:%i:%s")' .
						' END' .
						' AS time'
					)
					->select('ROUND(' . $db->qn('er.distance') . ',2) AS ride_distance')

					// Position variance is only applicable for results displayed for an event
					->select('CASE SIGN (' . $db->qn('position_variance') . ')' .
						' WHEN -1 THEN "arrow-down"' .
						' WHEN 1 THEN "arrow-up"' .
						' ELSE IF (' . $db->qn('ranked_rider_ind') . ' = TRUE, "arrow-left", "circle")' .
						' END' .
						' AS position_variance_ind'
					)
					->select('ABS(' . $db->qn('position_variance') . ')' .
						' AS position_variance_value'
					)

				// Rider details
					->select($db->qn(array('blacklist_ind', 'gender')))
					->select('CONCAT(' . $db->qn('first_name') . ', " ", ' . $db->qn('last_name') . ')' .
						' AS name'
					)
					->select('CONCAT(' . $db->qn('age_category_on_day') . ', " ", ' . $db->qn('gender') . ')' .
						' AS age_gender_category'
					)

				// Gender and Vets details
					->select($db->qn(array('gender_position', 'vets_position')))
					->select('CASE TIME_FORMAT(' . $db->qn('vets_standard_time') . ', "%k")' .
						' WHEN 0 THEN TRIM(TRAILING "00000" FROM(TIME_FORMAT(' . $db->qn('vets_standard_time') . ', IF(' . $db->qn('hill_climb_ind') . ' = TRUE, "%i:%s.%f", "%i:%s"))))' .
						' ELSE TIME_FORMAT(' . $db->qn('vets_standard_time') . ', "%k:%i:%s")' .
						' END' .
						' AS vets_standard_time'
					)
					->select('IF (' . $db->qn('time') . ' IN ("12:00:00", "24:00:00"), ' .
						$db->qn('er.distance') . ' - ' . $db->qn('vets_standard_distance') . ' , ' .
						' CASE TIME_FORMAT(' . $db->qn('vets_standard_result') . ', "%k")' .
						' WHEN 0 THEN TIME_FORMAT(' . $db->qn('vets_standard_result') . ', "%i:%s")' .
						' ELSE TIME_FORMAT(' . $db->qn('vets_standard_result') . ', "%k:%i:%s")' .
						' END' .
						') AS vets_standard_result'
					);
				break;

			case "rankings.hc.rides":
			case "rankings.tt.rides":
				// Data elements from rides table
				$query
					->select($db->qn(array('rider_id', 'event_id', 'ranking_points', 'counting_ride_ind')))
					->select('ROUND(' . $db->qn('distance') . ',2) AS ride_distance')

				// Event details are applicable for rides displayed for a rider or rankings
					->select($db->qn(array('event_date','event_name', 'duration_event_ind')))
					->select('CASE TIME_FORMAT(' . $db->qn('time') . ', "%k")' .
						' WHEN 0 THEN TIME_FORMAT(' . $db->qn('time') . ', "%i:%s")' .
						' ELSE TIME_FORMAT(' . $db->qn('time') . ', "%k:%i:%s")' .
						' END' .
						' AS time'
					)
					->select('CASE ' . $db->qn('event_distance') . ' WHEN 0 THEN "Other"' .
						' ELSE ' . $db->qn('event_distance') .
						' END' .
						' AS distance'
					);
				break;

			case "rider.entries":
			case "rider.resultspending":
				// Get all entries for a rider
				$this->setState('list.limit', 0);

				// Data elements from rides table
				$query
					->select($db->qn(array('r.rider_id', 'e.event_id')))

				// Event details are applicable for entries
					->select($db->qn(array('event_date','event_name', 'duration_event_ind','hill_climb_ind')))
					->select('CASE ' . $db->qn('e.distance') .
						' WHEN 0 THEN "Other"' .
						' ELSE ' . $db->qn('e.distance') .
						' END' .
						' AS event_distance'
					);
				break;

			case "rider.ttrides":
			case "rider.hcrides":
				// Get all rides for a rider
				$this->setState('list.limit', 0);

				// Data elements from rides table
				$query
					->select($db->qn(array('r.rider_id', 'e.event_id', 'position', 'ranking_points', 'counting_ride_ind')))
					->select('ROUND(' . $db->qn('r.distance') . ',2) AS ride_distance')
					->select('CASE ' .
						' WHEN ' . $db->qn('post_ride_form') . ' >= 27 THEN 2' .
						' WHEN ' . $db->qn('post_ride_form') . ' >= 17 THEN 1' .
						' ELSE 0' .
						' END' .
						' AS form'
					)

				// Improved score is only applicable for rides displayed for a rider
					->select('CASE ' . $db->qn('r.improved_score_ind') .
						' WHEN -1 THEN "arrow-down"' .
						' WHEN 1 THEN "arrow-up"' .
						' ELSE "circle"' .
						' END' .
						' AS improved_ride'
					)
					->select('CASE TIME_FORMAT(' . $db->qn('r.time') . ', "%k")' .
						' WHEN 0 THEN TRIM(TRAILING "00000" FROM(TIME_FORMAT(' . $db->qn('r.time') . ', IF(' . $db->qn('e.hill_climb_ind') . ' = TRUE, "%i:%s.%f", "%i:%s"))))' .
						' ELSE TIME_FORMAT(' . $db->qn('r.time') . ', "%k:%i:%s")' .
						' END' .
						' AS time'
					)

				// Rider category after event is only applicable for rides displayed for a rider
					->select($db->qn('rh.category') .
						' AS category_after_day'
					)

				// Event details are applicable for rides displayed for a rider or rankings
					->select($db->qn(array('event_date','event_name', 'duration_event_ind')))
					->select('CASE ' . $db->qn('e.distance') .
						' WHEN 0 THEN "Other"' .
						' ELSE ' . $db->qn('e.distance') .
						' END' .
						' AS event_distance'
					);
				break;
		}

		return $query;
	}

	/**
	 * getQueryFrom
	 *
	 * Builds the query from to be used by the rides model
	 *
	 * @param 	object 	$db 	Database object
	 * @param 	object 	$query 	Query object
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getQueryFrom($db, $query)
	{
		switch ($this->listContext)
		{
			case "event.entries":
				$query
					->from($db->qn('#__rides') . ' AS r')

				// Join over the rider
					->join('LEFT', $db->qn('#__riders', 'rr') .
						' ON (' . $db->qn('r.rider_id') . ' = ' . $db->qn('rr.rider_id') . ')'
					);
				break;

			case "event.results":
				$query
					->from('(' . $this->getSubqueryEventRides() . ') AS er')

				// Join over the event
					->join('LEFT', $db->qn('#__events', 'e') .
						' ON (' . $db->qn('er.event_id') . ' = ' . $db->qn('e.event_id') . ')'
					)

				// Join over the gender position
					->join('LEFT', '(' . $this->getSubqueryGenderPosition() . ') AS gp' .
						' ON (' . $db->qn('er.rider_id') . ' = ' . $db->qn('gp.rider_id') . ')'
					)

				// Join over the vets position
					->join('LEFT', '(' . $this->getSubqueryVetsPosition() . ') AS vp' .
						' ON (' . $db->qn('er.rider_id') . ' = ' . $db->qn('vp.rider_id') . ')'
					);
				break;

			case "rankings.tt.rides":
			case "rankings.hc.rides":
				$query
					->from('(' . $this->getSubqueryCountingRides() . ') as cr');
				break;

			case "rider.entries":
			case "rider.resultspending":
				$query
					->from($db->qn('#__rides') . ' AS r')

				// Join over the event
					->join('LEFT', $db->qn('#__events', 'e') . ' ON (' . $db->qn('r.event_id') . ' = ' . $db->qn('e.event_id') . ')');
				break;

			case "rider.ttrides":
			case "rider.hcrides":
				$query
					->from($db->qn('#__rides') . ' AS r')

				// Join over the event
					->join('LEFT', $db->qn('#__events', 'e') . ' ON (' . $db->qn('r.event_id') . ' = ' . $db->qn('e.event_id') . ')')

				// Join over the rider history
					->join('LEFT', $db->qn('#__' . $this->prefix . 'rider_history', 'rh') . ' ON (' . $db->qn('r.rider_id') . ' = ' . $db->qn('rh.rider_id') . ')');
				break;
		}

		return($query);
	}

	/**
	 * getQueryFilters
	 *
	 * Builds the query filters to be used by the rides model
	 *
	 * @param 	object 	$db 	Database object
	 * @param 	object 	$query 	Query object
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 **/
	protected function getQueryFilters($db, $query)
	{
		switch ($this->listContext)
		{
			case "event.entries":
				$query
					->where($db->qn('r.event_id') . ' = ' . (int) $this->eventId);
				break;

			case "event.results":
				break;

			case "rider.entries":
				$query
					->where($db->qn('r.rider_id') . ' = ' . (int) $this->riderId)
					->where($db->qn('e.results_ind') . ' = FALSE')
					->where($db->qn('e.event_date') . ' >= NOW()');
				break;

			case "rider.resultspending":
				$query
					->where($db->qn('r.rider_id') . ' = ' . (int) $this->riderId)
					->where($db->qn('e.results_ind') . ' = FALSE')
					->where($db->qn('e.event_date') . ' < NOW()');

				if (!empty($this->year))
				{
					$query
						->where('YEAR(' . $db->qn('event_date') . ') = ' . $this->year);
				}
				break;

			case "rider.hcrides":
				$query
					->where($db->qn('r.rider_id') . ' = ' . (int) $this->riderId)
					->where($db->qn('r.time') . ' > "00:00:00"')
					->where($db->qn('rh.effective_date') . ' = (' . $this->getSubqueryNextHistory() . ')')
					->where($db->qn('e.hill_climb_ind') . ' = TRUE');

				if (!empty($this->year))
				{
					$query
						->where('YEAR(' . $db->qn('event_date') . ') = ' . $this->year);
				}
				break;

			case "rider.ttrides":
				$query
					->where($db->qn('r.rider_id') . ' = ' . (int) $this->riderId)
					->where($db->qn('r.time') . ' > "00:00:00"')
					->where($db->qn('rh.effective_date') . ' = (' . $this->getSubqueryNextHistory() . ')')
					->where($db->qn('e.hill_climb_ind') . ' = FALSE');

				if (!empty($this->year))
				{
					$query
						->where('YEAR(' . $db->qn('event_date') . ') = ' . $this->year);
				}
				break;

			case "rankings.tt.rides":
				if ($this->rankingStatus == "Frequent rider")
				{
					$query
						->where($db->qn('event_date') . ' >= DATE_SUB("' . $this->getLastRunDate() . '", INTERVAL 4 MONTH)');
				}
				else
				{
					$query
						->where($db->qn('event_date') . ' >= DATE_SUB("' . $this->getLastRunDate() . '", INTERVAL 1 YEAR)');
				}
				break;

			case "rankings.hc.rides":
				$query
					->where('YEAR(' . $db->qn('event_date') . ') = ' . $this->year);
				break;
		}

		return $query;
	}


	/**
	 * getQueryOrder
	 *
	 * Builds the query ordering to be used by the rides model
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
		switch ($this->listContext)
		{
			case "event.entries":
				$query
					->order('-' . $db->qn('r.predicted_position') . ' DESC');
				break;

			case "event.results":
				$query
					->order($db->qn('position') . ' ASC');
				break;

			case "rider.entries":
			case "rider.resultspending":
				$query
					->order($db->qn('e.event_date') . ' ASC');
				break;

			case "rider.ttrides":
			case "rider.hcrides":
				$query
					->order($db->qn('e.event_date') . ' DESC')
					->order($db->qn('ranking_points') . ' DESC');
				break;

			case "rankings.tt.rides":
			case "rankings.hc.rides":
				$query
					->order($db->qn('ranking_points') . ' ASC')
					->order($db->qn('event_date') . ' DESC');
				break;
		}

		return $query;
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   2.0
	 */
	protected function getStoreId($id = '')
	{
		// Add the rider or event id to the store id
		switch ($this->listContext)
		{
			case "event.entries":
			case "event.results":
				$id .= ':' . $this->eventId;
				break;

			default:
				$id .= ':' . $this->riderId;
				break;
		}

		// Add the list state to the store id.
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');
		$id .= ':' . $this->getState('list.ordering');
		$id .= ':' . $this->getState('list.direction');

		return md5($this->context . ':' . $id);
	}

	/**
	 * getSubqueryCountingRides
	 *
	 * Builds the subquery used to return rides to be displayed against a rider's ranking
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getSubqueryCountingRides()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		// Select from rides
		$query
			->select('r.*')
			->from($db->qn('#__rides') . ' AS r');

		// Join over the event
		$query
			->select($db->qn(array('e.event_name', 'e.event_date', 'e.duration_event_ind')))
			->select($db->qn('e.distance') . ' AS event_distance')
			->join('LEFT', $db->qn('#__events', 'e') . ' ON (' . $db->qn('r.event_id') . ' = ' . $db->qn('e.event_id') . ')');

		// Filter the rides
		$query
			->where($db->qn('r.rider_id') . ' = ' . (int) $this->riderId)
			->where($db->qn('r.ranking_points') . ' > 0');

		if ($this->listContext == 'rankings.hc.rides')
		{
			$query
				->where($db->qn('e.hill_climb_ind') . ' = TRUE');
		}
		else
		{
			$query
				->where($db->qn('e.hill_climb_ind') . ' = FALSE');

			if ($this->rankingStatus === "Complete")
			{
				// For riders with Complete status a maximum of 8 rides are displayed, otherwise retrieve all the rides that match the criteria
				$query
					->setlimit(8);
			}
		}

		// Order the rides
		$query
			->order($db->qn('e.event_date') . ' DESC');

		return $query;
	}

	/**
	 * getSubqueryNextHistory
	 *
	 * Builds the subquery used to determine the next rider history record after an event. Used in rides list for a rider.
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getSubqueryNextHistory()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		$query
			->select('MIN(' . $db->qn('effective_date') . ')')
			->from($db->qn('#__' . $this->prefix . 'rider_history', 'rh2'))
			->where($db->qn('rh2.rider_id') . ' = ' . $this->riderId)
			->where($db->qn('rh2.effective_date') . ' >= e.event_date');

		return $query;
	}

	/**
	 * getSubqueryEventRides
	 *
	 * Builds the subquery used to return the set of rides for which position is to be calculated
	 *
	 * @return object Subquery object
	 **/
	protected function getSubqueryEventRides()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		// Select from rides
		$query
			->select('r.*')
			->from($db->qn('#__rides') . ' AS r');

		// Join over the rider
		$query
			->select($db->qn(array('rr.blacklist_ind', 'rr.gender', 'rr.first_name', 'rr.last_name')))
			->join('LEFT', $db->qn('#__riders', 'rr') . ' ON (' . $db->qn('r.rider_id') . ' = ' . $db->qn('rr.rider_id') . ')');

		$query
			->where($db->qn('r.event_id') . ' = ' . (int) $this->eventId)
			->where($db->qn('position') . ' > 0')
			->order($db->qn('time') . ' ASC')
			->order($db->qn('distance') . ' DESC');

		return $query;
	}

	/**
	 * getSubqueryGenderPosition
	 *
	 * Builds the subquery used to retrieve the gender position for the rides within an event
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getSubqueryGenderPosition()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		// Select from riders
		$query
			->select($db->qn('rr.rider_id'))
			->select('IF (' . $db->qn('gender') . ' = "Female",' .
				' CASE' .
				' WHEN @prev_female_value = ' . $db->qn('position') . ' THEN @female_position_count' .
				' WHEN @prev_female_value:= ' . $db->qn('position') . ' THEN @female_position_count:=@female_sequence' .
				' END,' .
				' CASE' .
				' WHEN @prev_male_value = ' . $db->qn('position') . ' THEN @male_position_count' .
				' WHEN @prev_male_value:= ' . $db->qn('position') . ' THEN @male_position_count:=@male_sequence' .
				' END)' .
				' AS gender_position'
			)
			->select('IF (' . $db->qn('gender') . ' = "Female",' .
				' @female_sequence:=@female_sequence + 1,' .
				' @male_sequence:=@male_sequence + 1)' .
				' AS gender_sequence'
			)

			->from('(' . $this->getSubqueryGenderInitialPosition() . ') AS gip')
			->from($db->qn('#__riders') . ' AS rr');

		// Join over the ride
		$query
			->join('INNER', $db->qn('#__rides', 'r') . ' ON (' . $db->qn('rr.rider_id') . ' = ' . $db->qn('r.rider_id') . ')');

		// Filter the rides
		$query
			->where($db->qn('r.event_id') . ' = ' . (int) $this->eventId)
			->where($db->qn('position') . ' > 0');

		// Order the rides
		$query
			->order($db->qn('time') . ' ASC')
			->order($db->qn('r.distance') . ' DESC');

		return $query;
	}

	/**
	 * getSubqueryGenderInitialPosition
	 *
	 * Builds the subquery used to calculate the gender initial position
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getSubqueryGenderInitialPosition()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		// First position is 1
		$offset = 1;

		$query
			->select('@prev_female_value:=NULL, @prev_male_value:=NULL, @female_position_count:=' . $offset . ' , @male_position_count:=' . $offset . ' , @female_sequence:=' . $offset . ' , @male_sequence:=' . $offset);

		return $query;
	}

	/**
	 * getSubqueryVetsPosition
	 *
	 * Builds the subquery used to retrieve the vets position for the rides within an event
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getSubqueryVetsPosition()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		$query
			->select($db->qn(array('rider_id', 'vets_standard_result')))
			->select('IF (' . $db->qn('time') . ' IN ("12:00:00", "24:00:00"),' .
				' CASE' .
				' WHEN @prev_vets_value = ' . $db->qn('vets_standard_result') . ' THEN @vets_position_count' .
				' WHEN @prev_vets_value:= ' . $db->qn('vets_standard_result') . ' THEN @vets_position_count:=@vets_sequence' .
				' END,' .
				' CASE' .
				' WHEN @prev_vets_value = TIME_TO_SEC(' . $db->qn('vets_standard_result') . ') THEN @vets_position_count' .
				' WHEN 99999 + @prev_vets_value:= TIME_TO_SEC(' . $db->qn('vets_standard_result') . ') THEN @vets_position_count:=@vets_sequence' .
				' END)' .
				' AS vets_position'
			)
			->select('@vets_sequence:=@vets_sequence + 1 AS vets_sequence')
			->from('(' . $this->getSubqueryVetsData() . ') AS vd')
			->from('(' . $this->getSubqueryVetsInitialPosition() . ') AS vip');

		return $query;
	}

	/**
	 * getSubqueryVetsData
	 *
	 * Builds the subquery used to retrieve the vets data for the rides within an event
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getSubqueryVetsData()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		$query
			->select($db->qn(array('r.rider_id', 'r.time')))
			->select('IF (' . $db->qn('time') . ' IN ("12:00:00", "24:00:00"), ' .
				$db->qn('r.distance') . ' - ' . $db->qn('vets_standard_distance') . ',' .
				' SUBTIME(' . $db->qn('vets_standard_time') . ', ' . $db->qn('time') . '))' .
				' AS vets_standard_result'
			)
			->from($db->qn('#__rides', 'r'));

		// Join over the event
		$query
			->join('INNER', $db->qn('#__events', 'e') . ' ON (' . $db->qn('e.event_id') . ' = ' . $db->qn('r.event_id') . ')');

		// Filter the rides
		$query
			->where($db->qn('r.event_id') . ' = ' . (int) $this->eventId)
			->where($db->qn('position') . ' > 0')

			->order('CASE' .
				' WHEN ' . $db->qn('e.duration_event_ind') . ' THEN ' . $db->qn('vets_standard_result') . ' + 0' .
				' ELSE TIME_TO_SEC(' . $db->qn('vets_standard_result') . ')' .
				' END' .
				' DESC'
			);

		return $query;
	}

	/**
	 * getSubqueryVetsInitialPosition
	 *
	 * Builds the subquery used to initialise the vets position
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getSubqueryVetsInitialPosition()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		// First position is 1
		$offset = 1;

		$query
			->select('@prev_vets_value:=NULL, @vets_position_count:=' . $offset . ', @vets_sequence:=' . $offset);

		return $query;
	}
}
