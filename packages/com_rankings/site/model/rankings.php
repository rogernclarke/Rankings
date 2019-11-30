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
 * Rankings Component Rankings Model
 *
 * @since 2.0
 */
class RankingsModelRankings extends RankingsModelList
{
	/**
	 * History - set to true if a historical ranking requested
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $history = false;

	/**
	 * Column name prefix
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $prefix = null;

	/**
	 * Ranking Type
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $rankingType = null;

	/**
	 * Year - used for hill climb rankings
	 *
	 * @var    integer
	 * @since  2.0
	 */
	protected $year = null;

	/**
	 * Constructor.
	 *
	 * @param   array  	$config  An optional associative array of configuration settings.
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
				'year',
				'gender',
				'name',
				'club_name',
				'district_code',
				'age_category'
			);
		}

		// Specify filter fields for model
		if (empty($config['check_fields']))
		{
			$config['check_fields'] = array(
				'status'
			);
		}

		parent::__construct($config);

		// Get the ranking type
		$jinput = JFactory::getApplication()->input;
		$array = $jinput->get('type', array(), 'ARRAY');

		// Store the year from the request, set to year of last run date if not set
		$this->year 		= (int) $this->getState('filter.year');
		$lastRunDateYear 	= date("Y", strtotime($this->getLastRunDate()));

		if (empty($this->year))
		{
			$this->year = $lastRunDateYear;
		}

		if ($this->year != $lastRunDateYear)
		{
			$this->history = true;
		}

		// If a ranking type of hc has been specified then set the prefix (for current rankings only)
		if (!empty($array))
		{
			$this->rankingType = $array[0];

			if ($this->rankingType == 'hc' && $this->history == false)
			{
				$this->prefix = 'hc_';
			}
		}
	}

	/**
	 * get Form
	 *
	 * Method to get the form.
	 *
	 * @return  form object
	 *
	 * @since 2.0
	 */
	public function getForm()
	{
		return parent::getForm($this->rankingType);
	}

	/**
	 * getItems
	 *
	 * Gets a list of rankings.
	 *
	 * @return 	model 	The requested rankings
	 *
	 * @since 2.0
	 */
	public function getItems()
	{
		$rankings = parent::getItems();

		// Set state for rides models
		$config = array();
		$state 	= new \Jobject;
		$state->set('list.limit', 0);

		$config['state'] 			= $state;
		$config['ignore_request'] 	= true;
		$config['subcontext'] 		= $this->getName() . '.' . $this->rankingType;

		// For each rider retrieve the list of rides to be shown on the rankings list
		foreach ($rankings as $rider)
		{
			$ridesModel = new RankingsModelRides($config);
			$ridesModel->set('riderId', $rider->rider_id);
			$ridesModel->set('rankingStatus', $rider->status);

			if ($this->rankingType == 'hc')
			{
				$ridesModel->set('year', $this->year);
			}

			$rider->rides = $ridesModel->getItems();
		}

		return $rankings;
	}

