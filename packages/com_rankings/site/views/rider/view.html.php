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
		// Load the models
		$this->loadModels();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			\JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		$this->prepareData();

		// Prepare the document
		$this->setDocumentTitle($this->rider->name);

		// Display
		return parent::display($tpl);
	}

	/**
	 * Load the models
	 *
	 * @return  void
	 *
	 * @since 2.0
	 */
	protected function loadModels()
	{
		// Get the model
		$model = $this->getModel();

		// Get the parameters
		$this->params = $model->getState('params');

		// Get some data from the model
		$this->rider = $this->get('item');

		// Get tt data from the model
		$this->ttRides = $this->get('items', 'ttrides');

		// Get tt rides pagination
		$this->ttRidesPagination = $this->get('pagination', 'ttrides');
		$this->ttRidesPagination->setAdditionalUrlParam('list', 'ttrides');

		// Get hc data from the model
		$this->hcRides = $this->get('items', 'hcrides');

		// Get hc rides pagination
		$this->hcRidesPagination = $this->get('pagination', 'hcrides');
		$this->hcRidesPagination->setAdditionalUrlParam('list', 'hcrides');

		// Get some data from the model
		$this->awards = $this->get('items', 'awards');

		// Get awards pagination
		$this->awardsPagination = $this->get('pagination', 'awards');
		$this->awardsPagination->setAdditionalUrlParam('list', 'awards');

		// Get some data from the model
		$this->riderhistories = $this->get('items', 'riderhistories');
	}

	/**
	 * Prepare the data
	 *
	 * @return  void
	 *
	 * @since 2.0
	 */
	protected function prepareData()
	{
		// TT Rides data
		$rideCount = 0;

		foreach ($this->ttRides as $ride)
		{
			// Assign awards to rides
			$awardCount = 0;

			foreach ($this->awards as $award)
			{
				if ($ride->event_id === $award->event_id)
				{
					$this->ttRides[$rideCount]->awards[$awardCount] = $award;
					$awardCount++;
				}
			}

			$rideCount++;

			// Compute the event link url
			$ride->link = JRoute::_(RankingsHelperRoute::getEventRoute($ride->event_id));
		}

		// HC Rides data
		$rideCount = 0;	

		foreach ($this->hcRides as $ride)
		{
			// Assign awards to rides
			$awardCount = 0;

			foreach ($this->awards as $award)
			{
				if ($ride->event_id === $award->event_id)
				{
					$this->hcRides[$rideCount]->awards[$awardCount] = $award;
					$awardCount++;
				}
			}

			$rideCount++;

			// Compute the hc ride event link url.
			$ride->link = JRoute::_(RankingsHelperRoute::getEventRoute($ride->event_id));
		}

		// Awards data
		foreach ($this->awards as $award)
		{
			// Compute the event link url
			$award->link = JRoute::_(RankingsHelperRoute::getEventRoute($award->event_id));
		}
	}
}
