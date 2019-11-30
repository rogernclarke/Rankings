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
 * Rankings Component Riders Model
 *
 * @since 2.0
 */
class RankingsModelRiders extends RankingsModelList
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
				'name',
				'club_name',
				'district_code'
			);
		}

		parent::__construct($config);
	}

	/**
	 * getQuerySelect
	 *
	 * Builds the query select to be used by the riders model
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
		// Select required fields from rider_current
		$query
			->select($this->getState('list.select', 'rc.*'))
			->select('CONCAT(' . $db->qn('age_category') . ' , " ", ' . $db->qn('gender') . ')' .
				' AS age_gender_category'
			)
			->select(
				'CASE ' . $db->qn('rc.gender') .
				' WHEN "Male" THEN "mars"' .
				' WHEN "Female" THEN "venus"' .
				' ELSE ""' .
				' END' .
				' AS gender_icon'
			)
			->select(
				'CASE ' . $db->qn('rc.ranking_status') .
				' WHEN "F" THEN "Frequent rider"' .
				' WHEN "C" THEN "Qualified"' .
				' WHEN "P" THEN "Provisional"' .
				' WHEN "L" THEN "Lapsed"' .
				' ELSE ""' .
				' END' .
				' AS status'
			)
			->select(
				'CASE ' . $db->qn('rc.hc_ranking_status') .
				' WHEN "F" THEN "Frequent rider"' .
				' WHEN "C" THEN "Qualified"' .
				' WHEN "P" THEN "Provisional"' .
				' WHEN "L" THEN "Lapsed"' .
				' ELSE ""' .
				' END' .
				' AS hc_status'
			);

		return $query;
	}

	/**
	 * getQueryFrom
	 *
	 * Builds the query from to be used by the riders model
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
			->from($db->qn('#__rider_current', 'rc'));

		return $query;
	}

	/**
	 * getQueryFilters
	 *
	 * Builds the query filters to be used by the riders model
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
		// Filter by district code
		$districtCode = $this->getState('filter.district_code');

		if (!empty($districtCode))
		{
			if ($districtCode != 'All')
			{
				$districtCode = $db->q(str_replace(' ', '%', $db->escape(trim($districtCode), true)));
				$query
					->where($db->qn('rc.district_code') . ' = ' . $districtCode);
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

		// Join over the districts for the district names.
		$query
			->select($db->qn('d.district_name'))
			->join('LEFT', $db->qn('#__districts', 'd') . ' ON (' . $db->qn('rc.district_code') . ' = ' . $db->qn('d.district_code') . ')');

		// Don't retrieve blacklisted riders.
		$query
			->where($db->qn('blacklist_ind') . ' = 0');

		return $query;
	}

	/**
	 * getQueryOrder
	 *
	 * Builds the query ordering to be used by the riders model
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
			->order('(SELECT REPLACE(' . $db->qn('name') . ',".","~")) ASC');

		return $query;
	}
}
