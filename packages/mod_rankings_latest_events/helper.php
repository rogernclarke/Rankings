<?php
/**
 * Rankings Latest Events Module for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Rankings Component Latest Events Module Helper
 *
 * @since   2.0
 */
class ModRankingsLatestEventsHelper
{
	/**
	 * Retrieves the latest events
	 *
	 * @param   array  	$params An object containing the module parameters
	 *
	 * @return 	object 	List of events
	 */
	public static function getEvents($params)
	{
		// Obtain a database connection
		$app                = JFactory::getApplication();
		$componentParams 	= $app->getParams('com_rankings');
		$db                 = JDatabaseDriver::getInstance($componentParams);

		// Store the parameters
		$eventStatus   	= $params['event_status'];
		$itemCount     	= $params['item_count'];
		$itemRowCount 	= $params['item_row_count'];

		// Set the query limits
		$limits = self::setQueryLimits($db, $eventStatus, $itemCount, $itemRowCount);

		// Get the latest updated events
		// Query 1 gets the events updated in the last week
		$query1 = $db->getQuery(true);
		$query1 = self::buildQuery($db, $query1, true);
		$query1 = self::buildWhere($db, $query1, $eventStatus, 'recent');

		// Query 2 gets future events with startsheets (for startsheet requests only)
		if ($eventStatus == 'Startsheets')
		{
			$query2 = $db->getQuery(true);
			$query2 = self::buildQuery($db, $query2);
			$query2 = self::buildWhere($db, $query2, $eventStatus, 'future');
		}

		// Query 3 gets the most recent additional events to fill the last slider item
		$limit  = $limits['event_total_limit'] - $limits['last_week_count'];

		$query3 = $db->getQuery(true);
		$query3 = self::buildQuery($db, $query3);
		$query3 = self::buildWhere($db, $query3, $eventStatus, 'past');
		$query3
			->order($db->qn('event_date') . ' DESC,' . $db->qn('distance') . ' DESC')
			->setLimit($limit);

		// Prepare the query
		$query = $db->getQuery(true);

		$query = self::buildQuery($db, $query, false);
		$query
			->where('1 = 0')
			->union($query1);

		if ($eventStatus == 'Startsheets')
		{
			$query
				->union($query2);
		}

		$query
			->union($query3)
			->order($db->qn('event_date') . ' DESC,' . $db->qn('distance') . ' DESC');

		// Execute query - load the events
		$events = $db->setQuery($query)->loadObjectList();

		// Prepare the data.
		// Compute the event link url.
		foreach ($events as $event)
		{
			$event->link = JRoute::_(RankingsHelperRoute::getEventRoute($event->event_id));
		}

		// Return the set of events
		return $events;
	}

	/**
	 * Determines the number of events to fetch
	 *
	 * @param   object  $db 			The database connection
	 * @param   string  $eventStatus 	Either Startsheets or Results
	 * @param   integer $itemCount 		The number of slider items
	 * @param   integer $itemRowCount 	The number of rows to display per item
	 *
	 * @return 	array 	Set of limits
	 */
	protected static function setQueryLimits($db, $eventStatus, $itemCount, $itemRowCount)
	{
		// Prepare the query to determine the number of events updated in the last week
		$query = $db->getQuery(true);

		$query
			->select('COUNT(' . $db->qn('event_id') . ')')
			->from($db->qn('#__events'));

		$query = self::buildWhere($db, $query, $eventStatus, 'recent');

		// Execute the query
		$lastWeekCount    = $db->setQuery($query)->loadResult();

		// Ensure the total number of events fills the minimum number of tabs requested
		$eventTotalLimit  = max($lastWeekCount, $itemCount * $itemRowCount);

		// Ensure the total number of events doesn't exceed 10 tabs
		$eventTotalLimit = min($eventTotalLimit, 10 * $itemRowCount);

		// Round up the limit to fill the last tab
		$eventTotalLimit = ceil($eventTotalLimit / $itemRowCount) * $itemRowCount;

		$limits['last_week_count']      = $lastWeekCount;
		$limits['event_total_limit']    = $eventTotalLimit;

		return $limits;
	}

	/**
	 * buildQuery
	 *
	 * Builds the query to be used
	 *
	 * @param   object  $db 		Database connection object
	 * @param   object  $query 		Query object
	 * @param   boolean $new 		TRUE if event is newly updated, FALSE if not
	 *
	 * @return  object  Query object
	 */
	protected static function buildQuery($db, $query, bool $new = false)
	{
		$query
			->select($db->qn(array('event_id', 'event_date', 'event_name', 'course_code', 'distance', 'duration_event_ind', 'hill_climb_ind')))
			->select($new ? 1 : 0 . ' AS new_ind')
			->from($db->qn('#__events'));

		return $query;
	}

	/**
	 * _buildWhere
	 *
	 * Builds the filter for the query
	 *
	 * @param   object  $db 			Database connection object
	 * @param   object  $query 			Query object
	 * @param   string  $eventStatus 	Either Startsheets, Results, or All
	 * @param   string  $timeframe 		Either recent, future, or past
	 *
	 * @return  object  Query object
	 */
	protected static function buildWhere($db, $query, string $eventStatus, string $timeframe)
	{
		switch ($eventStatus)
		{
			case 'Startsheets':
				$query
					->where($db->qn('startsheet_ind') . ' = TRUE');
				break;
			case 'Results':
				$query
					->where($db->qn('results_ind') . ' = TRUE');
				break;
			case 'All':
			default:
				break;
		}

		switch ($timeframe)
		{
			case 'recent':
				// Events updated within The last 7 days
				$query
					->where($db->qn('processed_date') . '> DATE_ADD(CURDATE(), INTERVAL -7 DAY)');

				if ($eventStatus == 'Startsheets')
				{
					// Exclude events which already have results published
					$query
						->where($db->qn('results_ind') . ' = FALSE');
				}
				break;
			case 'future':
				// Events taking place in the future - only applies to startsheets
				$query
					->where($db->qn('event_date') . '>= CURDATE()');
				break;
			case 'past':
			default:
				if ($eventStatus == 'Startsheets')
				{
					// For startsheets remaining events are filled with the most recent past events which have results
					$query
						->where($db->qn('event_date') . '< CURDATE()')
						->where($db->qn('results_ind') . ' = TRUE');
				}
				else
				{
					// For results remaining events are filled with the most recent events with a result sheet which have not been updated in the last 7 days
					$query
						->where($db->qn('processed_date') . '<= DATE_ADD(CURDATE(), INTERVAL -7 DAY)');
				}
				break;
		}

		return $query;
	}
}
