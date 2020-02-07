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
 * Rankings Component Riderhistories Model
 *
 * @since   2.0
 */
class RankingsModelRiderhistories extends RankingsModelList
{
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

		// Store the year from the request, set to year of last run date if not set
		//$this->year 		= (int) $this->getState('filter.year');
		//$lastRunDateYear 	= date("Y", strtotime($this->getLastRunDate()));
		
		//if (empty($this->year))
		//{
			$this->year = $lastRunDateYear;
		//}
	}

	/**
	 * getQuerySelect
	 *
	 * Builds the query select to be used by the riderhistories model
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
		// Get all riderhistories
		$this->state->set('list.limit', 0);

		// Select required fields from rider_history
		$query
			->select($this->getState('list.select', '*'))
			->select(
				'CASE ' . $db->qn('ranking_status') .
				' WHEN "F" THEN "Frequent"' .
				' WHEN "C" THEN "Qualified"' .
				' WHEN "P" THEN "Provisional"' .
				' WHEN "L" THEN "Lapsed"' .
				' ELSE ""' .
				' END' .
				' AS status'
			);

		return $query;
	}

	/**
	 * getQueryFrom
	 *
	 * Builds the query from to be used by the riderhistories model
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
		$query
			->from($db->qn('#__' . $this->prefix . 'rider_history'));

		return $query;
	}

	/**
	 * getQueryFilters
	 *
	 * Builds the query filters to be used by the riderhistories model
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
		$query
			->where($db->qn('rider_id') . ' = ' . $this->riderId);

		if (!empty($this->year))
		{
			$query
				->where('YEAR(' . $db->qn('effective_date') . ') = ' . $this->year);
		}

		return $query;
	}

	/**
	 * getQueryOrder
	 *
	 * Builds the query ordering to be used by the riderhistories model
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
			->order($db->qn('effective_date') . 'ASC');

		return $query;
	}
}
