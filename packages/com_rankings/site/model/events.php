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
 * Rankings Component Events Model
 *
 * @since 2.0
 */
class RankingsModelEvents extends RankingsModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JModelList
	 * @since   2.0
	 */
	public function __construct($config = array())
	{
		// Specify filter fields for model
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'event_name',
				'district_code',
				'course_code',
				'distance',
				'year'
			);
		}

		parent::__construct($config);
	}

	/**
	 * getItems
	 *
	 * Gets a list of events.
	 *
	 * @return  mixed  An array of events on success, false on failure.
	 *
	 * @since 2.0
	 */
	public function getItems()
	{
		// If the list of events has been filtered by course code then a check has to be made that there is at least one course in the list being returned. If there are no events then the filter is removed.
		If ($this->getState('filter.course_code') != 'All')
		{
			If ($this->getTotal() == 0)
			{
				$app = JFactory::getApplication();
				$app->setUserState($this->context . '.filter.course_code', 'All');
			}
		}

		return parent::getItems();
	}

	/**
	 * getQuerySelect
	 *
	 * Builds the query select to be used by the events model
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
		// Select required fields from events
		$query
			->select($this->getState('list.select', 'e.*'));

		return $query;
	}

	/**
	 * getQueryFrom
	 *
	 * Builds the query from to be used by the events model
	 *
	 * @param 	object 	$db 		Database object
	 * @param 	object 	$query 		Query object
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getQueryFrom($db, $query)
	{
		// Select required fields from events
		$query
			->from($db->qn('#__events', 'e'));

		return $query;
	}

	/**
	 * getQueryFilters
	 *
	 * Builds the query filters to be used by the events model
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
		// Filter by search in event name
		$search = $this->getState('filter.event_name');

		if (!empty($search))
		{
			$search = $db->q('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query
				->where($db->qn('e.event_name') . ' LIKE ' . $search);
		}

		// Filter by district code
		$districtCode = $this->getState('filter.district_code');

		if (!empty($districtCode))
		{
			if ($districtCode != 'All')
			{
				$search = $db->q(str_replace(' ', '%', $db->escape(trim($districtCode), true) . '%'));
				$query
					->where($db->qn('e.course_code') . ' LIKE ' . $search);
			}
		}

		// Filter by course code
		$courseCode = $this->getState('filter.course_code');

		if (!empty($courseCode))
		{
			if ($courseCode != 'All')
			{
				$find 	 = array(' ');
				$replace = array('%');
				$search  = $db->q(str_replace($find, $replace, $db->escape(trim($courseCode), true)));
				$query
					->where($db->qn('e.course_code') . ' LIKE ' . $search);
			}
		}

		// Filter by distance
		$distance = $this->getState('filter.distance');

		if (!empty($distance))
		{
			switch ($distance)
			{
				case 'Hill Climb':
					$query
						->where($db->qn('e.hill_climb_ind') . ' = TRUE');
					break;

				case 'Other':
					$query
						->where($db->qn('e.hill_climb_ind') . ' = FALSE')
						->where($db->qn('e.distance') . ' NOT IN(10, 25, 50, 100)')
						->where($db->qn('e.duration_event_ind') . ' = FALSE');
					break;

				case '12':
				case '24':
					// Duration events
					$search = $db->q(str_replace(' ', '%', $db->escape(trim($distance), true)));
					$query
						->where($db->qn('e.distance') . ' = ' . $search)
						->where($db->qn('e.duration_event_ind') . ' = TRUE');
					break;

				case '10':
				case '25':
				case '50':
				case '100':
					// Standard distance events
					$search = $db->q(str_replace(' ', '%', $db->escape(trim($distance), true)));
					$query
						->where($db->qn('e.distance') . ' = ' . $search)
						->where($db->qn('e.hill_climb_ind') . ' = FALSE');
					break;

				default:
					break;
			}
		}

		// Filter by year
		$year = $this->getState('filter.year');

		if (!empty($year))
		{
			if ($year != 'All')
			{
				$year = $db->q(str_replace(' ', '%',   $db->escape(trim($year), true)));
				$query
					->where('YEAR(' . $db->qn('e.event_date') . ') = ' . $year);
			}
		}

		return $query;
	}

	/**
	 * getQueryOrder
	 *
	 * Builds the query ordering to be used by the events model
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
		// Add the list ordering clause.
		$query
			->order($db->qn('e.event_date') . ' DESC')
			->order($db->qn('e.distance') . ' DESC')
			->order($db->qn('e.event_name') . ' ASC');

		return $query;
	}
}
