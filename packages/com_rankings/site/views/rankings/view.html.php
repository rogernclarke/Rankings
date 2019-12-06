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
 * Rankings List View
 *
 * @since 2.0
 */
class RankingsViewRankings extends JViewLegacy
{
	/**
	 * Display the Events view
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

		$app = \JFactory::getApplication();

		if ($app->input->getMethod() == 'POST')
		{
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
		$model = $this->getModel();

		// Get the parameters
		$this->params = $model->getState('params');

		// Get some data from the model
		$this->rankings = $this->get('items');

		// Get the form
		$this->form = $this->getForm();

		// Get the total number of riders
		$totalEvents = $this->get('total');

		// Get pagination
		$this->pagination = $this->get('pagination');

		// Get the list state
		$this->state = $this->get('state');

		// Get the rides for each ranking
		foreach ($this->rankings as $ranking)
		{
			// Get the rides model
			$ridesModel = $this->getModel('rides');

			$ridesModel->set('riderId', $ranking->rider_id);
			$ridesModel->set('rankingStatus', $ranking->status);
			$ridesModel->set('year', $rankingsModel->year);

			// Get some data from the model
			$ranking->rides = $this->get('items', 'rides');		
		}
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
		// Compute the link urls
		foreach ($this->rankings as $ranking)
		{
			$ranking->link = JRoute::_(RankingsHelperRoute::getRiderRoute($ranking->rider_id));

			foreach ($ranking->rides as $ride)
			{
				$ride->link = JRoute::_(RankingsHelperRoute::getEventRoute($ride->event_id));
			}
		}
	}
}
