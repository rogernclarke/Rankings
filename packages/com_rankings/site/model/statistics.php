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
 * Rankings Component Statistics Model
 *
 * @since 2.0
 */
class RankingsModelStatistics extends RankingsModelList
{
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
				'year'
			);
		}

		parent::__construct($config);

		// Store the year from the request, set to year of last run date if not set
		$this->year 		= (int) $this->getState('filter.year');
		/*$lastRunDateYear 	= date("Y", strtotime($this->getLastRunDate()));

		if (empty($this->year))
		{
			$this->year = $lastRunDateYear;
		} */
	}
}
