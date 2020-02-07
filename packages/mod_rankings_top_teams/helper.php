<?php
/**
 * Rankings Top Teams Module for Joomla 3.x
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
 * Rankings Component Top Teams Module Helper
 *
 * @since   2.0
 */
class ModRankingsTopTeamsHelper
{
	/**
	 * Retrieves the top ranked teams
	 *
	 * @param   array  $params An object containing the module parameters
	 *
	 * @return  object  List of teams
	 *
	 * @since 2.0
	 */
	public static function getTeams($params)
	{
		// Obtain a database connection
		$options = array();

		$app = JFactory::getApplication();
		$componentParams = $app->getParams('com_rankings');

		$db = JDatabaseDriver::getInstance($componentParams);

		// Prepare the query
		$limit = (int) $params['teams_count'];
		$gender = $db->quote(str_replace(' ', '%', $db->escape(trim($params['gender']), true)));

		$db->setQuery("call get_top_teams(" . $gender . ", 0, " . $limit . ")");

		// Load the teams
		$teams = $db->loadObjectList();

		// Prepare the data.
		// Compute the rider link urls.
		foreach ($teams as $team)
		{
			$team->riderLink1 = JRoute::_(RankingsHelperRoute::getRiderRoute($team->rider_id_1));
			$team->riderLink2 = JRoute::_(RankingsHelperRoute::getRiderRoute($team->rider_id_2));
			$team->riderLink3 = JRoute::_(RankingsHelperRoute::getRiderRoute($team->rider_id_3));
		}

		// Return the set of teams
		return $teams;
	}
}