	/**
	 * getLastRunDateByYear
	 *
	 * Returns the last rankings calculation date for a specified year - only for hc ranking
	 *
	 * @param 	integer $year 	Year
	 *
	 * @return 	date 	Date of last ranking calculation in specified year
	 *
	 * @since 2.0
	 */
	protected function getLastRunDateByYear($year)
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		$query
			->select('MAX(' . $db->qn('effective_date') . ')')
			->from($db->qn('#__hc_rider_history'))
			->where('YEAR(' . $db->qn('effective_date') . ') = ' . $year);

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * getQuerySelect
	 *
	 * Builds the query select to be used by the rankings model
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
		$query
			->select($this->getState('list.select', '*'))
			->select('CONCAT(' . $db->qn('age_category') . ' , " ", ' . $db->qn('gender') . ')' .
				' AS age_gender_category'
			)
			->select($db->qn(array($this->prefix . 'score', $this->prefix . 'overall_rank', $this->prefix . 'gender_rank', $this->prefix . 'category'), array('score', 'overall_rank', 'gender_rank', 'category')))
			->select('CASE ' . $db->qn($this->prefix . 'ranking_status') .
				' WHEN "F" THEN "Frequent rider"' .
				' WHEN "C" THEN "Complete"' .
				' WHEN "P" THEN "Provisional"' .
				' WHEN "L" THEN "Lapsed"' .
				' ELSE ""' .
				' END' .
				' AS status'
			)
			->select('CASE SIGN(' . $db->qn($this->prefix . 'change_in_overall_rank') . ')' .
				' WHEN -1 THEN "arrow-down"' .
				' WHEN 1 THEN "arrow-up"' .
				' ELSE IF (' . $db->qn($this->prefix . 'newly_complete_ind') . ' = FALSE, "arrow-left", "circle")' .
				' END' .
				' AS change_in_overall_rank_ind'
			)
			->select('ABS(' . $db->qn($this->prefix . 'change_in_overall_rank') . ')' .
				' AS change_in_overall_rank_value'
			)
			->select('CASE SIGN(' . $db->qn($this->prefix . 'change_in_gender_rank') . ')' .
				' WHEN -1 THEN "arrow-down"' .
				' WHEN 1 THEN "arrow-up"' .
				' ELSE IF (' . $db->qn($this->prefix . 'newly_complete_ind') . ' = FALSE, "arrow-left", "circle")' .
				' END' .
				' AS change_in_gender_rank_ind'
			)
			->select('ABS(' . $db->qn($this->prefix . 'change_in_gender_rank') . ')' .
				' AS change_in_gender_rank_value'
			);

		// Calculate position in list only if certain filters are applied
		if ($this->getState('filter.age_category') !== 'All' || $this->getState('filter.district_code') !== 'All' || $this->getState('filter.name') || $this->getState('filter.club_name'))
		{
			$query
				->select('CASE' .
					' WHEN @prev_value = ' . $db->qn($this->prefix . 'overall_rank') . ' THEN @position_count' .
					' WHEN @prev_value := ' . $db->qn($this->prefix . 'overall_rank') . ' THEN @position_count := @sequence' .
					' END' .
					' AS position, @sequence:=@sequence + 1'
				);
		}

		if ($this->history)
		{
			$query
				->select('CONCAT(' . $db->qn('first_name') . ' , " ", ' . $db->qn('last_name') .
					') AS name'
				);
		}

