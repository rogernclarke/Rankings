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
class RankingsViewRiders extends JViewLegacy
{
	/**
	 * Display the Riders view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed 	A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$app    = \JFactory::getApplication();
		$user   = \JFactory::getUser();
		$params = $app->getParams();

		// Get the model
		$model      = $this->getModel();
		$params 	= $model->getState('params');

		if ($app->input->getMethod() == 'POST')
		{
			return false;
		}

		// Get some data from the model
		$this->riders = $model->getItems();

		// Get the form
		$this->form = $model->getForm();

		// Get the total number of riders
		$totalRiders = $model->getTotal();

		// Get pagination
		$this->pagination = $this->get('Pagination');

		// Get the list state
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			\JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Prepare the data.
		// Compute the rider slug & link url.
		foreach ($this->riders as $rider)
		{
			$rider->link = JRoute::_(RankingsHelperRoute::getRiderRoute($rider->rider_id));
		}

		return parent::display($tpl);
	}

}
