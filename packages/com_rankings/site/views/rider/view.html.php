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

		// Get the form
		$this->form = $model->getForm();

		// Get the model state
		$this->state = $this->get('state');

		// Get rider data from the model
		$this->rider = $this->get('item');

		// Get last run date from the model
		$this->lastRunDate = date("Y", strtotime($this->get('lastrundate')));

		// Get entries data from the model
		$this->entries = $this->get('items', 'entries');

		// Get pending results data from the model
		$this->pending = $this->get('items', 'resultspending');

		// Get tt rides from the model
		$this->ttRides = $this->get('items', 'ttrides');

		// Get tt rides pagination
		//$this->ttRidesPagination = $this->get('pagination', 'ttrides');
		//$this->ttRidesPagination->setAdditionalUrlParam('list', 'ttrides');

		// Get hc rides from the model
		$this->hcRides = $this->get('items', 'hcrides');

		// Get hc rides pagination
		//$this->hcRidesPagination = $this->get('pagination', 'hcrides');
		//$this->hcRidesPagination->setAdditionalUrlParam('list', 'hcrides');

		// Get tt awards data from the model
		$this->ttAwards = $this->get('items', 'ttawards');

		// Get tt awards pagination
		//$this->ttAwardsPagination = $this->get('pagination', 'ttawards');
		//$this->ttAwardsPagination->setAdditionalUrlParam('list', 'ttawards');

		// Get hc awards data from the model
		$this->hcAwards = $this->get('items', 'hcawards');

		// Get hc awards pagination
		//$this->hcAwardsPagination = $this->get('pagination', 'hcawards');
		//$this->hcAwardsPagination->setAdditionalUrlParam('list', 'hcawards');

		// Get tt history data from the model
		$this->ttriderhistories = $this->get('items', 'ttriderhistories');

		// Get hc history data from the model
		$this->hcriderhistories = $this->get('items', 'hcriderhistories');
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
		// Rider data
		$this->ttriderhistory = end($this->ttriderhistories);
		$this->hcriderhistory = end($this->hcriderhistories);

		if ($this->rider->district_rank == 0)
		{
			$this->rider->district_rank = 'N/A';
		}

		if ($this->rider->hc_district_rank == 0)
		{
			$this->rider->hc_district_rank = 'N/A';
		}

		// Entries data
		$this->hcEntries = array();
		$this->ttEntries = array();

		foreach ($this->entries as $entry)
		{
			// Compute the event link url
			$entry->link = JRoute::_(RankingsHelperRoute::getEventRoute($entry->event_id));

			// Split time trials and hill climbs
			if ($entry->hill_climb_ind)
			{
				array_push($this->hcEntries, $entry);
			}
			else
			{
				array_push($this->ttEntries, $entry);
			}
		}

		// Pending Results data
		$this->hcPending = array();
		$this->ttPending = array();

		foreach ($this->pending as $pending)
		{
			// Compute the event link url
			$pending->link = JRoute::_(RankingsHelperRoute::getEventRoute($pending->event_id));

			// Split time trials and hill climbs
			if ($pending->hill_climb_ind)
			{
				array_push($this->hcPending, $pending);
			}
			else
			{
				array_push($this->ttPending, $pending);
			}
		}

		// TT Rides data
		$rideCount = 0;

		foreach ($this->ttRides as $ride)
		{
			// Set distance
			if ($ride->duration_event_ind)
			{
				$ride->distance = abs($ride->event_distance) . ' hours';
			}
			elseif ($ride->event_distance > 0)
			{
				$ride->distance = abs($ride->event_distance) . ' miles';
			}
			else
			{
				$ride->distance = '-';
			}

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
			// Set distance
			if ($ride->duration_event_ind)
			{
				$ride->distance = abs($ride->event_distance) . ' hours';
			}
			elseif ($ride->event_distance > 0)
			{
				$ride->distance = abs($ride->event_distance) . ' miles';
			}
			else
			{
				$ride->distance = '-';
			}
			
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
		foreach ($this->hcAwards as $award)
		{
			// Compute the event link url
			$award->link = JRoute::_(RankingsHelperRoute::getEventRoute($award->event_id));
		}
		foreach ($this->ttAwards as $award)
		{
			// Compute the event link url
			$award->link = JRoute::_(RankingsHelperRoute::getEventRoute($award->event_id));
		}
	}
}