		return $query;
	}

	/**
	 * getQueryFrom
	 *
	 * Builds the query from to be used by the rankings model
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
		// Calculate position in list only if certain filters are applied
		if ($this->getState('filter.age_category') !== 'All' || $this->getState('filter.district_code') !== 'All' || $this->getState('filter.name') || $this->getState('filter.club_name'))
		{
			$query
				->from('(' . $this->getSubqueryRankings() . ') AS rkg')
				->from('(' . $this->getSubqueryPosition() . ') AS pos');
		}
		else
		{
			if ($rankingType == 'tt')
			{
				$query
					->from($db->qn('#__rider_current') . 'AS rkg');
			}
			else
			{
				if ($this->history)
				{
					$this->year = $db->q(str_replace(' ', '%',   $db->escape(trim($this->year), true)));
					$query
						->from($db->qn('#__hc_rider_history') . 'AS rkg')

					// Join over the rider
						->join('LEFT', $db->qn('#__riders', 'rr') .
							' ON (' . $db->qn('rkg.rider_id') . ' = ' . $db->qn('rr.rider_id') . ')'
						);
				}
				else
				{
					$query
						->from($db->qn('#__rider_current') . 'AS rkg');
				}
			}
		}

		return $query;
	}

	/**
	 * getQueryFilters
	 *
	 * Builds the query filters to be used by the rankings model
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
		// Filter by gender
		$gender = $this->getState('filter.gender');

		if (!empty($gender))
		{
			if ($gender != 'All')
			{
				$gender = $db->q(str_replace(' ', '%', $db->escape(trim($gender), true)));
				$query
					->where($db->qn('gender') . ' = ' . $gender);
			}
		}

		// Filter by age category
		$ageCategory = $this->getState('filter.age_category');

		if (!empty($ageCategory))
		{
			if ($ageCategory != 'All')
			{
				$ageCategory = $db->q(str_replace(' ', '%', $db->escape(trim($ageCategory), true)));
				$query
					->where($db->qn('age_category') . ' = ' . $ageCategory);
			}
		}

		// Filter by district
		$districtCode = $this->getState('filter.district_code');

		if (!empty($districtCode))
		{
			if ($districtCode != 'All')
			{
				$districtCode = $db->q(str_replace(' ', '%', $db->escape(trim($districtCode), true)));
				$query
					->where($db->qn('district_code') . ' = ' . $districtCode);
			}
		}

		// Filter by search in name
		$search = $this->getState('filter.name');

		if (!empty($search))
		{
			$search = $db->q('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query
				->where($db->qn('name') . ' LIKE ' . $search);
		}

		// Filter by search in club name
		$search = $this->getState('filter.club_name');

		if (!empty($search))
		{
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query
				->where($db->qn('club_name') . ' LIKE ' . $search);
		}

		// Filter by ranking status
		$search = $this->getState('check.status');

		if ($search == 1)
		{
			$query
				->where($db->qn($this->prefix . 'ranking_status') . ' IN ("C", "F", "P")');
		}
		else
		{
			$query
				->where($db->qn($this->prefix . 'ranking_status') . ' IN ("C", "F")');
		}

		// Don't retrieve blacklisted riders
		$query
			->where($db->qn('blacklist_ind') . ' = 0');

		// For historical ranking specify date of ranking
		if ($this->history)
		{
			$query
				->where($db->qn('effective_date') . ' = "' . $this->getLastRunDateByYear($this->year) . '"');
		}

		return $query;
	}

	/**
	 * getQueryOrder
	 *
	 * Builds the query ordering to be used by the rankings model
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
		$query
			->order($db->qn($this->prefix . 'overall_rank') . ' ASC, ' .
				$db->qn($this->prefix . 'score') . ' ASC, ' .
				$db->qn($this->prefix . 'ranking_status') . ' ASC'
			);

		return $query;
	}

	/**
	 * getSubqueryRankings
	 *
	 * Method to get a JDatabaseQuery object for retrieving the rankings subquery.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the dataset.
	 *
	 * @since   2.0
	 */
	protected function getSubqueryRankings()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		// Determine if provisionally ranked riders are to be included
		if ($this->getState('check.status') == 1)
		{
			$statuses = array("F", "C", "P");
		}
		else
		{
			$statuses = array("F", "C");
		}

		if ($rankingType == 'tt')
		{
			$query
				->select('*')
				->from($db->qn('#__rider_current'));
		}
		else
		{
			if ($this->history)
			{
				$this->year = $db->q(str_replace(' ', '%',   $db->escape(trim($this->year), true)));
				$query
					->select('rkg.*')
					->select($db->qn(array('first_name', 'last_name', 'blacklist_ind', 'district_code', 'age_category', 'gender', 'club_name')))
					->select('CONCAT(' . $db->qn('first_name') . ' , " ", ' . $db->qn('last_name') .
						') AS name'
					)
					->from($db->qn('#__hc_rider_history') . 'AS rkg')

				// Join over the rider
					->join('LEFT', $db->qn('#__riders', 'rr') .
						' ON (' . $db->qn('rkg.rider_id') . ' = ' . $db->qn('rr.rider_id') . ')'
					)
					->where($db->qn('effective_date') . ' = "' . $this->getLastRunDateByYear($this->year) . '"');
			}
			else
			{
				$query
					->select('*')
					->from($db->qn('#__rider_current'));
			}
		}

		$query
			->where($db->qn($this->prefix . 'ranking_status') . ' IN ("' . implode('","', $statuses) . '")')
			->order($db->qn($this->prefix . 'overall_rank'));


		return $query;
	}

	/**
	 * getSubqueryPosition
	 *
	 * Method to get a JDatabaseQuery object for retrieving the position subquery.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the dataset.
	 *
	 * @since   2.0
	 */
	protected function getSubqueryPosition()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		// First position is set from number of items per page + 1
		$offset = $this->getState('list.start') + 1;

		$query
			->select('@prev_value:=NULL, @position_count:=' . $offset . ' , @sequence:=' . $offset);

		return $query;
	}
}
