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
 * Riders List View
 *
 * @since 2.0
 */
class RankingsViewRidersByRideCount extends JViewLegacy
{
	/**
	 * Display the Riders By Ride Count view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed 	A string if successful, otherwise an Error object.
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
		$this->setDocumentTitle($this->params->get('page_title'));

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
		$model = $this->getModel('statistics');

		// Get the parameters
		$this->params = $model->getState('params');

		// Get the form
		$this->form = $model->getForm('statistics');

		// Get the model state
		$this->state = $this->get('State');

		// Get some data from the model
		$this->riders = $this->get('ridersbyridecount');
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
		// Compute the event link url
		foreach ($this->riders as $rider)
		{
			$rider->link = JRoute::_(RankingsHelperRoute::getRiderRoute($rider->rider_id));
		}

	}
}
