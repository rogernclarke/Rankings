<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rider View
 *
 * @since 2.0
 */
class RankingsViewRider extends JViewLegacy
{
	/**
	 * Display the Rider view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed   A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->rider = $this->get('Item');

		// Prepare the data.
		// Compute the tt ride event link url.
		foreach ($this->rider->ttRides as $ride)
		{
			$ride->link = JRoute::_(RankingsHelperRoute::getEventRoute($ride->event_id));
		}

		// Compute the hc ride event link url.
		foreach ($this->rider->hcRides as $ride)
		{
			$ride->link = JRoute::_(RankingsHelperRoute::getEventRoute($ride->event_id));
		}

		// Compute the award event link url.
		foreach ($this->rider->awards as $award)
		{
			$award->link = JRoute::_(RankingsHelperRoute::getEventRoute($award->event_id));
		}

		// Display
		return parent::display($tpl);
	}
}
