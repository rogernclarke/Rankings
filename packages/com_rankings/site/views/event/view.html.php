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
 * Event View
 *
 * @since 2.0
 */
class RankingsViewEvent extends JViewLegacy
{
	/**
	 * Display the Event view
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
		$this->setDocumentTitle($this->event->event_name);

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
		$this->event = $this->get('item');

		// Get entries from the model
		$this->entries = $this->get('items', 'entries');

		// Get results from the model
		$this->results = $this->get('items', 'results');

		// Get awards from the model
		$this->awards = $this->get('items', 'awards');
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
		// Entries data
		foreach ($this->entries as $entry)
		{
			if (isset($entry->predicted_time))
			{
				$this->event->predictedResults = true;
			}

			// Compute the entry link url.
			$entry->link = JRoute::_(RankingsHelperRoute::getRiderRoute($entry->rider_id));
		}

		// Results data
		$rideCount = 0;

		foreach ($this->results as $ride)
		{
			if ($ride->gender === "Female")
			{
				$this->event->femaleResults = true;
			}
			else
			{
				$this->event->maleResults = true;
			}

			if (isset($ride->vets_standard_result))
			{
				$this->event->vetsResults = true;
			}

			// Assign awards to results
			$awardCount = 0;

			foreach ($this->awards as $award)
			{
				if ($ride->event_id === $award->event_id)
				{
					$this->results[$rideCount]->awards[$awardCount] = $award;
					$awardCount++;
				}
			}

			// Compute the result link url.
			$ride->link = JRoute::_(RankingsHelperRoute::getRiderRoute($ride->rider_id));

			$rideCount++;
		}

		// Awards data
		foreach ($this->awards as $award)
		{
			// Compute the award link url.
			$award->link = JRoute::_(RankingsHelperRoute::getRiderRoute($award->rider_id));
		}
	}
}
